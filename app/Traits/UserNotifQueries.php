<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Session;
use DateTime;
use Response;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Notification;
use App\UserNotification;


trait UserNotifQueries
{
	
	public function insertNotification($request)
    {  

        $_insertActivity = new Notification();
        $_insertActivity->creator_id = Session('userData')->account_id;
        $_insertActivity->notification = $this->generateActivity($request);
        $_insertActivity->ticket_id = $request->ticketID;
        $_insertActivity->created_date = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
        $_insertActivity->save();

        $request->request->add(['notificationID' => $_insertActivity->notification_id]);
        $this->insertUserNotification($request);

        // event(new \App\Events\FormSubmitted(1));
    }

    public function insertUserNotification($request)
    {

        if(!empty($request->userID)) {
            foreach ($request->userID as $key => $userID) {
                if($userID != '57591' && $userID != Session('userData')->account_id) {
                    $_insertUserNotif = new UserNotification();
                    $_insertUserNotif->notification_id = $request->notificationID;
                    $_insertUserNotif->user_id = $userID;
                    $_insertUserNotif->is_read = 0;
                    $_insertUserNotif->save();
                }
            }
        }
        
    }

    public function generateActivity($request)
    {
        // 1 create to admin | 2 assign admin to engr | 3 reply (engr to requestor) | 4 reply (requestor to engr) |
        // 5 reopened request

        $_cycle = $request->cycle;

        if($_cycle == 1) {
            $_activity =  'created a new request.Ticket#'.sprintf('%04d', $request->ticketID);
        } else if($_cycle == 2) {
            $_activity = ' updated Ticket#'.sprintf('%04d', $request->ticketID).' assignment.';
        } else if($_cycle == 3 || $_cycle == 4) {
            $_activity = ' posted a new reply to Ticket#'.sprintf('%04d', $request->ticketID).'.';
        } else if($_cycle == 5) {
            $_activity = ' closed Ticket#'.
                        sprintf('%04d', $request->ticketID).'.';
        } else if($_cycle == 6) {
            $_activity = ' reopened Ticket#'.
                        sprintf('%04d', $request->ticketID).'.';
        }  else if($_cycle == 7) {
            $_activity = ' posted a new reply to Ticket#'.sprintf('%04d', $request->ticketID).' and marked it as Closed.';
        }

        return $_activity;
    }
    
}