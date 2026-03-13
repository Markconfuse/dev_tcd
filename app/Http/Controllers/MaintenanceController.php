<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Storage;
use Session;
use Config;
use File;
use URL;
use DB;


use App\LibBrand;
use App\LibTransaction;

use App\Traits\LogQueries;
use App\Traits\BrandTicketQueries;

class MaintenanceController extends Controller
{
    use LogQueries;
	use BrandTicketQueries;

	public function __construct()
	{
		$this->db = Config::get('dbcon.db'); 
        $this->middleware('auth');
	}

    public function brandSettings()
    {	
    	$_brands = LibBrand::brandWithTrans();
    	$_brandType = LibTransaction::cursor();

        $this->saveLogs('Viewed Brand Settings');

    	return view('maintenance.brand.bs_main', compact('_brands', '_brandType'));
    }
}
