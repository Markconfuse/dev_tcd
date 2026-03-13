<?php

namespace App\Traits;

use DB;
use URL;
use Carbon;
use Session;

use App\History;

trait HistoryQueries
{
  public function insertHistory($request)
    {

      $_insertHistory = new History();
      $_insertHistory->ticket_id = $request->ticketID;
      $_insertHistory->history = $request->history;
      $_insertHistory->history_created = \Carbon\Carbon::now()->format('m/d/y H:i:s');
      $_insertHistory->save();

    }

}