<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'ticket_reply';

    protected $primaryKey = 'reply_id';

    public $timestamps = false;

    public function scopeGetTicketReply($query, $_ticketID)
    {

    	return $query->join('vw_crm_accounts as acc', 'ticket_reply.user_id', '=', 'acc.AccountID')
    				->select('*')
    	       		->where('ticket_reply.ticket_id', $_ticketID)
    	       		->orderBy('date_replied', 'desc')
    	       		->get();
    }
}
