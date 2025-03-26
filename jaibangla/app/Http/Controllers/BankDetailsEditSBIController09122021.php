<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\MapLavel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BankDetailsEditSBIController extends Controller
{
  public function shemeSelection(Request $request)
  {
    /*echo "<pre>";  
     //print_r($data);
     $bodyCode = $request->session()->get('bodyCode');
     echo $bodyCode;
     echo "</pre>";
     die();*/
    $user_id = Auth::user()->id;
    $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));
    if (Auth::user()->designation_id == "Approver") {
      return view('scheme-selection-bank-sbi-edit/main', compact('report'));
    } elseif (Auth::user()->designation_id == "Verifier") {      // 29-07-2020
      return view('scheme-selection-bank-sbi-edit/main', compact('report'));   // 29-07-2020
    } else {
      return redirect("/")->with('success', 'UnAuthorized');
    }
  }

  public function shemeSessionCheck(Request $request)
  {

    $user_id = Auth::user()->id;
    $scheme_id = 0;
    $ben_table = "";
    if ($request->get('pr1')) {
      if ($request->get('pr1') == "sc") {
        $scheme_id = 3;
        $ben_table = "PensionSc";
      } else if ($request->get('pr1') == "st") {
        $scheme_id = 1;
        $ben_table = "PensionSt";
      } else if ($request->get('pr1') == "manabik") {
        $scheme_id = 2;
        $ben_table = "Manabik";
      } else if ($request->get('pr1') == "lppret") {
        $scheme_id = 8;
        $ben_table = "PensionLPPRetainer";
      } else if ($request->get('pr1') == "lpppen") {
        $scheme_id = 9;
        $ben_table = "PensionLPPPensioner";
      } else if ($request->get('pr1') == "oap") {
        $scheme_id = 10;
        $ben_table = "PensionOAPWCD";
      } else if ($request->get('pr1') == "wp") {
        $scheme_id = 11;
        $ben_table = "PensionWPWCD";
      } else if ($request->get('pr1') == "purohits") {
        $scheme_id = 17;
        $ben_table = "PensionPurohitMonthlyICAD";
      } 
      else if ($request->get('pr1') == "oapfarmer") {
        $scheme_id = 13;
        $ben_table = "PensionOAPFarmer";
      } 
      else {
        return view('scheme-selection-bank-sbi-edit/main');
      }
    } else {
      return redirect("scheme-selection-bank-sbi-edit/main");
    }

    $is_active = 0;
    $roleArray = $request->session()->get('role');
    /*echo "<pre>";  
                print_r($roleArray);
                echo "</pre>";
                die();  */
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $request->session()->put('scheme_id', $scheme_id);
        $request->session()->put('ben_table', $ben_table);
        $request->session()->put('is_first', $roleObj['is_first']);
        $request->session()->put('is_urban', $roleObj['is_urban']);
        $request->session()->put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('bodyCode', $roleObj['urban_body_code']);
        } else {
          $request->session()->put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }

    if ($is_active == 1) {
      return 2;
    }
    if ($is_active == 0) {

      return $is_active;
    } else {
      return $is_active;
    }
    //  return view('scheme-selection-bank-sbi-edit/main');
  }

  public function applicationdetails(Request $request)
  {
    if (!$request->ajax()) {// echo 1;die;
      if (empty($request->get('pr1'))) {
        Session::flash('error', 'Oops. This scheme is not allowed for sbi failed');
        Session::flash('alert-class', 'alert alert-danger');
        return redirect(route('bank-details-edit-sbi'));
      } else { 
        $res = $this->shemeSessionCheck($request);
        if ($res == 0) {
          Session::flash('error', 'User Disabled');
          Session::flash('alert-class', 'alert alert-danger');
          return redirect(route('bank-details-edit-sbi'));
        } else {
          return $this->generalfunc($request);
        }
      }
    } else { //echo 2;die;
      return  $this->generalfunc($request);
    }
  }
  public function generalfunc($request)
  {

    $scheme_id = $request->session()->get('scheme_id');
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = Auth::user()->id;
    $schemeObj = DB::table('public.m_scheme')->where('id', $scheme_id)->first();

    if ($is_first) {   // First Level Verifier   	
      if ($mappingLevel == "State") {
        $level = "State";
      } else if ($mappingLevel == "District") {

        //$district_code = $duty->district_code;
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $rows = $modelName::where('lot_generated', -3)
          ->where('next_level_role_id', 0)
          ->where('scheme_id', $scheme_id)
          ->where('created_by_dist_code', $district_code)
          ->orderBy('id', 'desc')
          ->where('bank_edited', 0) //Temporary Code
          ->paginate(10);
        return view('pension_list', ['nhm_employee_details' => $rows]);
      } else if ($mappingLevel == "Subdiv") {

        if ($is_urban == 1) {

          $duty_level = "SubdivVerifier";
          $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();

          if (request()->ajax()) { // dd($urban_body_code);
            if (!empty($request->filter_1)  && empty($request->filter_2)) { //dd($urban_body_codes);     
              $body_code = $request->session()->get('bodyCode');
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              //$data=$modelName::where('next_level_role_id',0)             //29-07-2020
              $data = $modelName::where(function ($query) use ($role_id) {     //29-07-2020
                $query->where('next_level_role_id', $role_id)          //29-07-2020
                  ->orWhere('next_level_role_id', 0);                   //29-07-2020
              })                                                  //29-07-2020
                ->where('lot_generated', -3)
                ->where('scheme_id', $scheme_id)
                ->where('created_by_local_body_code', $body_code)
                ->where('block_ulb_code', $request->filter_1)
                //->where('gp_ward_code', $request->filter_2)
                ->where('bank_edited', 0) //Temporary Code
                ->orderBy('id', 'desc')
                ->get();
            } elseif (!empty($request->filter_1) && !empty($request->filter_2)) {

              $body_code = $request->session()->get('bodyCode');
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              //$data=$modelName::where('next_level_role_id',0)              //29-07-2020
              $data = $modelName::where(function ($query) use ($role_id) {     //29-07-2020
                $query->where('next_level_role_id', $role_id)           //29-07-2020
                  ->orWhere('next_level_role_id', 0);                    //29-07-2020
              })                                                   //29-07-2020
                ->where('lot_generated', -3)
                ->where('scheme_id', $scheme_id)
                ->where('created_by_local_body_code', $body_code)
                ->where('block_ulb_code', $request->filter_1)
                ->where('gp_ward_code', $request->filter_2)
                ->where('bank_edited', 0) //Temporary Code
                ->orderBy('id', 'desc')
                ->get();
            } else {
              $body_code = $request->session()->get('bodyCode'); //dd($body_code,$role_id);
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              //$data=$modelName::where('next_level_role_id',0)              //29-07-2020
              $data = $modelName::where(function ($query) use ($role_id) {     //29-07-2020
                $query->where('next_level_role_id', $role_id)           //29-07-2020
                  ->orWhere('next_level_role_id', 0);                    //29-07-2020
              })                                                   //29-07-2020
                ->where('lot_generated', -3)
                ->where('scheme_id', $scheme_id)
                ->where('created_by_local_body_code', $body_code)
                ->where('bank_edited', 0) //Temporary Code
                ->orderBy('id', 'desc')
                ->get();
              //dd($data);
            }
            return datatables()->of($data)
              ->addColumn('view', function ($data) {
                return '<a href="' . route('bank-edit.editApplicantDetails-sbi', $data->id) . '" class="btn btn-ls btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';
              })
              ->addColumn('id', function ($data) {
                return $data->getBenidAttribute();
              })
              ->addColumn('name', function ($data) {
                return $data->getName();
              })
              ->rawColumns(['view', 'id', 'name'])
              ->make(true);
          }
          return view('linelisting_bank_edit_sbi')->with('duty_level', $duty_level)->with('urban_bodys', $urban_bodys)->with('dist_code', $district_code)->with('scheme_name', $schemeObj->scheme_name);
        } else {

          //$taluka_code = $duty->taluka_code;
          $appPrefix = "App";
          $modelName = $appPrefix . "\\" . $ben_table;
          $rows = $modelName::where('next_level_role_id', 0)
            ->where('lot_generated', -3)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $taluka_code)
            ->where('bank_edited', 0) //Temporary Code
            ->orderBy('id', 'desc')->paginate(10);
          return view('pension_list', ['nhm_employee_details' => $rows]);
        }
      } else if ($mappingLevel == "Block") {
        $duty_level = "BlockVerifier";
        //$district_code=$duty->district_code;
        //$taluka_code = $duty->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
        if (request()->ajax()) {
          if (!empty($request->filter_1)) {

            $body_code = $request->session()->get('bodyCode');
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            //$data=$modelName::where('next_level_role_id',0)             //29-07-2020
            $data = $modelName::where(function ($query) use ($role_id) {     //29-07-2020
              $query->where('next_level_role_id', $role_id)              //29-07-2020
                ->orWhere('next_level_role_id', 0);                       //29-07-2020
            })                                                      //29-07-2020
              ->where('lot_generated', -3)
              ->where('scheme_id', $scheme_id)
              ->where('created_by_local_body_code', $body_code)
              //->where('block_ulb_code', $request->filter_2)
              ->where('gp_ward_code', $request->filter_1)
              ->where('bank_edited', 0) //Temporary Code
              ->get();
          } else {
            $body_code = $request->session()->get('bodyCode'); //dd($body_code,$role_id);
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            //$data=$modelName::where('next_level_role_id',0)             //29-07-2020
            $data = $modelName::where(function ($query) use ($role_id) {     //29-07-2020
              $query->where('next_level_role_id', $role_id)              //29-07-2020
                ->orWhere('next_level_role_id', 0);                       //29-07-2020
            })                                                      //29-07-2020
              ->where('lot_generated', -3)
              ->where('scheme_id', $scheme_id)
              ->where('created_by_local_body_code', $body_code)
              ->where('bank_edited', 0) //Temporary Code
              ->get();
          }
          return datatables()->of($data)
            ->addColumn('view', function ($data) {
              return '<a href="' . route('bank-edit.editApplicantDetails-sbi', $data->id) . '" class="btn btn-ls btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';
            })
            ->addColumn('id', function ($data) {
              return $data->getBenidAttribute();
            })
            ->addColumn('name', function ($data) {
              return $data->getName();
            })
            ->rawColumns(['view', 'id', 'name'])
            ->make(true);
        }
        return view('linelisting_bank_edit_sbi')->with('duty_level', $duty_level)
          ->with('gps', $gps)
          ->with('dist_code', $district_code)->with('scheme_name', $schemeObj->scheme_name);
      }

      //return view('pension_list',['nhm_employee_details' => $rows]);
    } else { //approver
      //dd("hi");
      //$mappingLevel=$duty->mapping_level;
      if ($mappingLevel == "State") {
        $duty_level = "State";
      } else if ($mappingLevel == "District") {
        $duty_level = 'DistrictApprover';
        $levels = [
          2 => 'Rural',
          1 => 'Urban',
        ]; //dd($district_code);
        if (request()->ajax()) {
          if (!empty($request->filter_1) && !empty($request->filter_2)) {

            $body_code = $request->session()->get('bodyCode');
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $data = $modelName::where('created_by_dist_code', $district_code)
              ->where(function ($query) use ($role_id) {
                $query->where('next_level_role_id', $role_id)
                  ->orWhere('next_level_role_id', 0);
              })
              ->where('block_ulb_code', $request->filter_2)
              ->where('lot_generated', -3)
              ->where('bank_edited', 0) //Temporary Code
              ->orderBy('id', 'desc')
              ->get();
          } else {
            //dd($ben_table);
            $body_code = $request->session()->get('bodyCode');
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $data = $modelName::where('created_by_dist_code', $district_code)
              ->where(function ($query) use ($role_id) {
                $query->where('next_level_role_id', $role_id)
                  ->orWhere('next_level_role_id', 0);
              })
              ->where('lot_generated', -3)
              ->where('bank_edited', 0) //Temporary Code
              ->orderBy('id', 'desc')
              ->get();
          }
          return datatables()->of($data)
            ->addColumn('view', function ($data) {
              return '<a href="' . route('bank-edit.editApplicantDetails-sbi', $data->id) . '" class="btn btn-xs btn-primary">Edit</a>';
            })
            ->addColumn('id', function ($data) {
              return $data->getBenidAttribute();
            })
            ->addColumn('name', function ($data) {
              return $data->getName();
            })
            ->rawColumns(['view', 'id', 'name'])
            ->make(true);
          /*$body_code = $request->session()->get('bodyCode');
                      $appPrefix = "App";
                      $modelName=$appPrefix . "\\" . $ben_table;
                      $data=$modelName::where('next_level_role_id',0)
                            ->orWhere('next_level_role_id',$role_id)
                            ->where('lot_generated',0)
                            ->where('scheme_id',$scheme_id)                               
                            ->where('created_by_local_body_code',$body_code)
                            ->where('bank_edited',0) //Temporary Code
                            ->get();  */
        }
        return view('linelisting_bank_edit_sbi')->with('duty_level', $duty_level)
          ->with('dist_code', $district_code)
          ->with('levels', $levels)
          ->with('scheme_name', $schemeObj->scheme_name);
      } else {

        if ($is_urban == 1) {
          $duty_level = "ULB";
        } else {
          $duty_level = "Block";
          $appPrefix = "App";
          $modelName = $appPrefix . "\\" . $ben_table;

          $rows = $data = $modelName::where('next_level_role_id', $role_id)->where('created_by_local_body_code', $taluka_code)->orderBy('id', 'desc')->paginate(10);
          //return view('pension_list',['nhm_employee_details' => $rows]);
          return view('linelisting_bank_edit_sbi', ['datas' => $rows, 'dist_code' => $district_code, 'duty_level' => $duty_level]);
        }
      }
      //  return view('linelisting_approved',['datas' => $rows]);
    }
  }
  public function editApplicantDetails(Request $request)
  {
    //DB::enableQueryLog();
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = Auth::user()->id;
    $body_code = $request->session()->get('bodyCode');
    $id = $request->id; //dd($id);
    $appPrefix = "App";
    $modelName = $appPrefix . "\\" . $ben_table;
    $single_employee_details = $modelName::where('id', '=', $id)
      ->where('scheme_id', $scheme_id)
      ->where(function ($query) use ($role_id) {
        $query->where('next_level_role_id', $role_id)
          ->orWhere('next_level_role_id', 0);
      })
      // ->where('next_level_role_id',0)
      // ->orWhere('next_level_role_id',$role_id)
      ->where('lot_generated', -3)
      ->where('created_by_dist_code', $district_code)
      ->where('bank_edited', 0) //Temporary Code
      ->first(); //dd($single_employee_details);
    //dd(DB::getQueryLog());

    $query="select  max(lot_no),status_code,c.description from (select max(lot_no) as lot_no,status_code from sbi.transaction_lot_details where pension_id=".$id."
    and scheme_id=".$scheme_id." group by status_code
     union all 
    select max(lot_no) as lot_no,status_code from sbi.transaction_lot_details_report where pension_id=".$id." and scheme_id=".$scheme_id." group by status_code) 
    p,sbi.credit_transaction_code c where p.status_code
    =c.code group by status_code,c.description order by max(lot_no) desc";
    $invalid_status='';
$query_res= DB::select($query);
if(!empty($query_res[0]->description)){
  $invalid_status=$query_res[0]->description;
}
    return view('bank-details-edit-sbi', ['row' => $single_employee_details,'invalid_status'=>$invalid_status]);
    // return view('pension_view_details', ['row' => $single_employee_details,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);

  }

  public function update(Request $request)
  {
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = Auth::user()->id;
    $body_code = $request->session()->get('bodyCode');
    if ($scheme_id == 1) {
      $pr1 = 'st';
    } elseif ($scheme_id == 3) {
      $pr1 = 'sc';
    } elseif ($scheme_id == 2) {
      $pr1 = 'manabik';
    } elseif ($scheme_id == 8) {
      $pr1 = 'lppret';
    } elseif ($scheme_id == 9) {
      $pr1 = 'lpppen';
    } elseif ($scheme_id == 10) {
      $pr1 = 'oap';
    } elseif ($scheme_id == 11) {
      $pr1 = 'wp';
    } elseif ($scheme_id == 17) {
      $pr1 = 'purohits';
    } 
    elseif ($scheme_id == 13) {
      $pr1 = 'oapfarmer';
    } 
    // else {
    //   $pr1 = 'st';
    // }
    //$body_code = $request->session()->get('bodyCode');
    $id = $request->benId;
    $bank_branch = $request->branch_name;
    $bank_code = $request->bank_account_number;
    $bank_ifsc = $request->bank_ifsc;
    $bank_name = $request->bank_name;
    //$mobile_no=$request->mobile_no;
    //$Verified="Verified";
    //$Rejected=1;
    //$comments=$request->comments;

    //$scheme_id = 3;
    $user_id = Auth::user()->id;
    $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

    $this->validateInput($request);
    if ($_POST['submit'] == 'Update') {
      $benDetails = BeneficiaryPensions::where('id', $id)->first();
      $old_bank_name = $benDetails->bank_name;
      $old_branch_name = $benDetails->branch_name;
      $old_bank_ifsc = $benDetails->bank_ifsc;
      $old_bank_code = $benDetails->bank_code;

      $input = [];
      $input_new = [];
      $old_value = [];

      $old_value['old_bank_name'] = trim($old_bank_name);
      $old_value['old_branch_name'] = trim($old_branch_name);
      $old_value['old_bank_ifsc'] = trim($old_bank_ifsc);
      $old_value['old_bank_code'] = trim($old_bank_code);

      $input_new['new_bank_name'] = $bank_name;
      $input_new['new_branch_name'] = $bank_branch;
      $input_new['new_bank_ifsc'] = $bank_ifsc;
      $input_new['new_bank_code'] = $bank_code;
      DB::beginTransaction();
      $updateBenObj = new  UpdateBenDetails();
      $updateBenObj->original_application_id =  $id;
      $updateBenObj->dist_code = $benDetails->dist_code;
      $updateBenObj->scheme_id = $benDetails->scheme_id;
      $updateBenObj->remarks = 'SBI Failed Update Bank Details';
      $updateBenObj->old_data = json_encode($old_value);
      $updateBenObj->new_data = json_encode($input_new);
      $updateBenObj->user_id = $user_id;
      $updateBenObj->update_code = 5;
      $updateBenObj->save();
    
      $input = ['bank_name' => $bank_name, 'branch_name' => $bank_branch, 'bank_code' => $bank_code, 'bank_ifsc' => $bank_ifsc, 'bank_edited' => 1];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      $is_status_updated = $modelName::where('id', $id)
        ->where('scheme_id', $scheme_id)
        ->where(function ($query) use ($role_id) {
          $query->where('next_level_role_id', $role_id)
            ->orWhere('next_level_role_id', 0);
        })
        ->where('lot_generated', -3)
        ->where('created_by_dist_code', $district_code)
        ->where('bank_edited', 0) //Temporary Code
        ->update($input);
      DB::commit();
    
      if ($is_status_updated) {
        Session::flash('success', 'Bank Details Updated Succesfully.');
        Session::flash('alert-class', 'alert alert-success');
       // return redirect(route('bank-details-edit-sbi'))->with('pr1', $pr1);
        return redirect('bank-edit-sbi?pr1='.$pr1)->with('pr1', $pr1);
        
      } else {
        DB::rollback();
        Session::flash('error', 'Oops. Bank Details Not Updated');
        Session::flash('alert-class', 'alert alert-danger');
       // return redirect(route('bank-details-edit-sbi'))->with('pr1', $pr1);
        return redirect('bank-edit-sbi?pr1='.$pr1)->with('pr1', $pr1);
      }
    }
  }

  private function validateInput($request)
  {
    $this->validate($request, [
      //'mobile_no' => 'required:|regex:/[0-9]{10}/',
      'bank_name' => 'required',
      'branch_name' => 'required',
      'bank_account_number' => 'required|numeric|between:00000000000000000000,9999999999999999999',
      'bank_ifsc' => 'required|max:20',

    ]);
  }
}
