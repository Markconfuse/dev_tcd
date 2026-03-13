<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterval;

use Storage;
use Session;
use Exception;
use Config;
use File;
use URL;
use DB;
use Mail;

use App\Ticket;
use App\TcdAccounts;
use App\TicketReplies;
use App\Reply;
use App\History;
use App\Attachments;
use App\Assignment;

use App\ESDAccount;
use App\CarbonCopy;

use App\Mail\ProdEditReplyNotif;

use App\Traits\LogQueries;
use App\Traits\HistoryQueries;
use App\Traits\TicketQueries;
use App\Traits\AttachmentQueries;
use App\Traits\EmailNotifQueries;


class EditReplyController extends Controller
{
    use LogQueries;
    use HistoryQueries;
    use AttachmentQueries;
	use EmailNotifQueries;
	
	public function __construct()
	{
		$this->db = Config::get('dbcon.db'); 
        $this->middleware('auth');
	}

    public function getReplyInfo(Request $request)
    {
        $singleReply = Reply::find($request->rId);

        $latestId = $request->rId;
        
        $exists = Reply::where('reply_id', '>', $latestId)->where('ticket_id', $singleReply->ticket_id)->exists();
        if ($exists){
            return response()->json(['replies' => '1']);
        } else {
            return response()->json(['replies' => '0']);
        }
    }

    public function getSingleReply(Request $request)
    {
        \Log::info('req: '. $request->rId);
        $singleReply = Reply::find($request->rId);

        $latestId = $request->rId;
        $exists = Reply::where('reply_id', '>', $latestId)->where('ticket_id', $singleReply->ticket_id)->exists();
            if ($exists){
                return response()->json(['replies' => '1']);
            } else {
                return response()->json(['replies' => $singleReply]);
            }
    }

    public function editReply(Request $request)
    {	
    	try {
            \Log::info($request->all());
            $eReply = Reply::find(intval($request->rIdx));
         
            if(!empty($eReply)){
                $eReply->ticket_id = $request->ticketId;
                $eReply->reply = $request->rValue;
				$eReply->date_updated = \Carbon\Carbon::now()->format('m/d/Y H:i:s');
                $eReply->is_deleted = 0;
                \Log::info('here to reply');
                $eReplySave = $eReply->save();
				
                $this->saveLogs('Reply with ID '.$request->rIdx.' has been edited');
                // $this->insertAttachment($request);

                if ($eReplySave){
                    $cDate = Carbon::now();
                    $dateNow = $cDate->toDateTimeString();
                    
                    $rHistory = new History;
                    $rHistory->ticket_id = $request->ticketId;
                    $rHistory->history = \Common::instance()->getCurrentUserName().' edited the reply with ID ('. $request->rIdx .')';
                    $rHistory->history_created = $dateNow;
                    $rHistory->save();

                // $request->request->add(['history' => 'Ticket status is '.LibStatus::find($request->statusID)->status_description.'.']);
                // $this->insertHistory($request);
					
					
				// $_ticketDetail = Ticket::getTicketDetails($this->tixParamUrl)[0];
				//$_ticketDetail = Ticket::where('ticket_id', $request->ticketId)->first();
				
				$_ticketDetail = Ticket::getTicketDetails($request->ticketId);
                $tDetail = $_ticketDetail[0];
                $ao_email = $tDetail->ao_email; 
                $req_email = $tDetail->requestor_email;
                $subjectDetail = $tDetail->subject;
				
				$txId = $request->ticketId;
				
				$_eLink = 'https://tcd-portal.ics.com.ph/view-request/'.base64_encode($txId);
				\Log::info('linkto: '. $_eLink);
				
				//$_content = \Common::instance()->getCurrentUserName() . ' Edited the reply with ID ('. $request->rIdx .')';
				$_content = $request->rValue;
				
				//$to = 'mescario@ics.com.ph';
				
				//$to = Assignment::getAssignedEmail($request->ticketId).','.CarbonCopy::getCCEmail($request->ticketId).','.ESDAccount::getAdminEmail().','.$req_email.','.$ao_email.','.Session('userData')->Email;

				//$to = trim(Assignment::getAssignedEmail($request->ticketId).','.ESDAccount::getAdminEmail().',');
				
				$to = $ao_email;
				
				$cc = trim(CarbonCopy::getCCEmail($request->ticketId).','.Assignment::getAssignedEmail($request->ticketId).','.ESDAccount::getAdminEmail().',npacheco@ics.com.ph'.','.'jwong@ics.com.ph',',');
				
				//$cc = 'dramos@ics.com.ph';
				
				
				$_subject = \Common::instance()->getCurrentUserName().' Edited a reply ('. $request->rIdx .') '. $subjectDetail;
				// $this->insertEmailNotif($_subject, $_content, $to, $cc);
				$this->insertEditReplyNotif($_subject, $_content, $to, $cc, $_eLink);
				
				//Mail::to($to)->cc($cc)->send(new ProdEditReplyNotif($_subject, $_content, $_eLink));
					
                    return response()->json(['updated' => 'Reply was updated']);
                }
            } 
			
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}

