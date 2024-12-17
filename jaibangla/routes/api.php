<?php

use App\District;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'states'], function () {
    Route::get('/{countryId}', 'StateController@loadStates');
});

Route::group(['prefix' => 'cities'], function () {
    Route::get('/{stateId}', 'CityController@loadCities');
});


Route::group(['prefix' => 'ruralurban'], function () {
    Route::get('/{rural_urban}/{district_code}', 'UpdatebenDetailsController@loadBlockUlb');
});



// Route::group(['prefix' => 'programmehead'], function()
// {
//     Route::get('/{major_programme_head_id}', 'NHMEmployeeController@loadprogrammeHead');
// });

// Route::get('/programmehead/{id}', 'NHMEmployeeController@loadprogrammeHead');


/*
 Route::get('/programmehead/{id}', 'NHMEmployeeController@loadprogrammeHead');//->name('custom_name');
*/


Route::group(['prefix' => 'programmehead'], function () {

    Route::get('/{major_programme_head_id}/{service_category}', 'NHMEmployeeController@loadprogrammeHead');
});

Route::group(['prefix' => 'programmeheadDesignation'], function () {

    Route::get('/{major_programme_head_id}/{service_category}', 'NHMDesignationListController@loadprogrammeHead');
});


Route::group(['prefix' => 'programmeheadDesignationEdit'], function () {

    Route::get('/{major_programme_head_id}/{service_category}', 'NHMDesignationListController@loadprogrammeHead');
});

// Route::group(['prefix' => 'designationlist'], function()
// {

//     Route::get('/{programme_head_id}/{service_category}/{major_programme_head_id}',
//         'NHMEmployeeController@loadDesignationList');


// });

Route::group(['prefix' => 'majorprogrammehead'], function () {

    Route::get('/{service_category_id}', 'NHMEmployeeController@loadMajorprogrammeHead');
});

Route::group(['prefix' => 'majorprogrammeheadDesignation'], function () {

    Route::get('/{service_category_id}', 'NHMDesignationListController@loadMajorprogrammeHead');
});


Route::group(['prefix' => 'majorprogrammeheadDesignationEdit'], function () {

    Route::get('/{service_category_id}', 'NHMDesignationListController@loadMajorprogrammeHead');
});


Route::group(['prefix' => 'localbody'], function () {

    Route::get('/{body_type}/{distrcit_id}', 'ConfigController@loadLocalBody');
});

Route::group(['prefix' => 'blocksubdiv'], function () {

    Route::get('/{body_type}/{distrcit_id}', 'ConfigController@loadBlockSubdiv');
});

/*****sd change 17-03-2020 verifier*******/
// Route::group(['prefix' => 'loadwards'], function () {
   

//     Route::get('/{municipality}', 'WorkflowController@loadWard');
// });
Route::group(['middleware' => 'auth', 'prefix' => 'loadwards'], function () {
    Route::get('/{municipality}', 'WorkflowController@loadWard');
});

/*****sd change 17-03-2020 verifier end*******/


Route::group(['prefix' => 'assembly'], function () {

    Route::get('/{distrcit_id}', 'ConfigController@loadAssembly');
});

Route::group(['prefix' => 'gpward'], function () {

    Route::get('/{body_type}/{body_code}', 'ConfigController@loadGPWard');
});



Route::group(['prefix' => 'loadlevel2'], function () {

    Route::get('/{level_name}', 'NHMEmployeeDrilldownReportController@loadlevel2');
});

Route::group(['prefix' => 'loadlevel3'], function () {

    Route::get('/{level_name}', 'NHMEmployeeDrilldownReportController@loadlevel3');
});

// Route::group(['prefix' => 'loadlevel4'], function(){

//     Route::get('/{reprotlevel1_data}/{reprotlevel2_data}/{reprotlevel3_data}', 'NHMEmployeeDrilldownReportController@loadlevel4');   

// });
Route::group(['prefix' => 'loadlevel4'], function () {

    Route::POST('/{id}', 'NHMEmployeeDrilldownReportController@loadlevel4');
});

Route::group(['prefix' => 'loadlevel4d'], function () {

    Route::POST('/{id}', 'NHMEmployeeDrilldownReportController@loadlevel4');
});



Route::group(['prefix' => 'loadlocationHealthFacilityEdit'], function () {

    Route::get('/{district_code}', 'NHMPlaceController@loadlocationHealthFacilityEdit');
});

Route::group(['prefix' => 'loadlocationHealthFacilityCreate'], function () {

    Route::get('/{district_code}', 'NHMPlaceController@loadlocationHealthFacilityEdit');
});

Route::group(['prefix' => 'loadlevel2d'], function () {

    Route::get('/{id}/{level1data}', 'NHMEmployeeDrilldownReportController@loadlevel2d');
});

Route::group(['prefix' => 'nextlevel'], function () {
    Route::get('/{schemeId}', 'MapLevelController@loadParentLevel');
});

//lot generation
Route::group(['prefix' => 'loadlevel2dlot'], function () {

    Route::get('/{level_name}/{id}', 'LotGenerationController@loadlevel2d');
});
Route::group(['prefix' => 'loadlevel2lot'], function () {

    Route::any('/{id}/{code}', 'LotGenerationController@loadlevel2');
});

Route::group(['prefix' => 'loadcountlot'], function () {

    Route::any('/{id}', 'LotGenerationController@loadcount');
});

//Subhankar Starts

Route::group(['prefix' => 'getpreviousben'], function () {
    Route::get('/{scheme_id}/{month}', 'CumulativeBeneficiaryDetailsController@perviousBenDetails');
});

Route::group(['prefix' => 'getduplicateben'], function () {
    Route::get('/{scheme}', 'ReportDuplicateStopPaymentBenController@showResult');
});

Route::group(['prefix' => 'paymentStatusError'], function () {
    Route::get('/{lot_no}/{scheme_id}/{pension_id}/{payment_type}', 'BeneficiaryPaymentStatusController@paymentStatusErrorMsg');
});
// Report Repeat Lot Master
Route::group(['prefix' => 'getReportRepeatLotRemarks'], function () {
    Route::get('/{child_lot_no}', 'ReportRepeatLotMasterController@showParentLotRemarks');
});

//Subhankar Ends
Route::post('/fetchlb', 'LbFetchController@fetch');

Route::post('responseBack','DupFetchDetailsController@returnResponse');
Route::post('dupCheckBankOAP','DuplicateResponseBackController@dupResponseBankOAP');
Route::post('dupCheckBankWP','DuplicateResponseBackController@dupResponseBankWP');
Route::post('dupCheckBankManabik','DuplicateResponseBackController@dupResponseBankManabik');
Route::post('dupCheckBankJohar','DuplicateResponseBackController@dupResponseBankJohar');
Route::post('dupCheckBankBandhu','DuplicateResponseBackController@dupResponseBankBandhu');
Route::post('dupCheckAadharOAP','DuplicateResponseBackController@dupResponseAadharOAP');
Route::post('dupCheckAadharWP','DuplicateResponseBackController@dupResponseAadharWP');
Route::post('dupCheckAadharManabik','DuplicateResponseBackController@dupResponseAadharManabik');
Route::post('dupCheckAadharJohar','DuplicateResponseBackController@dupResponseAadharJohar');
Route::post('dupCheckAadharBandhu','DuplicateResponseBackController@dupResponseAadharBandhu');


