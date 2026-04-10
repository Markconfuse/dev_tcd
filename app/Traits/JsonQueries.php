<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Session;
use DateTime;
use Response;
use DataTables;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Ticket;
use App\History;
use App\LibHeads;
use App\LibBrand;
use App\ESDAccount;
use App\UserNotification;

trait JsonQueries
{

    public function isHead()
    {
        return LibHeads::where('account_id', Session('userData')->account_id)->count();
    }

    public function searchTix(Request $request)
    {
        $_aoID = base64_decode($request->aoID);

        return response()->json(['data' => Ticket::ticketQry()->joinCarbon()
                                                    ->where('ticket.account_owner_id', $_aoID)
                                                    ->orWhere('ticket.requestor_id', $_aoID)
                                                    ->orWhere('cc.account_id', $_aoID)
                                                    ->get()]);
    }

   
    
    public function getTicket(Request $request)
    {   \Log::info('kamon');
        // 1 Unassigned | 2 Assigned | 3 Answered | 4 Closed
        // 31 Pending

        $_role = Session::get('userData')->role_name;
        $_id = Session::get('userData')->account_id;
        $_statusID = $request->sid;
        $_tTypeID = $request->tid;

        $selectedYear = \App\Setting::getYearFilter();
        $filterByYear = ($selectedYear !== 'All');
        // Status IDs that are action queues - NEVER apply year filter
        $actionQueueStatuses = ['1', '2', '5', '15', '16', '31'];

        // dd($request);

        if($_role == 'admin' || $_role == 'super_user') {

            if($_statusID == '5') { //Reassigned
                //$query = Ticket::ticketQry()->joinAssign()->Deleted();
				$query = Ticket::ticketQry()->joinAssign()->Deleted();
            } else if($_statusID == '6') {
                $query = Ticket::ticketQry()->where('esao.AccountGroup', 'CE01');
            } else if ($_statusID == '10') {
                $query = Ticket::ticketQry();
			} else if ($_statusID == '20'){
                $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked', 'escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*');
						
			} else if (Session('userData')->account_id == 57627){
				if ($_statusID == '1'){
					$query = Ticket::ticketQry()->ExcludeAppsdev()->statusID($_statusID);
				}
            } else {
                $query = Ticket::ticketQry()->ExcludeAppsdev()->statusID($_statusID);
            }
        } 


        if ($_role == 'engineer') {
            // 1 Pending | 3 Answered 4 // Closed
            if ($_statusID == '10') {
                $query = Ticket::ticketQry();
				
				//if (Session('userData')->account_id == 57786) {
                    //$query = Ticket::ticketQry()->Where('ticket.requestor_id', 57616)
                    //->orWhere('ticket.requestor_id', 57786);
                //}
				
				// Robles as EUC Consultant
				//if (Session('userData')->account_id == 57812) {
                    //$query = Ticket::ticketQry()->Where('ticket.requestor_id', 57619)
                    //->orWhere('ticket.requestor_id', 57812);
                //} else {
					//$query = Ticket::ticketQry();
				//}

                } else if ($_statusID == '15') {
                    \log::info('unread san engr.');
                $query = \App\Ticket::ticketQry()->joinAssign()
                  
                    ->where('ticket.is_deleted', 0)
                    ->where('assign.is_deleted', 0)
                    ->where('ticket.status_id', 1)
                    ->where('assign.is_read', 0)
                    ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                // ->get();

           } else if ($_statusID == '16') {
                $query = \App\Ticket::ticketQry()->joinAssign()
                    
                    ->where('ticket.is_deleted', 0)
                    ->where('assign.is_deleted', 0)
                    ->where('ticket.status_id', 1)
                    ->where('assign.is_read', 1)
                    ->where('assign.is_answered', 0)
                    ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                // ->get();
            
				
            } else {
                if( in_array(Session('userData')->account_id,array(57610,57615)) ) { // for Noel and Winkle
                  if($_statusID == '31' && Session('userData')->account_id == 57615) { //Pending of Winkle
                      $query = Ticket::getWithAssignment()->Unanswered()->NotDeleted();
                  } else if($_statusID == '5') { //Reassigned
                        $query = Ticket::ticketQry()->joinAssign()->Deleted();
						
						if (Session('userData')->account_id == 57786) {
                            $query = Ticket::ticketQry()->joinAssign()->Deleted()->Where('ticket.requestor_id', 57616)
                            ->orWhere('ticket.requestor_id', 57786);
                        }
						
                  } else if($_statusID == '6') {
                        $query = Ticket::ticketQry()->where('esao.AccountGroup', 'CE01');
						
						if (Session('userData')->account_id == 57786) {
                            $query = Ticket::ticketQry()->Where('ticket.requestor_id', 57616)
                            ->orWhere('ticket.requestor_id', 57786);
                        }
						
						if (Session('userData')->account_id == 57812) {
                            $query = Ticket::ticketQry()->Where('ticket.requestor_id', 57619)
                            ->orWhere('ticket.requestor_id', 57812);
                        }
						
  					    } else if($_statusID == '20'){
							  
							  if (Session('userData')->account_id == 57786) {

                        $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked','escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->Where('ticket.requestor_id', 57616)
                        ->orWhere('ticket.requestor_id', 57786);
                    
                        }
						
						if (Session('userData')->account_id == 57812) {

                        $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked','escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->Where('ticket.requestor_id', 57619)
                        ->orWhere('ticket.requestor_id', 57812);
                    
                        }
						
                          $query = Ticket::ticketQry()
                          ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                          ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                          ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                          ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                          ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                          ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked','escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                          'escalated_tickets.escalation_date', 'lrtx.request_type',
                          'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                          'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                          'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*');
                         
                    } else {
                          $query = Ticket::ticketQry()->statusID($_statusID);
                    }
                } else {
                    if($_statusID == '31') {
						if (Session('userData')->account_id == 57812){
                            $query = Ticket::GetRoblesWithAssignment()->Unanswered()->NotDeleted();
                        } else {
                        $query = Ticket::getWithAssignment()->Unanswered()->NotDeleted();
						}
                    } else if ($_statusID == '3') {
						if (Session('userData')->account_id == 57812){
                            
                        $query = Ticket::GetRoblesWithAssignment()->Answered()->NotDeleted()->notClosed();

                        } else {
                        $query = Ticket::getWithAssignment()->Answered()->NotDeleted()->notClosed();
						}
                    } else if($_statusID == '4') {
						if (Session('userData')->account_id == 57812){
                            $query = Ticket::getRoblesWithAssignment()->NotDeleted()->statusID($_statusID);
                        } else {
                        $query = Ticket::getWithAssignment()->NotDeleted()->statusID($_statusID);
						}
                    } else if($_statusID == '5') { //Reassigned
						if (Session('userData')->account_id == 57812){
                        $query = Ticket::getRoblesWithAssignment()->Deleted();
						} else {
							$query = Ticket::getWithAssignment()->Deleted();
						}
					} else if ($_statusID == '15') {
                        \Log::info('here la pot');
                        $query = \App\Ticket::ticketQry()->joinAssign()
                            ->where('assign.owner_id', $_id)
                            ->where('ticket.is_deleted', 0)
                            ->where('assign.is_deleted', 0)
                            ->where('ticket.status_id', 2)
                            ->where('assign.is_read', 0)
                            ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                        // ->get();

                    } else if ($_statusID == '16') {
                        $query = \App\Ticket::ticketQry()->joinAssign()
                            ->where('assign.owner_id', $_id)
                            ->where('ticket.is_deleted', 0)
                            ->where('assign.is_deleted', 0)
                            ->where('ticket.status_id', 2)
                            ->where('assign.is_read', 1)
                            ->where('assign.is_answered', 0)
                            ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                        // ->get();
                    
                    } else if($_statusID == '20'){
						// Robles EUC Consultant
						if(Session('userData')->account_id == 57812){
                        $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->join('ticket_assignment as TA1', 'TA1.ticket_id', '=', 'ticket.ticket_id')
                        ->join('vw_crm_accounts as AS1', 'AS1.AccountID', '=', 'TA1.owner_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked', 'escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->where('AS1.AccountID', '=', 57619);
											
						} else {
						$query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->join('ticket_assignment as TA1', 'TA1.ticket_id', '=', 'ticket.ticket_id')
                        ->join('vw_crm_accounts as AS1', 'AS1.AccountID', '=', 'TA1.owner_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked', 'escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->where('AS1.AccountID', '=', Session('userData')->account_id);
							 
						}
		
									
					}
                }
            }            
        } 

        // if ($_role == 'requestor') {

        //     if ($_statusID == '10') {
        //         $query = Ticket::ticketQry()->joinCarbon()->Where(function($query) {
        //             $query->AccountGroup()->orWhere('cc.account_id', Session('userData')->account_id);
        //         });
        //     } else {
        //         if($this->isHead() >= 1) {
        //             $query = Ticket::ticketQry()->statusID($_statusID)->AccountGroup();
        //         } else {
        //             $query = Ticket::ticketQry()->joinCarbon()->statusID($_statusID)->TixAccOwner()->orWhere->TixReqOwner()->statusID($_statusID)->orWhere->CC()->statusID($_statusID);
        //         }
        //     }
        // }

        if ($_role == 'requestor') {
            
            if ($_statusID == '10') {
                $query = Ticket::ticketQry()->joinCarbon()->Where(function($query) {
                    $query->AccountGroup()->orWhere('cc.account_id', Session('userData')->account_id);
                });

                } else if ($_statusID == '15') {
                    \Log::info('dinhi la pot');
                $query = \App\Ticket::ticketQry()->joinAssign()
                    ->where('assign.owner_id', $_id)
                    ->where('ticket.is_deleted', 0)
                    ->where('assign.is_deleted', 0)
                    ->where('ticket.status_id', 2)
                    ->where('assign.is_read', 0)
                    ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                // ->get();

            } else if ($_statusID == '16') {
                $query = \App\Ticket::ticketQry()->joinAssign()
                    ->where('assign.owner_id', $_id)
                    ->where('ticket.is_deleted', 0)
                    ->where('assign.is_deleted', 0)
                    ->where('ticket.status_id', 2)
                    ->where('assign.is_read', 1)
                    ->where('assign.is_answered', 0)
                    ->where('ticket.date_created', '<', DB::raw("DATEADD(HOUR, -24, GETDATE())"));
                // ->get();
            
            } else {
                if($this->isHead() >= 1) {
                    if($_statusID == '20'){
                        $_BU = [Session('userData')->AccountGroup];
                        if (Session('userData')->account_id == 387){
                            $_BU =  ['BU8','BU12','CE01'];
                        }
                        
                        $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked', 'escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->whereIn('esridx.AccountGroup', $_BU);
                       
                    }  else {
                        $query = Ticket::ticketQry()->statusID($_statusID)->AccountGroup();
                    }
                } else {
                    if ($_statusID == '20' && ( in_array(Session('userData')->account_id, array(1507,317)) ) ) {
                        
                        $query = Ticket::ticketQry()
                        ->join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
                        ->join('temp_search as tmpx', 'ticket.ticket_id', '=', 'tmpx.ticket_id')
                        ->join('lib_status as lsx', 'ticket.status_id', '=', 'lsx.status_id')
                        ->join('vw_crm_accounts as esridx', 'ticket.requestor_id', '=', 'esridx.AccountID')
                        ->join('lib_request_type as lrtx', 'ticket.request_type_id', '=', 'lrtx.request_type_id')
                        ->select('escalated_tickets.is_approved', 'escalated_tickets.is_checked', 'escalated_tickets.date_updated', 'escalated_tickets.approved_by','lsx.status_description', 
                        'escalated_tickets.escalation_date', 'lrtx.request_type',
                        'esridx.AccountID as ao_id', 'esridx.AccountName as requestor_name', 
                        'esridx.NickName as requestor_nickname', 'esridx.Email as requestor_email', 'esridx.GAvatar as GAvatarReq', 'esrid.AccountGroup',
                        'tmpx.ticket_reply', 'tmpx.OwnerName', 'tmpx.last_transaction', 'ticket.*')->whereIn('esridx.AccountGroup', [Session('userData')->AccountGroup]);
                       

                    } else {
                        $query = Ticket::ticketQry()->joinCarbon()->statusID($_statusID)->TixAccOwner()->orWhere->TixReqOwner()->statusID($_statusID)->orWhere->CC()->statusID($_statusID);
                    }
                }
            }
        }
		
		

        if ($_statusID == '17') {
            return $this->engineerStatsData($request);
        } else if ($_statusID == '19') {
            // ticket status counter
            return $this->engineerStatsCounter($request);
        } else {

            // Apply year filter for historical statuses only
            if ($filterByYear && !in_array($_statusID, $actionQueueStatuses)) {
                $query->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
            }

            \Log::info('DataTables raw data count: ' . $query->count());
            return Datatables::of($query)->orderColumn('status_id', 'last_updated $1')->blacklist(['status_id'])->filterColumn('ticket_content', function ($query, $keyword) {
                $query->whereRaw("CONCAT( ticket_content, ticket_reply, esao.AccountName, esrid.AccountName, OwnerName) LIKE ?", ["%{$keyword}%"]);
            })->editColumn('is_approved', function ($data) {
                if ($data->is_approved == '0' && $data->is_checked == '0') {
                    return 'Escalated';
                } else if ($data->is_approved == '1' && $data->is_checked == '1') {
                    return 'Acknowledged';
                } else if ($data->is_approved == '0' && $data->is_checked == '1') {
                    return 'Declined';
                } else {
                    return 'N/A';
                }
            })->make(true);
        }
    }
	
	private function getAllEngineers()
    {
        return  DB::connection('tcd_login')->table('vw_tcd_accounts as vta')
            ->join('ticket_assignment as ta', 'vta.account_id', '=', 'ta.owner_id')
            ->join('role as r', 'vta.role_id', '=', 'r.role_id')
            ->where('vta.role_id', 3)
            ->select('vta.AccountName', 'vta.GAvatar', 'vta.account_id')
            ->distinct()
            ->get();
    }


    private function getEngineerTicketCounts($date = null)
    {
        $query = DB::connection('tcd_login')->table('ticket_assignment as ta')
            ->join('ticket as t', 'ta.ticket_id', '=', 't.ticket_id')
            ->whereNotIn('t.status_id', [4])
            ->select('ta.owner_id', DB::raw('COUNT_BIG(*) as ticket_count'))
            ->groupBy('ta.owner_id')
            ->orderByDesc(DB::raw('COUNT_BIG(*)'));

        if (!is_null($date)) {
            $query->whereBetween('ta.date_assigned', [Carbon::now()->subHours(24), Carbon::now()]);
        }

        return $query->get();
    }



    private function getEngineerLastTickets()
    {
        // Subquery to get the last ticket per engineer
        $sub = DB::connection('tcd_login')->table('ticket_assignment as ta')
            ->join('ticket as t', 'ta.ticket_id', '=', 't.ticket_id')
            ->select(
                'ta.owner_id',
                'ta.date_assigned',
                't.ticket_id',
                't.last_updated',
                't.subject', // <-- add subject here
                DB::raw('ROW_NUMBER() OVER (PARTITION BY ta.owner_id ORDER BY t.ticket_id DESC) AS rn')
            );

        // Get only the last ticket per engineer
        return DB::connection('tcd_login')->table(DB::raw('ticket'))
            ->fromSub($sub, 'x')
            ->join('ticket as t', 'x.ticket_id', '=', 't.ticket_id')
            ->where('x.rn', 1)
            ->select('x.owner_id', 't.ticket_id', 'x.subject', 'x.date_assigned', 'x.last_updated') // <-- select the subject
            ->get();
    }

    private function getEngineerLastTicketStatuses()
    {
        return DB::connection('tcd_login')->table('ticket_assignment as ta')
            ->join('ticket as t', 'ta.ticket_id', '=', 't.ticket_id')
            ->leftJoin('escalated_tickets as et', 'et.ticket_id', '=', 't.ticket_id')
            ->select(
                'ta.owner_id',
                't.ticket_id',
                DB::raw("
                    CASE
                        WHEN et.ticket_id IS NOT NULL AND et.escalation_date IS NOT NULL AND et.is_checked = 0 AND et.is_approved = 0 THEN 'Escalated'
                        WHEN et.ticket_id IS NOT NULL AND et.is_checked = 1 AND et.is_approved = 0 THEN 'Escalation Checked'
                        WHEN et.ticket_id IS NOT NULL AND et.is_checked = 1 AND et.is_approved = 1 THEN 'Escalation Approved'
                        WHEN ta.is_read = 0 AND ta.is_answered = 0 THEN 'Not yet viewed'
                        WHEN ta.is_read = 1 AND ta.is_answered = 0 THEN 'Not yet answered'
                        WHEN ta.is_read = 1 AND ta.is_answered = 1 THEN 'Answered'
                        ELSE 'Unknown'
                    END AS ticket_status
                ")
            )
            ->get();
    }





    public function engineerStatsData(Request $request)
    {
        $engineers = $this->getAllEngineers(); // Account info
        $ticketCounts = $this->getEngineerTicketCounts(); // Ticket counts
        $lastTickets = $this->getEngineerLastTickets(); // Last ticket_id only
        $ticketStatuses = $this->getEngineerLastTicketStatuses(); // Ticket status logic

        $statusFilter = $request->status_filter;

        $merged = $engineers->map(function ($eng) use ($ticketCounts, $lastTickets, $ticketStatuses, $statusFilter) {
            $count = $ticketCounts->firstWhere('owner_id', $eng->account_id);
            $last = $lastTickets->firstWhere('owner_id', $eng->account_id);
            $status = null;
            $subject = null;
            $date_assigned = null;
            $last_updated = null;

            if ($last) {
                $statusData = $ticketStatuses->firstWhere('ticket_id', $last->ticket_id);
                $status = $statusData->ticket_status ?? null;
                $subject = $last->subject ?? null;
                $date_assigned = $last->date_assigned ?? null;
                $last_updated = $last->last_updated ?? null; // <-- new field
            }

            if ($statusFilter && $status !== $statusFilter) {
                return null;
            }

            $eng->ticket_count = $count->ticket_count ?? 0;
            $eng->last_ticket_number = $last->ticket_id ?? null;
            $eng->last_ticket_subject = $subject ?? '—';
            $eng->last_ticket_date_assigned = $date_assigned ?? '—';
            $eng->last_ticket_last_updated = $last_updated ?? '—'; // <-- assign here
            $eng->last_status = $status ?? '—';

            return $eng;
        });

        $merged = $merged->filter()->values();

        $merged = $merged->sortByDesc('ticket_count')->values();
        $totalTickets = $merged->sum('ticket_count');
        return DataTables::of($merged)
            ->addIndexColumn()
            ->with('total_ticket_count', $totalTickets)
            ->make(true);
    }

    public function getEngineerNotYetReadCount()
    {
        return Ticket::TicketQry()->joinAssign()
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->whereBetween('assign.date_assigned', [Carbon::now()->subHours(48), Carbon::now()])
            ->ticketIsUnread()
            ->ticketIsUnanswered();
    }
    public function getEngineerNotYetAnsweredCount()
    {
        return Ticket::TicketQry()->joinAssign()
            // Simple join with column mapping
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->whereBetween('assign.date_assigned', [Carbon::now()->subHours(24), Carbon::now()])
            ->ticketIsRead()
            ->ticketIsUnanswered();
    }

    public function getEngineerNotYetReadTickets($request)
    {
        \Log::info('dito: '. $request->ownerId);
        $query = Ticket::TicketQry()
        ->joinAssign()
        ->ticketStatusIsNot([4])
        ->ticketIsNotDeleted()
        ->ticketAssignedIsNotDeleted()
        ->whereBetween('assign.date_assigned', [
            Carbon::now()->subHours(48),
            Carbon::now()
        ])
        ->ticketIsUnread()
        ->ticketIsUnanswered()
        ->where('assign.owner_id', $request->ownerId)
        ->get();

        \Log::info($query);

        return $query;

    }

    public function engineerStatsCounter(Request $request)
    {
        $engineers = $this->getAllEngineers();
        $today = Carbon::now()->toDateString();

        // Total tickets today
        $ticketCounts = $this->getEngineerTicketCounts($today);

        // Not yet read tickets today
        $notYetReadCounts = $this->getEngineerNotYetReadCount()
            // ->whereDate('assign.date_assigned', $today)
            ->select('assign.owner_id', DB::raw('COUNT(*) as not_read_count'))
            ->groupBy('assign.owner_id')
            ->get();

        // Not yet answered tickets today
        $notYetAnsweredCounts = $this->getEngineerNotYetAnsweredCount()
            // ->whereDate('assign.date_assigned', $today)
            ->select('assign.owner_id', DB::raw('COUNT(*) as not_answered_count'))
            ->groupBy('assign.owner_id')
            ->get();

        $lastTickets = $this->getEngineerLastTickets(); // Last ticket_id only


        // Merge counts into engineers
        $merged = $engineers->map(function ($eng) use ($ticketCounts, $notYetReadCounts, $notYetAnsweredCounts, $lastTickets) {
            $count = $ticketCounts->firstWhere('owner_id', $eng->account_id);
            $notRead = $notYetReadCounts->firstWhere('owner_id', $eng->account_id);
            $notAnswered = $notYetAnsweredCounts->firstWhere('owner_id', $eng->account_id);
            $last = $lastTickets->firstWhere('owner_id', $eng->account_id);

            if ($last) {
                $date_assigned = $last->date_assigned ?? null;
            }

            $eng->ticket_count = $count->ticket_count ?? 0;
            $eng->not_yet_read = $notRead->not_read_count ?? 0;
            $eng->not_yet_answered = $notAnswered->not_answered_count ?? 0;
            $eng->last_ticket_date_assigned = $date_assigned ?? '—';

            return $eng;
        });

        // Sort by total tickets descending
        $merged = $merged->sortByDesc('ticket_count')->values();

        // Optionally, sum total tickets
        $totalTickets = $merged->sum('ticket_count');

        return DataTables::of($merged)
            ->addIndexColumn()
            ->with('total_ticket_count', $totalTickets)
            ->make(true);
    }

    public function unreadNotification()
    {
        return response()->json(['data' => UserNotification::OwnNotif()->Unread()->orderBy('user_notification.notification_id','desc')->take(10)->get()]);
    } 

    public function countUnread()
    {
        return response()->json(['count' => UserNotification::OwnNotif()->Unread()->count()]);
    }

    public function getHistory(Request $request)
    {
        $data = History::getHistory(base64_decode($request->tid));

        return response()->json(['data' => $data]);
    }
	
	
	

    //Sidebar Counter 
    public function getSideCount()
    {
        $_role = Session::get('userData')->role_name;
        $_account_id = Session::get('userData')->account_id;

        $selectedYear = \App\Setting::getYearFilter();
        $filterByYear = ($selectedYear !== 'All');

        if($_role == 'engineer') {
			
			 if(Session('userData')->account_id == '57812'){
                
                $pendingctr = Ticket::joinAssign()->RoblesAssigned()->NotDeleted()->Unanswered()->getCount();
                \Log::info('pen: '. $pendingctr);
                $answeredctr = Ticket::joinAssign()->RoblesAssigned()->NotDeleted()->Answered()->notClosed()
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                      \Log::info('ans: '. $answeredctr);
                $closedctr = Ticket::joinAssign()->RoblesAssigned()->NotDeleted()->statusID(4)
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                $reassignedctr = Ticket::joinAssign()->RoblesAssigned()->Deleted()->getCount();
                $escalatedQuery = Ticket::RoblesGetTXEscalatedPerEngr()->GetTXEscalated();
                if ($filterByYear) {
                    $escalatedQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
                }
                $escalatedctr = $escalatedQuery->getCount();
				
                return response()->json(['account_id' => $_account_id, 'user' => $_role, 'pendingctr' => $pendingctr, 'answeredctr' => $answeredctr,
                                         'closedctr' => $closedctr, 'reassignedctr' => $reassignedctr, 'escalatedctr' => $escalatedctr]);
            }

            if(in_array(Session('userData')->account_id,array(57610,57615)) ) { 
                $unassignedctr = Ticket::statusID(1)->getCount();
                $assignedctr = Ticket::statusID(2)->getCount();
                $answeredctr = Ticket::statusID(3)
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(date_created) = ?', [$selectedYear]))->getCount();
                $closedctr = Ticket::statusID(4)
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(date_created) = ?', [$selectedYear]))->getCount();
                $reassignedctr = Ticket::joinAssign()->Deleted()->getCount();
                $cebuQuery = Ticket::ticketQry()->where('esao.AccountGroup', 'CE01');
                if ($filterByYear) {
                    $cebuQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
                }
                $cebuctr = $cebuQuery->distinct()->count('ticket.ticket_id');
                $escalatedQuery = Ticket::getTXEscalated();
                if ($filterByYear) {
                    $escalatedQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
                }
                $escalatedctr = $escalatedQuery->getCount();
        $pendingctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->getCount();

                // dd(Ticket::getCount()->toSql());
				
                return response()->json(['account_id' => $_account_id, 'user' => $_role, 'unassignedctr' => $unassignedctr, 'assignedctr' => $assignedctr,
                                         'pendingctr' => $pendingctr, 'answeredctr' => $answeredctr, 'closedctr' => $closedctr, 'reassignedctr' => $reassignedctr, 'cebuctr' => $cebuctr, 'escalatedctr' => $escalatedctr]);

            } else {
                $pendingctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Unanswered()->getCount();
                $answeredctr = Ticket::joinAssign()->Assigned()->NotDeleted()->Answered()->notClosed()
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                $closedctr = Ticket::joinAssign()->Assigned()->NotDeleted()->statusID(4)
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                $reassignedctr = Ticket::joinAssign()->Assigned()->Deleted()->getCount();
                $escalatedQuery = Ticket::GetTXEscalatedPerEngr()->GetTXEscalated();
                if ($filterByYear) {
                    $escalatedQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
                }
                $escalatedctr = $escalatedQuery->getCount();

                return response()->json(['account_id' => $_account_id, 'user' => $_role, 'pendingctr' => $pendingctr, 'answeredctr' => $answeredctr,
                                         'closedctr' => $closedctr, 'reassignedctr' => $reassignedctr, 'escalatedctr' => $escalatedctr]);
            }

        }

        if($_role == 'admin' || $_role == 'super_user') {

            $unassignedctr = Ticket::statusID(1)->ExcludeAppsdev()->getCount();
            // $unassignedctr = Ticket::statusID(1)->ExcludeAppsdev()->getCount();
            // $unassignedctr = Ticket::statusID(1)->where('ticket.requestor_id', '!=', 57732)->getCount();
            $assignedctr = Ticket::statusID(2)->ExcludeAppsdev()->getCount();
            $answeredctr = Ticket::statusID(3)->ExcludeAppsdev()
                            ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(date_created) = ?', [$selectedYear]))->getCount();
            $closedctr = Ticket::statusID(4)->ExcludeAppsdev()
                            ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(date_created) = ?', [$selectedYear]))->getCount();
            $reassignedctr = Ticket::joinAssign()->Deleted()->getCount();
            $cebuQuery = Ticket::ticketQry()->where('esao.AccountGroup', 'CE01');
            if ($filterByYear) {
                $cebuQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
            }
            $cebuctr = $cebuQuery->distinct()->count('ticket.ticket_id');
            $escalatedQuery = Ticket::GetTXEscalated();
            if ($filterByYear) {
                $escalatedQuery->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]);
            }
            $escalatedctr = $escalatedQuery->getCount();
		
            return response()->json(['account_id' => $_account_id, 'user' => $_role, 'unassignedctr' => $unassignedctr, 'assignedctr' => $assignedctr,
                                     'answeredctr' => $answeredctr, 'closedctr' => $closedctr, 'reassignedctr' => $reassignedctr, 'cebuctr' => $cebuctr, 'escalatedctr' => $escalatedctr]);
        }

        if($_role == 'requestor') {

            if($this->isHead() >= 1) {
                $unassignedctr = Ticket::joinESAO()->statusID(1)->AccountGroup()->getCount();
                $assignedctr = Ticket::joinESAO()->statusID(2)->AccountGroup()->getCount();
                $answeredctr = Ticket::joinESAO()->statusID(3)->AccountGroup()
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                $closedctr = Ticket::joinESAO()->statusID(4)->AccountGroup()
                                ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
            } else {
                $unassignedctr = Ticket::joinCarbon()->TixAccOwner()->statusID(1)
                                    ->orWhere->TixReqOwner()->statusID(1)
                                    ->orWhere->CC()->statusID(1)
                                    ->getCount();
                $assignedctr = Ticket::joinCarbon()->TixAccOwner()->statusID(2)
                                    ->orWhere->TixReqOwner()->statusID(2)
                                    ->orWhere->CC()->statusID(2)
                                    ->getCount();
                $answeredctr = Ticket::joinCarbon()->TixAccOwner()->statusID(3)
                                    ->orWhere->CC()->statusID(3)
                                    ->orWhere->TixReqOwner()->statusID(3)
                                    ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
                $closedctr = Ticket::joinCarbon()->TixAccOwner()->statusID(4)
                                    ->orWhere->CC()->statusID(4)
                                    ->orWhere->TixReqOwner()->statusID(4)
                                    ->when($filterByYear, fn($q) => $q->whereRaw('YEAR(ticket.date_created) = ?', [$selectedYear]))->getCount();
            }

            return response()->json(['account_id' => $_account_id, 'user' => $_role, 'unassignedctr' => $unassignedctr, 'assignedctr' => $assignedctr,
                                     'answeredctr' => $answeredctr, 'closedctr' => $closedctr]);
        }
    }

    // New feature
    function getEngineerTicketDetails($request, $date = null)
    {
        \Log::info('Fetching ticket details for owner: ' . $request->ownerId);

        $query = DB::connection('tcd_login')
            ->table('ticket_assignment as ta')
            ->join('ticket as t', 'ta.ticket_id', '=', 't.ticket_id')
            ->where('ta.owner_id', $request->ownerId)
            ->whereNotIn('t.status_id', [4]) // Exclude closed
            ->select(
                't.ticket_id',
                't.subject',
                't.status_id',
                't.date_created',
                't.last_updated',
                'ta.date_assigned'
            )->orderByDesc('t.date_created');

        // 💡 Optional date filter
        if (!is_null($date)) {
            $query->whereBetween(
            'ta.date_assigned',
            [Carbon::now()->subHours(24), Carbon::now()]
            );
        }

        return $query->get();
    }

    public function getEngineerNotYetAnsweredTickets($request)
    {
        $query = Ticket::TicketQry()
            ->joinAssign()
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->whereBetween('assign.date_assigned', [Carbon::now()->subHours(24), Carbon::now()])
            ->ticketIsRead()
            ->ticketIsUnanswered()
            ->where('assign.owner_id', $request->ownerId)->get();
            return $query;
    }


    function getAllEngineerTicketDetails($date = null)
    {
        \Log::info('Fetching all ticket details');

        $query = DB::connection('tcd_login')
            ->table('ticket_assignment as ta')
            ->join('ticket as t', 'ta.ticket_id', '=', 't.ticket_id')
            ->whereNotIn('t.status_id', [4]) // Exclude closed
            ->select(
                't.ticket_id',
                't.subject',
                't.status_id',
                't.date_created',
                't.last_updated',
                'ta.date_assigned'
            )
            ->orderByDesc('t.date_created');

        // Optional date filter
        if (!is_null($date)) {
            $query->whereBetween(
                'ta.date_assigned',
                [Carbon::now()->subHours(24), Carbon::now()]
            );
        }

        return $query->get();
    }

}