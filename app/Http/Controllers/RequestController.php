<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use Storage;
use Session;
use Config;
use File;
use Str;
use URL;
use DB;

use App\LibTransaction;
use App\EmailExempted;
use App\RequestType;
use App\BrandTicket;
use App\ESDAccount;
use App\CarbonCopy;
use App\Assignment;
use App\Attachment;
use App\LibStatus;
use App\LibHeads;
use App\LibBrand;
use App\Ticket;
use App\Reply;

use App\Traits\BrandTicketQueries;
use App\Traits\TempSearchQueries;
use App\Traits\AttachmentQueries;
use App\Traits\CarbonCopyQueries;
use App\Traits\AssignmentQueries;
use App\Traits\EmailNotifQueries;
use App\Traits\UserNotifQueries;
use App\Traits\HistoryQueries;
use App\Traits\TicketQueries;
use App\Traits\ReplyQueries;
use App\Traits\JsonQueries;
use App\Traits\LogQueries;

ini_set('memory_limit', '4095M');

class RequestController extends Controller
{

    use BrandTicketQueries;
    use TempSearchQueries;
    use AttachmentQueries;
    use CarbonCopyQueries;
    use AssignmentQueries;
    use EmailNotifQueries;
    use UserNotifQueries;
    use HistoryQueries;
    use TicketQueries;
    use ReplyQueries;
    use JsonQueries;
    use LogQueries;


    public function __construct()
    {
        $this->db = Config::get('dbcon.db'); 
        $this->middleware('auth', ['except' => ['searchTix','appsdevDownload']]);
        $this->tixParamUrl = base64_decode(basename(URL::previous()));
        $this->ccDef ='57628';
        $this->url = "https://localhost:7000";
        $this->bcc = 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph';
    }


    public function setAcc($typeID)
    {
        DB::UPDATE('UPDATE user_role set role_id='.$typeID.' WHERE account_id='.Session('userData')->account_id);

        Session()->flush();

        return redirect()->route('googleRedirect');

    }

    public function composeRequest()
    {
      // dd(ESDAccount::getBUHead(Session::get('userData')->AccountGroup)->Main()->get());
        $_requestType = RequestType::cursor();
                                                  
        if(Session('userData')->AccountGroup == 'BU8' || Session('userData')->AccountGroup == 'BU12') {
            $_ao = ESDAccount::search('AccountGroup', ['BU8', 'BU12']);
        } else if (Session('userData')->AccountGroup== 'TCD') {
            $_ao = ESDAccount::cursor();
        } else {
            $_ao = ESDAccount::search('AccountGroup', [Session('userData')->AccountGroup]);
        }
		
		
		$_cc = ESDAccount::where('role_id','<>','3')->orWhere('account_id', '=', 57786)->orWhere('account_id', '=', 57812)->get();
		

        $_engineer = ESDAccount::where('role_id', 3)->get();

        $_brand = LibBrand::orderBy('brand', 'asc')->cursor();

        $this->saveLogs('Viewed Compose Request');

        return view('requestor.compose_request.cr_main', compact('_requestType', '_ao', '_cc', '_engineer','_brand'));
    }

    public function postRequest(Request $request)
    {

      // if ($request->aoID == 56395) {
      //   $this->insertAttachment($request);
      //   dd($request);
      //   // code...
      // }
        $request->request->add(['cycle' => 1]);

        if(!empty($request->engrID)) { // Engineer or Admin Created a request
            $_aoDetails = ESDAccount::where('account_id', $request->aoID)->get();
            $request->request->add(['ao_name' => $_aoDetails[0]->AccountName]);
            $_buHead = ESDAccount::getBUHead($_aoDetails[0]->AccountGroup)->Main()->get();

            if(Session('userData')->role_name == 'engineer') {
                $request->request->add(['statusID' => '3']);
                $_isType = 1;
            } else {
                $request->request->add(['statusID' => '2']);
                $_isType = 0;
            }
        } else { //If AO Created her own request
            $request->request->add(['statusID' => '1']);
            $_buHead = ESDAccount::getBUHead(Session::get('userData')->AccountGroup)->Main()->get();
        }

        $_ticketID = $this->insertTicket($request); 
        $request->request->add(['ticketID' => $_ticketID]);

        $_listExempted = EmailExempted::cursor()->pluck('account_id')->toArray();

        $_ccID = $request->ccID;

        if(empty($_ccID)) { $_ccID = []; } 

        if($_buHead->isNotEmpty()) {
            if(!in_array($_buHead[0]->account_id, $_listExempted) && $_buHead->isNotEmpty() 
                && $_buHead[0]->account_id !== Session('userData')->account_id) {
                $_ccID = array_merge(explode(',', $_buHead[0]->account_id), $_ccID);
            } 
        }

        // if jrevelo or rrosal then CC corporate bu10
        if (Session('userData')->account_id == 57673 || Session('userData')->account_id == 57674) {
          array_push($_ccID, '520');
          // dd($_ccID,$request->ccID,$_buHead[0]->account_id);
        }

        $request->request->add(['history' => \Common::instance()->historyCreate($request)]);
        $this->insertHistory($request);

        if(!empty($request->engrID)) {
            $this->insertAssignment($_ticketID, $request->engrID, $_isType);
            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' assigned '.ESDAccount::getAccountName(serialize($request->engrID)).'.']);
            $this->insertHistory($request);

            if(Session('userData')->AccountGroup == 'CE01') {
                $to = ESDAccount::getAccountEmail(serialize($request->engrID));
            } else {
                $to = $_aoDetails[0]->Email;
            }

            $cc = trim(ESDAccount::getAccountEmail(serialize($_ccID)).','.ESDAccount::getAdminEmail(), ',');
        } else {
            $to = ESDAccount::getAdminEmail();
            $cc = ESDAccount::getAccountEmail(serialize($_ccID));
        }

        $_content = $this->generateEmailNotif($request);

        $this->insertAttachment($request);

        $this->insertBrandTicket($_ticketID, $request->brandID);
                
        $this->insertCarbonCopy($_ticketID ,$_ccID);

        $_subject = \Common::instance()->getCurrentUserName().' created '.$request->subject;

        $cc = $cc.','.Session('userData')->Email;
		
		if (Session('userData')->AccountGroup == 'BU10' || Session('userData')->account_id == '310') {
           
           $cc = "bu10@ics.com.ph,dramos@ics.com.ph,EUCCONSULTANT@ICS.COM.PH,dramos@ics.com.ph,emisare@ics.com.ph,MESCARIO@ICS.COM.PH";
        }
        
        $this->insertEmailNotif($_subject, $_content, $to.',npacheco@ics.com.ph,jwong@ics.com.ph', trim($cc, ','));
        
        $this->saveLogs('Created Ticket#'.$_ticketID);
        
        $request->request->add(['history' => 'Ticket status is '.LibStatus::find($request->statusID)->status_description]);
        $this->insertHistory($request);

        $this->lastUpdate($_ticketID);
        
        \Session::flash('message', 'Your request has been sent.');
        \Session::flash('status', 'success');

        return redirect()->route('view-request', [base64_encode($_ticketID)]);
    }

    public function statusRequest(Request $request)
    {
      // dd($request->status);

        if($request->status == 'pending') {
            $_details = ['_statusID' => 31,
                         '_status' => Str::studly($request->status)];
        } else if($request->status == 'reassigned') {
            $_details = ['_statusID' => 5,
                         '_status' => Str::studly($request->status)];
        } else if($request->status == 'cebu') {
            $_details = ['_statusID' => 6,
                         '_status' => Str::studly($request->status)];
						 
		 } else if($request->status == 'escalated') {
                $_details = ['_statusID' => 7,
                             '_status' => Str::studly($request->status)];
		
        } else if ($request->status == 'all') {
            $_details = ['_statusID' => 10,
                         '_status' => Str::studly($request->status)];
						 
		/**
             * UNREAD | UNANSWERED | ENGINEER TICKET MONITORING
             */
        } else if ($request->status == 'unread') {
\Log::info('here 15 unread');
            $_details = [
                '_statusID' => 15,
                '_status' => Str::studly($request->status)
            ];
        } else if ($request->status == 'unanswered') {
            $_details = [
                '_statusID' => 16,
                '_status' => Str::studly($request->status)
            ];
        } else if ($request->status == 'counter') {
            $_details = [
                '_statusID' => 17,
                // '_status' => Str::studly($request->status)
                '_status' => 'Tickets Counter'
            ];

            /**
             *
             */
        } else if ($request->status == 'monitoring') {
            $_details = [
                '_statusID' => 18,
                // '_status' => Str::studly($request->status)
                '_status' => 'Tickets Monitoring'
            ];

            /**
             *
             */
        } else if ($request->status == 'engineers_ticket_monitoring') {
            $_details = [
                '_statusID' => 19,
                // '_status' => Str::studly($request->status)
                '_status' => 'Engrs Ticket Monitoring'
            ];
        } else {
            $_statusID = LibStatus::where('status_description', $request->status)->get()[0]->status_id;
            $_details = ['_statusID' => $_statusID,
                         '_status' => Str::studly($request->status)];
                         // dd($_statusID,$_details);
        }

        $this->saveLogs('Viewed '.$_details['_status'].' Tickets');

        if ($_details['_statusID'] == 17) {
            return view('requestor.status_request.sr_distribution', compact('_details'));
        } else if ($_details['_statusID'] == 15 || $_details['_statusID'] == 16 || $_details['_statusID'] == 18) {
            return view('requestor.status_request.unread_tickets_view', compact('_details'));
        } else if ($_details['_statusID'] == 19) {
            return view('requestor.status_request.sr_tickets_status_counter', compact('_details'));
        } else {
            return view('requestor.status_request.sr_main', compact('_details'));
        }
    }

    public function sampe()
    {
        $ticketID = 'MTIwMTY=';
        $_ticketID = base64_decode($ticketID);

        $_ticketDetail = Ticket::getTicketDetails($_ticketID);


        if($_ticketDetail->isNotEmpty()) {

            // $request->request->add(['ticketID' => $_ticketDetail[0]->ticket_id]);

            // if(Session('userData')->role_name == 'engineer') {
            //     $this->updateReadStatus($request);
            // }

            $_ticketAssignment = Assignment::getAssignedDetail($_ticketID)->get();

            $_ticketReply = Reply::getTicketReply($_ticketID);
            $_ticketCC = CarbonCopy::getCC($_ticketID);
            $_ticketBrand = BrandTicket::getBrandTicket($_ticketID);

            $_attachment = Attachment::getTicketFile($_ticketID);

            $_accounts = \Common::instance()->col2str($_ticketCC,'account_id');
            $_accounts .= ','. \Common::instance()->col2str($_ticketAssignment, 'owner_id');
            $_accounts .= ','.$_ticketDetail[0]->req_id.','.$_ticketDetail[0]->ao_id;
            $_accounts .= ','.ESDAccount::getAdminAccountID();

            $_cc = ESDAccount::whereNotIn('account_id', explode(',', $_accounts))->where('role_id','<>','3')->orderBy('AccountName')->get();

            if(in_array(Session::get('userData')->account_id, explode(',',$_accounts))) {
                $_isvalid = true;
            } else {
                $_isvalid = false;
            }

            $_engr = ESDAccount::where('role_id', 3)
                                ->whereNotIn('account_id', $_ticketAssignment
                                ->pluck('owner_id')->toArray())->get();
            
            $_head = LibHeads::where([['department', $_ticketDetail[0]->AccountGroup], 
                                        ['account_id', Session('userData')->account_id]])->count(); 

            // $this->saveLogs('Viewed Ticket#'.$_ticketID);

            if ($_ticketDetail[0]->ticket_id == 10957) {
              // dd($_ticketDetail,Session::get('userData'));
            }
            // dd($_ticketDetail);

            return view('requestor.view_request.vr_main', compact('_ticketDetail', '_ticketAssignment', '_ticketReply', '_ticketCC', '_ticketBrand', '_isvalid', '_attachment', '_cc', '_engr', '_head'));
        } else {
            $this->saveLogs('Viewed Ticket#'.$_ticketID.':No ticket found.');

            return view('errors.408');
        }

    }
	
	
	// For Escalated
    public function escalatedRequest(Request $request)
    {
      // dd($request->status);
	  \Log::info('escalate: '. $request->status);

        if($request->status == 'escalated') {
            $_details = ['_statusID' => 20,
                         '_status' => Str::studly($request->status)];
        } 

        $this->saveLogs('Viewed '.$_details['_status'].' Tickets');

        return view('requestor.status_request.sr_escalated', compact('_details'));
    }

    public function viewRequest(Request $request, $ticketID)
    {

        $_ticketID = base64_decode($ticketID);

        

        $_ticketDetail = Ticket::getTicketDetails($_ticketID);

        // if(Session('userData')->account_id == 57665){
        //     dd($_ticketDetail[0]);
        // }

        if($_ticketDetail->isNotEmpty()) {

            $request->request->add(['ticketID' => $_ticketDetail[0]->ticket_id]);

            if(Session('userData')->role_name == 'engineer') {
                $this->updateReadStatus($request);
            }

            $_ticketAssignment = Assignment::getAssignedDetail($_ticketID)->get();

            $_ticketReply = Reply::getTicketReply($_ticketID);
            $_ticketCC = CarbonCopy::getCC($_ticketID);
            $_ticketBrand = BrandTicket::getBrandTicket($_ticketID);

            $_attachment = Attachment::getTicketFile($_ticketID);

            $_accounts = \Common::instance()->col2str($_ticketCC,'account_id');
            $_accounts .= ','. \Common::instance()->col2str($_ticketAssignment, 'owner_id');
            $_accounts .= ','.$_ticketDetail[0]->req_id.','.$_ticketDetail[0]->ao_id;
            $_accounts .= ','.ESDAccount::getAdminAccountID();

            $_cc = ESDAccount::whereNotIn('account_id', explode(',', $_accounts))->where('role_id','<>','3')->orWhere('account_id', '=', 57786)
			->orWhere('account_id', 57812)->orderBy('AccountName')->get();

            if(in_array(Session::get('userData')->account_id, explode(',',$_accounts))) {
                $_isvalid = true;
            } else {
                $_isvalid = false;
            }

            $_engr = ESDAccount::where('role_id', 3)
                                ->whereNotIn('account_id', $_ticketAssignment
                                ->pluck('owner_id')->toArray())->get();
            
            $_head = LibHeads::where([['department', $_ticketDetail[0]->AccountGroup], 
                                        ['account_id', Session('userData')->account_id]])->count(); 

            $this->saveLogs('Viewed Ticket#'.$_ticketID);

            if ($_ticketDetail[0]->ticket_id == 19634) {
              // dd($_ticketDetail,Session::get('userData'));
              // dd($_accounts,in_array(Session::get('userData')->account_id, explode(',',$_accounts)));
            }

            return view('requestor.view_request.vr_main', compact('_ticketDetail', '_ticketAssignment', '_ticketReply', '_ticketCC', '_ticketBrand', '_isvalid', '_attachment', '_cc', '_engr', '_head'));
        } else {
            $this->saveLogs('Viewed Ticket#'.$_ticketID.':No ticket found.');

            return view('errors.408');
        }
    }   

    public function getEngineerTicketData(Request $request)
    {
        \Log::info("Loading unread tickets for owner: $request->ownerId");
        \Log::info('Type: '. $request->tType);
       
        if ($request->tType == "Tickets"){
            $tickets = $this->getEngineerTicketDetails($request, 'not_yet_red');
        } else if ($request->tType == "Not Yet Read"){
            $tickets = $this->getEngineerNotYetReadTickets($request);
        } else if($request->tType == "Not Yet Answered"){
            $tickets = $this->getEngineerNotYetAnsweredTickets($request);
        } else {
            $tickets = "";
        }

        $_details = [
            '_statusID' => 19,
            // '_status' => Str::studly($request->status)
            '_status' => 'Engineers Ticket Monitoring'
        ];

        return response()->json([
            'html' => view('requestor.partials.ticket_by_owner_table', compact('tickets'))->render()
        ]);
    }
}