<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
Use App\District;
use App\BeneficiaryPensions;
use Auth;
use App\Configduty;
use App\Scheme;

use App\ApplicationStatus;
use App\StatusCode;
use App\BankResponse;
use Illuminate\Support\Collection;
use Excel;

use App\UrbanBody;
use App\SubDistrict;
use App\PensionLPPPensioner;
use App\PensionLPPRetainer;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Input;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuthChecker;

class LPPSingleStepVerification extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($scheme)
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $district_code=$duty->district_code;
        $district_name=District::where('district_code',$district_code)->pluck('district_name')->first();
        $scheme_name = $this->getSchemeName($scheme);
    
        return view('lpp-singlestep.index')->with('district_name',$district_name)->with('district_code',$district_code)->with('scheme',$scheme)->with('scheme_name',$scheme_name);

    }
    private function getSchemeName($scheme)
    {
      $scheme_name = "";
      if($scheme == 8){
        $scheme_name = "LPP Retainer";		
      }else if($scheme == 9){
        $scheme_name = "LPP Pensioner";
      }
      
      return $scheme_name;
    }
    private function getModelName($scheme)
    {
      $model_name = "";
      if($scheme == 8){
        $model_name = "PensionLPPRetainer";		
      }else if($scheme == 9){
        $model_name = "PensionLPPPensioner";
      }
      $appPrefix = 'App';
      $modelName=$appPrefix . '\\' . $model_name;
      
      return $modelName;
    }
    public function getData(Request $request){
        //DB::enableQueryLog();
      if(request()->ajax())
      {
        $user_id = AuthChecker::getUserId();
        $district_code=$request->level1;
        $district_name=$request->level2;
        $serachvalue = $request->search['value'];
        $scheme = $request->scheme;

        $model_name = $this->getModelName($scheme);

        //Urban/Rural
        $level=$request->level3;
        //LocalBody
        $localBody=$request->level1a;

        $flag=1;
        $totalRecords = 0;
        $data = array(); 
        
 // WORKING QUERY
          
        $limit = $request->input('length');
        $offset = $request->input('start');

        $condition = array();
          
          if(!empty($district_code)){
            $condition["dist_code"]=$district_code;
          }
          if(!empty($level)){
           if(!empty($localBody)){
                $condition["created_by_local_body_code"]=$localBody;
                $condition["rural_urban_id"]=$level;
              }
            }
            
    if(empty($serachvalue)){      
          $data = $model_name::where($condition)->whereNull('next_level_role_id')->orderBy('id','DESC')->offset($offset)->limit($limit)->get();
          $totalRecords = $model_name::where($condition)->whereNull('next_level_role_id')->count();
          $filterRecords = count($data);
    }else{
              
          $data = $model_name::where('id', $serachvalue)->whereNull('next_level_role_id')->get();
          $filterRecords = $totalRecords = count($data);
    }      
          return datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('check', function ($data) {
              return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="'.$data->id.'">';
            })
            ->addColumn('application_id', function ($data) {
                    return $data->getBenidAttribute();
            })
            ->addColumn('ben_id', function ($data) {
                    return $data->id;
            })
            ->addColumn('ben_name', function ($data) {
                    return $data->getName();
            })
            ->addColumn('benf_name', function($data){
                return $data->getFatherName();
            })
            ->addColumn('old_beneficiary_id', function ($data) {
                      return $data->old_beneficiary_id;
                    })
            ->addColumn('bank_ifsc', function ($data) {
                      return $data->bank_ifsc;
                    })
            ->addColumn('bank_code', function ($data) {
                      return $data->bank_code;
              })
              ->addColumn('village_town_city', function ($data) {
                    return $data->village_town_city ;
              })
              ->addColumn('action', function ($data) {
                      $val = '';
                      //'<div class="btn-group" role="group" >';
                      $val = $val . '<button class="btn btn-primary ben_view_button">View</button>';
                      $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
                      // $val = $val . '<button class="btn btn-success ben_edit_approve_button">Edit</button>';
                      $val = $val . '<button class="btn btn-success ben_selective_edit_approve_button">Edit&Approve</button>';
                      //$val = $val . '</div>';
                      return $val;
                  })
              ->rawColumns(['check','ben_id','id','ben_name','old_beneficiary_id','bank_ifsc','bank_code','village_town_city','action'])
              ->make(true); 

      }
      return view('lpp-singlestep.index')->with('district_name',$district_name)->with('district_code',$district_code); 
    }  
    
    public function editBeneficiary(Request $request)
    {
      return redirect("/")->with('danger', 'User Disabled');
      $this->validate($request, [
        'ben_fname' => 'required|string|max:200',            
        'ben_mname' => 'string|nullable|max:200',
        'ben_lname' => 'string|nullable|max:200',
        'ben_bank' => 'required|string|max:200',
        'ben_mobile' => 'numeric|digits:10',
        'ben_dob' => 'required', 
        'ben_age' => 'numeric|required',       
        'ben_bank_branch' => 'required|string|max:200',
        'ben_bank_account' => 'required|numeric',
        'ben_bank_ifsc' => 'required|string|max:11',
      ]);

      $input = [
        'ben_fname' =>$request->ben_fname,
        'ben_mname' =>$request->ben_mname,
        'ben_lname' =>$request->ben_lname,
        'dob' =>$request->ben_dob,
        'ben_age' =>$request->ben_age,
        'mobile_no' =>$request->ben_mobile,
        'bank_name'  =>$request->ben_bank,
        'branch_name'   =>$request->ben_bank_branch,
        'bank_code'    =>$request->ben_bank_account,
        'bank_ifsc'   =>$request->ben_bank_ifsc, 
        'next_level_role_id'=>0,
        ];

      $scheme_id = $request->scheme;
      $ben_id = $request->id;
      $modelName = $this->getModelName($scheme_id);
      
      $modelName::find($ben_id)->update($input);
    }
    
    public function bulkApprove(Request $request)
    {
      return redirect("/")->with('danger', 'User Disabled');
	      set_time_limit(0);

        $user_id = AuthChecker::getUserId();

        $inputs_json = $request->approvalcheck;
        $scheme = $request->scheme;
        $inputs = json_decode($inputs_json, true);

        $model_name = $this->getModelName($scheme);
        DB::beginTransaction();
        try{
          foreach($inputs as $input){  
            
            $input_update = ['next_level_role_id' => '0']; 
            $model_name::find($input)->update($input_update);   
      
          }
        }catch(\Exception $e){
          DB::rollback();
        } 
        DB::commit();
      //});
        return count($inputs);
    }

    public function rejectApplication(Request $request)
    {
      return redirect("/")->with('danger', 'User Disabled');
      $ben_id = $request->ben_id;
     
      // $scheme_id = $request->session()->get('scheme_id');
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      
      $scheme = $request->scheme;
      $model_name = $this->getModelName($scheme);

      $role_id=$request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();
     
      //$reject_reason = $request->reject_reason;
      DB::beginTransaction();
      try{
        $input_update = ['next_level_role_id' => -1,'is_rejected' => 1,'is_approved' => 2,'is_verified' => 2]; 
        $model_name::find($ben_id)->update($input_update);
        
      }catch(\Exception $e){
        DB::rollback();
      } 
      DB::commit();
    }
    

    public function printSingleBeneficiary(Request $request)
    {
        $ben_id = $request->ben_id; 
        
        $ben = PensionSc::where('id',$ben_id)->first();
        $localBody="";
        if($ben->rural_urban=='Rural'){
          $localBody = GP::where('gram_panchyat_code',$ben->gp_code)->pluck('gram_panchyat_name');
        }else{  
          $localBody = Ward::where('urban_body_ward_code',$ben->ward_code)->pluck('urban_body_ward_name');
        }
        return view('lpp-singlestep.print_ben_dtl')->with('ben', $ben)
                                            ->with('localBody', $localBody);
    }

    public function getStatusCode(Request $request)
    {
      $statusCode = StatusCode::select('code','message')->where('code','>',5)->get();
      return $statusCode;
    }
   
    //Get Filter Dropdown
    public function getLocalBody(Request $request)
    {
      //UrbanBody/Taluka
      $urban_rural = $request->urban_rural;
      $district_code = $request->district_code;

      if($urban_rural == 1){
        $body = UrbanBody::where('district_code', '=', $district_code)->get(['urban_body_code AS id', 'urban_body_name AS name']);
      }else{
        $body = Taluka::where('district_code', '=', $district_code)->get(['block_code AS id', 'block_name AS name']);
      }
      return response()->json($body);

    }

    public function applicationeditview(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $id=$request->id; 
        
        //echo "scheme_id".$scheme_id;die();
        $scheme_id = $request->scheme_id;
        //$row = '';
        $row=array();
        $modelName = $this->getModelName($scheme_id);
        
        $row = $modelName::find($id);
           
        
        $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);
       
        return view('lpp-singlestep/pension_edit', ['row' => $row, 'districts' => $districts , 'scheme_id'=>$scheme_id]);
    }


    public function applicationupdate(Request $request)
    {
      return redirect("/")->with('danger', 'User Disabled');
       
        $base_url= url('/');
        $id=$request->id; 
        $scheme_id = $request->scheme_id;
        $this->validateInput($request, $scheme_id); 

        $modelName = $this->getModelName($scheme_id);
        $row = $modelName::find($id);
        
        $social_security_pension="";
        $receive_pension="";
        if($request->receive_pension !="")
        {
            $receive_pension = implode(',', $request->receive_pension);

        }

        if($request->social_security_pension !="")
        {
            $social_security_pension = implode(',', $request->social_security_pension);
        }    
    
        $block_ulb_name="";
        $gp_ward_name="";

        if($request->urban_code == 1){
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


            $block_ulb_name = $block_ulb->urban_body_name;
            $gp_ward_name   = $gp_ward->urban_body_ward_name;
        }
        else
        {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

            $block_ulb_name = $block_ulb->block_name;
            $gp_ward_name   = $gp_ward->gram_panchyat_name;
        }    
        $assembly = Assembly::where('ac_no','=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if(trim($request->marital_status)!="Married"){
            $request->spouse_first_name="";
            $request->spouse_middle_name="";
            $request->spouse_last_name="";
        }

        $input = [
            //'name' => $request['name']
        'ben_fname' =>$request->first_name,
        'ben_mname' =>$request->middle_name,
        'ben_lname' =>$request->last_name,
        'gender'=>$request->gender,
        'dob'=>$request->dob,
        'ben_age'=>$request->txt_age,

        'father_fname' =>$request->father_first_name,
        'father_mname' =>$request->father_middle_name,
        'father_lname' =>$request->father_last_name,
        'mother_fname' =>$request->mother_first_name,
        'mother_mname' =>$request->mother_middle_name,
        'mother_lname' =>$request->mother_last_name,
        'caste'=>$request->caste_category,
        'marital_status'=>$request->marital_status,

        'spouse_fname' =>$request->spouse_first_name,
        'spouse_mname' =>$request->spouse_middle_name,
        'spouse_lname' =>$request->spouse_last_name,
        //'bpl_y_n' =>$request->if_bpl,
        'bpl_seq_no' =>$request->bpl_seq_no,        
        'bpl_id_no' =>$request->bpl_id_no,
        'bpl_total_score' =>$request->bpl_total_score,
        'mothly_income' =>$request->monthly_income,

        'receive_pension'=>$receive_pension,
        'social_security_pension' => $social_security_pension,

        'ration_card_cat' => $request->ration_card_cat,
        'ration_card_no'  =>$request->ration_card_no,
        'ahl_tin'  =>$request->ahl_tin,
        'aadhar_no'  =>$request->aadhar_no,
        'epic_voter_id'  =>$request->epic_voter_id,
        'pan_no'  =>$request->pan_no,



        'dist_code' =>$request->district,
        'assembly_code'  =>$request->asmb_cons,
        'assembly_name' => $assembly_name,
        'rural_urban_id' => $request->urban_code, 
        'police_station'  =>$request->police_station,
        'block_ulb_code'  =>$request->block,
        'block_ulb_name' =>$block_ulb_name,
        'gp_ward_code' =>$request->gp_ward,
        'gp_ward_name' =>$gp_ward_name,
        'village_town_city'  =>$request->village,
        'house_premise_no'  =>$request->house,
        'post_office'  =>$request->post_office,
        'pincode' =>$request->pin_code,
        'residency_period' =>$request->residency_period,
        'mobile_no'  =>$request->mobile_no,
        'email' =>$request->email,



        'bank_name'  =>$request->name_of_bank,
        'branch_name'   =>$request->bank_branch,
        'bank_code'    =>$request->bank_account_number,
        'bank_ifsc'   =>$request->bank_ifsc_code, 
        'nominate_name' => $request->nominate_name,
        'nominate_address' => $request->nominate_address,
        'nominate_relationship' => $request->nominate_relationship,
        'next_level_role_id'=>0,
        ];

           
        $modelName::where('id', $id)->update($input);
             

        return redirect("lpp-verify/".$scheme_id)->with('success', 'Application Updated Successfully')
        ->with('id',  $id);
       
    }
    private function validateInput($request,$scheme_id) {

      $singleArray = array();
      $nicenameArray = array();
      $customMessage = array();
     

      $this->validate($request, array_merge([
          //'first_name' => 'required|string|max:200',
          'first_name' => 'required|string|max:200',            
          'middle_name' => 'string|nullable',
          'last_name' => 'required|string|max:200',
          'gender' => 'required',
         // 'dob' => '',
          'txt_age' => 'required|numeric',

          'father_first_name' => 'required|string|max:200',
          'father_middle_name' => 'string|nullable',
          'father_last_name' => 'required|string|max:200',
          'mother_first_name' => 'required|string|max:200',
          'mother_middle_name' => 'string|nullable',
          'mother_last_name' => 'required|string|max:200',
          'caste_category' => 'required',
          'marital_status' => 'required',

          'spouse_first_name' => 'string|nullable',
          'spouse_middle_name' => 'string|nullable',
          'spouse_last_name' => 'string|nullable',
          // 'if_bpl' => ,
          'bpl_seq_no' => 'string|nullable|max:12',        
          'bpl_id_no' => 'string|nullable|max:12',
          'bpl_total_score' => 'integer|nullable',
          'monthly_income' => 'required|numeric|between: 0.00,999999.99',


          'ration_card_cat' => 'required|string',
          'ration_card_no' => 'required|string|max:11',
          
          'ahl_tin' => 'string|nullable|max:100',
          'aadhar_no' => 'numeric|digits:12|nullable',
          'epic_voter_id' => 'required|string|max:20',
          'pan_no' => 'string|nullable|max:12',



        //  'district' => 'string',
          'asmb_cons' => 'required|string',
          'police_station' => 'required|string',
          //'block' => 'max:200',
         // 'gp_ward' => 'max:200',
          'village' => 'required|string|max:300',
          'house' => 'string|nullable',
          'post_office' => 'required|string',
          'pin_code' => 'required|numeric|digits:6',
          'residency_period' => 'required|integer',
          'mobile_no' => 'required|numeric|digits:10',        
          'email' => 'string|email|nullable',



          'name_of_bank' => 'required|string|max:200',
          'bank_branch' => 'required|string|max:200',
          'bank_account_number' => 'required|numeric',
          'bank_ifsc_code' => 'required|string|max:11',         

        
          
      ],$singleArray),$customMessage,$nicenameArray);
  }

  public function report($scheme,$approved_rejected)
  {
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id','=',$user_id)->first();
      // echo "<pre>";print_r($duty);die();
      $district_code=$duty->district_code;
      $district_name=District::where('district_code',$district_code)->pluck('district_name')->first();
      $scheme_name = $this->getSchemeName($scheme);
      $app_rej=0;
      if($approved_rejected == 'R'){
        $app_rej = -1;
      }
      return view('lpp-singlestep.report')->with('district_name',$district_name)->with('district_code',$district_code)->with('scheme',$scheme)->with('scheme_name',$scheme_name)
		->with('approved_rejected',$app_rej);

  }
  public function getProcessedData(Request $request){
    //DB::enableQueryLog();
    if(request()->ajax())
    {
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id','=',$user_id)->first();
      $district_code=$request->level1;
      $district_name=$request->level2;
      $serachvalue = $request->search['value'];
      $scheme = $request->scheme;

      $model_name = $this->getModelName($scheme);

      //Urban/Rural
      $level = $duty->is_urban;

      $flag=1;
      $totalRecords = 0;
      $data = array(); 
      
  // WORKING QUERY
        
      $limit = $request->input('length');
      $offset = $request->input('start');
      
      //approved_rejected=0 is Approved, -1 is Rejected
      $approved_rejected = $request->approved_rejected;

      //print_r($duty);

      //echo "ModelName: ".$model_name;
      //echo "Approved_Rejected: ".$approved_rejected;

      $condition = array();

        if(!empty($district_code)){
          $condition["dist_code"]=$district_code;
        }
        if(!empty($level)){
          //'Rural'
          if($level == 2){
            if(!empty($duty->taluka_code)){
              $condition["block_ulb_code"]=$duty->taluka_code;
            }
          }
          //'Urban'
          if($level == 1){
            if(!empty($duty->urban_body_code)){
              $condition["created_by_local_body_code"]=$duty->urban_body_code;
            }
          }
        }
        //$query = $query.$mainquery;
        if(empty($serachvalue)){      
              $data = $model_name::where($condition)->where('next_level_role_id',$approved_rejected)->orderBy('id','DESC')->offset($offset)->limit($limit)->get();
              $totalRecords = $model_name::where($condition)->where('next_level_role_id',$approved_rejected)->count();
              $filterRecords = count($data);
        }else{
              //$query = $query." and id='".$serachvalue."'";
                  
              $data = $model_name::where('id', $serachvalue)->where('next_level_role_id',$approved_rejected)->get();
              $filterRecords = $totalRecords = count($data);
                //$data = DB::connection('pgsql2')->select($query);
                //$totalRecords = count($data);
        }      
        return datatables()
          ->of($data)
          ->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('application_id', function ($data) {
                  return $data->getBenidAttribute();
          })
          ->addColumn('ben_id', function ($data) {
                  return $data->id;
          })
          ->addColumn('ben_name', function ($data) {
                  return $data->getName();
          })
          ->addColumn('ben_fname', function($data){
              return $data->getFatherName();
          })
          ->addColumn('old_beneficiary_id', function ($data) {
                    return $data->old_beneficiary_id;
                  })
          ->addColumn('bank_ifsc', function ($data) {
                    return $data->bank_ifsc;
                  })
          ->addColumn('bank_code', function ($data) {
                    return $data->bank_code;
            })
            ->addColumn('village_town_city', function ($data) {
                  return $data->village_town_city ;
            })
            ->addColumn('action', function ($data) {
              $val = '';
              //'<div class="btn-group" role="group" >';
              $val = $val . '<button class="btn btn-primary ben_view_button">View</button>';
              // $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
              return $val;
          })
            ->rawColumns(['ben_id','id','ben_name','old_beneficiary_id','bank_ifsc','bank_code','village_town_city','action'])
            ->make(true); 

    }
    return view('lpp-singlestep.report')->with('district_name',$district_name)->with('district_code',$district_code); 
  }
}
