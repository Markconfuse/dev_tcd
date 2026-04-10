<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'account_id',
        'key',
        'value',
        'created_by',
        'updated_by'
    ];
}
