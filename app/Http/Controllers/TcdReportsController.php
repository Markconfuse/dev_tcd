<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterval;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TcdExports;
use App\Classes\Constants\Filters;

use Storage;
use Session;
use Exception;
use Config;
use File;
use URL;
use DB;

use App\LibBrand;
use App\LibTransaction;
use App\Ticket;
use App\RequestType;
use App\UserRole;
use App\TicketAssignment;
use App\TcdAccounts;
use App\TCDReports;


use App\Traits\LogQueries;
use App\Traits\BrandTicketQueries;

class TcdReportsController extends Controller
{
    use LogQueries;
	// use BrandTicketQueries;
	public function __construct()
	{
		$this->db = Config::get('dbcon.db'); 
        $this->middleware('auth');
	}

    public function index(Request $request)
    {	
    	try {
            date_default_timezone_set('Asia/Manila');
            $dTime = date('F j, Y');
            $roles = Filters::ROLE;
            $months = Filters::MONTHS;
            $years = Filters::YEARS;
            return view('tcd_reports.reports', [
                'dateTime' => $dTime,
                'months' => $months,
                'years' => $years,
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function tcdReportsDataTable(Request $request)
{
    // Set timezone
    date_default_timezone_set('Asia/Manila');

    // Handle months: default to all if empty or 'ALL'
    $xMonths = $request->dMonths ? (is_array($request->dMonths) ? $request->dMonths : explode(',', $request->dMonths)) : ['ALL'];
    if (in_array('ALL', $xMonths)) {
        $xMonths = Filters::MONTHS;
    }

    // Handle years: default to current year if empty
    $xYears = $request->dYears ? (is_array($request->dYears) ? $request->dYears : explode(',', $request->dYears)) : [date('Y')];

    // Get ticket status
    $tStatus = $request->tStatus;

    // Base query
    $query = DB::table('ticket as T')
        ->leftJoin(DB::raw('(SELECT MIN(date_replied) as first_reply_engr, MAX(date_replied) as last_reply_engr, ticket_id
                            FROM ticket_reply
                            WHERE user_id IN (SELECT DISTINCT account_id FROM vw_tcd_accounts WHERE role_id IN (3))
                            GROUP BY ticket_id) as TREP'), 'T.ticket_id', '=', 'TREP.ticket_id')
        ->leftJoin(DB::raw('(SELECT MAX(date_replied) as last_reply_req, ticket_id
                            FROM ticket_reply
                            WHERE user_id IN (SELECT DISTINCT account_id FROM vw_tcd_accounts WHERE role_id IN (4))
                            GROUP BY ticket_id) as TREQ'), 'T.ticket_id', '=', 'TREQ.ticket_id')
        ->leftJoin(DB::raw('(SELECT MIN(date_assigned) AS date_assigned, ticket_id
                            FROM ticket_assignment
                            GROUP BY ticket_id) as TA'), 'T.ticket_id', '=', 'TA.ticket_id')
        ->leftJoin('lib_request_type as LRT', 'T.request_type_id', '=', 'LRT.request_type_id')
        ->leftJoin('vw_tcd_accounts as TCD1', 'T.account_owner_id', '=', 'TCD1.account_id')
        ->leftJoin('escalated_tickets as ET', 'T.ticket_id', '=', 'ET.ticket_id')
        ->select(
            'LRT.request_type',
            'T.project_name as project_name',
            'T.customer_name as company_name',
            'TCD1.AccountName as ao',
            'TCD1.AccountGroup as bu',
            'T.ticket_id as reference_number',
            'T.status_id as status',
            'T.date_created as date_requested',
            'T.last_updated as last_updated',
            'TA.date_assigned as date_assigned',
            'ET.date_updated as date_updated',
            'ET.is_approved as is_approved',
            'ET.is_checked as is_checked',
            'ET.escalated_reply as escalated_reply',
            'ET.escalation_date as escalation_date',
            'ET.approved_by as approved_by',
            DB::raw('(SELECT STUFF(
                        (SELECT \', \' + TCD2.AccountName
                            FROM ticket_assignment TA2
                            INNER JOIN dbo.vw_tcd_accounts TCD2 ON TA2.owner_id = TCD2.account_id
                            WHERE TA2.ticket_id = T.ticket_id
                            FOR XML PATH(\'\')
                        ), 1, 2, \'\')
                    ) AS engineers'),
            'TREP.first_reply_engr',
            'TREQ.last_reply_req',
            'TREP.last_reply_engr'
        )
        ->where(function($queryM) use ($xMonths) {
            foreach ($xMonths as $month) {
                if (!empty($month)) {
                    $queryM->orWhere(DB::raw('MONTH(T.date_created)'), Carbon::parse($month)->format('m'));
                }
            }
        })
        ->whereIn(DB::raw('YEAR(CAST(T.date_created as DATE))'), $xYears);

    // Apply status filters
    switch ($tStatus) {
        case 7: // Escalated
            $query->whereNotNull('ET.id');
            break;
        case 9: // Declined
            $query->where('ET.is_approved', 0)
                  ->where('ET.is_checked', 1);
            break;
        case 20: // All tickets
            // no extra filters
            break;
        default:
            // Specific status
            $query->where('T.status_id', $tStatus);
            break;
    }

    // Execute query
    $results = collect($query->get());

    // Datatables formatting
    return datatables()->of($results)
        ->editColumn('project_name', fn($data) => $data->project_name ?? 'NOT STATED BY THE USER')
        ->editColumn('status', function($data) {
    switch($data->status) {
        case 1:
            return 'Unassigned';
        case 2:
            return 'Assigned';
        case 3:
            return 'Answered';
        case 4:
            return 'Closed';
        default:
            return $data->status;
    }
})
        ->editColumn('last_updated', fn($data) => $data->status == 4 ? $data->last_updated : '')
        ->editColumn('is_approved', function($data) {
            if ($data->is_approved == '0' && $data->is_checked == '0') return 'Escalated';
            if ($data->is_approved == '1' && $data->is_checked == '1') return 'Acknowledged';
            if ($data->is_approved == '0' && $data->is_checked == '1') return 'Declined';
            return 'N/A';
        })
        ->editColumn('escalated_reply', function($data) {
            if (!empty($data->escalated_reply)) return $data->escalated_reply;
            if (!empty($data->escalation_date) && empty($data->escalated_reply)) return '';
            return 'N/A';
        })
        ->make();
}

   public function exportTcdMonthYear(Request $request)
{
    $xM = $request->month ?? ['ALL'];
    $xY = $request->year ?? [date('Y')];

    $xM = is_array($xM) ? $xM : explode(',', $xM);
    $xY = is_array($xY) ? $xY : explode(',', $xY);

    $monthNames = array_map(function($m) {
        if (strtoupper($m) === 'ALL') return 'ALL';
        if (is_numeric($m)) return date('M', mktime(0,0,0,$m,1));
        return $m;
    }, $xM);

    $monthsPart = implode('-', $monthNames);
    $yearsPart = implode('-', $xY);
    $timestamp = date('Ymd_His');

    $fileName = "TCD_{$monthsPart}_{$yearsPart}_{$timestamp}.xlsx";

    return Excel::download(new TcdExports($xM, $xY), $fileName);
}


}
