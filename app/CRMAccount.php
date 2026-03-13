<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CRMAccount extends Model
{
    protected $table = 'cdbAccounts';

    protected $connection = 'crm_login';
    
    protected $primaryKey = 'AccountID';

    protected $fillable = array('GAvatar');

    public $timestamps = false;

}
