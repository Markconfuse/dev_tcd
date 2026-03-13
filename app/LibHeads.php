<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibHeads extends Model
{
    protected $connection = 'proc_login';

    protected $table = 'lib_bu_heads';

    protected $primaryKey = 'approver_id';

    public $timestamps = false;
}
