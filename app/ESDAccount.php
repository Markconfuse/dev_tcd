<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ESDAccount extends Model
{
    protected $table = 'vw_tcd_accounts';
    public $timestamps = false;

    public function scopeSearch($query, $column, $keywords)
    {
        return $query->whereIn($column, $keywords)->get();
    }

    public function scopeSearchRoleID($query, $roleIDArr)
    {
        return $query->whereIn('role_id', explode(',', $roleIDArr))->get();
    }

    public function scopeGetAccountEmail($query, $accIDArr)
    {
        return \Common::instance()->col2str($query->whereIn('account_id', unserialize($accIDArr))->get('Email')->toJson(), 'Email');
    }

    public function scopeGetAccountName($query, $accIDArr)
    {
        return \Common::instance()->col2str($query->whereIn('account_id', unserialize($accIDArr))->get('AccountName')->toJson(), 'AccountName');
    }

    public function scopeGetAdminEmail($query)
    {
        return \Common::instance()->col2str($query->where('role_id', '2')->get('Email')->toJson(), 'Email');
    }

        public function scopeGetAdminAccountID($query)
    {
        return \Common::instance()->col2str($query->where('role_id', '2')->get('account_id')->toJson(), 'account_id');
    }

    public function scopeGetBUHead($query, $department)
    {
        return $query->join('Procurement.dbo.lib_bu_heads as heads', 'vw_tcd_accounts.account_id', '=', 'heads.account_id')
                    ->select('*')
                    ->where('heads.department', $department);
    }

    public function scopeMain($query)
    {
        return $query->where('approver_type', 'Main');
    }

    public function scopeBak($query)
    {
        return $query->where('approver_type', 'Backup');
    }

    public function scopeAccountID($query)
    {
        return $query->where('vw_tcd_accounts.account_id', Session('userData')->account_id);
    }
}
