<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    protected $table = 'email_notification';

    protected $primaryKey = 'email_id';

	protected $connection = 'proc_login';

	// protected $table = 'cust_creation_notification';

	// protected $connection = 'crm_login';

    public $timestamps = false;
}
