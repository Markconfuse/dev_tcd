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

    public static function getYearFilterValues($selectedValue = null)
    {
        $selected = $selectedValue ?? self::getYearFilter();

        if ($selected === 'All' || $selected === null || $selected === '') {
            return [];
        }

        if (preg_match('/^(\d{4})-(\d{4})$/', $selected, $m)) {
            $start = (int) $m[1];
            $end = (int) $m[2];

            if ($start > $end) {
                $tmp = $start;
                $start = $end;
                $end = $tmp;
            }

            return range($start, $end);
        }

        if (preg_match('/^\d{4}$/', (string) $selected)) {
            return [(int) $selected];
        }

        return [];
    }
}
