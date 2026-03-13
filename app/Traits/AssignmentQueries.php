<?php

namespace App\Traits;

use DB;
use App;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Ticket;
use App\CarbonCopy;
use App\ESDAccount;
use App\Assignment;
use App\CRMAccount;

trait AssignmentQueries
{

    public function insertAssignment($_ticketID, $_owner_id, $_isType)
    {
        if(!empty($_owner_id)) {
            foreach ($_owner_id as $key => $account_id) {
                $_insertAssignment = new Assignment();
                $_insertAssignment->ticket_id = $_ticketID;
                $_insertAssignment->owner_id = $account_id;
                $_insertAssignment->date_assigned = \Carbon\Carbon::now()->format('m/d/Y H:i:s'); 
                $_insertAssignment->is_read = $_isType;
                $_insertAssignment->is_answered = $_isType;
                $_insertAssignment->is_deleted = 0;
                $_insertAssignment->save();
            }
        }
    }

    public function checkReplyStatus($request) //Check if reply count is equal to answered 
    {
        $_assignee = Assignment::ticketID($this->tixParamUrl)->notDeleted();
        $_assigneeAns = Assignment::ticketID($this->tixParamUrl)->notDeleted()->answered();

        if($_assignee->count() == $_assigneeAns->count() && $_assignee->count() !== 0) {
            $request->request->add(['statusID' => '3']);
        } elseif($_assignee->count() == 0) {
            $request->request->add(['statusID' => '1']);
        } else {
            $request->request->add(['statusID' => '2']);
        }

        $this->updateTicketStatus($request);
    }

    public function updateReadStatus(Request $request)
    {
        $_checkQry = Assignment::validEngr($request->ticketID);

        if($_checkQry->count() > 0) {
            $_checkQry = $_checkQry->get();

            if($_checkQry[0]->is_deleted == 0 && $_checkQry[0]->is_answered == 0 && $_checkQry[0]->is_read == 0) {
                $_makeRead = Assignment::find($_checkQry[0]->assignment_id);
                $_makeRead->is_read = 1;
                $_makeRead->save();

                $request->request->add(['history' => \Common::instance()->getCurrentUserName().' seen Ticket#'.sprintf('%04d', $request->ticketID).'.']);
                $this->insertHistory($request);
                $this->lastUpdate($request->ticketID);
            }
        }
    }

    public function updateAnsweredStatus(Request $request)
    {

        $_assignID = Assignment::ownDetail($this->tixParamUrl)->pluck('assignment_id');

        if($_assignID->isNotEmpty()) {
            $_assignID = Assignment::ownDetail($this->tixParamUrl)->pluck('assignment_id')[0];
            $_updateIsAnswered = Assignment::find($_assignID);

            if($_updateIsAnswered->is_answered == 1) {
                    $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                            ' posted a new reply (ReplyID:'.$request->replyID.')']);
            } else {
                if(!empty($request->is_answered)) {
                    $_updateIsAnswered->is_answered = 1;
                    $_updateIsAnswered->save();

                    $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                                ' posted a new reply (ReplyID:'.$request->replyID.') and tagged self as Answered']);
                } else {
                    $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                        ' posted a new reply (ReplyID:'.$request->replyID.') without tagging self as Answered.']);
                }
            }

        } else {
            $request->request->add(['history' => \Common::instance()->getCurrentUserName().
                            ' posted a new reply (ReplyID:'.$request->replyID.')']);
        }

        return $request;
    }

    public function updateAssignment(Request $request)
    {

        $engr_id = $request->engrID;
        $assign_id = $request->assignmentID;
        $assign_remarks = $request->assignmentRemarks;

        $request->request->add(['ticketID' => $this->tixParamUrl]);
        $temp = array();

        if(!empty($assign_id)) { //UnAssigning Engineer
		
			$aEngrs = Assignment::where('ticket_id', $request->ticketID)->get();
            $aE = [];
            foreach ($aEngrs as $eId) {
                $acc = CRMAccount::where('AccountID', $eId->owner_id)->pluck('AccountName');
                $aE = array_merge($aE, $acc->toArray());
            }
            $pM = implode(', ', $aE);
            \Log::info($pM);
            $this->saveLogs('Update assignment and untagged '. $pM);
			
            foreach ($assign_id as $key => $assignmentID) {
                $_updateAssignment = Assignment::find($assignmentID);
                array_push($temp, $_updateAssignment->owner_id);
                $_updateAssignment->is_answered = 0;
                $_updateAssignment->is_read = 0;
                $_updateAssignment->is_deleted = 1;
                $_updateAssignment->save();
            }

            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' untagged '.ESDAccount::getAccountName(serialize($temp)).'.']);
            $this->insertHistory($request);
        }

        if(!empty($engr_id)) { //Assigning Engineer
            foreach ($engr_id as $key => $ownerID) {
                $checkAssign = Assignment::where([
                                    ['owner_id', '=', $ownerID],
                                    ['ticket_id', '=', $this->tixParamUrl]
                                ])->get();

                if($checkAssign->isNotEmpty()) {
                    $_updateAssignment = Assignment::find($checkAssign->pluck('assignment_id')[0]);
                    $_updateAssignment->is_answered = 0;
                    $_updateAssignment->is_deleted = 0;
                    $_updateAssignment->save();
                } else {
                    $this->insertAssignment($this->tixParamUrl, explode(',', $ownerID), 0);
                }
            }

            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' assigned '.ESDAccount::getAccountName(serialize($engr_id)).'.']);
            $this->insertHistory($request);
        }

        $_ccID = $request->ccID;
        
        if(!empty($request->ccID)) {
            $this->insertCarbonCopy($this->tixParamUrl,$_ccID);
            
            $_history = \Common::instance()->getCurrentUserName().' looped in ('.ESDAccount::getAccountName(serialize($_ccID)).').';
            $request->request->add(['history' => $_history]);
            $this->insertHistory($request);
        }

        $_ticketDetail = Ticket::getTicketDetails($this->tixParamUrl)[0];

        $to = $_ticketDetail->ao_email;

        $cc = trim(CarbonCopy::getCCEmail($this->tixParamUrl).','.Assignment::getAssignedEmail($this->tixParamUrl).','.ESDAccount::getAdminEmail().',npacheco@ics.com.ph',',');

        $_subject = \Common::instance()->getCurrentUserName().' updated the assignment of '.$_ticketDetail->subject;

        $_content = 'Hi Everyone,<br><br>'; 
        $_content .= \Common::instance()->getCurrentUserName().' updated the assignment of Ticket#'.sprintf('%04d', $request->ticketID).'.<br><br>';
        $_content .= 'CURRENT ASSIGNEE(S):<br>';
        $_content .= \Common::instance()->col2str(Assignment::getAssignedDetail($request->ticketID)->get()->toJson(), 'AccountName').'.<br><br>';
        $_content .= 'Please check and confirm.<br><br>';

        $_content = $this->renderEmailContent($_content, $this->url.'/view-request/'.base64_encode($request->ticketID));

        $this->insertEmailNotif($_subject, $_content, $to, $cc);

        if(!empty($assign_remarks)) {
            $request->request->add(['replyContent' => $assign_remarks]);
            $_tempCont = $this->insertReply($request);
            $_replyID = $_tempCont[0];
            $request->request->add(['history' => \Common::instance()->getCurrentUserName().' posted a remark (ReplyID:'.$_replyID.').']);
            $this->insertHistory($request);
        }
        
        $this->checkReplyStatus($request);

        \Session::flash('message', 'Ticket Assignment has been updated!');
        \Session::flash('status', 'success');

        $this->saveLogs('Updated Assignment of Ticket#'.$this->tixParamUrl); 

        $this->lastUpdate($this->tixParamUrl);

        return redirect()->back();
    }

    public function resetAssignmentStatus($_ticketID) //All Assignee
    {
        foreach (Assignment::getAssignedDetail($_ticketID)->notDeleted()->pluck('assignment_id') as $key => $assignmentID) {
            $_resetStatus = Assignment::find($assignmentID);
            $_resetStatus->is_answered = 0;
            $_resetStatus->is_read = 0;
            $_resetStatus->save();
        }
    }

    public function resetIndiAssignment($_resetListID) 
    {
        if(!empty($_resetListID)) {
            foreach($_resetListID as $key => $assignID) {
                $_resetStatus = Assignment::find($assignID);
                $_resetStatus->is_answered = 0;
                $_resetStatus->is_read = 0;
                $_resetStatus->save();
            }
        }
    }    

}