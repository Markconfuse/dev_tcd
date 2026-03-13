<?php

namespace App\Http\Controllers;

use DB;
use Config;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Ticket;
use App\Traits\LogQueries;

class DashboardController extends Controller
{
    use LogQueries;

    public function __construct()
    {
        $this->db = Config::get('dbcon.db'); 
        $this->middleware('auth');
    }

    public function dashboard() 
    {
        $this->saveLogs('Viewed Dashboard');

        $_buGroup = Session('userData')->AccountGroup;

        if($_buGroup == 'BU8' || $_buGroup == 'BU12') {
            $_buGroup = 'BU8,BU12';
        } 

        if(Session('userData')->role_id == '4') { //Requestor
            if(\Common::instance()->is_head(Session('userData')->AccountGroup) > 0) { //Head
                $dash_label = $_buGroup;

                $ticket_today = Ticket::joinESAO()
                                ->whereRaw('DATEADD(dd, 0, DATEDIFF(dd, 0, date_created)) = DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE()))')
                                ->AccountGroup()
                                ->getCount();

                $ticket_pending = Ticket::joinESAO()->statusID(1)->AccountGroup()->getCount();

                $ticket_open = Ticket::joinESAO()->whereIn('status_id', ['1', '2', '3'])->AccountGroup()->getCount();

                $ticket_closed = Ticket::joinESAO()->statusID(4)->AccountGroup()->getCount();

                $ticket_count_ao = DB::TABLE('vw_dash_ticket_count_ao')->whereIn('AccountGroup', explode(',', $_buGroup))->get();
            } else { //AO
                $dash_label = 'My';

                $ticket_today = Ticket::tixAccOwner()
                                ->whereRaw('DATEADD(dd, 0, DATEDIFF(dd, 0, date_created)) = DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE()))')->getCount();

                $ticket_pending = Ticket::tixAccOwner()->statusID(1)->getCount();

                $ticket_open = Ticket::whereIn('status_id', ['1', '2', '3'])->where('account_owner_id', Session('userData')->account_id)->getCount();

                $ticket_closed = Ticket::tixAccOwner()->statusID(4)->getCount();

                $ticket_count_ao = DB::TABLE('vw_dash_ticket_count_ao')->whereIn('AccountGroup', explode(',', $_buGroup))->get();
            }

            return view('dashboards.dash_main', compact('dash_label', 'ticket_today', 'ticket_pending', 'ticket_open', 'ticket_closed',
                                                    'ticket_count_ao'));
        } else { //Buyer, Admin, SuperUser
            $ticket_count_ao = DB::TABLE('vw_dash_ticket_count_ao')->whereNotIn('AccountGroup', ['IT', 'ESD', 'TCD', 'ESG'])->get();
            $ticket_count_engineer = DB::TABLE('vw_dash_ticket_count_engineer')->get();

            if(Session('userData')->role_name == 'engineer') {
                $dash_label = 'My';

                $ticket_today = Ticket::whereRaw('DATEADD(dd, 0, DATEDIFF(dd, 0, date_created)) = DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE()))')->getCount();

                $ticket_pending = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->getCount();

                $ticket_open = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->whereIn('status_id', ['1', '2', '5', '6'])->getCount();

                $ticket_closed = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->statusID(4)->getCount();

            } else {
                $dash_label = 'All';

                $ticket_today = Ticket::whereRaw('DATEADD(dd, 0, DATEDIFF(dd, 0, date_created)) = DATEADD(dd, 0, DATEDIFF(dd, 0, GETDATE()))')->getCount();

                $ticket_pending = Ticket::statusID(1)->getCount();

                $ticket_open = Ticket::whereIn('status_id', ['1', '2', '3'])->getCount();

                $ticket_closed = Ticket::statusID(4)->getCount();
            }

            $now = new DateTime();

            $_fromDate = $now->format('01/01/Y');
            $_toDate = $now->format('m/d/Y');

            $_dtpMonth = 'January 1, '.date('Y').' - '.$now->format('F d, Y');

            $_whereDate = " CAST(date_assigned AS DATE) >= CAST(''$_fromDate'' AS DATE) ";
            $_whereDate .= "AND CAST(date_assigned AS DATE) <= CAST(''$_toDate'' AS DATE) ";    

            $engineer_per_day = DB::SELECT("EXEC [dbo].[sp_engrReport] '$_whereDate'");

            return view('dashboards.dash_main', compact('dash_label', 'ticket_today', 'ticket_pending', 'ticket_open', 'ticket_closed',
                                                    'ticket_count_ao', 'ticket_count_engineer', 'engineer_per_day'));
        }

        
    }

    public function getChartRequestType()
    {

        $_strQry = 'SELECT B.request_type_id, request_type, ISNULL(cnt, 0) as count_per_rtype FROM (';
        $_strQry .= ' SELECT count(*) as cnt, request_type_id FROM ticket A';
        if(Session('userData')->role_name == 'requestor') {
            $_strQry .= " INNER JOIN vw_crm_accounts B ON B.AccountID = A.account_owner_id";
            if(Session('userData')->AccountGroup == 'BU8' || Session('userData')->AccountGroup == 'BU12') {
                $_strQry .= " WHERE AccountGroup IN('BU8', 'BU12')";
            } else {
                $_strQry .= " WHERE AccountGroup IN('";
                $_strQry .= Session('userData')->AccountGroup;
                $_strQry .= "')";
            }
        } 
        $_strQry .= ' GROUP BY request_type_id ) A right outer join lib_request_type B';
        $_strQry .= ' ON A.request_type_id = B.request_type_id';

        return response()->json(['data' => DB::SELECT($_strQry)]);
    }

    public function getChartPerAO()
    {
        $_strQry = 'SELECT ISNULL(SUM(cnt), 0) as cnt, AccountName, AccountGroup FROM (';
        $_strQry .= ' SELECT count(*) as cnt, account_owner_id FROM ticket A';
        $_strQry .= ' GROUP BY account_owner_id';
        $_strQry .= ' ) A RIGHT OUTER JOIN vw_crm_accounts B';
        $_strQry .= ' ON A.account_owner_id = B.AccountID';
        $_strQry .= ' WHERE';
        if(Session('userData')->role_name == 'requestor') {
            if(Session('userData')->AccountGroup == 'BU8' || Session('userData')->AccountGroup == 'BU12') {
                $_strQry .= " AccountGroup IN('BU8', 'BU12') AND";
            } else {
                $_strQry .= " AccountGroup IN('";
                $_strQry .= Session('userData')->AccountGroup;
                $_strQry .= "') AND";
            }
        } 
        $_strQry .= " B.AccountID IN (SELECT account_id FROM user_role) AND AccountGroup NOT IN ('IT', 'ESD', 'TCD', 'ESG')";
        $_strQry .= ' GROUP BY AccountName, AccountGroup ORDER BY 1 desc';

        return response()->json(['data' => DB::SELECT($_strQry)]);
    }

    public function getChartPerBU()
    {
        $chart_per_bu = DB::SELECT("SELECT SUM(cnt) as cnt, AccountGroup FROM (
                                    SELECT count(*) as cnt, requestor_id FROM ticket A 
                                    INNER JOIN vw_crm_accounts B
                                    ON A.requestor_id = B.AccountID
                                    GROUP BY requestor_id ) A INNER JOIN vw_crm_accounts B
                                    ON A.requestor_id = B.AccountID
                                    GROUP BY AccountGroup ORDER BY 1 desc");

        return response()->json(['data' => $chart_per_bu]);
    }

    public function getTicketPerBu(Request $request)
    {
        $_bu = $request->bu;

        $_qry = Ticket::ticketQry()->where('esrid.AccountGroup', $_bu)->get();

        return response()->json(['data' => $_qry]);
    }

    public function getTicketPerEngineer(Request $request)
    {
        $_data = explode('|', $request->data);

        //Buyer && Focus
        if($_data[1] == '1') {
            $_qry = Ticket::ticketQry()->joinAssign()->assignedWhere($_data[0])->notDeleted()->Unanswered()->get();
        } else if($_data[1] == '3') {
            $_qry = Ticket::ticketQry()->joinAssign()->assignedWhere($_data[0])->notDeleted()->Answered()->notClosed()->get();
        } else if($_data[1] == '4') { 
            $_qry = Ticket::ticketQry()->joinAssign()->assignedWhere($_data[0])->notDeleted()->statusID(4)->get();
        } 

        return response()->json(['data' => $_qry]);
    }
}
