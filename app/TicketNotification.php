<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketNotification extends Model
{
    protected $table = 'ticket_notifications';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'type',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = ((int) static::max('id')) + 1;
            }
        });
    }

    /**
     * Retrieve notifications safely mapped by Ticket ID 
     */
    public static function getBulkNotifications($ticketIds)
    {
        return self::whereIn('ticket_id', $ticketIds)
            ->get()
            ->keyBy('ticket_id');
    }

    /**
     * Securely update a ticket's status tracker state.
     */
    public static function logNotification($ticketId, $type)
    {
        return self::updateOrCreate(
            ['ticket_id' => $ticketId],
            [
                'type' => $type, 
                'sent_at' => \Carbon\Carbon::now()
            ]
        );
    }
}
