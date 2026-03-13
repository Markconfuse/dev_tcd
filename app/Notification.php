<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

    protected $primaryKey = 'notification_id';

    public $timestamps = false;
}
