<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;


class TcdExports implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    protected $xM;
    protected $xY;
    
    function __construct($xM, $xY) {
        $this->xM = $xM;
        $this->xY = $xY;
    }

    public function collection()
    {
        $xMonths = $this->xM;
        $xYears = $this->xY;
        $xToM = explode(',', $xMonths);
        $xToY = explode(',', $xYears);

        $data = $query = DB::table('ticket as T')
        ->join('lib_request_type as LRT', 'T.request_type_id', '=', 'LRT.request_type_id')
        ->join('user_role as UR', 'T.account_owner_id', '=', 'UR.account_id')
        ->join('ticket_assignment as TA', 'T.ticket_id', '=', 'TA.ticket_id')
        ->join('vw_tcd_accounts as TCD1', 'T.account_owner_id', '=', 'TCD1.account_id')
        ->join('vw_tcd_accounts as TCD2', 'TA.owner_id', '=', 'TCD2.account_id')
        ->select('LRT.request_type',
                'T.ticket_id as reference_number', 
                'T.project_name as project_name', 
                'T.customer_name as company_name',
                'TCD1.AccountName as ao', 
                'TCD1.AccountGroup as bu', 
                'TCD2.AccountName as engr')
              
        ->where(function($query1) use ($xToM, $xToY) {
            foreach ($xToM as $month) {
                $query1->orWhere(DB::raw('MONTH(T.date_created)'), carbon::parse($month)->format('m'));
            }
        })
        ->whereIn(DB::raw('YEAR(CAST(T.date_created as DATE))'), $xToY)
        ->get();
        if ($data->isEmpty()) {
            \Log::info('No data to export');
        } else {
            return collect($data);
        }
    }

    public function headings(): array
    {
        return [
            'request_type',
            'reference_number',
            'project_name',
            'customer_name',
            'ao',
            'bu',
            'engr'
        ];
     }

}

