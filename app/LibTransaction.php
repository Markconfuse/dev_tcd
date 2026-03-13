<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibTransaction extends Model
{
    protected $table = 'lib_transaction';

    protected $primaryKey = 'transaction_type_id';

    public $timestamps = false;
}
