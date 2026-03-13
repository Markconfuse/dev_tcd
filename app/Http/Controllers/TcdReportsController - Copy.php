<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TcdExports;

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
           
            $months = ['JANUARY', 
                'FEBRUARY', 
                'MARCH', 
                'APRIL', 
                'MAY', 
                'JUNE', 
                'JULY', 
                'AUGUST',
                'SEPTEMBER',
                'OCTOBER',
                'NOVEMBER',
                'DECEMBER'
            ];

            $years = ['2020', 
                '2021', 
                '2022', 
                '2023',
				'2024'
            ];
            
            date_default_timezone_set('Asia/Manila');
            $dTime = date('F j, Y');
			$this->saveLogs('Viewed: TCD Exports Page');
            return view('tcd_reports.reports', [
                'dateTime' => $dTime,
                'months' => $months,
                'years' => $years
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function tcdReportsDataTable(Request $request)
    {
        if($request->dMonths == ['ALL']){
            $xMonths =  ['JANUARY', 
                    'FEBRUARY', 
                    'MARCH', 
                    'APRIL', 
                    'MAY', 
                    'JUNE', 
                    'JULY', 
                    'AUGUST',
                    'SEPTEMBER',
                    'OCTOBER',
                    'NOVEMBER',
                    'DECEMBER'
                ];
        } else {
            $xMonths = $request->dMonths;
        }

        $xYears = $request->dYears; 
		//$sMonths = implode('', $request->dMonths);
		//$sYears = implode('', $request->dYears);
       
        date_default_timezone_set('Asia/Manila');
        $dTime = date('F j, Y');
       $query = DB::connection('tcd_login')
    ->table('ticket as T')
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
    )->where(function($queryM) use ($xMonths, $xYears) {
        foreach ($xMonths as $month) {
            $queryM->orWhere(DB::raw('MONTH(T.date_created)'), carbon::parse($month)->format('m'));
        }
    })
    ->whereIn(DB::raw('YEAR(CAST(T.date_created as DATE))'), $xYears)
   ->get();
            
            $results = collect($query);
            $reports = datatables()->of($results);
			$this->saveLogs('Exported TCD Data');
            return $reports
                ->editColumn('project_name', function ($data) {
                if ($data->project_name == NULL){
                    return 'NOT STATED BY THE REQUESTOR';
                } else {
                    return $data->project_name;
                }
        })
		->editColumn('status', function ($data) {
                if ($data->status == 1){
                    return 'Unassigned';
                } else if ($data->status == 2){
                    return 'Assigned';
                } elseif ($data->status == 3){
                    return 'Answered';
                } else if ($data->status == 4){
                    return 'Closed';
                } else {
                    return $data->status;
                }
                })
		->editColumn('last_updated', function ($data) {
                    if ($data->status == 4){
                        return $data->last_updated;
                    } else {
                        return 'N/A';
                    }
                })->make();

    }

    public function exportTcdMonthYear(Request $request){
        \Log::info($request->month);
        $xM = $request->month;
        $xY = $request->year;
        return Excel::download(new TcdExports($xM, $xY), 'tcd_reports.xlsx');
    }


}
