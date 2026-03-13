<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $table = 'user_logs';

    public $timestamps = false;
}


