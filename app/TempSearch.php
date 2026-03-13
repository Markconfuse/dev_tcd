<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class TempSearch extends Model
{
    protected $table = 'temp_search';

    protected $primaryKey = 'ticket_id';

    public $timestamps = false;
}