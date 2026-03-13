<?php

namespace App\Traits;

use DB;
use App;
use Config;
use Session;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;


use App\CarbonCopy;

trait CarbonCopyQueries
{
	
    public function insertCarbonCopy($_ticketID, $_ccID)
    {

        if(!empty($_ccID)) {    
            foreach ($_ccID as $key => $ccID) {
                $_insertCarbonCopy = new CarbonCopy();
                $_insertCarbonCopy->ticket_id = $_ticketID;
                $_insertCarbonCopy->account_id = $ccID;
                $_insertCarbonCopy->is_read = 0;
                $_insertCarbonCopy->date_added = Carbon::now()->format('m/d/Y H:i:s');
                $_insertCarbonCopy->save();
            }
            return true;
        } else {
            return false;
        }
    }

}