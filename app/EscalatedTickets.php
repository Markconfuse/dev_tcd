<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EscalatedTickets extends Model
{
    protected $table = 'escalated_tickets';

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['id'];

}
