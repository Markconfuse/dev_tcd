<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'ticket_history';

    protected $primaryKey = 'history_id';

    public $timestamps = false;

    public function scopeGetHistory($query, $_ticketID)
    {
    	return $query->where('ticket_id', $_ticketID)->orderBy('history_id', 'asc')->get();
    }
}
