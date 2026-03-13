<?php

namespace App;

use DB;
use Session;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table = 'ticket_assignment';

    protected $primaryKey = 'assignment_id';

    public $timestamps = false;

    public function scopeAssignQry($query)
    {
        return $query->join('ticket as ticket', 'ticket.ticket_id', '=', 'ticket_assignment.ticket_id')
                    ->join('vw_crm_accounts as acc', 'acc.AccountID', '=', 'ticket_assignment.owner_id')
                    ->select(DB::raw('assignment_id, owner_id, GAvatar, AccountName, NickName, Email,
                             ticket_assignment.is_answered, ticket_assignment.is_read, ticket_assignment.is_deleted'));
    }

    public function scopeGetAssignedDetail($query, $_ticketID)
    {
        return Self::assignQry()->ticketID($_ticketID)->notDeleted();
    }

    public function scopeOwnDetail($query, $_ticketID)
    {
        return Self::ticketID($_ticketID)->Owned($_ticketID);
    }

    public function scopeValidEngr($query, $_ticketID)
    {
        return Self::owned()->ticketID($_ticketID);
    }

    public function scopeOwned($query)
    {
        return $query->where('owner_id', Session('userData')->account_id)->notDeleted();
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('ticket_assignment.is_deleted', '0');
    }

    public function scopeAnswered($query)
    {
        return $query->where('ticket_assignment.is_answered', '1');
    }

    public function scopeGetAssignedEmail($query, $_ticketID)
    {
        return \Common::instance()->col2str(Self::getAssignedDetail($_ticketID)->get()->toJson(), 'Email');
    }

    public function scopeGetAssignedNickName($query, $_ticketID)
    {   
        return \Common::instance()->col2str(Self::getAssignedDetail($_ticketID)->get()->toJson(), 'NickName');
    }

    public function scopeGetAssignmentID($query, $_ticketID)
    {
        return Self::WHERE('ticket_id', $_ticketID)->notDeleted()->pluck('assignment_id')->toArray();
    }

    public function scopeTicketID($query, $_ticketID)
    {
        return $query->where('ticket_assignment.ticket_id', $_ticketID);
    }

}
