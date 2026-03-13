<?php

namespace App\Traits;

use DB;
use App;
use URL;
use Config;
use Session;
use DateTime;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;

use App\Reply;
use App\Ticket;
use App\ESDAccount;
use App\CarbonCopy;
use App\Assignment;

trait ReplyQueries
{

    public function postReply(Request $request)
    {
        // dd($request);

        $_tempCont = $this->insertReply($request);

        if ($this->tixParamUrl == 19052) {
          // dd($_tempCont);
        }

        $_replyID = $_tempCont[0];
        $_replyContent = $_tempCont[1];

        $_replyType = $request->replyType;

        $request->request->add(['ticketID' => $this->tixParamUrl, 'replyID' => $_replyID]);

        $this->insertAttachment($request);
        
        $_ticketDetail = Ticket::getTicketDetails($this->tixParamUrl)[0];
        
        $request->request->add(['ticketDetail' => $_ticketDetail]);

        $cc = Session('userData')->Email;

        $to ='';

        if($_replyType == 1) { // Normal Reply
            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.').';

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.'):'.$_ticketDetail->subject;

            $_content = 'Hi, <br><br>'.$_history;
        } else if($_replyType == 2) { // Reply with Close
            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
            $_history .= 'and marked this ticket as Closed.';

            $request->request->add(['statusID' => '4']);
            $this->updateTicketStatus($request);

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & closed '.$_ticketDetail->subject;

            $_content = 'Hi, <br><br>'.$_history;
        } else if ($_replyType == 3) { // Reply with reset Engineer Status
            $_tempList = explode(',', $request->engrResetList);

            $_resetListID = array_map(function($val) {
                return explode('|', $val)[0];
            }, $_tempList);

            $this->resetIndiAssignment($_resetListID, 0);

            $_resetListName = array_map(function($val) {
                return explode('|', $val)[1];
            }, $_tempList);

            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
            $_history .= ' and reset '.implode($_resetListName, ', ').' status from Answered to Pending.';

            $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & reset status:'.$_ticketDetail->subject;

            $_content = 'Hi, <br><br>'.$_history;
        } else if ($_replyType == 4) { //Reply with Reassignment

            $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
            $_history .= ' and asked for reassignment.<br><br>';
            $_history .= Assignment::getAssignedNickName($this->tixParamUrl).' will be no longer included in this request.';

            $_subject = \Common::instance()->getCurrentUserName().' is asking for reassignment:'.$_ticketDetail->subject;

            $cc = Session('userData')->Email.','.Assignment::getAssignedEmail($this->tixParamUrl);

            $this->resetIndiAssignment(Assignment::getAssignmentID($this->tixParamUrl), 1);

            $_content = 'Hi Liza/Noel, <br><br>'.$_history;
        
		} else if ($_replyType == 5) { //Reply with Escalated
        $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
        $_history .= 'and marked this ticket as Escalated.';
		
		// Send and Escalate with CC BU Heads
			if(Session('userData')->AccountGroup == 'BU10' || Session('userData')->account_id == '310') {
				// $cc = Session('userData')->Email.','.Assignment::getAssignedEmail($this->tixParamUrl);
				$cc = "ploria@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info('cc'); 
				\Log::info($cc);
			} else if(Session('userData')->AccountGroup == 'BU1' || Session('userData')->account_id == '926') {
				
				$cc = "mcarandang@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info($cc);
			} else if(Session('userData')->AccountGroup == 'BU2' || Session('userData')->account_id == '205') {
				
				$cc = "rdeguzman@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info('cc');
				\Log::info($cc);
			} else if(Session('userData')->AccountGroup == 'BU5' || Session('userData')->account_id == '856') {
				
				$cc = "fricaflanca@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info('cc');
				\Log::info($cc);
			} else if(Session('userData')->AccountGroup == 'BU6' || Session('userData')->account_id == '1094') {
				$cc = "bsanchez@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info('cc');
				\Log::info($cc);
			} else if(Session('userData')->AccountGroup == 'BU8' || Session('userData')->account_id == '387') {
				$cc = "smpenalosa@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
				\Log::info('cc');
				\Log::info($cc);
			} else {
				$cc = Session('userData')->Email.','.Assignment::getAssignedEmail($this->tixParamUrl);
			}
		
    	$to = 'npacheco@ics.com.ph,jwong@ics.com.ph,macosta@ics.com.ph,'; // escalation admins
        $request->request->add(['statusID' => '5']);

        // $this->updateTicketStatus($request);
        $this->insertEscalatedTickets($request);
        // $this->insertCarbonCopy($this->tixParamUrl,$to);
        
        $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & WITH ESCALATION - '.$_ticketDetail->subject;
		
        $_content = 'Hi, <br><br>'.$_history;

		} else if ($_replyType == 7) { // Approved Escalation
        \Log::info('approved');
        $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
        $_history .= 'and acknowledged this ticket as Escalated.';

        $request->request->add(['statusID' => '7']);
        // $this->updateTicketStatus($request);

        $this->approvedEscalatedTickets($request);

        $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & ACKNOWLEDGED THE ESCALATION - '.$_ticketDetail->subject;

        $_content = 'Hi, <br><br>'.$_history;
		
		} else if ($_replyType == 8) { // Decline Escalation
        \Log::info('declined');
        $_history = \Common::instance()->getCurrentUserName().' posted a new reply (ReplyID:'.$request->replyID.') ';
        $_history .= 'and declined the escalation of this ticket.';

        $request->request->add(['statusID' => '8']);
        // $this->updateTicketStatus($request);

        $this->declinedEscalatedTickets($request);

        $_subject = \Common::instance()->getCurrentUserName().' replied('.$_replyID.') & DECLINED THE ESCALATION - '.$_ticketDetail->subject;

        $_content = 'Hi, <br><br>'.$_history;
		
		}

        if(Session('userData')->role_name == 'engineer' && $_replyType != 3) {
            $request = $this->updateAnsweredStatus($request);
            $this->insertHistory($request);
            $this->checkReplyStatus($request);
        } else {
            $request->request->add(['history' => $_history]);
            $this->insertHistory($request);

            if($_replyType == 3 || $_replyType == 4) {
                $this->checkReplyStatus($request);
            }
        }

        $_ccID = $request->ccID;
        
        if(!empty($request->ccID)) {
            $this->insertCarbonCopy($this->tixParamUrl,$_ccID);
            
            $_history = \Common::instance()->getCurrentUserName().' looped in ('.ESDAccount::getAccountName(serialize($_ccID)).').';
            $request->request->add(['history' => $_history]);
            $this->insertHistory($request);

            $_content .= '<br><br>'.$_history;
        }

        $_content .='<br><br>Please check and confirm.<br><br>';

        // Build the base recipient list
		$to .= Assignment::getAssignedEmail($this->tixParamUrl).','.
			   CarbonCopy::getCCEmail($this->tixParamUrl).','.
			   ESDAccount::getAdminEmail().','.
			   $_ticketDetail->requestor_email.','.
			   $_ticketDetail->ao_email.','.
			   Session('userData')->Email;

		// Clean up: remove duplicates, empties, and (optionally) exclude the sender
		$toArray = array_unique(array_filter(explode(',', $to)));
		$toArray = array_diff($toArray, [Session('userData')->Email]); // avoid sending to self

		$to = implode(',', $toArray);

		// Render reply content
		$_content = $this->renderEmailContent(
			$_replyContent,
			$this->url.'/view-request/'.base64_encode($this->tixParamUrl)
		);

		// Special handling for BU10 or account 310
		if (Session('userData')->AccountGroup == 'BU10' || Session('userData')->account_id == '310') {

			$bu10Emails = [
				'bu10@ics.com.ph',
				'dramos@ics.com.ph',
				'EUCCONSULTANT@ICS.COM.PH',
				'emisare@ics.com.ph',
				'MESCARIO@ICS.COM.PH'
			];

			// Merge BU10 group into the normal recipients (don’t overwrite)
			$mergedTo = array_unique(array_merge($toArray, $bu10Emails));

			// You can CC BU10 separately too (optional)
			$cc = implode(',', $bu10Emails);
			$to = implode(',', $mergedTo);
		}


        $this->insertEmailNotif($_subject, $_content, $to, $cc);

        $this->saveLogs('Posted a new reply in Ticket#'.$this->tixParamUrl.'.');

        Session::flash('message', 'Reply has been posted.');
        Session::flash('status', 'success');

        $this->lastUpdate($this->tixParamUrl);

        return redirect()->back();
    }

    public function insertReply($request)
    {
        $_replyContent = $this->cleanSNote($request->replyContent);

        $_insertReply = new Reply();
        $_insertReply->ticket_id = base64_decode(basename(URL::previous()));
        $_insertReply->user_id = Session('userData')->account_id;
        $_insertReply->reply = $_replyContent;
        $_insertReply->date_replied = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
        $_insertReply->is_deleted = 0;
        $_insertReply->save();

        return ['0' => $_insertReply->reply_id, '1' => $_replyContent];
    }

}