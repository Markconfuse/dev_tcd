<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket';

    protected $primaryKey = 'ticket_id';

    public $timestamps = false;

    public function scopeTicketQry($query)
    {
        return $query->join('lib_request_type as lrt', 'ticket.request_type_id', '=', 'lrt.request_type_id')
                    ->join('vw_crm_accounts as esao', 'ticket.account_owner_id', '=', 'esao.AccountID')
                    ->join('vw_crm_accounts as esrid', 'ticket.requestor_id', '=', 'esrid.AccountID')
                    ->join('lib_status as ls', 'ticket.status_id', '=', 'ls.status_id')
                    ->join('temp_search as tmp', 'ticket.ticket_id', '=', 'tmp.ticket_id', 'left outer')
                    ->select(DB::raw("ticket.*, tmp.ticket_reply, tmp.OwnerName, tmp.last_transaction, ISNULL(tmp.reply_ctr, 0) as reply_ctr, ls.status_description, lrt.request_type, esrid.AccountID as ao_id, esrid.AccountName as requestor_name, esrid.NickName as requestor_nickname, esrid.Email as requestor_email, esrid.GAvatar as GAvatarReq, esrid.AccountGroup,
                        esao.AccountID as req_id, esao.AccountName as ao_name, esao.AccountGroup as ao_group,
                        esao.NickName as ao_nickname, esao.Email as ao_email,esao.GAvatar as GAvatarAO"))->distinct();
    }

    // public function scopeTicketQry($query)
    // {
    //     return $query->join('lib_request_type as lrt', 'ticket.request_type_id', '=', 'lrt.request_type_id')
    //                 ->join('vw_crm_accounts as esao', 'ticket.account_owner_id', '=', 'esao.AccountID')
    //                 ->join('vw_crm_accounts as esrid', 'ticket.requestor_id', '=', 'esrid.AccountID')
    //                 ->join('lib_status as ls', 'ticket.status_id', '=', 'ls.status_id')
    //                 ->leftjoin(DB::raw('(SELECT count(reply_id) as reply_ctr, ticket_id FROM ticket_reply GROUP BY ticket_id) as thread'), 
    //                     function($join) { $join->on('ticket.ticket_id', '=', 'thread.ticket_id'); })
    //                 ->join(DB::raw("(SELECT history as last_transaction, trans.ticket_id FROM ( 
    //                         SELECT MAX(history_id) as history_id, ticket_id FROM ticket_history 
    //                         WHERE history NOT LIKE '%ticket status%'
    //                         GROUP BY ticket_id ) trans INNER JOIN ticket_history th 
    //                         ON trans.history_id = th.history_id) as trans "), 
    //                     function($join) { $join->on('ticket.ticket_id', '=', 'trans.ticket_id'); })
    //                 ->select(DB::raw("ticket.*, trans.last_transaction, ISNULL(thread.reply_ctr, 0) as reply_ctr, ls.status_description, lrt.request_type, 
    //                          esrid.AccountID as ao_id, esrid.AccountName as requestor_name, esrid.NickName as requestor_nickname, esrid.Email as requestor_email, esrid.GAvatar as GAvatarReq, esrid.AccountGroup,
    //                          esao.AccountID as req_id, esao.AccountName as ao_name, esao.AccountGroup as ao_group,
    //                          esao.NickName as ao_nickname, esao.Email as ao_email,esao.GAvatar as GAvatarAO,
    //                          STUFF((SELECT ';' + acc.AccountName + ',' + ISNULL(acc.GAvatar, 0) + ',' + 
    //                             convert(nvarchar(255), ta.is_answered) + ',' + convert(nvarchar(255), ta.is_read)
    //                             FROM  ticket_assignment ta  
    //                             INNER JOIN vw_crm_accounts acc
    //                             ON ta.owner_id = acc.AccountID
    //                             WHERE ticket.ticket_id = ta.ticket_id AND ta.is_deleted = 0
    //                             FOR XML PATH('')), 1, 1, '') [OwnerName], 
    //                             STUFF((SELECT reply FROM ticket_reply tr
    //                             WHERE ticket.ticket_id = tr.ticket_id
    //                             FOR XML PATH('')), 1, 1, '') [ticket_reply]"))->distinct()
    //                             ->orderBy('last_updated', 'desc');
    // }

    public function scopeTicketQrySimp($query)
    {
        return $query->join('lib_request_type as lrt', 'ticket.request_type_id', '=', 'lrt.request_type_id')
                    ->join('vw_crm_accounts as esao', 'ticket.account_owner_id', '=', 'esao.AccountID')
                    ->join('vw_crm_accounts as esrid', 'ticket.requestor_id', '=', 'esrid.AccountID')
                    ->join('lib_status as ls', 'ticket.status_id', '=', 'ls.status_id')
                    ->leftJoin('escalated_tickets as et', 'ticket.ticket_id', '=', 'et.ticket_id')
                    ->select(DB::raw("ticket.*, ls.status_description, lrt.request_type, 
                             esrid.AccountID as ao_id, esrid.AccountName as requestor_name, esrid.NickName as requestor_nickname, esrid.Email as requestor_email, esrid.GAvatar as GAvatarReq, esrid.AccountGroup,
                             esao.AccountID as req_id, esao.AccountName as ao_name, esao.AccountGroup as ao_group,
                             esao.NickName as ao_nickname, esao.Email as ao_email ,esao.GAvatar as GAvatarAO
                             ,et.id as escalation_id,et.is_approved,et.is_checked
                    "))->distinct();
    }

    public function scopeGetCount($query)
    {
        return $query->addSelect(DB::RAW('COUNT(distinct ticket.ticket_id) as count'))->get()[0];
    }

    public function scopeGetTicketDetails($query, $_ticketID)
    {

        return Self::TicketQrySimp()->ticketID($_ticketID)->get();
    }

    public function scopeGetWithAssignment($query)
    {
        return Self::ticketQry()->joinAssign()->Assigned()
                     ->addSelect(['assign.is_read', 'assign.is_answered']);
    }
	
	public function scopeTicketAssignedIsNotDeleted($query)
    {
        return $query->where('assign.is_deleted', 0);
    }

    public function scopeJoinAssign($query)
    {
        return $query->join('ticket_assignment as assign', 'assign.ticket_id', '=', 'ticket.ticket_id');
    }
	
	public function scopeTicketStatusIsNot($query, array $status)
    {
        return $query->whereNotIn('ticket.status_id', $status);
    }

    public function scopeJoinESAO($query)
    {
        return $query->join('vw_crm_accounts as esao', 'ticket.account_owner_id', '=', 'esao.AccountID');
    }
	
	public function scopeTicketIsNotDeleted($query)
    {
        return $query->where('ticket.is_deleted', 0);
    }

    public function scopeJoinCarbon($query)
    {
        return $query->leftjoin('carbon_copy as cc', 'ticket.ticket_id', 'cc.ticket_id');
    }

    public function scopeCC($query)
    {
        return $query->where('cc.account_id', Session('userData')->account_id);
    }

    public function scopeTicketID($query, $_ticketID)
    {
        return $query->where('ticket.ticket_id', $_ticketID);
    }

    public function scopeAccountGroup($query)
    {   
        $account_group = Session('userData')->AccountGroup;
        if($account_group == 'BU8' || $account_group == 'BU12'|| $account_group == 'CE01') {
            return $query->whereIn('esao.AccountGroup', ['BU8', 'BU12','CE01']);
        } else {
            return $query->where('esao.AccountGroup', Session('userData')->AccountGroup);
        }
    }

    public function scopeExcludeAppsdev($query)
    {
      return $query->whereNotIn('ticket.requestor_id', [56395,57681,57732]);
    }

    public function scopeStatusID($query, $_statusID)
    {
        return $query->where('ticket.status_id', $_statusID);
    }

    public function scopeRequestType($query, $_statusID)
    {
        return $query->where('ticket.request_type_id', $_statusID);
    }

    public function scopeNotClosed($query)
    {
        return $query->whereRaw('ticket.status_id != 4');
    }

    public function scopeClose($query)
    {
        $query->where('ticket.is_closed', '1');
    }

    public function scopeAnswered($query)
    {
        $query->where('assign.is_answered', '1');
    }

    public function scopeUnanswered($query)
    {
        $query->where('assign.is_answered', '0');
    }

    public function scopeNotDeleted($query)
    {
        $query->where('assign.is_deleted', '0');
    }

    public function scopeDeleted($query)
    {
        $query->where('assign.is_deleted', '1');
    }

    public function scopeAssigned($query)
    {
        return $query->where('assign.owner_id', Session('userData')->account_id);
    }
	
	public function scopeRoblesAssigned($query)
    {
        return $query->where('assign.owner_id', 57619)->orWhere('assign.owner_id', 57812);
    }
	
	public function scopeGetRoblesWithAssignment($query)
    {
        return Self::ticketQry()->joinAssign()->RoblesAssigned()
                     ->addSelect(['assign.is_read', 'assign.is_answered']);
    }

	public function scopeRoblesGetTXEscalatedPerEngr($query)
    {
        return $query->join('ticket_assignment as assign', 'ticket.ticket_id', '=', 'assign.ticket_id')
                        ->where('assign.owner_id', 57619)->orWhere('assign.owner_id', 57812);
    }

    public function scopeAssignedWhere($query, $owner_id)
    {
        return $query->where('assign.owner_id', $owner_id);
    }

    public function scopeAdminAssigned($query)
    {
        return $query->whereIn('assign.owner_id', explode(',', $roleIDArr))->get();
    }

    public function scopeTixAccOwner($query)
    {
        return $query->where('ticket.account_owner_id', Session('userData')->account_id);
    }
	
    public function scopeTixReqOwner($query)
    {
        return $query->where('ticket.requestor_id', Session('userData')->account_id);
    }
	
	public function scopeJoinEscalated($query)
    {
        return $query->join('escalated_tickets as ET', 'ticket.ticket_id', '=', 'ET.ticket_id');
    
    }

    public function scopeGetTXEscalated($query)
    {
        return $query->join('escalated_tickets as ET', 'ticket.ticket_id', '=', 'ET.ticket_id');
                            
    }

    public function scopeGetTXEscalatedPerEngr($query)
    {
        return $query->join('ticket_assignment as assign', 'ticket.ticket_id', '=', 'assign.ticket_id')
                        ->where('assign.owner_id',Session('userData')->account_id);
    }

    public function scopeGetReqEscalated($query)
    {
        return $query->join('escalated_tickets as ET', 'ticket.ticket_id', '=', 'ET.ticket_id')
                            ->where('ET.is_approved', '!=', 0)->where('ET.is_approved', '!=', 0)
                            ->where('ticket.account_owner_id', '=', Session('userData')->account_id);
    }

    // public function FunctionName($value='')
    // {
    //   $dta = $query->join()
    //   ->where('ticket.account_owner_id', '=', $value);
    //   // status 
    //   // 1 - not escalated 
    //   // 2 - escalated 
    //   // 3 - acknowledge
    //   // 4 - declined

    //   $stat = 1

    //   if ($dta->id <> null) {
    //     if ($dta->is_approved == 0 && $dta->is_checked == 0) {
    //       $stat = 2;
    //     } elseif ($dta->is_approved == 1 && $dta->is_checked == 1) {
    //       $stat = 3;
    //     } elseif ($dta->is_approved == 0 && $dta->is_checked == 1) {
    //       $stat = 4;
    //     }
    //   }

    //   return $stat;
    // }
	
	public static function getEngineerTicketsCount()
    {
        return self::join('ticket_assignment as a', 'ticket.ticket_id', '=', 'a.ticket_id')
            ->join('vw_tcd_accounts as v', 'a.owner_id', '=', 'v.account_id')
            ->join('user_role as u', 'v.account_id', '=', 'u.account_id')
            ->where('u.role_id', 3)
            ->whereNotIn('ticket.status_id', [4])
            ->where('ticket.is_deleted', 0)
            ->whereBetween('ticket.date_created', ['2025-01-01', '2025-12-31']);
    }
	
	public function scopeTicketIsRead($query)
    {
        return $query->where('assign.is_read', 1);
    }
    public function scopeTicketIsUnread($query)
    {
        return $query->where('assign.is_read', 0);
    }

    public function scopeTicketIsUnanswered($query)
    {
        return $query->where('assign.is_answered', 0);
    }
    
}
