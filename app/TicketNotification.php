<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketNotification extends Model
{
    protected $table = 'ticket_notifications';

    protected $fillable = [
        'ticket_id',
        'type',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
