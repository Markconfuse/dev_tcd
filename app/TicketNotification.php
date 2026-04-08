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
}
