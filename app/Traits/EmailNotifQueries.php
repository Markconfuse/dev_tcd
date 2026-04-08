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

use App\Ticket;
use App\ESDAccount;
use App\EmailNotification;

use App\Mail\ProdNotif;
use App\Mail\ProdEditReplyNotif;
use Mail;

trait EmailNotifQueries
{
    
    function generateEmailNotif($request)
    {

        $email_content = $this->generateEmailContent($request);
        $email_link = $this->url.'/view-request/'.base64_encode($request->ticketID);

        return $this->renderEmailContent($email_content, $email_link);
    }

    function generateEmailContent($request)
    {
        // 1 create to admin | 2 assign admin to engr | 3 reply (engr to requestor) | 4 reply (requestor to engr) |
        // 5 reopened request

        $_cycle = $request->cycle;

        $_currentUser = \Common::instance()->getCurrentUserName();

        if($_cycle == 1 ) {
            if(Session('userData')->role_name == 'requestor') {
                if(Session('userData')->AccountGroup == 'CE01') {
                    $_content = 'Hi Engineer,<br><br>'; 
                    $_content .=  $_currentUser. ' created a new request and tagged you as the owner of this request.<br><br>';
                    $_content .=  'TICKET REFERENCE #'.sprintf('%04d', $request->ticketID).'.<br><br>';
                } else {
                    $_content = 'Hi '.\Common::instance()->col2str(ESDAccount::where('role_id', '2')->get(), 'AccountName').",<br><br>"; 
                    $_content .=  $_currentUser. ' created a new request.<br><br>';
                    $_content .=  'TICKET REFERENCE #'.sprintf('%04d', $request->ticketID).'.<br><br>';
                }
            } else {
                $_content = 'Hi '.$request->ao_name.",<br><br>"; 
                $_content .=  $_currentUser. ' created a new request and tagged you as the Account Owner of this request.<br><br>';
                $_content .=  'TICKET REFERENCE #'.sprintf('%04d', $request->ticketID).'.<br><br>';
            }
        } else if($_cycle == 2) {
            $_content = 'Hi '.$request->ticketDetail['requestor_name'].",<br><br>"; 
            $_content .=  $request->history.'.<br><br>';
        } else if($_cycle == 5) {
            $_content = "Hi ESD-Technology Consulting Team,<br><br>"; 
            $_content .= $_currentUser. ' closed ticket#'.sprintf('%04d', $request->ticketDetail['ticket_id']).'.<br><br>';
        } else if($_cycle == 6) {
            $_content = "Hi ESD-Technology Consulting Team,<br><br>"; 
            $_content .= $_currentUser. ' reopened ticket#'.sprintf('%04d', $request->ticketDetail['ticket_id']).'.<br><br>';
        }

        $_content .= "Please check and confirm.<br><br>";

        return $_content;        
    }

    function insertEmailNotif($_subject, $_content, $_to, $_cc)
    {
		\Log::info($_to);
		\Log::info('cc: '. $_cc);
		
        // creator | subject | message | sendTo | sendCC | sendBCC | dateCreated | status | dateSEnt 
        $_insertEmail = new EmailNotification();
        $_insertEmail->creator = Session('userData')->AccountName;
        $_insertEmail->subject = $_subject;
		$_insertEmail->message = $_content;

        if (strpos($_subject, 'it_appsdev_test') !== false) {
			\Log::info('apps_dev');
          $_insertEmail->sendTo = 'dramos@ics.com.ph';
          $_insertEmail->sendCC = '';
        } else {
			\Log::info('else to');
          $_insertEmail->sendTo = $_to;
          $_insertEmail->sendCC = $_cc;
        }
        
        //$_insertEmail->sendBCC = $this->bcc;
		$_insertEmail->sendBCC = 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph';
        $_insertEmail->dateCreated = Carbon::now()->format('m/d/Y H:i:s');
        //$_insertEmail->status = 0;
        $_insertEmail->sys_type = 'TCDPortal';

        $eCC = str_replace(',', ' ', strtolower($_cc));
        $sCC = explode(',', $eCC);
        $gCC = explode(' ', $sCC[0]);
		
		$eTo = str_replace(',', ' ', $_to);
        $sTo = explode(',', $eTo);
        $gTo = explode(' ', $sTo[0]);
		
	    //$eBCC = str_replace(',', ' ', $this->bcc);
		$eBCC = str_replace(',', ' ', 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph');
        $sBCC = explode(',', $eBCC);
        $gBCC = explode(' ', $sBCC[0]);
        
        $stat = Mail::to($gTo)
        ->cc($gCC)->bcc($gBCC)
    ->send(new ProdNotif($_subject, $_content, $_to, $_cc));
		
		$checkMail = count(Mail::failures());
		
		if($checkMail == 0){
			\Log::info('sent: '. $checkMail);
			$_insertEmail->status = 1;
			$_insertEmail->dateSent = Carbon::now()->format('m/d/y H:i:s');
			$_insertEmail->save();
		} else {
			\Log::info('failed: '. $checkMail);
			$_insertEmail->status = 0;
			$_insertEmail->save();
		}

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('POST', 'https://proport.ics.com.ph/api/postMail', [
        //     'form_params' => [
        //         'creator' => base64_encode(Session('userData')->AccountName),
        //         'subject' => base64_encode($_subject),
        //         'message' => base64_encode($_content),
        //         'sendTo' => base64_encode($_to),
        //         'sendCC' => base64_encode($_cc),
        //         'sendBCC' => base64_encode($this->bcc),
        //         'sysType' => base64_encode('TCDPortalCloud')
        //     ]
        // ]);
    }
	
	function insertEditReplyNotif($_subject, $_content, $_to, $_cc, $_eLink)
    {
		\Log::info($_to);
		\Log::info('ticket content: '. $_content);
	
        // creator | subject | message | sendTo | sendCC | sendBCC | dateCreated | status | dateSEnt 
        $_insertEmail = new EmailNotification();
        $_insertEmail->creator = Session('userData')->AccountName;
        $_insertEmail->subject = $_subject;
		$_insertEmail->message = $_content;

        if (strpos($_subject, 'it_appsdev_test') !== false) {
			\Log::info('apps_dev');
          $_insertEmail->sendTo = 'dramos@ics.com.ph';
          $_insertEmail->sendCC = '';
        } else {
			\Log::info('else to');
          $_insertEmail->sendTo = $_to;
          $_insertEmail->sendCC = $_cc;
        }
        
        //$_insertEmail->sendBCC = $this->bcc;
		$_insertEmail->sendBCC = 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph';
        $_insertEmail->dateCreated = Carbon::now()->format('m/d/Y H:i:s');
        //$_insertEmail->status = 0;
        $_insertEmail->sys_type = 'TCDPortal';

        $eCC = str_replace(',', ' ', strtolower($_cc));
        $sCC = explode(',', $eCC);
        $gCC = explode(' ', $sCC[0]);
		
		$eTo = str_replace(',', ' ', $_to);
        $sTo = explode(',', $eTo);
        $gTo = explode(' ', $sTo[0]);
		
	    //$eBCC = str_replace(',', ' ', $this->bcc);
		$eBCC = str_replace(',', ' ', 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph');
        $sBCC = explode(',', $eBCC);
        $gBCC = explode(' ', $sBCC[0]);
        
		

        $stat = Mail::to($gTo)
        ->cc($gCC)->bcc($gBCC)
        ->send(new ProdEditReplyNotif($_subject, $_content, $_eLink, $_to, $_cc));
		
		$checkMail = count(Mail::failures());
		
		if($checkMail == 0){
			\Log::info('sent: '. $checkMail);
		
			$_insertEmail->status = 1;
			$_insertEmail->dateSent = Carbon::now()->format('m/d/y H:i:s');
			$_insertEmail->save();
		} else {
			\Log::info('failed: '. $checkMail);
			$_insertEmail->status = 0;
			$_insertEmail->save();
		}

        // $client = new \GuzzleHttp\Client();
        // $response = $client->request('POST', 'https://proport.ics.com.ph/api/postMail', [
        //     'form_params' => [
        //         'creator' => base64_encode(Session('userData')->AccountName),
        //         'subject' => base64_encode($_subject),
        //         'message' => base64_encode($_content),
        //         'sendTo' => base64_encode($_to),
        //         'sendCC' => base64_encode($_cc),
        //         'sendBCC' => base64_encode($this->bcc),
        //         'sysType' => base64_encode('TCDPortalCloud')
        //     ]
        // ]);
    }

    function renderEmailContent($email_content, $email_link)
    {
        return view('mail.email_template',compact('email_content', 'email_link'))->render();
    }
}