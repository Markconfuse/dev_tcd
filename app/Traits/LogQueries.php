<?php

namespace App\Traits;

use DB;
use URL;
use Carbon;
use Session;

use App\UserLog;

trait LogQueries
{
  public function saveLogs($activity)
    {

      $_insertLogs = new UserLog();
      $_insertLogs->domain_account = Session('userData')->DomainAccount;
      $_insertLogs->date_time = \Carbon\Carbon::now()->format('m/d/Y H:i:s');
      $_insertLogs->module = URL::current();
      $_insertLogs->activity = $activity;
      $_insertLogs->ip_address = \Request::ip();
      $_insertLogs->user_agent = \Request::server('HTTP_USER_AGENT');
      $_insertLogs->version = 'PROD';
      $_insertLogs->save();

    }

}