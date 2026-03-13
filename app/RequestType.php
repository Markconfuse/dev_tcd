<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'lib_request_type';

    protected $primaryKey = 'request_type_id';

    public $timestamps = false;
}
