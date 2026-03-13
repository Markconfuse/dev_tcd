<?php

namespace App\Traits;

use DB;
use App;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\TempSearch;

trait TempSearchQueries
{

    public function updateTempSearch($_ticketID)
    {

        $temp_exists = TempSearch::find($_ticketID);

        if(empty($temp_exists)) {
            $_insertTemp = new TempSearch();
            $_insertTemp->ticket_id = $_ticketID;
            $_insertTemp->save();
        } 

        DB::UPDATE("UPDATE temp_search
                SET 

                ticket_reply = ( SELECT STUFF((SELECT reply FROM ticket_reply tr
                WHERE ticket.ticket_id = tr.ticket_id
                FOR XML PATH('')), 1, 1, '') [ticket_reply] FROM ticket
                WHERE ticket_id = $_ticketID
                GROUP BY ticket_id),

                OwnerName = (SELECT STUFF((SELECT ';' + acc.AccountName + ',' + ISNULL(acc.GAvatar, 0) + ',' +
                convert(nvarchar(255), ta.is_answered) + ',' + convert(nvarchar(255), ta.is_read)
                FROM  ticket_assignment ta
                INNER JOIN vw_crm_accounts acc
                ON ta.owner_id = acc.AccountID
                WHERE ticket.ticket_id = ta.ticket_id AND ta.is_deleted = 0
                FOR XML PATH('')), 1, 1, '') [OwnerName] FROM ticket 
                WHERE ticket_id = $_ticketID
                GROUP BY ticket_id),

                last_transaction = (SELECT history as last_transaction FROM (
                SELECT MAX(history_id) as history_id, ticket_id FROM ticket_history
                WHERE history NOT LIKE '%ticket status%' AND
                ticket_id = $_ticketID
                GROUP BY ticket_id ) trans INNER JOIN ticket_history th
                ON trans.history_id = th.history_id),

                reply_ctr = (SELECT count(reply_id) as reply_ctr FROM ticket_reply 
                WHERE ticket_id = $_ticketID GROUP BY ticket_id)

                WHERE ticket_id = $_ticketID");
    }

    public function searchTicket(Request $request)
    {

        return redirect()->route('view-request', [base64_encode(preg_replace('~\D~', '', $request->refNo))]);
    }
}