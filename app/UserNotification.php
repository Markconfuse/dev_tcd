<?php

namespace App;

use DB;
use Session;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notification';

    protected $primaryKey = 'user_notification_id';

    public $timestamps = false;

    public function scopeOwnNotif($query)
    {

    	return $query->join('notification as notif', 'user_notification.notification_id', '=', 'notif.notification_id')
                    ->join('vw_crm_accounts as indi', 'user_notification.user_id', '=', 'indi.AccountID')
                    ->join('vw_crm_accounts as creator', 'notif.creator_id', '=', 'creator.AccountID')
    				->select(DB::raw('ticket_id, notification, is_read, created_date,
                                creator.GAvatar as CGAvatar, creator.AccountName as CName,
                                indi.GAvatar as IGAvatar, indi.AccountName as IName'))
    	       		->where('indi.AccountID', Session('userData')->account_id);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', '0');
    }

}
