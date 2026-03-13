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
use App\LibStatus;
use App\ESDAccount;
use App\Assignment;
use App\CarbonCopy;
use App\EscalatedTickets;

trait TicketQueries
{

    public function insertTicket($request)
    {
        
        $_inserTicket = new Ticket();
        $_inserTicket->requestor_id = Session('userData')->account_id;
        $_inserTicket->subject = $request->subject;
        $_inserTicket->ticket_content = $this->cleanSNote($request->requestContent);
        $_inserTicket->customer_id = $request->customerID;
        $_inserTicket->customer_name = $request->customerName;
        $_inserTicket->project_name = $request->projectName;
        $_inserTicket->account_owner_id = $request->aoID;
        $_inserTicket->request_type_id = $request->requestTypeID;
        $_inserTicket->date_created = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
        $_inserTicket->last_updated = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
        $_inserTicket->is_deleted = 0;
        $_inserTicket->status_id = $request->statusID;
        $_inserTicket->save();

        return $_inserTicket->ticket_id;
    }

    public function updateTicketStatus($request)
    {
        $_updateTicket = Ticket::find($request->ticketID);
        $_updateTicket->status_id = $request->statusID;
        $_updateTicket->save();

        $request->request->add(['history' => 'Ticket status is '.LibStatus::find($request->statusID)->status_description.'.']);

        $this->insertHistory($request);
    }

    public function lastUpdate($_ticketID)
    {
        $_updateTicket = Ticket::find($_ticketID);
        $_updateTicket->last_updated = \Carbon\Carbon::now()->format('m/d/Y H:i:s');
        $_updateTicket->save();

        $this->updateTempSearch($_ticketID);
        
        // event(new \App\Events\FormSubmitted(1));
    }
	
	public function insertEscalatedTickets($request)
    {

        // $eId = mt_rand(100, 999);

        $toEscalate = EscalatedTickets::where('ticket_id', $request->ticketID)->first();
		
        if(!empty($toEscalate)){
            $toEscalate->is_checked = 0;
            $toEscalate->is_approved = 0;
            $toEscalate->approved_by = NULL;
            $toEscalate->escalated_reply = strip_tags($this->cleanSNote($request->replyContent));
            $toEscalate->escalation_date = Carbon::now();
            $toEscalate->date_updated = NULL;
            $toEscalate->save();

        } else {
            $addEscalated = new EscalatedTickets();
         
            $addEscalated->ticket_id = $request->ticketID;
            $addEscalated->is_checked = 0;
            $addEscalated->is_approved = 0;
            // $addEscalated->id = $eId;
		    $addEscalated->escalated_reply = strip_tags($this->cleanSNote($request->replyContent));

            $addEscalated->escalation_date = Carbon::now();
            $addEscalated->save();

            // escalation admins
            // 57610 - npacheco@ics.com.ph
            // 57615 -jwong@ics.com.ph,
            // 758 - macosta@ics.com.ph,'
            $this->insertCarbonCopy($request->ticketID,explode(',','758,57610,57615'));
        }

        //$request->request->add(['history' => 'Ticket '.LibStatus::find($request->statusID)->status_description.'.']);
		
    	$request->request->add(['history' => 'Ticket Escalation Requested']);
       
        $this->insertHistory($request);
    }

    public function approvedEscalatedTickets($request)
    {
        $toEscalate = EscalatedTickets::where('ticket_id', $request->ticketID)->first();

        if(!empty($toEscalate)){
            $toEscalate->is_checked = 1;
            $toEscalate->is_approved = 1;
            $toEscalate->approved_by = Session('userData')->AccountName;
            $toEscalate->date_updated = Carbon::now();
            $toEscalate->save();
            //$request->request->add(['history' => 'Ticket was '.LibStatus::find($request->statusID)->status_description.'.']);
			     $request->request->add(['history' => 'Ticket was Escalated']);
        }  

        $this->insertHistory($request);
    }

    public function declinedEscalatedTickets($request)
    {
        \Log::info('here in declined escalatedTickets');
        $decEscalate = EscalatedTickets::where('ticket_id', $request->ticketID)->first();

        if(!empty($decEscalate)){
            $decEscalate->is_checked = 1;
            $decEscalate->is_approved = 0;
            $decEscalate->approved_by = Session('userData')->AccountName;
            $decEscalate->date_updated = Carbon::now();
            $decEscalate->save();
            // $request->request->add(['history' => 'Ticket is '.LibStatus::find($request->statusID)->status_description.'.']);
            $request->request->add(['history' => 'Ticket escalation was declined']);
        }  

        $this->insertHistory($request);
    }

    public function tagUpdate(Request $request)
    {
        $_statusID = $request->tixStatID;

        $_ticketID = $this->tixParamUrl;
        $_ticketDetail = Ticket::getTicketDetails($_ticketID)[0];


        $request->request->add(['ticketID' => $_ticketID, 'statusID' => $_statusID, 'ticketDetail' => $_ticketDetail]);

        if($_statusID == 4) {
            $cycle = 5;
            $to = Assignment::getAssignedEmail($_ticketID);
            $this->saveLogs('Ticket#'.$_ticketID.' has been closed');

            $_subject = \Common::instance()->getCurrentUserName().' closed '.$_ticketDetail->subject;

            $request->request->add(['history' => Session('userData')->AccountName.' closed Ticket#'.sprintf('%04d',$_ticketID)]);
            \Session::flash('message', 'Ticket has been successfully closed.');
        } else if ($_statusID == 2) {
            $cycle = 6;
            $to = Assignment::getAssignedEmail($_ticketID);

            $this->resetAssignmentStatus($_ticketID);
            $this->saveLogs('Ticket#'.$_ticketID.' has been reopened');

            $_subject = \Common::instance()->getCurrentUserName().' reopened '.$_ticketDetail->subject;

            $request->request->add(['history' => Session('userData')->AccountName.' reopened Ticket#'.sprintf('%04d',$_ticketID)]);
            \Session::flash('message', 'Ticket has been successfully reopened.');
        }

        $this->insertHistory($request);
        $this->updateTicketStatus($request);

        $request->request->add(['cycle' => $cycle]);
            
        $cc = trim(CarbonCopy::getCCEmail($_ticketID).','.ESDAccount::getAdminEmail(), ',');

        $_content = $this->generateEmailNotif($request);

        $this->insertEmailNotif($_subject, $_content, $to, $cc);

        $this->lastUpdate($_ticketID);

        \Session::flash('status', 'success');

        return redirect()->back();
    }

}