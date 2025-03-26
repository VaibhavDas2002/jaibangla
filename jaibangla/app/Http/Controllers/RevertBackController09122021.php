<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\MapLavel;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevertBackController extends Controller
{
    public function shemeSelection(Request $request){
     /*echo "<pre>";  
     //print_r($data);
     $bodyCode = $request->session()->get('bodyCode');
     echo $bodyCode;
     echo "</pre>";
     die();*/

     if(Auth::user()->designation_id == "Verifier"){
        return view('scheme-selection-revert/main');
     }else{
        return redirect("/")->with('success', 'UnAuthorized');
     }

     
    }

    public function shemeSessionCheck(Request $request){

      $user_id = Auth::user()->id;
      $scheme_id=0;
      $ben_table="";

       if($request->get('pr1')){
          if($request->get('pr1')=="sc"){
            $scheme_id=3;
            $ben_table="PensionSc";
                  
          }else if($request->get('pr1')=="st"){
            $scheme_id=1;            
            $ben_table="PensionSt";
          
          }else if($request->get('pr1')=="manabik"){
            $scheme_id=2;
            $ben_table="Manabik";
          }
          else if($request->get('pr1')=="oap_wcd"){
            $scheme_id=10;
            $ben_table="PensionOAPWCD";
          }
          else if($request->get('pr1')=="wp_wcd"){
            $scheme_id=11;
            $ben_table="PensionWPWCD";
          }
           else if($request->get('pr1')=="foap"){
            $scheme_id=13;
            $ben_table="PensionOAPFarmer";
          }
          else if($request->get('pr1')=="fisherman"){
            $scheme_id=5;
            $ben_table="PensionFisherman";
          }
          else if($request->get('pr1')=="weaver"){
            $scheme_id=6;
            $ben_table="PensionMSME";
          }
          else if($request->get('pr1')=="lpp"){
            $scheme_id=9;
            $ben_table="PensionLPPPensioner";
          }else if($request->get('pr1')=="oap_st"){
            $scheme_id=19;
            $ben_table="PensionOAPST";
          }
      }else{
        return redirect("scheme-selection/main");
      }

      $is_active=0;
      $roleArray=$request->session()->get('role');    
      /*echo "<pre>";  
                print_r($roleArray);
                echo "</pre>";
                die();  */  
      foreach ($roleArray as $roleObj) {           
        if($roleObj['scheme_id'] == $scheme_id){
          $is_active=1;
          $request->session()->put('level', $roleObj['mapping_level']);
          $request->session()->put('distCode', $roleObj['district_code']); 
          $request->session()->put('scheme_id',$scheme_id);
          $request->session()->put('ben_table',$ben_table);        
          $request->session()->put('is_first',$roleObj['is_first']); 
          $request->session()->put('is_urban',$roleObj['is_urban']); 
          $request->session()->put('role_id',$roleObj['id']);                 
          if($roleObj['is_urban']==1){              
              $request->session()->put('bodyCode', $roleObj['urban_body_code']);
          }else{
              $request->session()->put('bodyCode', $roleObj['taluka_code']);
          }     
          break;
        }

      }

      if($is_active==1){        
          
          return true;
  
      }if($is_active==0){

          return redirect("/")->with('success', 'User Disabled');
      }else{

          return redirect("/")->with('success', 'User Disabled');
      }
      return view('scheme-selection/main');
    }

    public function applicationdetails(Request $request){
    	$this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
    	$user_id = Auth::user()->id;

        $schemeObj = DB::table('public.m_scheme')->where('id', $scheme_id)->first();

    	if($is_first){   // First Level Verifier   	

	        if($mappingLevel=="State"){
	           $level="State";
	        }else if($mappingLevel=="District"){

	           //$district_code = $duty->district_code;
               $appPrefix = "App";
               $modelName=$appPrefix . "\\" . $ben_table;
	           $rows = $modelName::where('lot_generated',-1)
             ->where('next_level_role_id',0)             
             ->where('scheme_id',$scheme_id)
             ->where('created_by_dist_code',$district_code)
             ->orderBy('id', 'desc')
             ->where('bank_edited',0) //Temporary Code
             ->paginate(10);
	    		return view('pension_list',['nhm_employee_details' => $rows]); 

	        }else if($mappingLevel=="Subdiv"){
	           
	            if($is_urban==1){

                $duty_level="SubdivVerifier";
                $urban_bodys=UrbanBody::where('sub_district_code',$urban_body_code)->select('urban_body_code','urban_body_name')->get();

                if(request()->ajax())
                {  
                    if(!empty($request->filter_1)  && empty($request->filter_2) )
                    { //dd($urban_body_codes);     
                        $body_code = $request->session()->get('bodyCode');
                        $appPrefix = "App";
                        $modelName=$appPrefix . "\\" . $ben_table;
                        $data=$modelName::where('next_level_role_id',0)
                        	    ->where('lot_generated',-1)                            
                              ->where('scheme_id',$scheme_id)
                              ->where('created_by_local_body_code',$body_code)
                              ->where('block_ulb_code', $request->filter_1)
                              //->where('gp_ward_code', $request->filter_2)
                              ->where('bank_edited',0) //Temporary Code
                              ->orderBy('id', 'desc')
                              ->get();       
                    }
                    elseif(!empty($request->filter_1) && !empty($request->filter_2))
                    {
                          
                        $body_code = $request->session()->get('bodyCode');
                        $appPrefix = "App";
                        $modelName=$appPrefix . "\\" . $ben_table;
                        $data=$modelName::where('next_level_role_id',0)
                        	   ->where('lot_generated',-1)                             
                              ->where('scheme_id',$scheme_id)
                              ->where('created_by_local_body_code',$body_code)
                              ->where('block_ulb_code', $request->filter_1)
                              ->where('gp_ward_code', $request->filter_2)
                              ->where('bank_edited',0) //Temporary Code
                              ->orderBy('id', 'desc')
                              ->get();
                    }
                    else
                    {
                        $body_code = $request->session()->get('bodyCode');
                        $appPrefix = "App";
                        $modelName=$appPrefix . "\\" . $ben_table;
                        $data=$modelName::where('next_level_role_id',0)
                        	  ->where('lot_generated',-1)
                            ->where('scheme_id',$scheme_id)
                              ->where('created_by_local_body_code',$body_code)
                              ->where('bank_edited',0) //Temporary Code
                              ->orderBy('id', 'desc')
                              ->get();
                    }
                    return datatables()->of($data)
                           ->addColumn('view', function ($data) {
                            return '<a href="'. route('revert.editApplicantDetails', $data->id) .'" class="btn btn-ls btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                            })
                            ->addColumn('id', function ($data) {
                                return $data->getBenidAttribute();
                            })
                            ->addColumn('name', function ($data) {
                                return $data->getName();
                            })
                            ->rawColumns(['view','id','name'])
                            ->make(true); 
                     }
                    return view('linelisting_reverted_subdiv')->with('duty_level',$duty_level)->with('urban_bodys',$urban_bodys)->with('dist_code',$district_code)->with('scheme_name', $schemeObj->scheme_name);
                    
	            }else
	            { 
	               	               
                    //$taluka_code = $duty->taluka_code;
                    $appPrefix = "App";
                    $modelName=$appPrefix . "\\" . $ben_table;
	                $rows = $modelName::where('next_level_role_id',0)
                      ->where('lot_generated',-1)
                      ->where('scheme_id',$scheme_id)
	                		->where('created_by_local_body_code',$taluka_code)
                      ->where('bank_edited',0) //Temporary Code
	                		->orderBy('id', 'desc')->paginate(10);
	    			return view('pension_list',['nhm_employee_details' => $rows]);    
	            }    
	        }else if($mappingLevel=="Block")
	        {
                	$duty_level="BlockVerifier";
                    //$district_code=$duty->district_code;
                    //$taluka_code = $duty->taluka_code;
    
                    $gps=GP::where('block_code',$taluka_code)->select('gram_panchyat_code','gram_panchyat_name')->get();
                    if(request()->ajax())
                    {  
                        if(!empty($request->filter_1))
                        {

                            $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',0)
                             ->where('lot_generated',-1)                             
                             ->where('scheme_id',$scheme_id)
                             ->where('created_by_local_body_code',$body_code)
                              //->where('block_ulb_code', $request->filter_2)
                             ->where('gp_ward_code', $request->filter_1)
                             ->where('bank_edited',0) //Temporary Code
                             ->get();
                        }

                        else
                        {   
                            $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',0)
                             	    ->where('lot_generated',-1)
                                  ->where('scheme_id',$scheme_id)                               
                                  ->where('created_by_local_body_code',$body_code)
                                  ->where('bank_edited',0) //Temporary Code
                                  ->get();   
                        }
                        return datatables()->of($data)
                            ->addColumn('view', function ($data) {
                                return '<a href="'. route('revert.editApplicantDetails', $data->id) .'" class="btn btn-ls btn-primary"><i class="glyphicon glyphicon-edit"></i></a>';
                                })
                            ->addColumn('id', function ($data) {
                                return $data->getBenidAttribute();
                                })
                            ->addColumn('name', function ($data) {
                                return $data->getName();
                                })
                            ->rawColumns(['view','id','name'])
                            ->make(true); 
                     }
                    return view('linelisting_reverted')->with('duty_level',$duty_level)
						   ->with('gps',$gps)
                           ->with('dist_code',$district_code)
                           ->with('scheme_name', $schemeObj->scheme_name);       

            }

            //return view('pension_list',['nhm_employee_details' => $rows]);
    	}
     //  else{//approver

    	// 	//$mappingLevel=$duty->mapping_level;
	    //     if($mappingLevel=="State"){
	    //        $duty_level="State";
	    //     }else if($mappingLevel=="District")
	    //     {
     //        	$duty_level='DistrictApprover';

     //        	$levels = [
     //                     2 => 'Rural',
     //                     1 => 'Urban',
     //            ];
     //        	//$district_code = $duty->district_code;
     //        	if(request()->ajax())
     //         	{  
     //              if(!empty($request->filter_1) && !empty($request->filter_2))
     //              {
     //                $body_code = $request->session()->get('bodyCode');
     //                $appPrefix = "App";
     //                $modelName=$appPrefix . "\\" . $ben_table;
     //                $data=$modelName::where('next_level_role_id',$role_id)
     //                          ->where('created_by_dist_code',$district_code)
     //                          ->where('block_ulb_code', $request->filter_2)
     //                          ->get();
     //              }
     //            else
     //            {
     //              $body_code = $request->session()->get('bodyCode');
     //              $appPrefix = "App";
     //              $modelName=$appPrefix . "\\" . $ben_table;
     //              $data=$modelName::where('next_level_role_id',$role_id)
     //                         ->where('created_by_dist_code',$district_code)
     //                         ->orderBy('id', 'desc')
     //                         ->get(); 
     //            }
     //            return datatables()->of($data)
     //                ->addColumn('view', function ($data) {
     //                    return '<a href="'. route('nhmemployee.showApplicantDetails', $data->id) .'" class="btn btn-ls btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
     //                    })
     //                ->addColumn('check', function ($data) {
     //                    return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="'.$data->id.'">';
     //                    })
     //                ->addColumn('id', function ($data) {
     //                            return $data->getBenidAttribute();
     //                            })
     //                ->addColumn('name', function ($data) {
     //                            return $data->getName();
     //                            })
     //                ->rawColumns(['view','check','id','name'])
     //                ->make(true); 
     //         }
                   
     //        return view('linelisting_reverted')->with('duty_level',$duty_level)->with('levels',$levels)
     //                                            ->with('dist_code',$district_code);
     //        }else
     //        {
	    //         if($is_urban==1){
	    //             $duty_level="ULB";
	    //         }else{
	    //             $duty_level="Block";
     //                $appPrefix = "App";
     //                $modelName=$appPrefix . "\\" . $ben_table;
                  
	    //             $rows = $data=$modelName::where('next_level_role_id',$role_id)->where('created_by_local_body_code',$taluka_code)->orderBy('id', 'desc')->paginate(10);
	    // 			//return view('pension_list',['nhm_employee_details' => $rows]);
	    // 			return view('linelisting_reverted',['datas' => $rows,'dist_code' => $district_code]);
	    //         }     
	    //     }
     //      //  return view('linelisting_approved',['datas' => $rows]);
    	// }
    }

    public function editApplicantDetails(Request $request){
        $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
        $user_id = Auth::user()->id;
        $body_code = $request->session()->get('bodyCode');
        $id=$request->id;
        $appPrefix = "App";
        $modelName=$appPrefix . "\\" . $ben_table;
        $single_employee_details = $modelName::where('id','=',$id)
                             ->where('scheme_id',$scheme_id) 
                             ->where('next_level_role_id',0)
                             ->where('lot_generated',-1)
                             ->where('created_by_local_body_code',$body_code)
                             ->where('bank_edited',0) //Temporary Code
                             ->first();
                             
                             $query="select  max(lot_no),ifms_status from (select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details where pension_id=".$id." group by ifms_status
                             union all 
                             select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details_report where pension_id=".$id." group by ifms_status) 
                             p group by ifms_status order by max(lot_no) desc";
                             $invalid_status='';
                       $query_res= DB::select($query);
                 if(!empty($query_res[0]->ifms_status)){
                   $invalid_status=$query_res[0]->ifms_status;
                 }
        

        return view('pension-revert-edit', ['row' => $single_employee_details,'invalid_status'=>$invalid_status]);
        // return view('pension_view_details', ['row' => $single_employee_details,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);

    }

    public function update(Request $request){
    	$this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
        $user_id = Auth::user()->id;
        $body_code = $request->session()->get('bodyCode');
        if($scheme_id==1){
         $pr1='st';
        }elseif($scheme_id==2){
         $pr1='manabik';
        }else{
         $pr1='st';
        }
        //$body_code = $request->session()->get('bodyCode');
        $id=$request->benId;
        $bank_branch=$request->branch_name;
        $bank_code=$request->bank_account_number;
        $bank_ifsc=$request->bank_ifsc;
        $bank_name=$request->bank_name;
        $mobile_no=$request->mobile_no;
        //$Verified="Verified";
        //$Rejected=1;
        //$comments=$request->comments;
        
        //$scheme_id = 3;
    	$user_id = Auth::user()->id;    	
    	$duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
    	$role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();

    	$this->validateInput($request);
    	if ($_POST['submit'] == 'Update') {
            $benDetails = BeneficiaryPensions::where('id', $id)->first();
            $old_bank_name = $benDetails->bank_name;
            $old_branch_name = $benDetails->branch_name;
            $old_bank_ifsc = $benDetails->bank_ifsc;
            $old_bank_code = $benDetails->bank_code;
            
            $input = [];
            $old_value = [];
            $input_new = [];

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
            $updateBenObj->remarks = 'IFMS Failed Update Bank Details';
            $updateBenObj->old_data = json_encode($old_value);
            $updateBenObj->new_data = json_encode($input_new);
            $updateBenObj->user_id = $user_id;
            $updateBenObj->update_code = 7;
            $updateBenObj->save();

            $input = ['bank_name' => $bank_name,'branch_name' => $bank_branch,'bank_code'=>$bank_code,'bank_ifsc'=>$bank_ifsc,'bank_edited' => 1, 'mobile_no' => $mobile_no];  
            $appPrefix = "App";
            $modelName=$appPrefix . "\\" . $ben_table;          
           	$is_status_updated=$modelName::where('id', $id)
                              ->where('scheme_id',$scheme_id) 
                              ->where('next_level_role_id',0)
                              ->where('lot_generated',-1)
                             ->where('created_by_local_body_code',$body_code)
                             ->where('bank_edited',0) //Temporary Code
                             ->update($input);
            DB::commit();            
            if($is_status_updated){
            	return redirect('scheme-selection-revert')->with('success','Bank Details Updated Succesfully!')->with('pr1',$pr1);            
            }
            else{
                DB::rollback();
                return redirect('scheme-selection-revert')->with('error','Oops! Bank Details Updation Failed.')->with('pr1',$pr1);
            }
   		 }
    }

    private function validateInput($request) {
        $this->validate($request, [
            'mobile_no' => 'required:|regex:/[0-9]{10}/',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'bank_account_number' => 'required|numeric|between:00000000000000000000,9999999999999999999',
            'bank_ifsc' => 'required|max:20',
            
        ]);
    } 
}
