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

    public static function getYearFilter()
    {
        $userId = session('userData') ? session('userData')->account_id : null;
        $value = self::where('key', 'year_filter')->where('account_id', $userId)->value('value');
        return $value ?? 'All';
    }
}
