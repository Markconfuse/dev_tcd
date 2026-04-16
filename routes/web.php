<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  if (Auth::check()) {
    return redirect()->route('compose-request');
  }

    // return redirect()->intended('http://proport.southeastasia.cloudapp.azure.com/login');
  	// return view('errors.503');
    return redirect()->route('login');
});


Auth::routes(['login' => 'auth.login']);
Route::get('/logout', ['as' =>'logout', 'uses' => 'Auth\LoginController@logout']);


Route::get('auth/google',['as' => 'googleRedirect', 'uses' => 'SocialController@redirectToGoogle']);
Route::get('google/callback', ['as' => 'googleCallBack', 'uses' => 'SocialController@handleGoogleCallback']);

Route::get('google/quickie', 'SocialController@handleQuickie');


//Temporary Route
Route::get('setAcc/{tid}', ['as' => 'setAcc', 'uses' => 'RequestController@setAcc']);

Route::post('search-ticket', ['as' => 'search-ticket', 'uses' => 'RequestController@searchTicket']);
//End of Temporary Route

/*
|--------------------------------------------------------------------------
| Home Controller
|--------------------------------------------------------------------------|
*/

Auth::routes();

// Side Bar Routes

Route::get('compose-request', ['as' => 'compose-request', 'uses' => 'RequestController@composeRequest']);

Route::get('sampe', ['as' => 'sampe', 'uses' => 'RequestController@sampe']);

Route::get('view-request/{ticketID}', ['as' => 'view-request', 'uses' => 'RequestController@viewRequest']);

Route::get('status-request', ['as' => 'status-request', 'uses' => 'RequestController@statusRequest']);

//Escalated
Route::get('esclated-request', ['as' => 'escalated-request', 'uses' => 'RequestController@escalatedRequest']);

// Edit Reply
Route::get('get_single_reply/{rId}', 'EditReplyController@getSingleReply');
// Get Reply Info
Route::get('get_reply_info/{rId}', 'EditReplyController@getReplyInfo');
// Submit Edited Reply
Route::post('submit_edited_reply', ['as' => 'editReply', 'uses' => 'EditReplyController@editReply']);

// End of Side Bar Routes
Route::get('tcd-reports', ['as' => 'tcd-reports', 'uses' => 'TcdReportsController@index']);
Route::get('/tcd_reports_datatable', 'TcdReportsController@tcdReportsDataTable')->name('tcd_reports_datatable');

Route::get('/filter_reports', 'TcdReportsController@filterReports')->name('filter_reports');

// Route::get('/filter_tcd_reports', 'TcdReportsController@filtertcdReportsDataTable')->name('filter_tcd_reports');
Route::get('/filtered_tcd_reports', 'TcdReportsController@filteredResultsDataTable');
Route::get('/export_tcd/{month}/{year}', 'TcdReportsController@exportTcdMonthYear');

// TCD Reports

Route::get('tcd-reports', ['as' => 'tcd-reports', 'uses' => 'TcdReportsController@index']);
Route::get('/tcd_reports_datatable', 'TcdReportsController@tcdReportsDataTable')->name('tcd_reports_datatable');

Route::get('/filter_reports', 'TcdReportsController@filterReports')->name('filter_reports');

// Route::get('/filter_tcd_reports', 'TcdReportsController@filtertcdReportsDataTable')->name('filter_tcd_reports');
Route::get('/filtered_tcd_reports', 'TcdReportsController@filteredResultsDataTable');
Route::get('/export_tcd/{month}/{year}', 'TcdReportsController@exportTcdMonthYear');

// End of TCD Reports

//Start of Dashboard Routes
Route::get('home', ['as' => 'home', 'uses' => 'DashboardController@dashboard']);

Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);

Route::get('getChartRequestType', 'DashboardController@getChartRequestType');

Route::get('getChartPerBU', 'DashboardController@getChartPerBU');

Route::get('getChartPerAO', 'DashboardController@getChartPerAO');

Route::get('getTicketPerBu', ['as' => 'getTicketPerBu', 'uses' => 'DashboardController@getTicketPerBu']);

Route::get('getTicketPerEngineer', ['as' => 'getTicketPerEngineer', 'uses' => 'DashboardController@getTicketPerEngineer']);

Route::get('getMonthlyTicketTrend', ['as' => 'getMonthlyTicketTrend', 'uses' => 'DashboardController@getMonthlyTicketTrend']);

Route::get('getAvgHandlingTimePerEngineer', ['as' => 'getAvgHandlingTimePerEngineer', 'uses' => 'DashboardController@getAvgHandlingTimePerEngineer']);


Route::get('/tickets/{tType}/{ownerId}', 
    'RequestController@getEngineerTicketData'
);

//End of Dashboard Route


//Start of User Logs
Route::get('user-logs', function() {
	if (Auth::check()) {
	    	$logs = DB::SELECT("SELECT TOP 100 * FROM user_logs A INNER JOIN vw_crm_accounts B ON A.domain_account = B.DomainAccount 
					WHERE ip_address NOT IN('192.168.15.12','192.168.15.13', '192.168.15.17','192.168.85.100')
					AND Email NOT IN('laranda@ics.com.ph', 'fquiano@ics.com.ph', 'dramos@ics.com.ph',
					'rdiana@ics.com.ph', 'mbaliza@ics.com.ph', 'ddyu@ics.com.ph', 'aeugenio@ics.com.ph')
					ORDER BY 1 desc");
			return view('userlogs.userlogs', compact('logs'));
	}
	    return redirect()->route('login');
});
//End of User Logs

//List of Ajax Request

Route::get('getTicket', ['as' => 'getTicket', 'uses' => 'RequestController@getTicket']);


Route::get('unreadNotification', ['as' => 'unreadNotification', 'uses' => 'RequestController@unreadNotification']);

Route::get('countUnread', ['as' => 'countUnread', 'uses' => 'RequestController@countUnread']);

Route::get('getHistory', ['as' => 'getHistory', 'uses' => 'RequestController@getHistory']);

Route::get('getSideCount', ['as' => 'getSideCount', 'uses' => 'RequestController@getSideCount']);

// Announcements
Route::get('api/announcements/active', 'AnnouncementController@getActiveAnnouncements');
Route::post('api/announcements/acknowledge', 'AnnouncementController@acknowledge');

//End of Ajax Request


//Start of System Maintenance
 
Route::get('brand-settings', ['as' => 'brand-settings', 'uses' => 'MaintenanceController@brandSettings']);

Route::post('add-brand', 'MaintenanceController@insertBrand');

Route::get('delete-brand',['as' => 'delete-brand', 'uses' => 'MaintenanceController@deleteBrand']);

Route::post('edit-brand',['as' => 'edit-brand', 'uses' => 'MaintenanceController@editBrand']);



//End of System Maintenance

// Settings
Route::post('settings/engineer-reminder', ['as' => 'settings.engineer_reminder', 'uses' => 'SettingsController@updateEngineerReminder']);
Route::post('settings/year-filter', ['as' => 'settings.year_filter', 'uses' => 'SettingsController@updateYearFilter']);

Route::get('viewFile/{file_name}',['as' => 'viewFile', 'uses' => 'RequestController@viewFile']);

Route::get('appsdevDownload',['as' => 'appsdevDownload', 'uses' => 'RequestController@appsdevDownload']);


Route::post('post-request', 'RequestController@postRequest');

Route::post('post-reply', 'RequestController@postReply');



Route::post('dropzone', ['as' => 'dropzone', 'uses' => 'RequestController@dropzone']);

Route::post('attachment-delete', ['as' => 'attachment-delete', 'uses' => 'RequestController@attachmentDelete']);

Route::post('update-assignment', 'RequestController@updateAssignment');

Route::post('tag-update', 'RequestController@tagUpdate');

Route::get('test-pusher', function() {

	event(new \App\Events\FormSubmitted(1));
});


Route::fallback(function() {
    return view('errors.404');
});


Route::get('checkSession','SocialController@checkSession');