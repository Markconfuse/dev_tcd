<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    public $timestamps = false;

    public function scopeGetTicketFile($query, $_ticketID)
    {
    	return $query->join('tcd_docs as stream', 'attachments.stream_id', '=', 'stream.stream_id')
    	             ->select('attachments.ticket_id', 'attachments.reply_id', 
    	             	'stream.name', 'stream.creation_time', 'stream.file_type')
    	             ->where('attachments.ticket_id', $_ticketID)->get();
    }

}
