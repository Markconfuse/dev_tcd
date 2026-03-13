<?php

namespace App\Traits;

use DB;
use App;
use Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


use App\BrandTicket;
use App\LibBrand;

trait BrandTicketQueries
{
	
    public function insertBrandTicket($_ticketID, $_brandID)
    {

        if(!empty($_brandID)) {        
            foreach ($_brandID as $key => $brandID) {
                $_insertBrandTicket = new BrandTicket();
                $_insertBrandTicket->ticket_id = $_ticketID;
                $_insertBrandTicket->brand_id = $brandID;
                $_insertBrandTicket->save();
            }
            return true;
        } else {
            return false;
        }
    }

    public function insertBrand(Request $request)
    {

        if(!empty($request->brandName)) {        
            foreach ($request->brandName as $key => $name) {
                $_insertBrand = new LibBrand();
                $_insertBrand->brand = strtoupper($name);
                $_insertBrand->transaction_type_id = $request->brandTypeID[$key];
                $_insertBrand->is_deleted = 0;
                $_insertBrand->save();

                $this->saveLogs('Added New Brand:'.$name);
            }
            Session::flash('message', 'Transaction Successful!');
            Session::flash('status', 'success');
        } else {
            Session::flash('message', 'Transaction Failed!');
            Session::flash('status', 'error');
        }

        return redirect()->back();
    }

    public function deleteBrand(Request $request)
    {
        $_deleteBrand = LibBrand::find($request->bid);
        $_deleteBrand->is_deleted = 1;
        $_deleteBrand->save();

        $this->saveLogs('Deleted BrandID:'.$request->bid);

        Session::flash('message', 'Successfully Deleted!');
        Session::flash('status', 'success');

        return redirect()->back();
    }

    public function editBrand(Request $request)
    {
        $_editBrand = LibBrand::find($request->editBrandID);
        $_editBrand->brand = $request->editBrandName;
        $_editBrand->transaction_type_id = $request->editBrandTypeID;
        $_editBrand->save();

        $this->saveLogs('Updated BrandID:'.$request->bid);

        Session::flash('message', 'Successfully Updated!');
        Session::flash('status', 'success');

        return redirect()->back();
    }

}