<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\applicationModel;
use App\User;
use App\PicUpload;
use App\Policestation;
use Illuminate\Support\Facades\Storage;
use App\Configduty;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\MapLavel;
use App\Scheme;
use App\UserManual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BSKPensionFormController;
use App\Helpers\BanglaSahayataKendraEntry;

class BSKLoginController extends Controller
{
  public function index(Request $request)
  {
    $userId = $request->get('UserId');
    $ticketNo = $request->get('TicketNo');
    // $mobile_no = BanglaSahayataKendraEntry::decrypto($userId, $ticketNo);
    $mobile_no = 5555566666;
    if ($mobile_no == 'INVALID') {
      \Session::flash('error', 'Token expired, please try once again from the beginning.');
      return redirect(route('bsk-entry-done'));
    }
    else {
      $userObj = User::where('mobile_no', $mobile_no)->first();
      // print_r($userObj); die();
      if (isset($userObj)) {
        $user_id = $userObj->id;
        $role = [];
        $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->where('scheme_id',2)->get();
        // print_r($duty);die;
        foreach ($duty as $dutyObj) {
          $mapArr = MapLavel::where('scheme_id', $dutyObj->scheme_id)->where('role_name', $userObj->designation_id)->where('stack_level', $dutyObj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->toArray();

          // print_r($mapArr);die();
          if (count($mapArr) > 0) {
            $newArr = array_merge($mapArr[0], ['district_code' => $dutyObj->district_code, 'mapping_level' => $dutyObj->mapping_level, 'taluka_code' => $dutyObj->taluka_code, 'urban_body_code' => $dutyObj->urban_body_code, 'is_urban' => $dutyObj->is_urban , 'userMobileNo' => $userObj->mobile_no, 'ticketNo' => $ticketNo]);
            array_push($role, $newArr);
          }
        }

        // echo "<pre>";
        //         //echo json_encode($duty);
        //         print_r($role);
        //         echo "</pre>";
        //         die();
        
        $request->session()->put('role', $role);
        //$BSKPensionFormController = new BSKPensionFormController();
        //return $BSKPensionFormController->index($request);
        return redirect('mainEntryForm');    
      }
      else {
        // return redirect("/bsk-entry-done")->with('error', 'User Disabled');
        \Session::flash('error', 'User not found in the jai bangla portal');
        return redirect(route('bsk-entry-done'));
      }
    }
    // $BSKPensionFormController = new BSKPensionFormController();
    // return $BSKPensionFormController->index($request);
  }

  public function verifyBSKOTP(Request $request)
  {
    \Session::put('status', 'Please enter correct mobile number..!!');
    return back();
  }
}
