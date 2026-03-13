<?php

namespace App\Helpers;

use Session;

use App\Ticket;
use App\LibHeads;
use App\ESDAccount;
use App\Assignment;
use App\CarbonCopy;

class Common
{

     public static function instance()
     {
         return new Common();
     }

    public function col2str($collection, $column)
    {
        return implode(',' ,array_column(json_decode($collection,true), $column));
    }

    public function is_head($account_group)
    {
        return LibHeads::where([['department', $account_group], 
                                ['account_id', Session('userData')->account_id]])->count(); 
    }

    public function checkStatus($status)
    {

        if($status == 'Pending' || $status == 'Unassigned') {
            return 'badge badge-warning';
        } else if($status == 'Assigned') {
            return 'badge badge-success';
        } else if($status == 'Answered') {
            return 'badge badge-info';
        } else if($status == 'Closed') {
            return 'badge badge-closed';
        } 

    }

    public function fileIcon($fileType)
    {
        if($fileType == 'xlsx') {
            return $_icon = 'fas fa-file-excel';
        } else if($fileType == 'pdf') {
            return $_icon = 'far fa-file-pdf';
        } else if($fileType == 'word') {
            return $_icon = 'far fa-file-word';
        } else if($fileType == 'csv') {
            return $_icon = 'fas fa-file-csv'; 
        } else if($fileType == 'png' || $fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'gif') {
            return $_icon = 'far fa-image';
        } else {
            return $_icon = 'far fa-file-exclamation'; 
        }
    }

    public function checkIcon($is_answered, $is_read)
    {
        if($is_answered == 0 && $is_read == 0) {
            return 'img-bordered-unanswered-sm';
        } else if ($is_answered == 0 && $is_read == 1) {
            return 'img-bordered-read-sm';
        } else if($is_answered == 1 && $is_read == 1) {
            return 'img-bordered-answered-sm';
        }
    }

    public function getActivityUserID($_ticketID, $request)
    {
        $_ticketDetail = Ticket::getTicketDetails($_ticketID)[0];
        $_assignedID = Assignment::assignedID($_ticketID);
        $_ccID = CarbonCopy::ccID($_ticketID);

        $toID = $_ticketDetail->ao_id;
        if($_ticketDetail->ao_id !== $_ticketDetail->req_id) {
            $toID .= ','.$_ticketDetail->req_id;
        }

        if(Session('userData')->role_name !== 'admin') {
            $toID .= ','.ESDAccount::getAdminAccountID();
        }

        $request->request->add(['userID' => array_merge($_assignedID, $_ccID, explode(',', $toID))]);

        return $request;
    }

    public function historyCreate($request)
    {
        $history = $this->getCurrentUserName().' created a';
        // dd($request);
        if(Session('userData')->account_id !== $request->aoID) {
            $history .= ' request';
            $history .= ' for '.ESDAccount::where('account_id', $request->aoID)->first()->AccountName;
        } else {
            $history .= ' request.';
        }

        return $history;
    }

    public function getCurrentUserName()
    {
        $_currentUser = Session::get('userData')->NickName;

        if(empty($_currentUser)) {
            $_currentUser = Session::get('userData')->AccountName;
        }

        return $_currentUser;
    }

    public function nullRetZero($var)
    {
        if(empty($var)) {
            return 0;
        } else {
            return $var;
        }
    }

}