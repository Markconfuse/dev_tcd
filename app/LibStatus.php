<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibStatus extends Model
{
    protected $table = 'lib_status';

    protected $primaryKey = 'status_id';

    public $timestamps = false;
}
