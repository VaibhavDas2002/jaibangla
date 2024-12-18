<?php

use App\Http\Controllers\JBPensionController;
use App\SchemecodeStatic;

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

/*Route::get('/dashboard', function () {
//    return view('dashboard')->with('message','test');
echo 'Test';
})->middleware('auth');
*/


Route::get('/', 'DashboardController@index');
Route::get('/dashboard', 'DashboardController@index');
Route::get('/backendlogin', 'DashboardController@index');
Auth::routes();
Route::get('reset-password', '\App\Http\Controllers\Auth\LoginController@setResetPassword')->name('reset-password');
Route::post('reset-password-post', '\App\Http\Controllers\Auth\LoginController@setResetPasswordPost')->name('reset-password-post');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('/config', 'ConfigController@index');
Route::post('/mapconfig', 'ConfigController@mapconfig');
Route::get('/mapsetting', 'ConfigController@mapsetting');
Route::get('/destroy/{id}', 'ConfigController@destroyId');

Route::get('/mapconfigEdit/{id}', 'ConfigController@mapconfigEdit');
Route::post('/updateConfig/{id}', 'ConfigController@updateConfig');

Route::post('user-management/search', 'UserManagementController@search')->name('user-management.search');
Route::resource('user-management', 'UserManagementController');
Route::get('user-management/find', 'UserManagementController@findEmployee');

Route::resource('employee-management', 'EmployeeManagementController');
Route::post('employee-management/search', 'EmployeeManagementController@search')->name('employee-management.search');

Route::resource('system-management/department', 'DepartmentController');
Route::post('system-management/department/search', 'DepartmentController@search')->name('department.search');

Route::resource('system-management/division', 'DivisionController');
Route::post('system-management/division/search', 'DivisionController@search')->name('division.search');

Route::resource('system-management/country', 'CountryController');
Route::post('system-management/country/search', 'CountryController@search')->name('country.search');

Route::resource('system-management/state', 'StateController');
Route::post('system-management/state/search', 'StateController@search')->name('state.search');

Route::resource('system-management/city', 'CityController');
Route::post('system-management/city/search', 'CityController@search')->name('city.search');

Route::get('system-management/report', 'ReportController@index');
Route::get('system-management/report/pending', 'ReportController@app_pending');
Route::get('system-management/report/processing', 'ReportController@app_processing');
Route::get('system-management/report/rejected', 'ReportController@app_rejected');
Route::post('system-management/report/search', 'ReportController@search')->name('report.search');
Route::post('system-management/report/excel', 'ReportController@exportExcel')->name('report.excel');
Route::post('system-management/report/pdf', 'ReportController@exportPDF')->name('report.pdf');

Route::resource('system-management/crimehead', 'CrimeHeadController');
Route::post('system-management/crimehead/search', 'CrimeHeadController@search')->name('crimehead.search');

Route::get('avatars/{name}', 'EmployeeManagementController@load');


Route::resource('system-management/commissionerate', 'PolicestationController');
Route::post('system-management/commissionerate/search', 'PolicestationController@search')->name('commissionerate.search');

Route::resource('system-management/policestation', 'CSpolicestationController');
Route::post('system-management/policestation/search', 'CSpolicestationController@search')->name('policestation.search');


Route::resource('system-management/designation', 'DesignationController');
Route::post('system-management/designation/search', 'DesignationController@search')->name('designation.search');


Route::get('generalCrime', 'SitationreportController@create');
Route::get('arrestSeizure', 'SitationreportController@arrestSeizure');
Route::get('arrestSeizurePost', 'SitationreportController@arrestSeizurePost');
Route::get('excise_act', 'SitationreportController@excise_act');
Route::get('missingDetails', 'SitationreportController@missingDetails');
Route::get('warrentDetails', 'SitationreportController@warrentDetails');
Route::post('postData', 'SitationreportController@saveMissingDetails');
Route::get('caseReport', 'SitationreportController@caseReport');
Route::get('sitrepReport', 'SitationreportController@sitrepReport');
Route::get('slipDetails', 'SitationreportController@slipDetails');
Route::get('preventiveWarrentDetails', 'SitationreportController@preventiveWarrentDetails');

Route::resource('dailysitrep', 'DailySitRepController');

Route::get('getReport', 'SitationreportController@viewDailySitrep');


Route::resource('report-management/configuresitrep', 'SitrepConfigurationController');

Route::post('saveCompoundSlip', 'SitationreportController@saveCompoundSlip');
Route::post('saveArrestSizure', 'SitationreportController@saveArrestSizure');
Route::post('saveWarrent', 'SitationreportController@saveWarrent');
Route::post('savePreventiveArrest', 'SitationreportController@savePreventiveArrest');
Route::post('saveExciseArrest', 'SitationreportController@saveExciseArrest');

Route::get('backDateCase', 'SitationreportController@backDateCase');
Route::post('saveBackDateCaseDetails', 'SitationreportController@saveBackDateCaseDetails');

Route::get('casesummarydetails', 'SitationreportController@generalCaseSummaryReport');
Route::get('arrestsizuredetails', 'SitationreportController@arrestSizureReport');
Route::get('arrestunserexciseact', 'SitationreportController@arrestExciseReport');




Route::post('verifynhmemployeedata', 'NHMEmployeeController@verifydata'); //->name('nhmemployee.verifydata');
Route::get('verifynhmemployee', 'NHMEmployeeController@verify');

Route::get('approvenhmemployee', 'NHMEmployeeController@approve');
Route::post('approvenhmemployeedata', 'NHMEmployeeController@approvedata');
// Route::post('massapprovenhmemployeedata','NHMEmployeeController@MassEmployeeApproval')->name('nhmemployee.MassEmployeeApproval');

Route::post('shownhmemployee/{id}', 'NHMEmployeeController@showSingleEmployee')->name('nhmemployee.showSingleEmployee');
Route::get('shownhmemployeeapproval/{id}', 'NHMEmployeeController@showSingleEmployeeApproval')->name('nhmemployee.showSingleEmployeeApproval');



Route::post('printnhmemployee/{id}', 'NHMEmployeeController@printSingleEmployee')->name('nhmemployee.printSingleEmployee');

Route::get('admingetreports', 'NHMEmployeeController@admingetreports');

Route::resource('nhmemployee', 'NHMEmployeeController');





Route::resource('system-management/nhmLevel', 'nhmLevelController');
Route::post('system-management/nhmLeveledit/{id}', 'nhmLevelController@edit');
Route::post('system-management/nhmLeveldestroy/{id}', 'nhmLevelController@destroy'); //->name('nhmLevel.destroy');
Route::resource('system-management/nhmPlace', 'NHMPlaceController');

Route::resource('nhmDesignationList', 'NHMDesignationListController');


Route::post('system-management/nhmLevel/search', 'nhmLevelController@search')->name('nhmLevel.search');
Route::post('system-management/nhmPlace/search', 'NHMPlaceController@search')->name('nhmPlace.search');

Route::post('system-management/nhmPlace/store', 'NHMPlaceController@store')->name('nhmPlace.store');

Route::post('nhmDesignationList/search', 'NHMDesignationListController@search')->name('nhmDesignationList.search');
//Route::get('verifynhmemployee','NHMEmployeeController@verify');


//Route::get('/{major_programme_head_id}', 'NHMEmployeeController@loadprogrammeHead');
//Route::get('/programmehead/{programme_head_id}', 'NHMEmployeeController@loadDesignationList');

// Route::group(['prefix' => 'public/programmehead'], function()
// {
//     Route::get('/{programme_head_id}', 'NHMEmployeeController@loadDesignationList');
// });
Route::resource('employee-report', 'EmployeeReportGeneration');
Route::post('employee-report/distreport', 'NHMEmployeeDrilldownReportController@loadreport')->name('employeereport.fetch');

Route::resource('system-management/program_head_master', 'ProgramHeadMasterController');
Route::post('system-management/program_head_master/search', 'ProgramHeadMasterController@search')->name('program_head_master.search');

Route::resource('system-management/major_program_head_master', 'MajorProgramHeadMasterController');

Route::get('ddoemployeelist', 'DDOControllerNHM@index');
Route::post('ddogenerateemployeepay/{id}', 'DDOControllerNHM@generatePayView')->name('ddogenerateemployeepay');

Route::post('ddogenerateemployeepaysave/{id}', 'DDOControllerNHM@SaveSalary')->name('ddogenerateemployeepaysave');

Route::get('resetpassword', 'ManualResetPasswordController@index');
Route::post('resetpasswords', 'ManualResetPasswordController@update')->name('resetpasswords');

Route::group(['prefix' => 'designationlist'], function () {

    Route::get(
        '/{programme_head_id}/{service_category}/{major_programme_head_id}',
        'NHMEmployeeController@loadDesignationList'
    );
});


Route::get('employee-report-drilldown', 'NHMEmployeeDrilldownReportController@index');

Route::group(['prefix' => 'loadreportafterlevel2'], function () {

    Route::post('/{id}', 'NHMEmployeeDrilldownReportController@loadreportafterlevel2');
});

Route::group(['prefix' => 'loadreportafterlevel3'], function () {

    Route::post('/{id}', 'NHMEmployeeDrilldownReportController@loadreportafterlevel3');
});

Route::group(['prefix' => 'loadreportafterlevel4'], function () {

    Route::post('/{id}', 'NHMEmployeeDrilldownReportController@loadreportafterlevel4');
});

Route::get('approvalResult', 'SubmittedApprovecController@approvalResult');


Route::group(['prefix' => 'postingplace'], function () {

    Route::get('/{posting_level}', 'NHMEmployeeController@loadPostingPlace');
});

Route::get('duplicateentry', 'NHMDuplicatedataController@verified_not_approved');
Route::get('enabledisable', 'ConfigController@enabledisable')->name('enabledisable');


Route::any('employee-report-drilldown/appsubmitted', 'NHMEmployeeDrilldownReportController@loadAppSubmitted')->name('employeereport.appsubmitted');
Route::post('employee-report-drilldown/pendingverification', 'NHMEmployeeDrilldownReportController@loadPendingVerification')->name('employeereport.pendingverification');
Route::any('employee-report-drilldown/appverified', 'NHMEmployeeDrilldownReportController@loadApppVerified')->name('employeereport.appverified');
Route::any('employee-report-drilldown/rejectedverification', 'NHMEmployeeDrilldownReportController@loadRejectVerification')->name('employeereport.rejectedverification');
Route::any('employee-report-drilldown/pendingapproval', 'NHMEmployeeDrilldownReportController@loadPendingApproval')->name('employeereport.pendingapproval');
Route::any('employee-report-drilldown/approved', 'NHMEmployeeDrilldownReportController@loadApproved')->name('employeereport.approved');
Route::any('employee-report-drilldown/rejectedapproval', 'NHMEmployeeDrilldownReportController@loadRejectApproval')->name('employeereport.rejectedapproval');

Route::any('shownhmemployeereport/{id}', 'NHMEmployeeDrilldownReportController@showSingleEmployeeReport')->name('nhmemployee.showSingleEmployeeReport');


Route::any('allposts', 'NHMEmployeeDrilldownReportController@allPosts')->name('allposts');
Route::any('allpostsapproved', 'NHMEmployeeDrilldownReportController@allPostsapproved')->name('allpostsapproved');
Route::any('allpostsverified', 'NHMEmployeeDrilldownReportController@allPostsverified')->name('allpostsverified');



Route::get('mainform', 'WorkflowController@formEntryOption');

Route::any('under_maintainance', function () {
    return view('portal.maintainance');
});


Route::resource('scheme-management/SchemeType', 'SchemeTypeController');
Route::post('system-management/SchemeType/search', 'SchemeTypeController@search')->name('SchemeType.search');

Route::resource('scheme-management/scheme', 'SchemeController');
Route::post('scheme-management/scheme/search', 'SchemeController@search')->name('scheme.search');

// Start gobinda
Route::resource('pensionform', 'PensionformController');

Route::resource('fisherman', 'FishermanformController');
Route::resource('msme', 'MSMEformController');
Route::resource('textile', 'TextileformController');


Route::any('application-list', 'PensionformController@applicationlist');

Route::any('application-details/{id}', 'PensionformController@applicationdetails')->name('pensionform.application-details');


Route::any('application-edit', 'PensionformController@applicationeditview')->name('pensionform.application-edit-view');
Route::any('application-update/{id}', 'PensionformController@applicationupdate')->name('pensionform.application-update');
Route::any('fisherman/application-update/{id}', 'FishermanformController@applicationupdate')->name('fisherman.application-update');
Route::any('msme/application-update/{id}', 'MSMEformController@applicationupdate')->name('msme.application-update');
Route::any('textile/application-update/{id}', 'TextileformController@applicationupdate')->name('textile.application-update');

Route::resource('pensionfront', 'PensionfrontController');

//Route::any('manabik_old','PensionformController@manabik')->name('pensionform.manabik');

Route::resource('manabik', 'ManabikController');

// Route::get('schemelist', function () {
//    return view('portal.scheme');
// });
Route::get('schemelist', function () {
    $arr = SchemecodeStatic::getpr1ListPurohit();
    $monthlySlug = $arr['monthly']['slug'];
    $housingSlug = $arr['housing']['slug'];
    //$bothSlug=$arr['both']['slug'];
    return view('portal.scheme', ['monthlySlug' => $monthlySlug, 'housingSlug' => $housingSlug]);
});


// End gobinda

//Rajib 

Route::resource('scheme-management/SchemeType', 'SchemeTypeController');
Route::post('system-management/SchemeType/search', 'SchemeTypeController@search')->name('SchemeType.search');

Route::resource('scheme-management/scheme', 'SchemeController');
Route::post('scheme-management/scheme/search', 'SchemeController@search')->name('scheme.search');

Route::resource('maplevel-management', 'MapLevelController');
Route::post('maplevel-management/scheme/search', 'MapLevelController@search')->name('MapLevel.search');


Route::any('workflow', 'WorkflowController@applicationdetails');
Route::post('forward', 'WorkflowController@verifydata')->name('forward');

Route::get('/images/{slug}', [
    'as' => 'images.show',
    'uses' => 'ImageController@show',
    'middleware' => 'auth',
]);
Route::get('/images_wcd/{slug}', [
    'as' => 'images.show_wcd',
    'uses' => 'ImageController@show_wcd',
    'middleware' => 'auth',
]);
Route::get('/images_manabik/{slug}', [
    'as' => 'images.show_manabik',
    'uses' => 'ImageController@show_manabik',
    'middleware' => 'auth',
]);
Route::get('/images_wp/{slug}', [
    'as' => 'images.show_wp',
    'uses' => 'ImageController@show_wp',
    'middleware' => 'auth',
]);
Route::get('/images_oap/{slug}', [
    'as' => 'images.show_oap',
    'uses' => 'ImageController@show_oap',
    'middleware' => 'auth',
]);
Route::get('/images_sc/{slug}', [
    'as' => 'images.show_sc',
    'uses' => 'ImageController@show_sc',
    'middleware' => 'auth',
]);
Route::get('/images_st/{slug}', [
    'as' => 'images.show_st',
    'uses' => 'ImageController@show_st',
    'middleware' => 'auth',
]);
//End Rajib

//Start Sayantika
Route::get('/config-duty-mgmnt', 'ConfigurableDutyManagementController@index');

Route::get('/mapsetting-config-duty-mgmnt', 'ConfigurableDutyManagementController@mapsetting');

Route::post('/mapconfig-config-duty-mgmnt', 'ConfigurableDutyManagementController@mapconfig');

Route::get('enabledisable-config-duty', 'ConfigurableDutyManagementController@enabledisable')->name('enabledisable-config-duty');
// Route::any('shownhmemployeereport/{id}', 'NHMEmployeeDrilldownReportController@showSingleEmployeeReport')->name('nhmemployee.showSingleEmployeeReport');
//Block drilldown
Route::get('block-drill-down', 'BlockDrillDownReport@index');

Route::post('getdatas', 'BlockDrillDownReport@getdata')->name('getdatas');
Route::get('block-drill-down-submiited/{block_code}/{scheme_id}', 'BlockDrillDownReport@getlistsubmitted')->name('block-drill-down-submiited');
Route::get('block-drill-down-verified/{block_code}/{scheme_id}', 'BlockDrillDownReport@getlistverified')->name('block-drill-down-verified');
Route::get('block-drill-down-approved/{block_code}/{scheme_id}', 'BlockDrillDownReport@getlistapproved')->name('block-drill-down-approved');

Route::any('shownhmemployeereport/{id}', 'WorkflowController@showSingleEmployeeReport')->name('nhmemployee.showSingleEmployeeReport');

Route::any('showApplicantDetails/{id}', 'WorkflowController@showApplicantDetails')->name('nhmemployee.showApplicantDetails');

Route::post('massapprovenhmemployeedata', 'WorkflowController@MassEmployeeApproval')->name('nhmemployee.MassEmployeeApproval');

// approval
Route::post('forward-approve', 'WorkflowController@approvedata')->name('forward-approve');
// scheme selection
Route::get('scheme-selection', 'WorkflowController@shemeSelection')->name('scheme-selection');

//lot generation
Route::get('lot-generation', 'LotGenerationController@index')->name('lot-generation');
Route::any('getlotdata', 'LotGenerationController@getdata')->name('getlotdata');
Route::post('pension.generatelot', 'LotGenerationController@generatelot')->name('pension.generatelot');
//push to ifms
Route::get('push-to-ifms', 'PushToIfmsController@index');

Route::POST('push-to-ifms/showlist', 'PushToIfmsController@showlist')->name('push-to-ifms.showlist');

Route::POST('push-to-ifms/forward', 'PushToIfmsController@forward')->name('push-to-ifms.forward');

Route::Post('push-to-ifms/export', 'PushToIfmsController@exportXml')->name('push-to-ifms.export');
Route::POST('/receive_status', 'PushToIfmsController@receive_status')->name('receive_status');
Route::POST('/ack_status', 'PushToIfmsController@ack_status')->name('ack_status');
Route::get('/wrong_file_status', 'PushToIfmsController@get_wrong_file_status')->name('wrong_file_status');
Route::get('/payment_status', 'PushToIfmsController@payment_status')->name('payment_status');
Route::POST('/rbi_payment_status', 'PushToIfmsController@rbi_payment_status')->name('rbi_payment_status');
Route::get('/temp_payment_status', 'PushToIfmsController@temp_payment_status')->name('temp_payment_status');
Route::get('/wrong_file_test', 'PushToIfmsController@wrong_file_test')->name('wrong_file_test');
Route::get('/log_test', 'PushToIfmsController@log_test')->name('log_test');
Route::get('/import_sent_xml', 'PushToIfmsController@import_sent_xml')->name('import_sent_xml');
Route::get('/import_ack_xml', 'PushToIfmsController@import_ack_xml')->name('import_ack_xml');
Route::get('/import_ifms_error_xml', 'PushToIfmsController@import_ifms_error_xml')->name('import_ifms_error_xml');
Route::get('/import_ifms_error', 'PushToIfmsController@import_ifms_error')->name('import_ifms_error');
Route::get('/import_rbi_xml', 'PushToIfmsController@import_rbi_xml')->name('import_rbi_xml');
Route::get('/import_rbi_list', 'PushToIfmsController@import_rbi_list');
//Route::get('/import_rbi_response', 'CronJobController@getRbiResponse');

Route::get('/import_new_rbi_file', 'PushToIfmsController@import_new_rbi_file');
Route::get('/new_payment_status', 'PushToIfmsController@new_payment_status');
Route::get('/create_sms_csv', 'PushToIfmsController@create_sms_csv')->name('create_sms_csv');
Route::get('/create_sms_lot', 'PushToIfmsController@create_sms_lot')->name('create_sms_lot');
Route::get('/report_lot_master', 'ReportLotMasterController@selectYearMonth');
Route::POST('/report_lot_master_main', 'ReportLotMasterController@index')->name('report_lot_master_main');
Route::get('/import_rbi_response', 'ImportResponseController@import_rbi_response');
//End sayantika

Route::resource('emp-user-duty', 'EmployeeUserDutyController');


Route::any('application-list-read-only', 'PensionformController@applicationlistReadOnly');

Route::any('application-details-read_only/{id}', 'PensionformController@applicationdetailsReadOnly')->name('pensionform.application-details-read-only');


// Route::get('/images/{slug}', [
//      'as'         => 'images.show',
//      'uses'       => 'ImageController@show',
//      'middleware' => 'auth',
// ]);

Route::get('/import_excel', 'ImportExcelController@index');
Route::post('/import_excel/import', 'ImportExcelController@import');
Route::get('/export_xml', 'ImportExcelController@exportXml');


/*==========Subhankar Start=================*/
Route::get('/publiclogin', 'PublicLoginController@index');
Route::post('/sendOtp', 'PublicLoginController@sendOtp');
Route::post('/verifyOtp', 'PublicLoginController@verifyOtp');
Route::get('/refereshcapcha', 'PublicLoginController@refreshCaptcha')->name('refereshcapcha');
Route::get('/pagelogout', 'PublicLoginController@pagelogout');
Route::get('/public-home', 'PublicHomeController@index');

//Route::resource('pensionfront', 'PensionfrontController');

Route::resource('publicloginpensionform', 'PublicLoginPensionformScStController');
Route::resource('publicloginmanabik', 'PublicLoginManabikController');
// Duplicate Approve -Reject
Route::get('duplicate-approval', 'DuplicateApprovalCheckController@index');
Route::post('show-duplicate-approval', 'DuplicateApprovalCheckController@duplicateListing')->name('show-duplicate-approval');
Route::post('accept-one-approval', 'DuplicateApprovalCheckController@acceptOneBen')->name('accept-one-approval');
Route::post('store-accept-one-approval', 'DuplicateApprovalCheckController@storeAcceptOneBen')->name('store-accept-one-approval');
Route::any('report-duplicate-approve', 'DuplicateApprovalCheckController@duplicateApprove')->name('report-duplicate-approve');
Route::post('show-report-dup-reject', 'DuplicateApprovalCheckController@duplicateApprove');


//Bank edit
// Route::get('update-ben-details', 'UpdatebenDetailsController@index')->name('update-ben-details');
Route::post('search-by-name', 'UpdatebenDetailsController@searchByBenName')->name('search-by-name');
Route::post('edit-details', 'UpdatebenDetailsController@editBenDetails');
Route::post('update-bank-details/{id}', 'UpdatebenDetailsController@updateBenDetails');

// Payment Status Report Download Excel
Route::get('download-payment-status', 'DownloadPaymentStatusController@index')->name('download-payment-status');
Route::post('payment-excel-generate', 'DownloadPaymentStatusController@paymentStatusGenerateExcel')->name('payment-excel-generate');

// Payment Status beneficiary
Route::get('ben-payment-status', 'BeneficiaryPaymentStatusController@index')->name('ben-payment-status');
Route::post('search-by-name-pmt', 'BeneficiaryPaymentStatusController@searchByBenName')->name('search-by-name-pmt');
Route::post('view-status/{id}/{scheme_id}', 'BeneficiaryPaymentStatusController@viewStatus');

// Application Status beneficiary
Route::get('ben-application-status', 'ApplicationStatusController@index')->name('ben-application-status');
Route::post('list-app-status', 'ApplicationStatusController@searchByBenName')->name('list-app-status');
Route::post('view-application-status/{id}/{scheme_id}', 'ApplicationStatusController@viewStatus');

// New Beneficiary Application Status Date: 22-06-2021
Route::get('ben-app-status', 'BeneficiaryApplicationStatusController@index')->name('ben-app-status');
Route::post('result-app-status', 'BeneficiaryApplicationStatusController@searchResult')->name('result-app-status');
Route::get('app-status-savePdf', 'BeneficiaryApplicationStatusController@savePdf')->name('app-status-savePdf');
Route::post('personal-details', 'BeneficiaryApplicationStatusController@personalDetails')->name('personal-details');
Route::post('update-ben-details', 'BeneficiaryApplicationStatusController@updateBenDetails')->name('update-ben-details');
Route::post('ifms-payment', 'BeneficiaryApplicationStatusController@ifmsPayment')->name('ifms-payment');
Route::post('sbi-payment', 'BeneficiaryApplicationStatusController@sbiPayment')->name('sbi-payment');
Route::post('lot-master', 'BeneficiaryApplicationStatusController@lotMaster')->name('lot-master');
Route::post('duplicate-beneficiary', 'BeneficiaryApplicationStatusController@duplicateBeneficiary')->name('duplicate-beneficiary');
Route::post('sbi-transaction-lot', 'BeneficiaryApplicationStatusController@sbiTransaction')->name('sbi-transaction-lot');
Route::post('ifms-transaction-lot', 'BeneficiaryApplicationStatusController@ifmsTransaction')->name('ifms-transaction-lot');

// cumulative-beneficiary-details
Route::get('cumulative-beneficiary-details', 'CumulativeBeneficiaryDetailsController@index')->name('cumulative-beneficiary-details');
Route::post('result-cumulative-ben-details', 'CumulativeBeneficiaryDetailsController@store')->name('result-cumulative-ben-details');

Route::get('duplicate-and-stop-payment-report', 'ReportDuplicateStopPaymentBenController@index')->name('duplicate-and-stop-payment-report');

Route::post('lot_payment_xls_generate', 'ReportLotMasterExcelGenerateController@lotWiseGenerateExcelFunction')->name('lot_payment_xls_generate');

// Report repeat Lot Master
Route::get('report_repeat_lot_master_info', 'ReportRepeatLotMasterController@selectYearMonth')->name('report_repeat_lot_master_info');
Route::post('report_repeat_lot_master_result', 'ReportRepeatLotMasterController@index')->name('report_repeat_lot_master_result');

//Pause Payment (LPP)
Route::get('pause-ben-payment/{id}', 'UpdatebenDetailsController@pauseBenPayment');
Route::post('resume-ben-payment', 'UpdatebenDetailsController@resumeBenPayment');

// Retainer To Pensioner (LPP)
Route::get('retainer-to-pensioner', 'RetainerToPensionerController@index')->name('retainer-to-pensioner');
Route::post('retainerToPensionerList', 'RetainerToPensionerController@retainerToPensionerList')->name('retainerToPensionerList');
Route::post('retainter-to-pensioner-store', 'RetainerToPensionerController@store')->name('retainter-to-pensioner-store');
Route::get('retainer-to-pensioner-report', 'RetainerToPensionerController@generateReport')->name('retainer-to-pensioner-report');

// Purohit Monthly Report
Route::get('purohit-monthly-report', 'ReportPurohitMonthlyBeneficiaryController@index')->name('purohit-monthly-report');
Route::post('filter-purohit-monthly', 'ReportPurohitMonthlyBeneficiaryController@filterBlockUlb')->name('filter-purohit-monthly');
Route::get('generate-excel-purohit/{dist_code}/{block_ulb_code}', 'ReportPurohitMonthlyBeneficiaryController@generateExcelPurohit');
Route::get('generate-excel-purohit-hod/{dist_code}', 'ReportPurohitMonthlyBeneficiaryController@generateExcelPurohitHOD');

//Duare Sarkar Reports
Route::get('duare-sarkar-report', 'DuareSarkarReportController@index')->name('duare-sarkar-report');
Route::post('duare-sarkar-generate-report', 'DuareSarkarReportController@generateReport')->name('duare-sarkar-generate-report');
Route::get('download-datewise-report/{name}', 'DuareSarkarReportController@reportDatewise');


// Stop payment and duplicate beneficiary report districtwise (HOD Login)
Route::get('report-duplicate-stop-payment', 'ReportDuplicateStopPaymentBenController@index1')->name('report-duplicate-stop-payment');
Route::post('linelisting-duplicate-stop-report', 'ReportDuplicateStopPaymentBenController@linelistingReport')->name('linelisting-duplicate-stop-report');
Route::any('excel-report-duplicate-reject', 'ReportDuplicateStopPaymentBenController@excelDuplicateReject')->name('excel-report-duplicate-reject');
Route::any('excel-report-stop-payment', 'ReportDuplicateStopPaymentBenController@excelStopPayment')->name('excel-report-stop-payment');

// Consolidated Lot Status Report
Route::get('monthly_lot_status_report', 'ReportMonthwiseReportLotStatusController@index')->name('monthly_lot_status_report');
Route::post('monthly_lot_status_report', 'ReportMonthwiseReportLotStatusController@index')->name('monthly_lot_status_report');
//Route::post('get_lot_of_monthly_lot_status_report', 'ReportMonthwiseReportLotStatusController@getLotDetails');
Route::post('get_response_pending_lot_details', 'ReportMonthwiseReportLotStatusController@getReponsePendingLotDetails');
Route::post('get_repeat_pending_lot_details', 'ReportMonthwiseReportLotStatusController@getRepeatPendingLotDetails');

// Sending Mail to Users
Route::get('sending-mail-to-users', 'SendingMailToUserController@index')->name('sending-mail-to-users');
Route::post('get_user_email_address', 'SendingMailToUserController@getEmailAddress')->name('get_user_email_address');
Route::post('store-sending-mail-to-user', 'SendingMailToUserController@sendingMail')->name('store-sending-mail-to-user');
Route::post('get_email_using_mobile_no', 'SendingMailToUserController@getEmailUsingMobileNo')->name('get_email_using_mobile_no');

/*============Subhankar Code End===================*/

//Document Management
Route::resource('document-mgmt', 'DocumentTypeController');
Route::get('scheme-doc-map', 'DocumentTypeController@assigndocumenttoscheme');
Route::get('ajaxschemeChnageRequest/{id}', 'DocumentTypeController@ajaxschemeChnageRequest');
Route::post('documentsetupforScheme', 'DocumentTypeController@documentsetupforScheme');
Route::get('ajaxschemenameRequest/{id}', 'DocumentTypeController@ajaxschemenameRequest');


Route::get('ben-search', 'SearchController@index')->name('search');

Route::resource('line-dept-duty', 'LineDepartmentDutyController');

Route::get('district-drill-down', 'DistrictDrillDownReport@index');
Route::post('getdatas-district', 'DistrictDrillDownReport@getdata')->name('getdatas-district');
Route::get('district-drill-down-submiited/{district_code}/{scheme_id}', 'DistrictDrillDownReport@getlistsubmitted')->name('district-drill-down-submiited');
Route::get('district-drill-down-verified/{district_code}/{scheme_id}', 'DistrictDrillDownReport@getlistverified')->name('district-drill-down-verified');
Route::get('district-drill-down-approved/{district_code}/{scheme_id}', 'DistrictDrillDownReport@getlistapproved')->name('district-drill-down-approved');
Route::any('district-drill-down-district/{district_code}', 'DistrictDrillDownReport@getblocklist')->name('district-drill-down-district');

Route::get('block-drill-down-dist/{district_code}', 'BlockDrillDownReport@indexdist')->name('block-drill-down-dist');

Route::any('showsinglebenefeciary/{id}/{s_id}', 'DistrictDrillDownReport@showSingleEmployeeReport')->name('drilldown.showSingleEmployeeReport');

Route::get('ifms-status', 'PushToIfmsController@ifmsStatus');

Route::any('approved-list-read-only', 'PensionformController@approvedlistReadOnly');

// Route::get('approved_schemelist', function () {
//         return view('portal.scheme_approved');
// });

Route::get('approved_schemelist', function () {
    $arr = SchemecodeStatic::getpr1ListPurohit();
    $monthlySlug = $arr['monthly']['slug'];
    $housingSlug = $arr['housing']['slug'];
    return view('portal.scheme_approved', ['monthlySlug' => $monthlySlug, 'housingSlug' => $housingSlug]);
});


Route::get('repeat-lot-generation', 'RepeatLotController@index')->name('repeat-lot-generation');

Route::post('repeat-lot-generation.generatelot', 'RepeatLotController@store')->name('repeat-lot-generation.generatelot');


Route::get('lot-verification-selectYearMonth', 'LotVerificationController@selectYearMonth');
Route::post('lot-verification', 'LotVerificationController@index')->name('lot-verification');
Route::POST('lot-verification/showlist', 'PushToIfmsController@showlist')->name('lot-verification.showlist');
Route::get('lot-verification', 'LotVerificationController@index');

Route::POST('checkLotDuplicate', 'CheckLotDetailsDuplicateController@checkLotDuplicate')->name('checkLotDuplicate');
Route::POST('duplicateReject', 'CheckLotDetailsDuplicateController@duplicateReject')->name('duplicateReject');
// //Block drilldown
// Route::get('block-drill-down-payment', 'BlockDrillDownReport@payment');
// Route::post('getdatas_payment', 'BlockDrillDownReport@getpaymentdata' )->name('getdatas_payment');
// Route::get('block-drill-down-payment-dist/{district_code}', 'BlockDrillDownReport@indexdistpayment')->name('block-drill-down-payment-dist');


// //District drilldown
// Route::get('district-drill-down-payment', 'DistrictDrillDownReport@payment');
// Route::post('getpaymentdata-district', 'DistrictDrillDownReport@getpaymentdata' )->name('getdatas_payment_dist');
// Route::any('district-drill-down-payment-district/{district_code}','DistrictDrillDownReport@getblockpaymentlist')->name('district-drill-down-payment-district');


//Block drilldown
Route::get('block-drill-down-payment/{type}', 'BlockDrillDownReport@payment');
Route::post('excelDownloadFailedPaymentList', 'DistrictDrillDownReport@excelDownloadFailedPaymentList')->name('excelDownloadFailedPaymentList');
Route::post('getdatas_payment', 'BlockDrillDownReport@getpaymentdata')->name('getdatas_payment');
Route::get('block-drill-down-payment-dist/{district_code}/{type}', 'BlockDrillDownReport@indexdistpayment')->name('block-drill-down-payment-dist');


//District drilldown
Route::any('district-drill-down-payment/{type}', 'DistrictDrillDownReport@payment');
Route::post('getpaymentdata-district', 'DistrictDrillDownReport@getpaymentdata')->name('getdatas_payment_dist');
Route::any('district-drill-down-payment-district/{district_code}/{type}', 'DistrictDrillDownReport@getblockpaymentlist')->name('district-drill-down-payment-district');






//Route::get('scheme-selection-revert', 'RevertBackController@shemeSelection')->name('scheme-selection-revert');
Route::get('scheme-selection-revert', function () {
    return redirect("/")->with('success', 'This link is moved to another link please select from side manu list');
})->name('scheme-selection-revert');
Route::any('revert', 'RevertBackController@applicationdetails');

Route::any('editApplicantDetails/{id}', 'RevertBackController@editApplicantDetails')->name('revert.editApplicantDetails');
Route::post('revert-update', 'RevertBackController@update')->name('revert-update');

Route::get('internal-ifms-status', 'InternalLotCheckController@ifmsStatus');

Route::get('large-lot-generation', 'LargeLotGenerationController@index')->name('large-lot-generation');

Route::post('large-lot-generation.generatelot', 'LargeLotGenerationController@store')->name('large-lot-generation.generatelot');

Route::any('getloadcount', 'LotGenerationController@loadcount')->name('getloadcount');

//District drilldown consolidated report
Route::get('district-drill-down-consolidated', 'DistrictDrillDownReport@consol_report');
Route::post('getconsol_report-district', 'DistrictDrillDownReport@getconsol_reportData')->name('getdatas_consol_report_dist');
Route::any('district-drill-down-consol-district/{district_code}', 'DistrictDrillDownReport@getblockconsollist')->name('district-drill-down-consol-district');


//Block drilldown consolidated report
Route::get('block-drill-down-consolidated', 'BlockDrillDownReport@consol_report');
Route::post('getconsol_report-block', 'BlockDrillDownReport@getconsol_reportData')->name('getdatas_consol_report_block');
Route::get('block-drill-down-consol-dist/{district_code}', 'BlockDrillDownReport@indexdistconsol')->name('block-drill-down-consol-dist');

//SBI Consolidated Report
Route::get('district-drill-down-consolidated-sbi', 'DistrictDrillDownReport@consol_report_sbi');
Route::post('getconsol_report-district-sbi', 'DistrictDrillDownReport@getconsol_reportData_sbi')->name('getdatas_consol_report_dist_sbi');
Route::any('district-drill-down-consol-district-sbi/{district_code}', 'DistrictDrillDownReport@getblockconsollist_sbi')->name('district-drill-down-consol-district-sbi');
Route::get('block-drill-down-consolidated-sbi', 'BlockDrillDownReport@consol_report_sbi');
Route::post('getconsol_report-block-sbi', 'BlockDrillDownReport@getconsol_reportData_sbi')->name('getdatas_consol_report_block_sbi');
Route::get('block-drill-down-consol-dist-sbi/{district_code}', 'BlockDrillDownReport@indexdistconsol_sbi')->name('block-drill-down-consol-dist-sbi');

//Route::get('scheme-selection-revert-rbi', 'RBIRevertBackController@shemeSelection')->name('scheme-selection-revert-rbi');
Route::get('scheme-selection-revert-rbi', function () {
    return redirect("/")->with('success', 'This link is moved to another link please select from side manu list');
})->name('scheme-selection-revert-rbi');
Route::any('revert-rbi', 'RBIRevertBackController@applicationdetails');

Route::any('rbieditApplicantDetails/{id}', 'RBIRevertBackController@editApplicantDetails')->name('revert.rbieditApplicantDetails');
Route::post('revert-update-rbi', 'RBIRevertBackController@update')->name('revert-update-rbi');




//Parijayi
Route::get('parijayi/{app_type}', 'ParijayiController@index')->name('parijayi');
Route::post('getPrijiyiData', 'ParijayiController@getData');
Route::post('getBenDetailById', 'ParijayiController@getSingleBeneficiary');
Route::post('benBulkApprove', 'ParijayiController@bulkApprove');
Route::post('benReject', 'ParijayiController@rejectApplication');
Route::post('getStatusCode', 'ParijayiController@getStatusCode');

//lotGeneration
Route::get('parijayi_generate_lot', 'ParijayiController@lot_generation');
Route::post('parijayi_new_lot', 'ParijayiController@createNewLot');
Route::post('bulk_addtoLot', 'ParijayiController@bulkAddToLot');
Route::post('parijayi_process_lot', 'ParijayiController@processLot');

//Location Master
Route::post('loadLocalBody', 'ParijayiController@getLocalBody');

//Print Single Beneficiary
Route::post('printSingleBenf', 'ParijayiController@printSingleBeneficiary')->name('printSingleBenf');


//Import Bank Response
Route::get('bank_response', 'ParijayiController@importBankResponse');
Route::post('import_bank_response', 'ParijayiController@importBankResponseFile');
Route::post('bank_response_lot', 'ParijayiController@importBankResponseByLot');
Route::post('process_bank_response_lot', 'ParijayiController@processBankResponse');

//MIS
Route::get('parijayi_mis', 'ParijayiMISController@getStateReport');
Route::post('getPrijiyiMISData', 'ParijayiMISController@getMISData');

//DUplicate MIS
Route::get('parijayi_duplicate_mis', 'ParijayiMISController@indexDuplicateMIS');
Route::post('parijayi_duplicate_getdata', 'ParijayiMISController@getDuplicateRecord');

//DUplicate Accoutn No MIS
Route::get('parijayi_duplicate_accno_mis', 'ParijayiMISController@indexDuplicateAccNoMIS');
Route::post('parijayi_duplicate_accno_getdata', 'ParijayiMISController@getDuplicateAccNoRecord');


//Save Mandate Excel
Route::get('/SP/{slug}', [
    'as' => 'SP.show',
    'uses' => 'ImageController@spshow',
    'middleware' => 'auth',
]);



//Prachesta
Route::get('user-add-scheme/{scheme_id}/{user_id}', 'EmployeeUserDutyController@assignScheme');
Route::get('user-add-scheme-index', 'EmployeeUserDutyController@addNewSchemeIndex');

Route::resource('prachesta', 'PrachestaformController');

Route::get('pending_for_lot/{scheme}/{lotMonth}/{category}', 'LargeLotGenerationController@getPendingCount');


//new
Route::get('append-lot', 'PushToIfmsController@lotAppendIndex');
Route::post('store-append-lot', 'PushToIfmsController@viewAppendLotResult')->name('store-append-lot');
Route::post('append-lot-number', 'PushToIfmsController@appendLotNumber')->name('append-lot-number');

Route::get('report-lot-master-sbi/index', 'ReportLotMasterSbiController@index');
Route::post('lot-master-sbi-list', 'ReportLotMasterSbiController@lot_listing')->name('lot-master-sbi-list');

//Route::Post('push-to-sbi/export', 'PushToSBIController@prepareSignXML_test')->name('push-to-sbi.export');
Route::get('/receive_sbi_ack_status', 'PushToSBIController@receive_sbi_ack_status_test')->name('receive_sbi_ack_status');
Route::post('/sbi_payment_status', 'PushToSBIController@sbi_payment_status_test')->name('sbi_payment_status');


Route::get('push-to-sbi', 'PushToSBIController@index');
Route::POST('push-to-sbi/lot_listing', 'PushToSBIController@lot_listing')->name('push-sbi-lot-listing');
Route::POST('push-to-sbi', 'PushToSBIController@push_single_lot')->name('push-to-sbi-single-lot');
Route::POST('push-to-sbi/showlist', 'PushToSBIController@showlist')->name('push-to-sbi.showlist');
Route::Post('push-to-sbi/export', 'PushToSBIController@prepareSignXML')->name('push-to-sbi.export');

Route::get('large-lot-generation-sbi', 'LargeLotGenerationSbiController@index')->name('large-lot-generation-sbi');
Route::post('large-lot-generation-sbi.generatelot', 'LargeLotGenerationSbiController@store')->name('large-lot-generation-sbi.generatelot');

Route::post('stop-payment/{id}', 'UpdatebenDetailsController@stopPayment');


//Legacy Configuration

Route::resource('legacy/pensionform', 'PensionformLegacyController');
Route::any('legacy/application-edit', 'PensionformLegacyController@applicationeditview')->name('legacy.pensionform.application-edit-view');
Route::any('legacy/application-update/{id}', 'PensionformLegacyController@applicationupdate')->name('legacy.pensionform.application-update');

Route::any('legacy/application-list-read-only', 'PensionformLegacyController@applicationlistReadOnly');
Route::any('legacy/application-details-read_only/{id}', 'PensionformLegacyController@applicationdetailsReadOnly')->name('legacy.pensionform.application-details-read-only');

Route::get('legacy/{app_type}', 'LegacyProcessController@index')->name('legacy');
Route::post('legacy/getData', 'LegacyProcessController@getData');
Route::post('legacy/benBulkApprove', 'LegacyProcessController@bulkApprove');
Route::post('legacy/benReject', 'LegacyProcessController@rejectApplication');
Route::post('legacy/getStatusCode', 'LegacyProcessController@getStatusCode');
//Location Master
Route::post('legacy/loadLocalBody', 'LegacyProcessController@getLocalBody');
//Bank Details
Route::post('legacy/getBankDetails', 'LegacyProcessController@getBankDetails');

//MIS
Route::get('legacy_mis', 'LegacyProcessController@getStateReport');
Route::post('getLegacyMISData', 'LegacyProcessController@getMISData');

//Document Saving
Route::get('/images_legacy/{slug}', [
    'as' => 'images_legacy.show',
    'uses' => 'ImageController@show_legacy',
    'middleware' => 'auth',
]);



//Clear Cache facade value:
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear Config cache:
Route::get('/config-clear', function () {
    $exitCode = Artisan::call('config:clear');
    return '<h1>Clear Config cleared</h1>';
});

Route::resource('dept-user-duty', 'DepartmentMapDutyController');
Route::get('enabledisable-config-duty-dept', 'DepartmentMapDutyController@enabledisable')->name('enabledisable-config-duty-dept');
Route::get('enabledisable-config-duty-emp', 'EmployeeUserDutyController@enabledisable')->name('enabledisable-config-duty-emp');

//Route::get('bank-details-edit-sbi', 'BankDetailsEditSBIController@shemeSelection')->name('bank-details-edit-sbi');
Route::get('bank-details-edit-sbi', function () {
    return redirect("/")->with('success', 'This link is moved to another link please select from side manu list');
})->name('bank-details-edit-sbi');
Route::any('bank-edit-sbi', 'BankDetailsEditSBIController@applicationdetails');

Route::any('bank-edit-editApplicantDetails-sbi/{id}', 'BankDetailsEditSBIController@editApplicantDetails')->name('bank-edit.editApplicantDetails-sbi');
Route::post('bank-edit-update-sbi', 'BankDetailsEditSBIController@update')->name('revert-update-sbi');

Route::any('add-scheme-existing-user', 'AddSchemeToExistingUserController@index')->name('add-scheme-existing-user');
Route::post('add-scheme-existing-user-map', 'AddSchemeToExistingUserController@map')->name('add-scheme-existing-user-map');

Route::get('districtwise-statistics', 'DistrictwiseStatisticsController@index');
Route::post('linelisting-dist-block-ulb', 'DistrictwiseStatisticsController@lineListDistBlockUlb')->name('linelisting-dist-block-ulb');
Route::get('generateExcel/{str}', 'DistrictwiseStatisticsController@generateExcel');

//Single Verification
Route::get('verify/{scheme}', 'SingleStepVerification@index')->name('verify');
Route::post('getSingleStepVerifyData', 'SingleStepVerification@getData');
Route::post('getSingleStepBenDetailById', 'SingleStepVerification@getSingleBeneficiary');
Route::post('singleStepBenBulkApprove', 'SingleStepVerification@bulkApprove');
Route::post('singleStepBenReject', 'SingleStepVerification@rejectApplication');
Route::post('singleStepBenStatusCode', 'SingleStepVerification@getStatusCode');

Route::post('singleStepBenLocationDetails', 'SingleStepVerification@getLocalBody');
Route::post('singleStepBulkLocationChange', 'SingleStepVerification@bulkLocationChange');
Route::get('verifydistrict/{scheme}', 'SingleStepVerification@indexDistrict')->name('verifydistrict');
Route::post('getSingleStepVerifyDistrictData', 'SingleStepVerification@getDistrictData');

Route::get('selectlschemelocationchange', function () {
    return view('singlestep.locationchangemaster');
});

Route::get('selectscheme', function () {
    return view('singlestep.main');
});

Route::any('singlestep/application-edit', 'SingleStepVerification@applicationeditview')->name('singlestep.application-edit-view');
Route::any('singlestep/application-update/{id}', 'SingleStepVerification@applicationupdate')->name('singlestep.application-update');
Route::post('singlestep/editBeneficiary', 'SingleStepVerification@editBeneficiary');

Route::get('singlestep_report/{scheme}/{approved_rejected}', 'SingleStepVerification@report')->name('singlestep_report');
Route::post('getSingleStepVerifyProcessedData', 'SingleStepVerification@getProcessedData');

Route::get('selectschemeapproved', function () {
    return view('singlestep.approved');
});
Route::get('selectschemerejected', function () {
    return view('singlestep.rejected');
});



//LPP Single Verification
Route::get('lpp-verify/{scheme}', 'LPPSingleStepVerification@index')->name('lpp-verify');
Route::post('getLppSingleStepVerifyData', 'LPPSingleStepVerification@getData');
Route::post('getLppSingleStepBenDetailById', 'LPPSingleStepVerification@getSingleBeneficiary');
Route::post('lppSingleStepBenBulkApprove', 'LPPSingleStepVerification@bulkApprove');
Route::post('lppSingleStepBenReject', 'LPPSingleStepVerification@rejectApplication');
Route::post('lppSingleStepBenStatusCode', 'LPPSingleStepVerification@getStatusCode');

Route::get('lppscheme', function () {
    return view('lpp-singlestep.main');
});
Route::any('lpp-singlestep/application-edit', 'LPPSingleStepVerification@applicationeditview')->name('lpp-singlestep.application-edit-view');
Route::any('lpp-singlestep/application-update/{id}', 'LPPSingleStepVerification@applicationupdate')->name('lpp-singlestep.application-update');
Route::post('lpp-singlestep/loadLocalBody', 'LPPSingleStepVerification@getLocalBody');
Route::post('lpp-singlestep/editBeneficiary', 'LPPSingleStepVerification@editBeneficiary');

Route::get('lpp-singlestep_report/{scheme}/{approved_rejected}', 'LPPSingleStepVerification@report')->name('lpp-singlestep_report');
Route::post('getLppSingleStepVerifyProcessedData', 'LPPSingleStepVerification@getProcessedData');

Route::get('lppselectschemeapproved', function () {
    return view('lpp-singlestep.approved');
});
Route::get('lppselectschemerejected', function () {
    return view('lpp-singlestep.rejected');
});


//Dynamic Menu
Route::get('menu-management/home', 'MenuManagementController@menu_index');
Route::post('getMenuList', 'MenuManagementController@getMenuList');
Route::get('menu-management/loadMenuItemFormMaster', 'MenuManagementController@loadMenuItemFormMaster')->name('menu-management.loadMenuItemFormMaster');
Route::get('menu-management/getMenuUsingRole/{role}', 'MenuManagementController@getMenuUsingRole')->name('menu-management.getMenuUsingRole');
Route::post('menu-management/addRemoveMenuItemUserRole', 'MenuManagementController@addRemoveMenuItemUserRole')->name('menu-management.addRemoveMenuItemUserRole');
Route::post('menu-management/menuItemToggleActivate', 'MenuManagementController@menuItemToggleActivate')->name('menu-management.menuItemToggleActivate');
Route::post('menu-management/getMenuItemFromRole', 'MenuManagementController@getMenuItemFromRole')->name('menu-management.getMenuItemFromRole');
Route::post('menu-management/updateRoleBasedMenuRank', 'MenuManagementController@updateRoleBasedMenuRank')->name('menu-management.updateRoleBasedMenuRank');

Route::post('menu-management/destroy', 'MenuManagementController@destroy')->name('menu-management.destroy');
Route::get('menu-management/getdesignationListfromMenu/{menu_id}', 'MenuManagementController@getdesignationListfromMenu')->name('menu-management.getdesignationListfromMenu');
Route::get('menu-management/getdeMenuDetails/{id}', 'MenuManagementController@getdeMenuDetails')->name('menu-management.getdeMenuDetails');
Route::post('menu-management/store', 'MenuManagementController@store')->name('menu-management.store');

//Scheme Onboard
Route::get('onboardscheme', 'SchemeOnboardingController@index');
Route::get('getschemefromtype', 'SchemeOnboardingController@getschemefromtype');
Route::post('schemeOnboardToggleActivate', 'SchemeOnboardingController@schemeOnboardToggleActivate');
Route::post('workflowListView', 'SchemeOnboardingController@workflowListView');

Route::post('getAddUpdateLevelInfo', 'SchemeOnboardingController@getAddUpdateLevelInfo');
Route::post('addUpdateMap', 'SchemeOnboardingController@addUpdateMap');
Route::post('addNewSchemeType', 'SchemeOnboardingController@addNewSchemeType');
Route::post('addworkflow', 'SchemeOnboardingController@addWorkflow');

//Scheme
Route::post('getSchemeDetail', 'SchemeOnboardingController@getSchemeDetail');
Route::post('addUpdateScheme', 'SchemeOnboardingController@addUpdateScheme');
Route::post('getAllItemList', 'SchemeOnboardingController@getAllItemList');
Route::post('toggleItemStatus', 'SchemeOnboardingController@toggleItemStatus');
Route::post('deleteItem', 'SchemeOnboardingController@deleteItem');
//New Update 
// Rouet::post('getScheme', 'SchemeOnboardingController@getScheme');


//WCD SCHEME
//Temporary
Route::resource('oap-wcd', 'OAPWCDformController');
Route::any('oap-wcd/application-update/{id}', 'OAPWCDformController@applicationupdate')->name('oap-wcd.application-update');
//Temporary
Route::resource('wp-wcd', 'WPWCDformController');
Route::any('wp-wcd/application-update/{id}', 'WPWCDformController@applicationupdate')->name('wp-wcd.application-update');

Route::resource('manabik-wcd', 'ManabikWCDformController');
Route::any('manabik-wcd/application-update/{id}', 'ManabikWCDformController@applicationupdate')->name('manabik-wcd.application-update');

//Document Saving
Route::get('/images_wcd/{slug}', [
    'as' => 'images_wcd.show',
    'uses' => 'ImageController@show_wcd',
    'middleware' => 'auth',
]);

//Document CRUD
Route::get('document-mgmt-list', 'DocumentTypeController@index')->name('getDocumentList');
Route::post('documentToggleActivate', 'DocumentTypeController@documentToggleActivate')->name('documentToggleActivate');
Route::post('deleteDocument', 'DocumentTypeController@deleteDocument')->name('deleteDocument');
Route::post('documentSave', 'DocumentTypeController@documentSaveUpdate')->name('documentSave');
Route::post('editDocument', 'DocumentTypeController@editDocument')->name('editDocument');
Route::post('documentUpdate', 'DocumentTypeController@documentSaveUpdate')->name('documentUpdate');


//Division Master
Route::get('getDivisionList', 'DivisionController@index')->name('getDivisionList');
Route::post('divisionSave', 'DivisionController@divisionSaveUpdate')->name('divisionSave');
Route::post('divisionUpdate', 'DivisionController@divisionSaveUpdate')->name('divisionUpdate');
Route::post('editDivision', 'DivisionController@editDivision')->name('editDivision');
Route::post('deleteDivision', 'DivisionController@deleteDivision')->name('deleteDivision');

//Department Master
Route::get('getDepartmentList', 'DepartmentController@index')->name('getDepartmentList');
Route::post('departmentSave', 'DepartmentController@departmentSaveUpdate')->name('departmentSave');
Route::post('editDepartment', 'DepartmentController@editDepartment')->name('editDepartment');

Route::post('departmentUpdate', 'DepartmentController@departmentSaveUpdate')->name('departmentUpdate');
Route::post('deleteDepartment', 'DepartmentController@deleteDepartment')->name('deleteDepartment');

//Country Master
Route::get('getCountrytList', 'CountryController@index')->name('getCountrytList');
Route::post('countrySave', 'CountryController@countrySaveUpdate')->name('countrySave');
Route::post('editCountry', 'CountryController@editCountry')->name('editCountry');

Route::post('countryUpdate', 'CountryController@countrySaveUpdate')->name('countryUpdate');
Route::post('deleteCountry', 'CountryController@deleteCountry')->name('deleteCountry');



//State Master
Route::get('getStatetList', 'StateController@index')->name('getStatetList');
Route::post('stateSave', 'StateController@stateSaveUpdate')->name('stateSave');
Route::post('editState', 'StateController@editState')->name('editState');

Route::post('stateUpdate', 'StateController@stateSaveUpdate')->name('stateUpdate');
Route::post('deleteState', 'StateController@deleteState')->name('deleteState');

//District drilldown wcd consolidated report
Route::get('district-drill-down-consolidatedwcd', 'DistrictDrillDownReport@wcdconsol_report');
Route::post('getwcdconsol_report-district', 'DistrictDrillDownReport@getwcdconsol_reportData')->name('getwcddatas_consol_report_dist');
Route::any('district-drill-down-consolwcd-district/{district_code}', 'DistrictDrillDownReport@getwcdblockconsollist')->name('district-drill-down-consolwcd-district');

//Block drilldown consolidated report
Route::get('block-drill-down-consolidatedwcd', 'BlockDrillDownReport@wcdconsol_report');
Route::post('getwcdconsol_report-block', 'BlockDrillDownReport@getwcdconsol_reportData')->name('getwcddatas_consol_report_block');
Route::get('block-drill-down-consolwcd-dist/{district_code}', 'BlockDrillDownReport@indexdistwcdconsol')->name('block-drill-down-consolwcd-dist');

//Generic Lot Generation
Route::get('generic-lot', 'GenericLotController@lotGenericIndex')->name('generic-lot');
Route::post('store-generic-lot', 'GenericLotController@viewAppendLotResult')->name('store-generic-lot');
Route::post('generic-lot-number', 'GenericLotController@appendLotNumber')->name('generic-lot-number');
Route::post('getPendingBeneficiaryCount', 'GenericLotController@getPendingBeneficiaryCount')->name('getPendingBeneficiaryCount');
Route::post('getPaymentMode', 'GenericLotController@getPaymentMode')->name('getPaymentMode');
Route::post('getSchemeWiseLot', 'GenericLotController@getSchemeWiseLot')->name('getSchemeWiseLot');
Route::post('getAmountMonthWise', 'GenericLotController@getAmountMonthWise')->name('getAmountMonthWise');

Route::post('getMonthData', 'GenericLotController@getMonthData')->name('getMonthData');
Route::post('getPaymentPending', 'DashboardController@getPaymentPending')->name('getPaymentPending');
Route::post('getRepeatPending', 'DashboardController@getRepeatPending')->name('getRepeatPending');
Route::post('import_sbi_pending', 'DashboardController@importSBIReportPending')->name('import_sbi_pending');
Route::post('import_rbi_report_pending', 'DashboardController@importRbiReportPending')->name('import_rbi_report_pending');
Route::post('getStandardLotPending', 'DashboardController@getStandardLotPending')->name('getStandardLotPending');
Route::post('markDuplicateAccountNumber', 'DashboardReportController@markDuplicateAccountNumber')->name('markDuplicateAccountNumber');
Route::post('markDeDuplicateSchemeWise', 'DashboardReportController@markDeDuplicateSchemeWise')->name('markDeDuplicateSchemeWise');
Route::post('getLppApprovedCount', 'DashboardReportController@getLppApprovedCount')->name('getLppApprovedCount');
Route::post('getBeneficiaryPaymentPending', 'DashboardReportController@getBeneficiaryPaymentPending')->name('getBeneficiaryPaymentPending');
Route::post('getInstantReportData', 'DashboardReportController@getInstantReportData')->name('getInstantReportData');
Route::post('getInstantPaymentReport', 'DashboardReportController@getInstantPaymentReport')->name('getInstantPaymentReport');

Route::get('exchange-scheme-selection', 'ExchangeDataController@shemeSelection')->name('exchange-scheme-selection');
Route::any('exchangeflow', 'ExchangeDataController@applicationdetails');

//Purohits
Route::resource('purohit', 'PurohitICADformController');
Route::any('purohit/application-update/{id}', 'PurohitICADformController@applicationupdate')->name('purohit.application-update');

Route::post('forward-purohits', 'WorkflowController@verifyPurohitdata')->name('forward-purohits');
Route::post('forward-approve-purohits', 'WorkflowController@approvePurohitdata')->name('forward-approve-purohits');

Route::get('purohits_report/{scheme}/{approved_rejected}', 'PurohitICADformController@report')->name('purohits_report');
Route::post('getPurohitsVerifyProcessedData', 'PurohitICADformController@getProcessedData');

//UserMobile&EmailUpdate
Route::get('userMobileEmailUpdate', 'UserMobileEmailUpdateController@index');
Route::get('userMobileEmailUpdate/userManagementSearch', 'UserMobileEmailUpdateController@userManagementSearch');
Route::post('userMobileEmailUpdate/getUserInfo', 'UserMobileEmailUpdateController@getUserInfo');
Route::post('userMobileEmailUpdate/updateMobileOrEmail', 'UserMobileEmailUpdateController@updateMobileOrEmail');

//CommonReport
Route::any('application-list-common', 'PensionformReportController@applicationStatusList');
Route::post('benReject-common', 'PensionformReportController@rejectApplication');
Route::post('benRevert-common', 'PensionformReportController@revertApplication');
Route::get('scheme-selection-common', 'PensionformReportController@schemeSelection');

//WCD Legacy Data Entry
Route::get('wcd_oap_manabik', 'WcdOapManabikController@index')->name('wcd_oap_manabik');
Route::post('wcd_oap_manabik/store', 'WcdOapManabikController@store')->name('wcd_oap_manabik/store');

Route::get('wcd_oap_manabik/bankAccountedit', 'WcdOapManabikController@bankAccountedit')->name('wcd_oap_manabik/bankAccountedit');
Route::post('wcd_oap_manabik/bankAccounteditApplicantSearch', 'WcdOapManabikController@bankAccounteditApplicantSearch')->name('wcd_oap_manabik/bankAccounteditApplicantSearch');
Route::post('wcd_oap_manabik/bankAccounteditApplicantEdit', 'WcdOapManabikController@bankAccounteditApplicantEdit')->name('wcd_oap_manabik/bankAccounteditApplicantEdit');

//WCD Brief Data Entry Report
Route::get('wcd20210202Report', 'WcdOapManabikController@wcdconsol_report20210202');
Route::get('wcd20210202ReportPost', 'WcdOapManabikController@wcdconsol_report20210202post');
Route::get('wcd20210202Report/{consolidate}', 'WcdOapManabikController@wcdconsol_report20210202');
Route::post('briefdataApproval', 'SingleStepVerification@briefdataApproval');

Route::get('scheme-selection-brief/{type}', 'BriefReportController@schemeSelection');
Route::any('application-list-common-brief', 'BriefReportController@applicationStatusList');
Route::post('benReject-common-brief', 'BriefReportController@rejectApplication');
Route::post('benRevert-common-brief', 'BriefReportController@revertApplication');

Route::get('selectSchemeDuplicate', function () {
    return view('Duplicate.selectscheme');
});

Route::post('duplicate-excel', 'DuplicateController@export_excel')->name('duplicate-excel');

Route::get('system-reject-duplicate', 'PensionformReportController@reject_duplicates')->middleware('approver');
Route::post('system-rejected-generate_excel', 'PensionformReportController@generate_excel')->middleware('approver');



Route::get('selectSchemeDuplicateReject', function () {
    return view('Duplicate.selectschemeDuplicateReject');
});
Route::get('duplicateReject', function () {
    return view('Duplicate.duplicateReject');
});

Route::post('duplicate_reject', 'DuplicateController@duplicate_reject')->name('duplicate_reject');

Route::get('wcdoapwpreport', 'BriefReportController@wcdoapwpreport');
Route::post('wcdoapwpreportPost', 'BriefReportController@wcdoapwpreportPost');

//Route::get('farmer/{app_type}', 'SingleStepVerificationFarmer@index')->name('verifyFarmer');

Route::post('getSingleStepVerifyDataFarmer', 'SingleStepVerificationFarmer@getData');

Route::post('singleStepBenBulkApproveFarmer', 'SingleStepVerificationFarmer@bulkApprove');

Route::post('singleStepBenRejectFarmer', 'SingleStepVerificationFarmer@rejectApplication');
Route::post('getApplicant_Farmer', 'SingleStepVerificationFarmer@getApplicantRow')->name('getApplicant_Farmer');
Route::post('farmerApproval', 'SingleStepVerificationFarmer@farmerApproval');

//Pradyut
Route::get('maintenance', function () {
    return view('maintenance');
})->name('maintenance');

Route::get('clubbed_lot_report', 'ClubbedLotReportController@selectYearMonth')->name('clubbed_lot_report');
Route::post('clubbed_lot_report_result', 'ClubbedLotReportController@result')->name('clubbed_lot_report_result');
Route::get('clubbed_consolidated_lot_report', 'ClubbedLotReportController@index')->name('clubbed_consolidated_lot_report');
Route::post('clubbed_consolidated_lot_report', 'ClubbedLotReportController@index')->name('clubbed_consolidated_lot_report');
Route::post('get_response_pending_clubbed_lot_details', 'ReportMonthwiseReportLotStatusController@getReponsePendingLotDetails');
Route::post('get_repeat_pending_clubbed_lot_details', 'ReportMonthwiseReportLotStatusController@getRepeatPendingLotDetails');
/*
Clubbed Lot SBI Transaction
*/
Route::get('clubbed-report-lot-master-sbi/index', 'PushToSBIClubbedController@clubbedIndex');
Route::post('clubbed-lot-master-sbi-list', 'PushToSBIClubbedController@clubbedLotListing')->name('clubbed-lot-master-sbi-list');
Route::POST('clubbed-push-to-sbi', 'PushToSBIClubbedController@clubbed_push_single_lot')->name('clubbed-push-to-sbi-single-lot');
Route::POST('clubbed-push-to-sbi/showlist', 'PushToSBIClubbedController@showlist')->name('clubbed-push-to-sbi.showlist');
Route::Post('clubbed-push-to-sbi/export', 'PushToSBIClubbedController@clubbed_prepareSignXML')->name('clubbed-push-to-sbi.export');
Route::get('/clubbed_sbi_payment_status', 'PushToSBIClubbedController@clubbed_sbi_payment_status_test')->name('clubbed_sbi_payment_status');

/*
End Clubbed Lot SBI Transaction
*/

/*
Lot Validation
*/

Route::get('pre-lot-consolidated-check', 'LotCheckController@preconsolidated');
Route::post('pre-lot-consolidated-check', 'LotCheckController@preconsolidated');

Route::get('post-lot-consolidated-check', 'LotCheckController@postconsolidated');
Route::post('post-lot-consolidated-check', 'LotCheckController@postconsolidated');

Route::get('lot-file-movement-check-sbi', 'LotCheckController@lotFileMovementCheckSbi');
Route::post('lot-file-movement-check-sbi', 'LotCheckController@lotFileMovementCheckSbi');

Route::get('lotCheckExcelDownloadPre', 'LotCheckController@lotCheckExcelDownloadPre')->name('lotCheckExcelDownloadPre');
Route::get('lotCheckExcelDownloadPost', 'LotCheckController@lotCheckExcelDownloadPost')->name('lotCheckExcelDownloadPost');


Route::get('findDuplicatePensionId', 'DuplicatePensionIdController@findList');
Route::post('findDuplicatePensionId', 'DuplicatePensionIdController@findList');
Route::get('updateDuplicatePensionId', 'DuplicatePensionIdController@updteList')->name('updateDuplicatePensionId');

/*
End Lot Validation
*/
// Stop Payment Report MIS
Route::get('de_activated_ben', 'UpdatebenDetailsController@stopPaymentReport')->name('de_activated_ben');

Route::any('district-drill-down-consolidated-report', 'DrillDownReport@district_consol_report');
Route::any('block-drill-down-consolidated-report', 'DrillDownReport@block_consol_report');
Route::get('monthly_payment_status_schemewise', 'MonthlyPaymentStatus@index');
Route::get('monthly_payment_status_schemewise_post', 'MonthlyPaymentStatus@getData');
Route::get('get_scheme_based_on_payment_mode', 'MonthlyPaymentStatus@getschemeonPaymentMode');

// OTP
Route::get('getOtp', 'GetOtp@index');
Route::post('getOtp_post', 'GetOtp@getData');

// LakkhiBhandar
Route::any('lb-wcd-search', 'LakkhiBhandarWCDformController@search');
Route::get('lkwcd-mark-family-head', 'LakkhiBhandarWCDformController@markhed');

Route::resource('lb-wcd', 'LakkhiBhandarWCDformController');
Route::get('lb-wcd-family-edit', 'LakkhiBhandarWCDformController@applicationeditview')->name('lb-wcd-family-edit');
Route::any('application-update-family/{id}', 'LakkhiBhandarWCDformController@applicationupdatefamily')->name('application-update-family');

Route::any('lb-wcd/application-update/{id}', 'LakkhiBhandarWCDformController@applicationupdate')->name('lb-wcd.application-update');
Route::get('lkwcd-download-pdf', 'LakkhiBhandarWCD@index');
Route::post('lkwcd-download-pdf-post', 'LakkhiBhandarWCD@download');
// For Save to Storage
Route::any('lkwcd-download-pdf-admin', 'LakkhiBhandarWCD@indexadmin');
Route::get('lk_download_pdf_static', 'LakkhiBhandarWCD@downloadstaticpdf');

Route::get('lot_payment_xls_generate_new', 'ReportLotMasterExcelGenerateController@lotWiseGenerateExcelFunction')->name('lot_payment_xls_generate_new');

Route::any('upload-user-manual', 'UserManualController@upload');
Route::get('get-user-manual', 'UserManualController@get')->name('get-user-manual');
Route::get('download_user_manual', 'UserManualController@downloadstaticpdf');

Route::get('applicationstatreport', 'BlockDrillDownReport@applicationstatreport');
Route::post('applicationstatreportpost', 'BlockDrillDownReport@applicationstatreportpost');

Route::post('getApproverBenARPending', 'DashboardController@getApproverBenARPending')->name('getApproverBenARPending');
Route::post('getBankEditPending', 'DashboardController@getBankEditPending')->name('getBankEditPending');
Route::post('getApproverDashboardData', 'DashboardController@getApproverDashboardData')->name('getApproverDashboardData');

Route::any('editOapList', 'OAPWCDformController@editList');
Route::get('editOap/{id}', 'OAPWCDformController@editUnlock');
Route::post('editOapPost', 'OAPWCDformController@editOapPost')->name('editOapPost');
Route::get('getAgeOap', 'OAPWCDformController@ajaxgetage');
Route::any('workflowwcdEdit', 'WorkflowControllerWcdEdit@applicationdetails');
Route::any('benDetailsWcdEdit', 'WorkflowControllerWcdEdit@showApplicantDetails')->name('benDetailsWcdEdit');
Route::post('forwardWcdEdit', 'WorkflowControllerWcdEdit@verifydata')->name('forwardWcdEdit');
Route::post('forward-approve-wcd-edit', 'WorkflowControllerWcdEdit@approvedata')->name('forward-approve-wcd-edit');
Route::post('bulkApprovewcdEdit', 'WorkflowControllerWcdEdit@MassEmployeeApproval')->name('bulkApprovewcdEdit');

Route::any('editWpList', 'WPWCDformController@editList');
Route::get('editWp/{id}', 'WPWCDformController@editUnlock');
Route::post('editWpPost', 'WPWCDformController@editWpPost')->name('editWpPost');
Route::get('getAgeWp', 'OAPWCDformController@ajaxgetage');

//Oap Farmer Gobinda Halder
Route::resource('oap-farmer', 'OAPFarmerformController');
Route::any('oap-farmer/application-update/{id}', 'OAPFarmerformController@applicationupdate')->name('oap-farmer.application-update');

Route::get('/images_farmer/{slug}', [
    'as' => 'images_farmer.show',
    'uses' => 'ImageController@show_farmer',
    'middleware' => 'auth',
]);

// Duare Sarkar
//Route::resource('shortEntry', 'shortEntryformController');
Route::any('shortEntryList', 'shortEntryformController@entryList');
Route::get('shortEntryView', 'shortEntryformController@view');
Route::post('shortEntryUpdate/{id}', 'shortEntryformController@applicationupdate')->name('shortEntryUpdate');
Route::get('getAgeShortEntry', 'shortEntryformController@ajaxgetage');
Route::post('DuareFormReject', 'shortEntryformController@rejectApplication');

/*****************Duare Sarkar********** */
Route::get('getDistrictApplicationReport', 'DuareSarkarApplicationController@getDistrictApplicationReport');
Route::post('datatableBlockApplicationReport', 'DuareSarkarApplicationController@datatableBlockApplicationReport')->name('datatableBlockApplicationReport');

Route::post('datatableDistrictApplicationReport', 'DuareSarkarApplicationController@datatableDistrictApplicationReport')->name('datatableDistrictApplicationReport');
Route::post('getGpMuniData', 'DuareSarkarApplicationController@getGpMuniData')->name('getGpMuniData');

Route::get('dsReport', 'DuareSarkarApplicationController@dsReport');
Route::post('dsReportPost', 'DuareSarkarApplicationController@getData')->name('dsReportPost');
// #########  Identity Report Gobinda Halder #########
Route::get('identity-report', 'IdentityDrillDownReportController@identity_report');
Route::post('get-identity-report', 'IdentityDrillDownReportController@get_identity_report')->name('get-identity-report');

Route::any('block-subdiv-identity-report/{district_code}', 'IdentityDrillDownReportController@block_subdiv_identity_report')->name('block-subdiv-identity-report');

Route::post('get-block-subdiv-identity-report', 'IdentityDrillDownReportController@get_block_subdiv_identity_report')->name('get-block-subdiv-identity-report');
Route::get('brieftofullReport', 'BrieftofullReportController@index');
Route::post('brieftofullReportPost', 'BrieftofullReportController@getData')->name('brieftofullReportPost');

// #########  Duplicate Bank account and IFSC #########
Route::get('dedupBankCron', 'DuplicateControllerBank@dedupBankCron')->name('dedupBankCron');
Route::get('dedupBankSelectScheme', 'DuplicateControllerBank@dedupBankSelectScheme');
Route::get('dedupBankListView', 'DuplicateControllerBank@dedupBankListView');
Route::post('ajaxGetEncloser', 'DuplicateControllerBank@ajaxGetEncloser');
Route::get('dedupBankView', 'DuplicateControllerBank@dedupBankView');
Route::post('dupBankReject', 'DuplicateControllerBank@dupBankReject')->name('dupBankReject');
Route::post('DupBankAccounttExcelDistrict', 'DuplicateControllerBank@generate_excel_list');
Route::get('DupBankAccounttExcelState', 'DuplicateControllerBank@generate_excel_list_state')->name('DupBankAccounttExcelState');
Route::post('DupBankAccountDownload', 'DuplicateControllerBank@generate_excel_list_state_download')->name('DupBankAccountDownload');
Route::get('dedupBankUpdate', 'DuplicateControllerBank@dedupBankUpdate')->name('dedupBankUpdate');
Route::post('dedupBankUpdatePost', 'DuplicateControllerBank@dedupBankUpdatePost')->name('dedupBankUpdatePost');
Route::post('dedupBankSamePost', 'DuplicateControllerBank@dedupBankSamePost')->name('dedupBankSamePost');
//Farmer Short Entry Update
Route::any('editOapFarmerList', 'OAPFarmerformController@editOapFarmerList');
Route::get('editOapFarmer/{id}', 'OAPFarmerformController@editOapFarmer');
Route::post('editOapFarmerPost', 'OAPFarmerformController@editOapFarmerPost')->name('editOapFarmerPost');
Route::get('getAgeOapFarmer', 'OAPFarmerformController@ajaxgetAgeOapFarmer');
Route::any('oapFarmerApprovedEdit', 'OAPFarmerformController@oapFarmerApprovedEdit');
Route::any('benDetailsOapFarmerEdit', 'OAPFarmerformController@showApplicantDetails')->name('benDetailsOapFarmerEdit');
Route::post('forwardOapFarmerEdit', 'OAPFarmerformController@verifydata')->name('forwardOapFarmerEdit');
Route::post('forward-approve-oap-farmer-edit', 'OAPFarmerformController@approvedata')->name('forward-approve-oap-farmer-edit');
Route::post('bulkApproveOapFarmerEdit', 'OAPFarmerformController@MassEmployeeApproval')->name('bulkApproveOapFarmerEdit');
//Wp  Entry Report from 18-12-2021
Route::get('wcdReport', 'WcdReportController@index');
Route::post('wcdReportPost', 'WcdReportController@getData')->name('wcdReportPost');

/* 
New Update/De-activate Beneficiary At approver end Date: 28-12-2021
*/
Route::get('update-deactivate-beneficiary', 'UpdateBankStopPaymentController@index')->name('update-deactivate-beneficiary');
Route::post('search-using-id-name', 'UpdateBankStopPaymentController@searchByBenName')->name('search-using-id-name');
Route::post('getModalDataUpdateStop', 'UpdateBankStopPaymentController@getModalDataUpdateStop')->name('getModalDataUpdateStop');
Route::post('updateBenBankDetails', 'UpdateBankStopPaymentController@updateBenBankDetails')->name('updateBenBankDetails');
Route::post('stopPaymentBenDetails', 'UpdateBankStopPaymentController@stopPaymentBenDetails')->name('stopPaymentBenDetails');
Route::post('lppPausePaymentDetails', 'UpdateBankStopPaymentController@lppPausePaymentDetails')->name('lppPausePaymentDetails');
Route::post('lppResumePaymentDetails', 'UpdateBankStopPaymentController@lppResumePaymentDetails')->name('lppResumePaymentDetails');
Route::post('updateMobileBenDetails', 'UpdateBankStopPaymentController@updateMobileBenDetails')->name('updateMobileBenDetails');
Route::post('ajaxViewPassbook', 'UpdateBankStopPaymentController@ajaxViewPassbook')->name('ajaxViewPassbook');

// Stop / De-activated payment approval
Route::get('approve-stop-payment', 'DeactivatedWorkFlowController@indexApprove')->name('approve-stop-payment');
Route::post('linelistingApproveStoppedBen', 'DeactivatedWorkFlowController@linelistingApproveStoppedBen')->name('linelistingApproveStoppedBen');
Route::post('modalViewApproveStopPayment', 'DeactivatedWorkFlowController@modalViewApproveStopPayment')->name('modalViewApproveStopPayment');
Route::post('approveStopPaymentData', 'DeactivatedWorkFlowController@approveStopPaymentData')->name('approveStopPaymentData');

//##################  Gobinda aadhar Update ###################################

////////////////////////////////////////////////////////////////

//####################  Opaerator ############################

//*****************  Below routs for Opearator menu *****************************
/*
Route::get('schemelist-aadhar-update', 'AadharUpdateController@schemeList');
Route::any('application-list-aadhar-update', 'AadharUpdateController@applicationlistAadharUpdate');
Route::any('application-aadhar-update-view', 'AadharUpdateController@applicationeditviewAadharUpdate')->name('application-aadhar-update-view');
Route::any('aadhar-update-post/{id}', 'AadharUpdateController@aadharUpdatePost')->name('aadhar-update-post');

// #################### verifier ####################

//*****************  Below routs for verifier menu *****************************
Route::get('scheme-selection-aadhar-update', 'AadharUpdateController@shemeSelectionAadharUpdateVerifier');
Route::any('aadhar-update-list-verifier', 'AadharUpdateController@aadharUpdateListVerifer');
Route::any('showAadharApplicantDetails', 'AadharUpdateController@showAadharApplicantDetails')->name('showAadharApplicantDetails');
Route::post('aadhar-forward-verify', 'AadharUpdateController@aadharVerifyData')->name('aadhar-forward-verify');


// #################### Approver ####################

//*****************  Below routs for Approver menu *****************************

Route::get('scheme-selection-aadhar-update-approver', 'AadharUpdateController@shemeSelectionAadharUpdateApprover');
Route::any('aadhar-update-list-approver', 'AadharUpdateController@aadharUpdateListApprover');
Route::any('showAadharApplicantDetailsApprover', 'AadharUpdateController@showAadharApplicantDetailsApprover')->name('showAadharApplicantDetailsApprover');
Route::post('aadhar-forward-approve', 'AadharUpdateController@aadharApproveData')->name('aadhar-forward-approve');
*/
/*
New Failed Update SBI/RBI/IFMS (Generic) Date: 15/01/2022
*/
Route::get('failed-bank-edit/{payment_mode}', 'FailedBankDetailsEditController@index');
Route::post('getFailedBankListPaymentModeWise', 'FailedBankDetailsEditController@getFailedBankListPaymentModeWise')->name('getFailedBankListPaymentModeWise');
Route::post('getModalDataFailedBankEdit', 'FailedBankDetailsEditController@getModalDataFailedBankEdit')->name('getModalDataFailedBankEdit');
Route::post('updateFailedBankDetails', 'FailedBankDetailsEditController@updateFailedBankDetails')->name('updateFailedBankDetails');
Route::post('ajaxViewPassbookfailed', 'FailedBankDetailsEditController@ajaxViewPassbook')->name('ajaxViewPassbookfailed');
/*
Manabik Edit Module Date: 18/01/2022
*/
Route::any('editManabikList', 'ManabikWCDformController@editList');
Route::get('editManabik/{id}', 'ManabikWCDformController@editUnlock');
Route::post('editManabikPost', 'ManabikWCDformController@editManabikPost')->name('editManabikPost');
Route::get('getAgeManabik', 'ManabikWCDformController@ajaxgetage');
/*
New Application List Date: 18/01/2022
*/

Route::get('schemelistforUpdate', 'PensionformController@schemelistforUpdate');
Route::any('application-list-read-only-edit', 'PensionformController@editList');

/*
Track Application Status Date: 29/01/2022
*/
Route::get('track-application-status', 'TrackApplicationStatusController@index')->name('track-application-status');
Route::post('getTrackApplicantDetails', 'TrackApplicationStatusController@getTrackApplicantDetails')->name('getTrackApplicantDetails');
Route::post('getPaymentStatusDetails', 'TrackApplicationStatusController@getPaymentStatusDetails')->name('getPaymentStatusDetails');
Route::post('getStatusUTRAndErrorFun', 'TrackApplicationStatusController@getStatusUTRAndErrorFun')->name('getStatusUTRAndErrorFun');

// Count List For Approver & HOD //
Route::get('scheme-selection-count-list', 'AadharUpdateMisReportController@countListIndex');
Route::any('aadhar-update-count-list-approver', 'AadharUpdateMisReportController@aadharUpdateCountListApprover')->name('aadhar-update-count-list-approver');

// #################### HOD ####################
//*****************  Below routs for HOD menu *****************************

Route::get('scheme-selection-count-list-hod', 'AadharUpdateController@countListIndexforHod');
Route::any('aadhar-update-count-list-hod', 'AadharUpdateController@aadharUpdateCountListHod')->name('aadhar-update-count-list-approver');

Route::post('applicationListExcel', 'PensionReportControllerExcel@generate_excel')->name('applicationListExcel');
Route::post('applicationListExcelPhasewise', 'PensionReportControllerExcel@generate_excel_phasewise')->name('applicationListExcelPhasewise');

// Duare Sarkar Status Check
Route::get('/ds_status_check/{scheme_id}', 'DuareSarkarStatusCheckController@index');
Route::post('ds_status_check_sendotp', 'DuareSarkarStatusCheckController@ds_status_check_sendotp')->name('ds_status_check_sendotp');
Route::get('ds_status_check_otp', 'DuareSarkarStatusCheckController@ds_status_check_otp')->name('ds_status_check_otp');
Route::post('ds_status_check_otp_Post', 'DuareSarkarStatusCheckController@ds_status_check_otp_Post')->name('ds_status_check_otp_Post');
Route::get('ds_status_check_report', 'DuareSarkarStatusCheckController@ds_status_check_report')->name('ds_status_check_report');
Route::get('ds_status_check_resendotp', 'DuareSarkarStatusCheckController@ds_status_check_resendotp')->name('ds_status_check_resendotp');
// End of Duare Sarkar Status Check
// Duare Sarkar Report
Route::get('/dsReportCommon', 'DuareSarkarApplicationController@dsReportCommon')->name('dsReportCommon');
Route::post('/dsReportCommonPost', 'DuareSarkarApplicationController@dsReportCommonPost')->name('dsReportCommonPost');
// End of Duare Sarkar Report

/*
Duplicate A/c No. Report
*/
Route::get('de-duplicate-bank-report', 'DuplicateControllerBankReport@index')->name('de-duplicate-bank-report');
Route::post('deDuplicateBankReportGetData', 'DuplicateControllerBankReport@deDuplicateBankReportGetData')->name('deDuplicateBankReportGetData');


// Aadhaar Find
Route::any('/findAadhaar', 'AadhaarController@find')->name('findAadhaar');
// End of Aadhaar Find
// Bank Account Deduplication Report
Route::get('dedupBankMis', 'DuplicateControllerBank@dedupBankMis');
Route::post('dedupBankMisPost', 'DuplicateControllerBank@getData')->name('dedupBankMisPost');
// End of Bank Account Deduplication Report

// -------  LPP Short Entry Form (Date:- 25-05-2022) -------------
Route::get('lpp-short-entry', function () {
    return view('lpp-shortEntryForm.main');
});
Route::get('lppShortEntryForm', 'LPPShortEntryFormController@index')->name('lppShortEntryForm');
Route::post('store-lppshortEntry', 'LPPShortEntryFormController@store')->name('store-lppshortEntry');

// End  LPP Short Entry Form 
//Special Quota
Route::get('mainformwtQuota', 'WorkflowController@formEntryOptionwtQuota');

/* Age Cohort and Stop Report  Date - 14-07-2022*/
// Route::get('stopPaymentOAPReport', 'CommonReportController@wcdAgeDiffStopPaymentIndex')->name('stopPaymentOAPReport');
// Route::post('wcdAgeDiffStopPaymentGetData', 'CommonReportController@wcdAgeDiffStopPaymentGetData')->name('wcdAgeDiffStopPaymentGetData');
// Route::post('wcdStopPaymentReport', 'CommonReportController@wcdStopPaymentReport')->name('wcdStopPaymentReport');

/* User Edit Trail by Admin*/
Route::any('userManagementAdmin', 'AdminController@useredit');


// ============ Bangla Sahayak Kendra ============

// ============ (1) User Data Entry Section ===========
Route::get('bsk-entry', function () {
    return view('BSKPensionForm.landingPageBSK');
});
Route::post('bsk-data-entry', 'BSKLoginController@index');
Route::get('mainEntryForm', 'BSKPensionFormController@index')->name('mainEntryForm');
Route::post('bsk-manabik-wcd', 'BSKPensionFormController@store');
Route::get('bsk-entry-done', function () {
    return view('BSKPensionForm/entryDone');
})->name('bsk-entry-done');
Route::post('legacy/getBankDetailsBsk', 'BSKPensionFormController@getBankDetails');

// ============ (2) User Duty Management ==============
Route::get('bsk-emp-user-duty', 'BSKEmployeeUserDutyManagement@index');
Route::post('bskEmpUserGetData', 'BSKEmployeeUserDutyManagement@bskEmpUserGetData');
Route::post('bskAddUserEmp', 'BSKEmployeeUserDutyManagement@bskAddUserEmp')->name('bskAddUserEmp');
Route::post('bsk-enabledisable-config-duty-emp', 'BSKEmployeeUserDutyManagement@enabledDisabled')->name('bsk-enabledisable-config-duty-emp');

// BSK Main Table Entry through Jai Bangla Operator
Route::get('schemelistforUpdateBsk', 'BSKPensionFormMainEntryController@schemelistforUpdateBsk');
Route::any('application-list-read-only-edit-bsk', 'BSKPensionFormMainEntryController@editListBsk');
Route::any('application-details-read_only-bsk/{id}', 'BSKPensionFormMainEntryController@applicationdetailsReadOnlyBsk')->name('pensionform.application-details-read-only-bsk');
Route::any('application-edit-bsk', 'BSKPensionFormMainEntryController@applicationeditviewBsk')->name('pensionform.application-edit-view-bsk');
Route::any('application-update-bsk/{id}', 'BSKPensionFormMainEntryController@applicationupdateBsk')->name('pensionform.application-update-bsk');
Route::post('application-reject-bsk', 'BSKPensionFormMainEntryController@applicationRejectBsk')->name('pensionform.application-reject-bsk');

// For API through Jai Bangla
Route::post('getDatewiseBskEntry', 'BSKFetchApplicationDataController@getDatewiseBskEntry')->name('getDatewiseBskEntry');
Route::post('getSingleEntryStatus', 'BSKFetchApplicationDataController@getSingleEntryStatus')->name('getSingleEntryStatus');

// ============ Age Cohort Report==============
Route::get('ageCohortReport', 'AgeCohortReportController@index')->name('ageCohortReport');
Route::get('ageCohortReportPost', 'AgeCohortReportController@getData');

/* Age Cohort Beneficiary List Date - 26-07-2022 */
Route::get('age-cohort-list', 'AgeCohortBeneficiaryListController@index')->name('age-cohort-list');
Route::post('getAgeCohortBenList', 'AgeCohortBeneficiaryListController@getAgeCohortBenList')->name('getAgeCohortBenList');
Route::post('getAgeCohortGroupList', 'AgeCohortBeneficiaryListController@getAgeCohortGroupList')->name('getAgeCohortGroupList');
Route::post('generateAgeCohortGroupListExcel', 'AgeCohortBeneficiaryListController@generateAgeCohortGroupListExcel')->name('generateAgeCohortGroupListExcel');


// ============ Death/Ineligible beneficiaries : Stop Payment Report Date : 04-08-2022 =================
Route::get('death-ineligible-report', 'DeathIneligibleReportController@index')->name('death-ineligible-report');
Route::post('deathInaligibleReportGetData', 'DeathIneligibleReportController@deathInaligibleReportGetData')->name('deathInaligibleReportGetData');

// ============ Age cohort wise beneficiaries Date : 04-08-2022 ===============
Route::get('different-age-cohort-report', 'DifferentAgeCohortReportController@index')->name('different-age-cohort-report');
Route::post('differentAgeCohortReportGetData', 'DifferentAgeCohortReportController@getData')->name('differentAgeCohortReportGetData');

// =========== Download Payment Status Date : 18-07-2022 Only For DDO and HOD ============
Route::get('get-payee-list', 'DownloadPaymentStatusController@getPayeeListIndex')->name('get-payee-list');
Route::post('getPayeeListGetData', 'DownloadPaymentStatusController@getPayeeListGetData')->name('getPayeeListGetData');
Route::post('getPayeeListGetDataExcel', 'DownloadPaymentStatusController@getPayeeListGetDataExcel')->name('getPayeeListGetDataExcel');

// Download rejected beneficiary data Date: 25-08-2022
Route::get('all-rejected-beneficiary-list', 'RejectedReportController@index')->name('all-rejected-beneficiary-list');
Route::post('getAllRejectedDataList', 'RejectedReportController@getAllRejectedDataList')->name('getAllRejectedDataList');
Route::post('getAllRejectedDataListExcelData', 'RejectedReportController@getAllRejectedDataListExcelData')->name('getAllRejectedDataListExcelData');

// Frequently Asked Questions
Route::get('faq', 'FAQController@index')->name('faq');

// Policy Links
Route::get('copyright-policy', 'PolicyController@copyright')->name('copyright-policy');
Route::get('privacy-policy', 'PolicyController@privacy')->name('privacy-policy');
Route::get('hyperlink-policy', 'PolicyController@hyperlink')->name('hyperlink-policy');
Route::get('terms-policy', 'PolicyController@terms_condition')->name('terms-policy');

// ============ Location Chnage==============
Route::any('location_change', 'LocationChnageController@applicationStatusList');
Route::any('location_change_post', 'LocationChnageController@update')->name('location_change_post');

//Duty Management
Route::get('userDutymanagement/Search', 'userDutymanagementController@Search');
Route::get('userDutymanagement', 'userDutymanagementController@index')->name('userDutymanagement');
Route::post('userDutymanagement/toggleActivate', 'userDutymanagementController@toggleActivate')->name('userDutymanagement/toggleActivate');
Route::get('adduser', 'userDutymanagementController@adduser')->name('adduser');
Route::post('adduserpost', 'userDutymanagementController@adduserpost')->name('adduserpost');
Route::post('getUserInfo', 'userDutymanagementController@getUserInfo');
Route::post('userDutymanagement/Update', 'userDutymanagementController@Update');
Route::post('userDutymanagement/toggleDuty', 'userDutymanagementController@toggleDuty')->name('userDutymanagement/toggleDuty');
Route::post('userDutymanagement/mapNewScheme', 'userDutymanagementController@mapNewScheme')->name('userDutymanagement/mapNewScheme');

//OAP  Entry Report after 09-12-2022
Route::get('wcdOAPReport091222', 'WcdReportController@oapindex091222');
Route::post('wcdOAPReportPost091222', 'WcdReportController@oappost091222')->name('wcdOAPReportPost091222');

// Date - 23-12-2022
// Duplicate Aadhar Mobile List
Route::get('dup-aadhar-mobile', 'DuplicateAadharMobileController@index')->name('dup-aadhar-mobile');
Route::post('linelisting-duplicate-aadhar-mobile-report', 'DuplicateAadharMobileController@linelistingReport')->name('linelisting-duplicate-aadhar-mobile-report');

//Duplicate Aadhar & Mobile Ben List
Route::get('dup-aadhar-mobile-ben', 'AadharMobileDuplicateBenListController@index')->name('dup-aadhar-mobile-ben');
Route::post('dup-aadhar-mobile-ben-list', 'AadharMobileDuplicateBenListController@benList')->name('dup-aadhar-mobile-ben-list');
Route::post('getDuplicateAadharMobile', 'AadharMobileDuplicateBenListController@exportExcel')->name('getDuplicateAadharMobile');

// Verified & Approved list of Duplicate Aadhar Mobile MIS Report //
Route::get('duplicateAadharMobileMISReport', 'DuplicateAadharMobileMisReportController@index')->name('duplicateAadharMobileMISReport');
Route::post('duplicateAadharMobileReport', 'DuplicateAadharMobileMisReportController@MisReport');

/* 
De-duplicate aadhar card and mobile number Date - 23-12-2022
*/
Route::get('de-duplicate-aadhar-mobile', 'DuplicateAadharUpdateController@index')->name('de-duplicate-aadhar-mobile');
Route::post('getDuplicateAadharListView', 'DuplicateAadharUpdateController@getDuplicateAadharListView')->name('getDuplicateAadharListView');
Route::post('getDuplicateAadharBenModalView', 'DuplicateAadharUpdateController@getDuplicateAadharBenModalView')->name('getDuplicateAadharBenModalView');
Route::post('updateDeDuplicateBenAadharDetails', 'DuplicateAadharUpdateController@updateDeDuplicateBenAadharDetails')->name('updateDeDuplicateBenAadharDetails');
Route::post('getDuplicateMobileBenModalView', 'DuplicateAadharUpdateController@getDuplicateMobileBenModalView')->name('getDuplicateMobileBenModalView');
Route::post('updateDeDuplicateBenMobileNoDetails', 'DuplicateAadharUpdateController@updateDeDuplicateBenMobileNoDetails')->name('updateDeDuplicateBenMobileNoDetails');
Route::post('getNoMobileBenModalView', 'DuplicateAadharUpdateController@getNoMobileBenModalView')->name('getNoMobileBenModalView');
Route::post('updateNoBenMobileDetails', 'DuplicateAadharUpdateController@updateNoBenMobileDetails')->name('updateNoBenMobileDetails');

// Verify Duplicate Aadhar & Mobile No. Date - 06-01-2023
Route::get('verify-de-duplicate-aadhar-mobile', 'AadharMobileDeDuplicateWorkFlowController@index')->name('verify-de-duplicate-aadhar-mobile');
Route::post('verifyDeDuplicateAadharMobileGetData', 'AadharMobileDeDuplicateWorkFlowController@verifyDeDuplicateAadharMobileGetData')->name('verifyDeDuplicateAadharMobileGetData');
Route::post('getVerifyDuplicateBenModalView', 'AadharMobileDeDuplicateWorkFlowController@getVerifyDuplicateBenModalView')->name('getVerifyDuplicateBenModalView');
Route::post('updateVerifiedDuplicateBen', 'AadharMobileDeDuplicateWorkFlowController@updateVerifiedDuplicateBen')->name('updateVerifiedDuplicateBen');
Route::post('viewVerifyDeDupAadharCard', 'AadharMobileDeDuplicateWorkFlowController@viewVerifyDeDupAadharCard')->name('viewVerifyDeDupAadharCard');

// Approve Duplicate Aadhar & Mobile No. Date - 06-01-2023
Route::get('approve-de-duplicate-aadhar-mobile', 'AadharMobileDeDuplicateWorkFlowController@indexApprove')->name('approve-de-duplicate-aadhar-mobile');
Route::post('approveDeDuplicateGetData', 'AadharMobileDeDuplicateWorkFlowController@approveDeDuplicateGetData')->name('approveDeDuplicateGetData');
Route::post('approveSingleAadharMobileBenData', 'AadharMobileDeDuplicateWorkFlowController@approveSingleAadharMobileBenData')->name('approveSingleAadharMobileBenData');
Route::post('updateApprovedDeDupBenData', 'AadharMobileDeDuplicateWorkFlowController@updateApprovedDeDupBenData')->name('updateApprovedDeDupBenData');
// NSAP Marked

Route::any('showapplicantnsap/{id}/{scheme_id}', 'WorkflowNsapController@showApplicantDetails')->name('showapplicantnsap');
Route::post('forward-nsap', 'WorkflowNsapController@verifydata')->name('forward-nsap');
Route::get('scheme-selection-nsap-marked', 'WorkflowNsapController@shemeSelectionnsapmarked')->name('scheme-selection-marked');
Route::any('nsap-marked-list', 'WorkflowNsapController@nsap_marked_list')->name('nsap-marked-list');
;
Route::get('nsapMis', 'WorkflowNsapController@nsapMis');
Route::post('nsapMisPost', 'WorkflowNsapController@nsapMisPost')->name('nsapMisPost');
Route::post('forward-nsap-bulk', 'WorkflowNsapController@bulkapprove')->name('forward-nsap-bulk');

// Calender Datewise payment Report
Route::get('calender-date-payment-report', 'PaymentReportController@calenderPaymentIndex')->name('calender-date-payment-report');
Route::post('calenderPaymentIndexGetData', 'PaymentReportController@calenderPaymentIndexGetData')->name('calenderPaymentIndexGetData');
Route::post('calenderPaymentGetDataLotwise', 'PaymentReportController@calenderPaymentGetDataLotwise')->name('calenderPaymentGetDataLotwise');

// NSAP Marked Beneficiary List
Route::get('nsap-marked', 'NSAPMarkedController@index')->name('nsap-marked');
Route::post('nsapMarkedBenData', 'NSAPMarkedController@nsapBenDetails')->name('nsapMarkedBenData');
Route::post('getNSAPMarkedData', 'NSAPMarkedController@exportExcel')->name('getNSAPMarkedData');

// Lot wise beneficiary districtwise count
Route::get('lot-wise-ben-count', 'lotWiseBenCountController@index')->name('lot-wise-ben-count');
Route::post('getLot', 'lotWiseBenCountController@getLot')->name('getLot');
Route::post('lotWiseBeneficiaryCount', 'lotWiseBenCountController@lotWiseBeneficiaryCount')->name('lotWiseBeneficiaryCount');

// Visual Dashboard
Route::get('jb-chart', 'ChartController@index')->name('jb-chart');
Route::any('jb-chart-dist-aadhar-capture', 'ChartController@distaadharcapture')->name('jb-chart-dist-aadhar-capture');

//Aadhar Check mis with PDS

Route::any('wbpdsmis', 'wBPdsController@wbpdsmis');
Route::get('wbpdsmisPost', 'wBPdsController@wbpdsmispost')->name('wbpdsmisPost');

// BSK Drilldown Report
Route::get('bsk-drilldown-report', 'BSKdrilldownController@index')->name('bsk-drilldown-report');
Route::post('drillDownReport', 'BSKdrilldownController@drillDownReport');
// WBPDS Drilldown Report
Route::any('wbpdsaadharScheme', 'wBPdsController@selectscheme');
Route::get('drilldownwbpdsdistrictwise', 'wBPdsController@drilldistrictwise')->name('drilldownwbpdsdistrictwise');
Route::get('drilldownwbpdsbloksubwise', 'wBPdsController@drillblksubwise')->name('drilldownwbpdsbloksubwise');
Route::get('wbpdsapplicantreport', 'wBPdsController@wbpdsapplicantreport')->name('wbpdsapplicantreport');
Route::any('wbpdsaadharSchemeOp', 'wBPdsChangeController@selectschemeOp');
Route::any('pdsnamemismatchlist', 'wBPdsChangeController@namemismatchdlist')->name('pdsnamemismatchlist');
Route::get('Viewpdsnamemismatch', 'wBPdsChangeController@ViewMismatchName')->name('Viewpdsnamemismatch');
Route::post('ViewpdsnamemismatchPost', 'wBPdsChangeController@ViewpdsnamemismatchPost')->name('ViewpdsnamemismatchPost');
Route::post('BulkApprovePds', 'wBPdsChangeController@bulkApprove')->name('BulkApprovePds');
Route::get('aadharNameValidMIS', 'wBPdsChangeController@aadharNameValidMIS');
Route::post('aadharNameValidMISPost', 'wBPdsChangeController@getData')->name('aadharNameValidMISPost');

// LokkhiBhandar 60 years
Route::get('selectsehmelb60', 'WorkflowLb60Controller@shemeSelection');
Route::get('workflow-lb60', 'WorkflowLb60Controller@ListView')->name('workflow-lb60');
Route::get('View60lbapplication', 'WorkflowLb60Controller@View60lbapplication')->name('View60lbapplication');
Route::post('lbapplicationVerify', 'WorkflowLb60Controller@lbapplicationVerify')->name('lbapplicationVerify');
Route::post('lbapplicationbulkApprove', 'WorkflowLb60Controller@lbapplicationbulkApprove')->name('lbapplicationbulkApprove');
Route::get('downaloadEncloser', 'WorkflowLb60Controller@viewEncloser');
Route::post('jb_ajax_encloser_entry', 'WorkflowLb60Controller@encloserEntry');
Route::get('lb60misreport', 'WorkflowLb60Controller@lb60misreport');
Route::post('lb60misreportPost', 'WorkflowLb60Controller@lb60misreportpost')->name('lb60misreportPost');
Route::get('fetchlbdocument', 'LbFetchController@fetchlbdocument')->name('fetchlbdocument');
Route::get('fetchlbdocumenttestapi', 'LbFetchController@fetchlbdocumenttestapi')->name('fetchlbdocumenttestapi');
Route::get('fetchlbdocumenttestview', 'LbFetchController@fetchlbdocumenttestview')->name('fetchlbdocumenttestview');
Route::post('applicationListLb60Excel', 'WorkflowLb60Controller@applicationListExcel')->name('applicationListLb60Excel');
Route::get('lbdbtest', 'LbFetchController@lbdbtest')->name('lbdbtest');

/*
Beneficiary Name Validation Failed from bank
*/

Route::any('benvalidationselectScheme', 'BenValidationFailedController@selectscheme');
Route::any('benaccnamefaliledlist', 'BenValidationFailedController@benaccnamefaliledlist')->name('benaccnamefaliledlist');
Route::get('ViewFailedbenAccName', 'BenValidationFailedController@ViewFailedbenAccName')->name('ViewFailedbenAccName');
Route::post('benaccnamefaliledlistPost', 'BenValidationFailedController@benaccnamefaliledlistPost')->name('benaccnamefaliledlistPost');
Route::post('benaccnamefaliledBulkApprove', 'BenValidationFailedController@bulkApprove')->name('benaccnamefaliledBulkApprove');
Route::get('bankNameValidMIS', 'BenValidationFailedController@bankNameValidMIS');
Route::post('bankNameValidMISPost', 'BenValidationFailedController@getData')->name('bankNameValidMISPost');
/*
Mail Check
*/
Route::get('checkmail', 'CheckLoginOtpMailSendController@checkLoginOtpMail');
Route::get('temp_work_ca', 'TempController@index');

/*
Duare Sarkar Report Date:31-03-2023 ** Debit Talukdar **
*/
Route::get('dsreportphaseselect', 'DuareSarkarApplicationphaseController@dsReportphaseCommon')->name('dsreportphaseselect');
Route::post('dsreportphase', 'DuareSarkarApplicationphaseController@dsReport')->name('dsreportphase');
Route::post('dsreportphasePost', 'DuareSarkarApplicationphaseController@dsgetData')->name('dsreportphasePost');

// Janmo mirtyu potal 
Route::get('jnmp-data', 'JnmpController@index')->name('jnmp-data');
Route::post('jnmpMarkedData', 'JnmpController@jnmpMarkedData')->name('jnmpMarkedData');
Route::post('modalViewData', 'JnmpController@modalViewData')->name('modalViewData');
Route::post('activeBeneficiary', 'JnmpController@activeBeneficiary')->name('activeBeneficiary');
Route::post('generateExcel', 'JnmpController@generateExcel')->name('generateExcel');
//Payment Issue
Route::any('stop-list-selectscheme', 'StopBeneficiaryController@selectscheme');
Route::any('stop-list', 'StopBeneficiaryController@listReport');
Route::post('stop-list-excel', 'StopBeneficiaryController@generate_excel');
Route::any('stop-list-mis', 'StopBeneficiaryController@mishod');
Route::any('stop-list-hod', 'StopBeneficiaryController@selectschemehod');

/********** Added By ANJAN 27/04/2023 ***********/

Route::any('LB-import-ben-list', 'WorkflowLb60Controller@importBenList');
Route::get('workflow-lb60-Ben-List', 'WorkflowLb60Controller@BenListView')->name('workflow-lb60-Ben-List');
Route::post('benApplicationListLb60Excel', 'WorkflowLb60Controller@benApplicationListExcel')->name('benApplicationListLb60Excel');
Route::get('BenView60lbapplication', 'WorkflowLb60Controller@BenView60lbapplication')->name('BenView60lbapplication');
/********** Life Certificate ***********/
Route::any('lifeCertificte', 'LifeCertificateController@editList')->name('lifeCertificte');
Route::get('editLifeCertificate', 'LifeCertificateController@editUnlock');
Route::post('editLifeCertificatePost', 'LifeCertificateController@editLifeCertificatePost')->name('editLifeCertificatePost');
Route::post('SingleApproveLifeCertificate', 'LifeCertificateController@SingleApproveLifeCertificate')->name('SingleApproveLifeCertificate');
Route::post('bulkApproveLifeCertificate', 'LifeCertificateController@bulkApproveLifeCertificate')->name('bulkApproveLifeCertificate');


/*
For Lakshmir Bhandar Document migration
*/
Route::get('fetch-data_file_migrate', 'fetchMigrateController@index');

// Department Special Cases
Route::get('scheme-selection-dept-special', 'WorkflowDeptSpecialController@shemeSelection')->name('scheme-selection-dept-special');
Route::any('dept-special-marked-list', 'WorkflowDeptSpecialController@marked_list')->name('dept-special-marked-list');
;
Route::any('showapplicantdeptspecial/{id}/{scheme_id}', 'WorkflowDeptSpecialController@showApplicantDetails')->name('showapplicantdeptspecial');
Route::post('forward-dept-special', 'WorkflowDeptSpecialController@verifydata')->name('forward-dept-special');

// View Beneficiary details API 
Route::get('view-application-track-status', 'ViewTrackApplicationStatusController@index');

// Pending Lot Response In SBI
Route::get('payment-pending-status-report', 'PaymentPendingReportController@index')->name('payment-pending-status-report');
Route::post('payment-pending-data', 'PaymentPendingReportController@getDeta')->name('payment-pending-data');
Route::post('payment-view-data', 'PaymentPendingReportController@fetchData')->name('payment-view-data');
// Pending Lot Response In SBI for CRON JOB
Route::get('cron_mail_pending_response_sbi', 'PaymentPendingReportController@mailPendingResponseSBI');

Route::get('uatexportXml', 'UATPushToIfmsController@uatexportXml')->name('uatexportXml');

Route::get('testDocImageServer', 'TempController@testDocImageServer');

Route::get('scheme-select-image', 'ImageFetchDBController@SchemeSelect');
Route::get('fetch-image-db', 'ImageFetchDBControllerSchemewise@view');
Route::any('store-image-db', 'ImageFetchDBController@store');
Route::post('total-image-db', 'ImageFetchDBController@totalImage');
Route::get('view-migrated-image', 'ImageFetchDBController@viewImage');

// Route::any('store-image-db-johar','ImageFetchDBControllerSchemewise@storeJohar');
Route::any('store-image-db-blkulb', 'ImageFetchDBControllerSchemewise@storeblkulb');
Route::any('store-image-db-cron', 'ImageFetchDBControllerSchemewise@storeCronDocTransfer');
Route::any('store-image-db-inactive', 'ImageFetchDBControllerSchemewise@storeinactive');
Route::any('store-image-db-stop', 'ImageFetchDBControllerSchemewise@stopPaymentFilesMove');

// Re-activate Beneficiary - By Surojit
Route::get('inactive-special', 'InactiveActiveController@index');
Route::post('inactiveBeneficiaryDetails', 'InactiveActiveController@inactive')->name('inactiveBeneficiaryDetails');
Route::post('inactiveModalView', 'InactiveActiveController@modalView')->name('inactiveModalView');
Route::post('activatedBeneficiary', 'InactiveActiveController@activeBen')->name('activatedBeneficiary');

//Migration Routes
Route::get('jbDownload', 'CommonReportController@viewEncloser')->name('jbDownload');

Route::resource('manabik-wcd-mg', 'ManabikWCDformNewController');
Route::any('manabik-wcd-mg/application-update/{id}', 'ManabikWCDformNewController@applicationupdate')->name('manabik-wcd-mg.application-update');
Route::post('editManabikMgPost', 'ManabikWCDformNewController@editManabikPost')->name('editManabikMgPost');

// Name Validation correction minor mismatch report by debjit
Route::get('scheme-selection-minormismatch-list', 'MisReportMinormismatchController@schemeSelection')->name('scheme-selection-minormismatch-list');
Route::any('lb-misReport-Minormismatch', 'MisReportMinormismatchController@applicationStatusList')->name('lb-misReport-Minormismatch');
Route::post('minormismatchapplicationListExcel', 'MisReportMinormismatchController@generate_excel');

//Purohit View
Route::any('showApplicantDetailsPurohit/{id}', 'PurohitICADformController@showApplicantDetails')->name('showApplicantDetailsPurohit');
//Workflow Common View
Route::any('showApplicantDetailsCommon/{id}/{scheme_id}', 'WorkflowController@showApplicantDetailsCommon')->name('showApplicantDetailsCommon');

////////////////////////////Aadhar not Avilable//////////////////////////////////////////////////////
Route::get('schemelist-noaadhar', 'NoAadharChangeController@shemeSelection');
Route::any('noaadharlist', 'NoAadharChangeController@list')->name('noaadharlist');
Route::get('Viewnoaadhar', 'NoAadharChangeController@Viewnoaadhar')->name('Viewnoaadhar');
Route::post('noaadharPost', 'NoAadharChangeController@noaadharPost')->name('noaadharPost');
Route::post('BulkApprovenoaadhar', 'NoAadharChangeController@bulkApprove')->name('BulkApprovenoaadhar');
Route::get('noaadharPdfDownload', 'NoAadharChangeController@pdf')->name('noaadharPdfDownload');
Route::any('noaadharMisReport', 'NoAadharChangeController@misReport')->name('noaadharMisReport');
Route::any('noaadharMisReportPost', 'NoAadharChangeController@misReportPost')->name('noaadharMisReportPost');
Route::post('noaadharapplicationListNoaadharExcel', 'NoAadharChangeController@generate_excel')->name('noaadharapplicationListNoaadharExcel');
/////////////////////////////End Aadhar not Avilable//////////////////////////////////////////////////////

// Lot type Beneficiary List
Route::get('lot-type-wise-beneficiary', 'LotTypeWiseBeneficiaryController@index')->name('lot-type-wise-beneficiary');
Route::post('lotTypeWiseBeneficiary', 'LotTypeWiseBeneficiaryController@lotTypeWiseBeneficiary')->name('lotTypeWiseBeneficiary');
Route::post('lotWiseBenExcel', 'LotTypeWiseBeneficiaryController@lotWiseBenExcel')->name('lotWiseBenExcel');
Route::post('schemeWiseLotType', "LotTypeWiseBeneficiaryController@schemeWiseLotType")->name('schemeWiseLotType');


// Napix Aadhaar validation API
// Route::any('Aadhaarvalidate', 'NapixAadharValidateCeontroller@check')->name('Aadhaarvalidate');
// Check Duplicate
Route::get('dup-check', 'AadharBankDupCountComtroller@index')->name('dup-check');
Route::post('checkDuplicate', 'AadharBankDupCountComtroller@checkDuplicate')->name('checkDuplicate');

////////////////////////////Sarasori Mukhyamantri//////////////////////////////////////////////////////
Route::get('scheme_selection-sm', 'WorkflowControllerSm@shemeSelection');
Route::any('mark-sm', 'WorkflowControllerSm@list')->name('mark-sm');
Route::get('ViewSm', 'WorkflowControllerSm@ViewSm')->name('ViewSm');
Route::post('MarkSmPost', 'WorkflowControllerSm@markpost')->name('MarkSmPost');
Route::post('SmPostReject', 'WorkflowControllerSm@SmReject')->name('SmPostReject');
Route::post('SmPostRevert', 'WorkflowControllerSm@SmRevert')->name('SmPostRevert');
/////////////////////////////End Sarasori Mukhyamantri//////////////////////////////////////////////////////

//////////////////////////Operator Rejection////////////////////////////////////////////////////////////
Route::any('application-reject', 'PensionformController@applicationreject')->name('application-reject');

//////////////////////////Operator No Aadhar Accept////////////////////////////////////////////////////////////
Route::get('scheme-updatenoaadhar', 'updatenoaadharController@schemeSelection');
Route::any('application-noaadhar', 'updatenoaadharController@applicationStatusList');
Route::post('benAccept-noaadhar', 'updatenoaadharController@acceptnoaadharApplication');


Route::post('BulkAccept', 'updatenoaadharController@bulkAccept')->name('BulkAccept');
//////////////////////////Operator No Aadhar Accept////////////////////////////////////////////////////////////


////////////////////////////Bank Failure Cases//////////////////////////////////////////////////////
Route::get('scheme_selection_longpen', 'BanfailurelongpenController@shemeSelection');
Route::any('longpenbankfailedlist', 'BanfailurelongpenController@list')->name('longpenbankfailedlist');
Route::get('Viewlongpenbankfailed', 'BanfailurelongpenController@View')->name('Viewlongpenbankfailed');
Route::post('BulkRejectlongpenbankfailed', 'BanfailurelongpenController@bulkApprove')->name('BulkRejectlongpenbankfailed');
Route::post('applicationListNoaadharExcel', 'BanfailurelongpenController@generate_excel')->name('applicationListNoaadharExcel');
/////////////////////////////End Bank Failure Cases//////////////////////////////////////////////////////

////////////////////////////Document Upload//////////////////////////////////////////////////////
Route::get('scheme_selection_doc_upload', 'JBDocUploadController@shemeSelection');
Route::any('jbdocuploadlist', 'JBDocUploadController@ListView')->name('jbdocuploadlist');
Route::get('Viewjbdocupload', 'JBDocUploadController@View')->name('Viewjbdocupload');
/////////////////////////////End of Document Upload//////////////////////////////////////////////////////

///////////////////////////Inc Department Edit//////////////////////////////////////////////////////
Route::get('scheme_selection_brief', 'ICADController@shemeSelection');
Route::any('icadpendingbrieflist', 'ICADController@pendinglist')->name('icadpendingbrieflist');
Route::get('ViewlicadBriefPending', 'ICADController@editUnlock')->name('ViewlicadBriefPending');
Route::post('editICADPost', 'ICADController@editicadPost')->name('editICADPost');

/////////////////////////////End of Document Upload//////////////////////////////////////////////////////
/////////////////////////////Payment Report/////////////////////////////////////////////////////////////
Route::get('payment-report', 'PaymentReportgetController@index');
Route::post('get-payment-data-sbi', 'PaymentReportgetController@getdataSBI')->name('get-payment-data-sbi');
Route::post('get-payment-data-ifms', 'PaymentReportgetController@getdataIFMS')->name('get-payment-data-ifms');
/////////////////////////////End of Payment Report/////////////////////////////////////////////////////////////

/////////////////////////////SM Report/////////////////////////////////////////////////////////////////////
Route::any('sm-cmoMisReport', 'CmoGrivanceReportController@misReport')->name('sm-cmoMisReport');
Route::any('sm-cmoMisReportPost', 'CmoGrivanceReportController@misReportPostCmo')->name('sm-cmoMisReportPost');

Route::get('sm-cmoMisReportlist', 'CmoGrivanceReportController@index')->name('sm-cmoMisReportlist');
Route::post('sm-cmoMisReportlistPost', 'CmoGrivanceReportController@benListsm')->name('sm-cmoMisReportlistPost');
Route::post('sm-cmoMisReportlistExcel', 'CmoGrivanceReportController@exportExcelcmo')->name('sm-cmoMisReportlistExcel');
Route::post('sm-cmo-revert', 'CmoGrivanceReportController@revertApplication')->name('sm-cmo-revert');
Route::post('sm-cmo-revert-bulk', 'CmoGrivanceReportController@bulkRevert')->name('sm-cmo-revert-bulk');
Route::post('sm-cmo-unmark', 'CmoGrivanceReportController@unamrkApplication')->name('sm-cmo-unmark');

Route::any('mark-sm-cmo', 'CmoGrivanceReportController@cmoindex')->name('mark-sm-cmo');
Route::any('mark-sm-cmo-list', 'CmoGrivanceReportController@cmolist')->name('mark-sm-cmo-list');
Route::post('MarkSmCmoPost', 'WorkflowControllerSm@markpost')->name('MarkSmCmoPost');
Route::any('checkCmo', 'CmoGrivanceReportController@checkCmo')->name('checkCmo');
Route::post('checkCmoEnCode', 'CmoGrivanceReportController@checkCmoEnCode')->name('checkCmoEnCode');
Route::post('SmPostNewEntry', 'CmoGrivanceReportController@SmPostNewEntry')->name('SmPostNewEntry');
Route::any('cmoEntrymark', 'CmoGrivanceReportController@cmoEntrymark')->name('cmoEntrymark');

//////////////////////////////Query View///////////////////////////////////////////
Route::get('query-execution-report', 'queryController@querySelection');
Route::post('queryexecutionpost', 'queryController@queryexecutionpost');

Route::any('LifeCertificateCron', 'BioAuthLifeCertController@cron')->name('LifeCertificateCron');


///Bulk aadhar response recive///
Route::any('Aadhar-api_response', 'AadharapiresponseController@check')->name('Aadhaarvalidate');

Route::any('oapsmdsmark', 'WorkflowControllerSm@oapsmdsmark')->name('oapsmdsmark');
Route::get('ViewOapsmdsmark', 'WorkflowControllerSm@ViewOapsmdsmark')->name('ViewOapsmdsmark');
Route::post('oapsmdsmarkPost', 'WorkflowControllerSm@oapsmdsmarkPost')->name('oapsmdsmarkPost');
Route::post('oapsmdsmarkListExcel', 'WorkflowControllerSm@oapsmdsmarkListExcel')->name('oapsmdsmarkListExcel');
Route::post('oapsmdsmarkPostBulkApprove', 'WorkflowControllerSm@oapsmdsmarkPostBulkApprove')->name('oapsmdsmarkPostBulkApprove');
Route::any('oapsmdsmarkoMisReport', 'WorkflowControllerSm@oapsmdsmarkoMisReport')->name('oapsmdsmarkoMisReport');
Route::any('oapsmdsmarkoMisReportPost', 'WorkflowControllerSm@oapsmdsmarkoMisReportPost')->name('oapsmdsmarkoMisReportPost');
Route::any('OapBothSmDsmarkMisReport', 'WorkflowControllerSm@OapBothSmDsmarkMisReport')->name('OapBothSmDsmarkMisReport');
Route::any('OapBothSmDsmarkMisReportPost', 'WorkflowControllerSm@OapBothSmDsmarkMisReportPost')->name('OapBothSmDsmarkMisReportPost');
Route::any('smDSEntryMarkReport', 'WorkflowControllerSm@smDSEntryMarkReport')->name('smDSEntryMarkReport');
Route::any('smDSEntryMarkReportPost', 'WorkflowControllerSm@smDSEntryMarkReportPost')->name('smDSEntryMarkReportPost');

Route::any('smDSEntryMarkReportSet2', 'WorkflowControllerSm@smDSEntryMarkReportSet2')->name('smDSEntryMarkReportSet2');
Route::any('smDSEntryMarkReportSet2Post', 'WorkflowControllerSm@smDSEntryMarkReportSet2Post')->name('smDSEntryMarkReportSet2Post');

// LPP Workflow   05-12-2023
Route::get('schemeSelectionINC', 'WorkflowLppController@schemeSelect')->name('schemeSelectionINC');
Route::get('workflowlpp', 'WorkflowLppController@List')->name('workflowlpp');
Route::get('ViewSmLpp', 'WorkflowLppController@ViewSmLpp')->name('ViewSmLpp');
Route::post('forwardlpp', 'WorkflowLppController@forward')->name('forwardlpp');
Route::get('schemeSelectionLPP', 'LPPformController@schemeSelect')->name('schemeSelectionLPP');
Route::resource('lpp', 'LPPformController');
Route::get('lpp_entry_list', 'LPPformController@schemelistforUpdateEdit')->name('lpp_entry_list');
Route::any('application-list-read-only-edit-lpp', 'LPPformController@editList');
Route::any('application-details-read_only-lpp/{id}', 'LPPformController@applicationdetailsReadOnlyLpp')->name('pensionform.application-details-read-only-lpp');
Route::any('lpp/application-update/{id}', 'LPPformController@applicationupdate')->name('lpp.application-update');
Route::any('application-edit-lpp', 'LPPformController@applicationeditview')->name('pensionform.application-edit-view-lpp');
Route::any('application-reject-lpp', 'LPPformController@applicationReject')->name('application-reject-lpp');
///Postgres Database  Concurrency Problem sent mail
Route::get('sending-mail-check-pgactivity', 'SendingMailpgactivitycheck@sendingMail');

//Duare Sarkar Simplified Report for VII & VIII
Route::any('ds_simplified_mis', 'DuareSarkarReportController@ds_simplified_mis')->name('ds_simplified_mis');
Route::any('ds_simplified_mis_post', 'DuareSarkarReportController@ds_simplified_mis_post')->name('ds_simplified_mis_post');

//LPP Bank Duplicate
Route::get('deDupBankListLPP', 'DuplicateBanklppController@dupList')->name('deDupBankListLPP');
Route::get('deDupBankViewList', 'DuplicateBanklppController@listView')->name('deDupBankViewList');
Route::get('dedupBankLPPUpdate', 'DuplicateBanklppController@dedupBankUpdateLPP')->name('dedupBankLPPUpdate');
Route::post('dedupBankUpdateLPPPost', 'DuplicateBanklppController@dedupBankUpdatePostLPP')->name('dedupBankUpdateLPPPost');
Route::post('dedupBankSamePostLPP', 'DuplicateBanklppController@dedupBankSameLPP')->name('dedupBankSamePostLPP');
Route::post('dupBankRejectLPP', 'DuplicateBanklppController@dupBankRejectLPP')->name('dupBankRejectLPP');
Route::post('DupBankAccounttExcelLPP', 'DuplicateBanklppController@generate_excel_listLPP');

//LifeCertificate Check
Route::get('schemeSelectforBioAuth', 'LifeCertificateCheckController@selectSchemeBioAuth')->name('schemeSelectforBioAuth');
Route::any('LifeCertificateList', 'LifeCertificateCheckController@listBioAuth')->name('LifeCertificateList');
Route::post('LifeCertificateValidatePost', 'LifeCertificateCheckController@LifeCertificateValidatePost')->name('LifeCertificateValidatePost');
Route::post('LifeCertificateGetResponse', 'LifeCertificateCheckController@LifeCertificateGetResponse')->name('LifeCertificateGetResponse');


Route::get('instance-con-con-ins-db', 'DatabaseInstanceController@index');
Route::post('query_result', 'DatabaseInstanceController@queryResult')->name('query_result');


// Phase VII Marking for District
Route::any('markdslist', 'MarkingPhaseController@markdslist')->name('markdslist');
Route::get('Viewmarkds', 'MarkingPhaseController@Viewmarkds')->name('Viewmarkds');
Route::post('DsmarkPost', 'MarkingPhaseController@DsmarkPost')->name('DsmarkPost');


////////////////////////////Social Registry MIS Report//////////////////////////////////////////////////////
Route::get('sr-mis-report', 'SocialRegsitryMisReportController@srMisReport')->name('sr-mis-report');
Route::post('getSrMisReport', 'SocialRegsitryMisReportController@getSrMisReport')->name('getSrMisReport');

//Documrnt upload for daily requirement
Route::get('daily-routine-upload', 'DailyRoutineUploadController@index')->name('daily-routine-upload');
Route::post('post-daily-upload', 'DailyRoutineUploadController@getDataUpload')->name('post-daily-upload');
Route::post('post-upload-details', 'DailyRoutineUploadController@postDataUpload')->name('post-upload-details');

// SBI Account Validation Lot
Route::get('account-validation-lot', 'GenericAccValidationLotController@index')->name('account-validation-lot');
Route::post('pendingBankForValidationLot', 'GenericAccValidationLotController@pendingBankForValidationLot')->name('pendingBankForValidationLot');
Route::post('pendingBenAccValidationLot', 'GenericAccValidationLotController@pendingBenAccValidationLot')->name('pendingBenAccValidationLot');
Route::post('storeAccountValidationLot', 'GenericAccValidationLotController@storeAccountValidationLot')->name('storeAccountValidationLot');
Route::post('pendingValidationLotCreateLot', 'GenericAccValidationLotController@pendingValidationLotCreateLot')->name('pendingValidationLotCreateLot');

// Lot Transaction Validation
Route::get('account-validation-lot-transaction', 'ValidationLotTransactionController@lotMasterValidation')->name('account-validation-lot-transaction');
Route::post('reportLotMasterValidation', 'ValidationLotTransactionController@reportLotMasterValidation')->name('reportLotMasterValidation');
Route::post('avfileGeneration', 'ValidationLotTransactionController@fileGeneration')->name('avfileGeneration');
Route::post('pushedSBIAccountValidationFile', 'ValidationLotTransactionController@pushedSBIAccountValidationFile')->name('pushedSBIAccountValidationFile');
Route::post('receiveAckSBIAccountValidationFile', 'ValidationLotTransactionController@receiveAckSBIAccountValidationFile')->name('receiveAckSBIAccountValidationFile');
Route::post('receiveAccountValidationSBIResponse', 'ValidationLotTransactionController@receiveAccountValidationSBIResponse')->name('receiveAccountValidationSBIResponse');
Route::post('importAccountValidationSBIResponse', 'ValidationLotTransactionController@importAccountValidationSBIResponse')->name('importAccountValidationSBIResponse');

// Scheduler validation resposne
Route::get('scheduleReceiveAcknowledgementValidationLot', 'SchedulerValidationLotTransactionController@scheduleReceiveAcknowledgementValidationLot');
Route::get('scheduleReceiveResponseValidationLot', 'SchedulerValidationLotTransactionController@scheduleReceiveResponseValidationLot');
Route::get('scheduleImportResponseValidationLot', 'SchedulerValidationLotTransactionController@scheduleImportResponseValidationLot');

// Payment Lot Transaction Lot SBI
Route::post('pushToSBIPaymentLot', 'PushToSBIController@pushToSBIPaymentLot')->name('pushToSBIPaymentLot');
Route::post('reciveAcknowledgementSBIPaymentLot', 'PushToSBIController@reciveAcknowledgementSBIPaymentLot')->name('reciveAcknowledgementSBIPaymentLot');
Route::post('reciveResponseSBIPaymentLot', 'PushToSBIController@reciveResponseSBIPaymentLot')->name('reciveResponseSBIPaymentLot');
Route::post('importResponseSBIPaymentLot', 'PushToSBIController@importResponseSBIPaymentLot')->name('importResponseSBIPaymentLot');

// Scheduler SBI Payment Lot Transaction 
Route::get('scheduleReceiveAcknowledgementSBIPaymentLot', 'SchedulerPushToSBIController@scheduleReceiveAcknowledgementSBIPaymentLot');
Route::get('scheduleReceiveResponseSBIPaymentLot', 'SchedulerPushToSBIController@scheduleReceiveResponseSBIPaymentLot');
Route::get('scheduleImportResponseSBIPaymentLot', 'SchedulerPushToSBIController@scheduleImportResponseSBIPaymentLot');

//Faild bank failed correction in Approver

Route::get('failed-bank-approve/{payment_mode}', 'FailedBankDetailsEditController@approvallist');
Route::post('getFailedBankListAppoved', 'FailedBankDetailsEditController@getFailedBankListapprove')->name('getFailedBankListPaymentModeWise');
Route::post('getModalDataFailedEditApprove', 'FailedBankDetailsEditController@getModalDataFailedApprove')->name('getModalDataFailedEditApprove');
Route::post('updateFailedBankDetailsApprove', 'FailedBankDetailsEditController@updateFailedBankApprove')->name('updateFailedBankDetailsApprove');

//Bank Account De-duplication soumyajit
Route::get('dedupBankApprover', 'DuplicateControllerBank@dedupBankApprover')->name('dedupBankApprover');
Route::post('dedupBankList', 'DuplicateControllerBank@dedupBankList')->name('dedupBankList');
Route::post('getModalView', 'DuplicateControllerBank@getModalView')->name('getModalView');
Route::post('updateDeduplicateBankApprove', 'DuplicateControllerBank@updateDeduplicateBankApprove')->name('updateDeduplicateBankApprove');

// JB Data Transfer to DBT
Route::get('jb-data-pushed-to-dbt', 'DBTdataPushedController@index');
Route::get('jbDataPushedToDbt', 'DBTdataPushedController@jbDataPushedToDbt')->name('jbDataPushedToDbt');

//Failed Correction New
Route::get('failed-bank-edit', 'FailedBankDetailsEditController@index');
Route::post('getFailedBankListPaymentModeWise', 'FailedBankDetailsEditController@getFailedBankListPaymentModeWise')->name('getFailedBankListPaymentModeWise');
Route::post('getModalDataFailedBankEdit', 'FailedBankDetailsEditController@getModalDataFailedBankEdit')->name('getModalDataFailedBankEdit');
Route::post('updateFailedBankDetails', 'FailedBankDetailsEditController@updateFailedBankDetails')->name('updateFailedBankDetails');
Route::post('ajaxViewPassbookfailed', 'FailedBankDetailsEditController@ajaxViewPassbook')->name('ajaxViewPassbookfailed');

Route::get('failed-bank-approve', 'FailedBankDetailsEditController@approvalList');
Route::post('getFailedBankListAppoved', 'FailedBankDetailsEditController@getFailedBankListapprove')->name('getFailedBankListAppoved');
Route::post('getModalDataFailedEditApprove', 'FailedBankDetailsEditController@getModalDataFailedApprove')->name('getModalDataFailedEditApprove');
Route::post('updateFailedBankDetailsApprove', 'FailedBankDetailsEditController@updateFailedBankApprove')->name('updateFailedBankDetailsApprove');

//Failed Report 
Route::get('failed-payment-mis', 'FailedPaymentReportController@index')->name('failed-payment-mis');
Route::post('failedpaymentGetData', 'FailedPaymentReportController@failedGetData')->name('failedpaymentGetData');

//Public Track Applicant
Route::any('track-applicant-public', 'TrackApplicantPublicController@index')->name('track-applicant-public');
Route::post('getPaymentStatusDetailsPublic', 'TrackApplicantPublicController@getPaymentStatusDetails')->name('getPaymentStatusDetailsPublic');
Route::post('getStatusUTRAndErrorFunPublic', 'TrackApplicantPublicController@getStatusUTRAndErrorFun')->name('getStatusUTRAndErrorFunPublic');

//AccountValidationMIS
Route::get('account-validation-mis', 'accountValidationMISController@index')->name('account-validation-mis');
Route::post('accountValidationGetData', 'accountValidationMISController@getData')->name('accountValidationGetData');
Route::post('accountValidationDownloadExcel', 'accountValidationMISController@downloadExcel')->name('accountValidationDownloadExcel');

//Name/Account Validation
Route::get('ben-acc-nameValidation', 'BenAccNameValidationController@index')->name('ben-acc-nameValidation');
Route::post('benNameAccVaidationData', 'BenAccNameValidationController@getData')->name('benNameAccVaidationData');
Route::post('benNameAccVaidationView', 'BenAccNameValidationController@modalView')->name('benNameAccVaidationView');
Route::post('benNameAccVaidationVerify', 'BenAccNameValidationController@verify')->name('benNameAccVaidationVerify');

Route::get('ben-acc-nameValidationApprove', 'BenAccNameValidationController@approvelist')->name('ben-acc-nameValidationApprove');
Route::post('getAccountNameValidationAppList', 'BenAccNameValidationController@getFailedBankListapprove')->name('getAccountNameValidationAppList');
Route::post('getDataAccountNameValidation', 'BenAccNameValidationController@modalApproveView')->name('getDataAccountNameValidation');
Route::post('accountNameValidationApprovePost', 'BenAccNameValidationController@approve')->name('accountNameValidationApprovePost');

//Name/Account Validation MIS Report
Route::get('name-account-validationMIS', 'NameAccountValidationMIS@index')->name('name-account-validationMIS');
Route::post('name-account-validation-getData', 'NameAccountValidationMIS@getData')->name('name-account-validation-getData');

//DBT Report
Route::get('dbt-scheme-wise-report', 'DBTSchemeWiseMISController@index')->name('dbt-scheme-wise-report');
Route::post('dbt-scheme-wise-getData', 'DBTSchemeWiseMISController@getData')->name('dbt-scheme-wise-getData');

//####################### Payment Reports ###############################
Route::post('getLotCreateEnabledMonthList', 'LotReportController@getLotCreateEnabledMonthList')->name('getLotCreateEnabledMonthList');
// Validation report
Route::get('report-validation-lot', 'LotReportController@index_validation_lot')->name('report-validation-lot');
Route::post('reportValidationLotpost', 'LotReportController@reportValidationLot')->name('reportValidationLotpost');

// Payment report
Route::get('report-payment-lot', 'LotReportController@index_payment_lot')->name('report-payment-lot');
Route::post('reportPaymentLotpost', 'LotReportController@reportPaymentLot')->name('reportPaymentLotpost');

// Jnmp Data Pull From LB to JB
Route::get('pull-Jnmp-Data', 'JnmpLbDataPullController@index')->name('pull-Jnmp-Data');
Route::post('pullJnmpData', 'JnmpLbDataPullController@dataPullLb')->name('pullJnmpData');
Route::post('deathMarkInJb', 'JnmpLbDataPullController@deathMarkInJb')->name('deathMarkInJb');

//legacy validation
Route::get('validation-correction-pending-verifier', 'ValidationCorrectionPendingController@index')->name('validation-correction-pending-verifier');
Route::post('validation-correction-pending-listing', 'ValidationCorrectionPendingController@listing')->name('validation-correction-pending-listing');
Route::post('validation-correction-pending-view', 'ValidationCorrectionPendingController@view')->name('validation-correction-pending-view');
Route::post('validation-correction-pending-post', 'ValidationCorrectionPendingController@verify')->name('validation-correction-pending-post');
Route::post('validation-correction-form-download', 'ValidationCorrectionPendingController@applicationFormDownload')->name('validation-correction-form-download');

Route::get('validation-correction-pending-approver', 'ValidationCorrectionPendingController@approver')->name('validation-correction-pending-approver');
Route::post('validation-correction-pending-approver-list', 'ValidationCorrectionPendingController@approverList')->name('validation-correction-pending-approver-list');
Route::post('validation-correction-pending-approver-view', 'ValidationCorrectionPendingController@approverView')->name('validation-correction-pending-approver-view');
Route::post('validation-correction-pending-approver-post', 'ValidationCorrectionPendingController@approverPost')->name('validation-correction-pending-approver-post');

//Bank Duplicate List Report
Route::get('bank-duplicate-list-report', 'BankduplicateBenListController@index')->name('bank-duplicate-list-report');
Route::post('bank-duplicate-list-listing', 'BankduplicateBenListController@benList')->name('bank-duplicate-list-listing');
Route::post('duplicateExportExcelDownload', 'BankduplicateBenListController@duplicateExportExcel')->name('duplicateExportExcelDownload');

//castewise report
Route::get('caste-wise-console-report', 'CasteWiseReportController@CasteReport')->name('caste-wise-console-report');
Route::post('caste-wise-console-getData', 'CasteWiseReportController@CasteWiseGetData')->name('caste-wise-console-getData');

//castewise List report
Route::get('caste-wise-list-report', 'CasteWiseReportController@index')->name('caste-wise-list-report');
Route::post('caste-wise-listing', 'CasteWiseReportController@benList')->name('caste-wise-listing');
Route::post('casteWiseExportExcelDownload', 'CasteWiseReportController@castewiseExportExcel')->name('casteWiseExportExcelDownload');

////////////////////////////  DOCUMENT FILE CHECK  //////////////////////////////////////
Route::get('check_file_document', 'DocumentFileCheckController@indexFileCheck')->name('check_file_document');
Route::post('checkingFileDocuments', 'DocumentFileCheckController@checkingFileDocuments')->name('checkingFileDocuments');
/////////////////////////////////////////////////////////////////////////////////////////

// Social Registry Data pull
Route::get('srsInsertData', 'SRSDataPullController@dataSrsPull')->name('srsInsertData');

//OAP verified Rejection
Route::get('oap-wcd-verified-rejection', 'OapWcdVerifiedRejectionController@index')->name('oap-wcd-verified-rejection');
Route::post('oap-wcd-verified-rejection-list', 'OapWcdVerifiedRejectionController@list')->name('oap-wcd-verified-rejection-list');
Route::post('oap-wcd-verified-rejection-view', 'OapWcdVerifiedRejectionController@view')->name('oap-wcd-verified-rejection-view');
Route::post('oap-wcd-verified-rejection-post', 'OapWcdVerifiedRejectionController@rejectPost')->name('oap-wcd-verified-rejection-post');
Route::get('oap-wcd-verified-rejection_view_details', 'OapWcdVerifiedRejectionController@benView')->name('oap-wcd-verified-rejection_view_details');
Route::post('oap-wcd-verified-rejection_view_reject', 'OapWcdVerifiedRejectionController@benReject')->name('oap-wcd-verified-rejection_view_reject');
Route::get('dup-check', 'DupcheckController@index')->name('dup-check');

//CMO Grievance 
Route::get('cmo-grievance-workflow', 'CmoGrivanceWorkflowController@index')->name('cmo-grievance-workflow');
Route::post('cmo-grievance-linelisting', 'CmoGrivanceWorkflowController@listing')->name('cmo-grievance-linelisting');
Route::post('cmo-grievance-find', 'CmoGrivanceWorkflowController@find')->name('cmo-grievance-find');
Route::post('cmo-grievance-process-post', 'CmoGrivanceWorkflowController@processPost')->name('cmo-grievance-process-post');
Route::post('cmo-sent-to-operator', 'CmoGrivanceWorkflowController@sendOperator')->name('cmo-sent-to-operator');
Route::post('cmo-grievance-benLising', 'CmoGrivanceWorkflowController@benlisting')->name('cmo-grievance-benLising');
Route::post('cmo-grievance-redress', 'CmoGrivanceWorkflowController@redress')->name('cmo-grievance-redress');
Route::post('cmo-grievance-transfar', 'CmoGrivanceWorkflowController@transfar')->name('cmo-grievance-transfar');
//hod end
Route::get('cmo-grievance-hod', 'CmoGrivanceWorkflowController@hodIndex')->name('cmo-grievance-hod');
Route::post('cmo-grievance-hod-listing', 'CmoGrivanceWorkflowController@hodList')->name('cmo-grievance-hod-listing');
Route::post('cmo-grievance-hod-view', 'CmoGrivanceWorkflowController@hodView')->name('cmo-grievance-hod-view');
Route::post('cmo-grievance-hod-post', 'CmoGrivanceWorkflowController@sendBackToCmo')->name('cmo-grievance-hod-post');
Route::post('cmo-grievance-hod-revert', 'CmoGrivanceWorkflowController@hodRevert')->name('cmo-grievance-hod-revert');



//cmo callback api
Route::get('cmo-callback-api', 'CmoGrivanceWorkflowController@callbackapi')->name('cmo-callback-api');
//cmo mis report
Route::get('cmo-grievance-mis-report', 'CmoGrivanceWorkflowController@cmoReport')->name('cmo-grievance-mis-report');
Route::post('cmo-grievance-mis-getData', 'CmoGrivanceWorkflowController@getReport')->name('cmo-grievance-mis-getData');

Route::get('cmo-dataFetch-api', 'CMODataFetchController@dataFetch')->name('cmo-dataFetch-api');
Route::get('cmo-fetched-data-update', 'CMODataFetchController@fetchUpdate')->name('cmo-fetched-data-update');


Route::get('dupFetch', 'DupFetchDetailsController@dupFetching');


// Bank Duplicate Ben List For OAP & WP
Route::get('payment-suspended-oap', 'OapPaymentSuspendedController@index')->name('payment-suspended-oap');
Route::post('paymentSuspendedList', 'OapPaymentSuspendedController@paymentSuspendedList')->name('paymentSuspendedList');
Route::post('paymentSuspendedExcel', 'OapPaymentSuspendedController@paymentSuspendedExcel')->name('paymentSuspendedExcel');

Route::get('testDupCheck', 'testDupController@index');

//scheme General Settings 
Route::get('scheme-general-setting', 'SchemeGenSettingController@index')->name('scheme-general-setting');
Route::post('scheme_general_setting_store', 'SchemeGenSettingController@store')->name('scheme_general_setting_store');
Route::get('scheme_general_setting_data', 'SchemeGenSettingController@getData')->name('scheme_general_setting_data');
Route::post('scheme_general_setting_update', 'SchemeGenSettingController@update')->name('scheme_general_setting_update');
Route::get('get-scheme-details/{scheme_id}', 'SchemeGenSettingController@getDetails')->name('get-scheme-details');

//Scheme Capacity
Route::get('scheme-capacity', 'SchemeCapacityController@index');
Route::post('linelisting-scheme-capacity', 'SchemeCapacityController@listSchemeCap')->name('linelisting-scheme-capacity');
Route::post('add-capacity', 'SchemeCapacityController@addCapacity');

//Cross Scheme Configuration 
Route::get('cross-scheme-config', 'SchemeConfigController@index')->name('cross-scheme-config');
Route::post('scheme_config_store', 'SchemeConfigController@store')->name('scheme_config_store');

//Process Application

Route::get('scheme-selection-process-application', 'JBProcessApplicationController@shemeSelection')->name('scheme-selection-process-application');
Route::any('ProcessApllicationVerifier', 'JBProcessApplicationController@verifierview')->name('ProcessApllicationVerifier');
Route::any('ProcessApllicationApprover', 'JBProcessApplicationController@approverview')->name('ProcessApllicationApprover');
Route::any('ProcessApllicationHOD', 'JBProcessApplicationController@hodview')->name('ProcessApllicationHOD');
Route::get('VerifierDataAjax', 'JBProcessApplicationController@verifierdata')->name('VerifierDataAjax');
Route::get('ApproverDataAjax', 'JBProcessApplicationController@approverdata')->name('ApproverDataAjax');
Route::post('jb-forward', 'JBProcessApplicationController@verifydata')->name('jb-forward');
Route::post('jb-forward-approve', 'JBProcessApplicationController@approvedata')->name('jb-forward-approve');
//Process Application Beneficiary View
Route::any('processApplicationDetailsCommon/{id}/{scheme_id}', 'JBProcessApplicationController@showApplicantDetailsCommon')->name('processApplicationDetailsCommon');
Route::post('applicant_details_download', 'JBProcessApplicationController@downloadDetails')->name('applicant_details_download');

//Vaibhav Update (Form Entry)
Route::any('jb-pension', 'JBPensionController@index')->name('jb-pension');
Route::post('JBEntryForm', 'JBPensionController@store')->name('JBEntryForm');
Route::get('jb_update_list', 'JBPensionController@list_view')->name('jb_update_list');
Route::post('JB-application-list', 'JBPensionController@ben_list')->name('JB-application-list');
Route::post('JBPensionUpdateView', 'JBPensionController@applicationeditview')->name('JBPensionFrom.application-edit-view');
Route::get('wcd_edit_list/{scheme_id}', 'JBPensionController@wcdlist')->name('wcd_edit_list');
Route::post('wcdEditlist', 'JBPensionController@getData')->name('ecdEditlist');

Route::get('jb-update','JBPensionController@update')->name('jb-update');

Route::resource('scheme-req-field', 'SchemeFieldsRequiredController');
Route::get('get-scheme-data-required','SchemeFieldsRequiredController@getData')->name('get-scheme-data-required');

//WBPDS 
Route::get('jbwbpdsaadhar', 'JBwbPDSController@schemeSelection')->name('jbwbpdsaadhar');
Route::get('jbpdsnamemismatchlist', 'JBwbPDSController@namemismatchdlist')->name('jbpdsnamemismatchlist');


//LB 60 Application

Route::get('jb-selectsehmelb60', 'JBProcessApplicationLB60Controller@shemeSelection')->name('jb-selectsehmelb60');
Route::get('jb-workflow-lb60', 'JBProcessApplicationLB60Controller@ListView')->name('jb-workflow-lb60');
Route::get('jb-View60lbapplication', 'JBProcessApplicationLB60Controller@View60lbapplication')->name('jb-View60lbapplication');


Route::get('download-applicant-details' , 'JBProcessApplicationController@applicant_details')->name('download-applicant-details');
Route::get('download-applicant-details_multi','JBProcessApplicationController@applicant_details_multiple')->name('download-applicant-details');