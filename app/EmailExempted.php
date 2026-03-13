<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailExempted extends Model
{
    protected $table = 'email_exempted';

    protected $primaryKey = 'exemp_id';

	protected $connection = 'proc_login';

    public $timestamps = false;
}
