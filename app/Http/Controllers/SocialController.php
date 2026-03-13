<?php

namespace App\Http\Controllers;

use App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use DB;
use Auth;
use Config;
use Session;
use Socialite;
use Carbon\Carbon;

use App\Traits\LogQueries;

use App\User;
use App\ESDAccount;
use App\CRMAccount;

class SocialController extends Controller
{

    use LogQueries;
    
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    public function handleGoogleCallback(Request $request)
    {
        $_user = Socialite::driver('google')->stateless()->user();

        // dd($_user);
		
		// \Log::info($_user->avatar);

        $request->session()->put('Guser', $_user);

        $_email = $_user->getEmail();
       
        // if($_email == 'dramos@ics.com.ph') { $_email = 'appsdevtester@ics.com.ph'; }
		//if($_email == 'dramos@ics.com.ph') { $_email = 'amarquez@ics.com.ph'; }
        // if($_email == 'mescario@ics.com.ph') { $_email = 'jwong@ics.com.ph'; }
        // if($_email == 'mescario@ics.com.ph') { $_email = 'macosta@ics.com.ph'; }
		
		//if($_email == 'dramos@ics.com.ph') { $_email = 'smpenalosa@ics.com.ph'; }
		//if($_email == 'mescario@ics.com.ph') { $_email = 'npacheco@ics.com.ph'; }
	
		//if($_email == 'mescario@ics.com.ph') { $_email = 'eucconsultant@ics.com.ph'; }
		//if($_email == 'mescario@ics.com.ph') { $_email = 'mborromeo@ics.com.ph'; }
	 
	 	//if($_email == 'mescario@ics.com.ph') { $_email = 'mmanuel@ics.com.ph'; }
		 
		//if($_email == 'mescario@ics.com.ph') { $_email = 'aantonio@ics.com.ph'; }
		
	    //if($_email == 'mescario@ics.com.ph') { $_email = 'rdadios@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'randres@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'bbelocura@ics.com.ph'; }
		
	    //if($_email == 'mescario@ics.com.ph') { $_email = 'simperial@ics.com.ph'; }
		//if($_email == 'mescario@ics.com.ph') { $_email = 'jrabanillo@ics.com.ph'; }
	    //if($_email == 'mescario@ics.com.ph') { $_email = 'jwong@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'cornum@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'emercado@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'simperial@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'dramos@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'simperial@ics.com.ph'; }
		
	    //if($_email == 'mescario@ics.com.ph') { $_email = 'agarfin@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'bsanchez@ics.com.ph'; }
		
		//if($_email == 'mescario@ics.com.ph') { $_email = 'jmagangan@ics.com.ph'; }
		
	    //if($_email == 'mescario@ics.com.ph') { $_email = 'apelayo@ics.com.ph'; }

		//if($_email == 'mescario@ics.com.ph') { $_email = 'npacheco@ics.com.ph'; }
        // if($_email <> 'dramos@ics.com.ph') {
        //     return redirect()->intended('http://tcdportal.southeastasia.cloudapp.azure.com/login');
        // } 

        
        $_userData = ESDAccount::where('Email', $_email)->first();
		\Log::info(json_encode($_userData));
		\Log::info(json_encode($_user));
        //dd($_user,$_email,$_userData);

        if(empty($_userData)) {
            \Session::flash('message', 'Insufficient Access. Please contact any of the APPSDEV TEAM.');
            \Session::flash('status', 'error');

            return redirect()->route('login');
        }

        $_crmAcc = CRMAccount::where('Email', $_email)->first();

        //if ($_crmAcc->GAvatar !== Session('Guser')->avatar_original) {
			//\Log::info(Session('Guser')->avatar_original);
            //$_crmAcc->update(array('GAvatar' => Session('Guser')->avatar_original));
        //}

        $request->session()->put('userData', $_userData);

        $_checkToken = User::where('email', $_email)->first();
		//dd($_email, $_checkToken);
        if (empty($_checkToken)) { 
            $newUser = new User;
            $newUser->first_name = $_user->user['given_name'];
            $newUser->last_name = $_user->user['family_name'];
            $newUser->email = $_email;
            $_pass = Str::random(10);
            $newUser->password = bcrypt($_pass);
            $newUser->token = $_user->token;
            $newUser->activated = !config('settings.activation');
            $newUser->save();
            $_userID = $newUser->id;
        } else {
            $_userID = $_checkToken->id;
        }
		
		//dd($_checkToken);
		
        Auth::loginUsingId($_userID);

        $this->saveLogs('Logged In');

        \Session::flash('message', 'Successfully logged in as '. Str::studly($_userData->role_name));
        \Session::flash('status', 'success');


        if (!empty($request->session()->get('url')['intended'])) {
          $_redUrl = $request->session()->get('url')['intended'];

          return redirect($_redUrl);
        } else {
          return redirect()->to('http://localhost:7000/status-request?status=all');
        }
    }

    public function handleQuickie(Request $request)
    {

        if(!empty($request->em) && base64_encode(base64_decode($request->em, true)) === $request->em) {

            $_email = base64_decode($request->em);

            if(!empty(ESDAccount::where('Email', $_email)->first()) && !empty(User::where('email', $_email)->first())) {


                Auth::loginUsingId(User::where('email', $_email)->first()->id);
                Session::put('userData', ESDAccount::where('Email', $_email)->first());
                
                
                $this->saveLogs('Logged In');

                \Session::flash('message', 'You have successfully signed in.');
                \Session::flash('status', 'success');

                return redirect()->route('status-request', ['status' => 'all']);
            }

            return redirect()->route('login');
        }

        return redirect()->route('login');
    }
}
