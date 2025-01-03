<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthChecker;


class UpdatebenDetailsController extends Controller
{
    public function __construct(){
    	//$this->middleware('auth');
    }
    
    public function index(){
        $user_id = AuthChecker::getUserId();
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        /*$scheme = Configduty::leftJoin('m_scheme','m_scheme.id','duty_assignement.scheme_id')
                ->where('duty_assignement.user_id',$user_id)
                ->where('duty_assignement.is_active',1)
                ->where('m_scheme.is_active',1)->get(['duty_assignement.scheme_id as scheme_id']);*/
        $scheme = Configduty::select('scheme_id')->where('user_id',$user_id)->where('is_active',1)->get();
        return view('update-ben-details/index',['schemes' => $scheme, 'mapping_level' => $mapObj->mapping_level]);
    }
    
    public function searchByBenName(Request $request){
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id;
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        $dist_code = $mapObj->district_code;
        if ($mapObj->is_urban == 1) {
            $block_ulb = $mapObj->urban_body_code;
        }
        else {
            $block_ulb = $mapObj->taluka_code;
        }   
        $map_level = $mapObj->mapping_level;

        //$ben_id = $request->ben_id;
        /*Application Id*/
        if (strlen($request->ben_id) == 20) {
            $str = substr($request->ben_id, -14);
            $ben_id = ltrim($str, "0");
        }
        else {
            $ben_id = $request->ben_id;
        }
        //print $ben_id;die();
        $scheme_id = $request->scheme_id;

        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)){
              $schema_name = 'pension';
            }
            $table_name =  $schema_name . '.beneficiary';
        }
        else {
            $table_name =  'pension.beneficiary';
        }

        if ($designation == 'Approver') {
            $type = $request->select_type;
            $first_name = strtoUpper(trim($request->ben_fname));
            $middle_name = strtoUpper(trim($request->ben_mname));
            $last_name = strtoUpper(trim($request->ben_lname));
            $rural_urban = $request->is_rural_urban;
            $block_ulb = $request->block_ulb;
            if ($type == 'b_id' && !is_null($ben_id)) {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code." ";
                if (!is_null($ben_id)) {
                    $query .= " and id = ".$ben_id."";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
                if (!is_null($block_ulb)) {
                    $query .= " and block_ulb_code = ".$block_ulb."";
                }
            }
            else {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code." ";
                if ($first_name!='') {
                    $query .= " and ben_fname ILIKE '".$first_name."%' ";
                }
                if ($middle_name != '') {
                    $query .= " and ben_mname ILIKE '".$middle_name."%' ";
                }
                if ($last_name != '') {
                    $query .= " and ben_lname ILIKE '".$last_name."%' ";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
                if (!is_null($block_ulb)) {
                    $query .= " and block_ulb_code = ".$block_ulb."";
                }   
            }
            // print $query;die();
            $data = DB::connection('pgsql_mis')->select($query);
            // print_r($result);die();
            return datatables()->of($data)
            ->addColumn('ben_name', function ($data) {
              return $data->ben_fname.' '.$data->ben_mname.' '.$data->ben_lname;
            })
            ->addColumn('f_name', function ($data) {
              return $data->father_fname.' '.$data->father_mname.' '.$data->father_lname;
            })
            ->addColumn('ration_card', function ($data) {
              return $data->ration_card_cat.' '.$data->ration_card_no;
            })
            ->addColumn('bank_details', function ($data) {
              $html = '';
              $html = '<div align="center" class="text-success"><b>IFSC: '.$data->bank_ifsc.' </b></div>
                    <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: '. $data->bank_code .'</b></div>
                    <div align="center" class="text-danger"><i><b>';
              if ($data->lot_generated == -1) {
                $html .= 'Under IFMS Modification from Block/Sub-Division';
              }
              else if ($data->lot_generated == -2) {
                $html .= 'Under RBI Modification from Block/Sub-Division';
              }
              else if ($data->lot_generated == -3) {
                $html .= 'Under SBI Modification from Block/Sub-Division';
              }
              else{}
                    
              $html .= '</b></i></div';
              return $html;
            })
            ->addColumn('edit', function ($data) {
              $html = '';
              if($data->next_level_role_id == '0'){
                $html = '<form method="POST" action="'. url('edit-details') .'" id="myForm_'.$data->id.'">
                    '. csrf_field() .'
                    <input type="hidden" name="select_item" class="itemUpdate" value="'.$data->id.'">
                    <div>
                        <select class="form-control" name="select_item_update" id="select_item_update_'.$data->id.'" required>
                            <option value="">-- Select --</option>
                            <option value="bank">Update Bank Details</option>
                            <option value="stop_payment">Stop Payment</option>
                        </select>
                        <span id="text_error" class="text-danger"></span>
                    </div>
                    <div align="center" style="margin-top:5px;">
                      <button class="btn btn-info btn-block btn-sm" name="ben_edit" class="ben_edit" value="'.$data->id.'" onclick="editFunction('.$data->id.');">Edit</button>
                    </div>
                </form>';
              }
              else if($data->is_rejected==1){
                $html = '<h5><label class="label label-danger"><b>Inactive Beneficiary</b></lavel></h5>';
              }
              else if(($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0) || (is_null($data->next_level_role_id)))
                $html = '<h5><label class="label label-warning"><b>Under Approval</b></label></h5>';
              else{}
              return $html;
            })
            ->addColumn('action', function ($data) {
              $html = '';
              if($data->next_level_role_id == -98) {
                // $html = '<button class="btn btn-info resume_button" value="'.$data->id.'_'.$data->lot_generated.'">Resume</button>';
                $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
              }
              else if($data->next_level_role_id == 0 and ($data->scheme_id ==8 or $data->scheme_id==9 or $data->scheme_id==17)) {
                // $html = '<a href="'. url('pause-ben-payment/'.$data->id) .'" class="btn btn-success pause_button" onclick="return confirm(\'Are you sure want to pause this beneficiary ?\')"> Pause</a>';
                $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
              }
              else {}
              return $html;
            })
            ->rawColumns(['ben_name','f_name','ration_card','bank_details','edit','action'])
            ->make(true);
            //return view('update-ben-details/ben_search_details',['results' => $result, 'mapping_level' => $map_level, 'designation' => $designation]);
        }       
    }
/*
    public function editBenDetails(Request $request){
    	$id = $request->select_item;
    	$update_info = $request->select_item_update;
    	
    	$ben_details = BeneficiaryPensions::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($ben_details == null ) {
            return redirect()->intended('/update-ben-details');
        }
        return view('update-ben-details/edit_bank_details', ['ben_detail' => $ben_details]);
        
    }*/
    public function editBenDetails(Request $request){
        $id = $request->select_item;
        $update_info = $request->select_item_update;
		//echo $id;
		//echo $update_info;
        
       if($update_info =='bank'){

           /*  $ben_details = BeneficiaryPensions::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($ben_details == null ) {
            return redirect()->intended('/update-ben-details');
        }
        return view('update-ben-details/edit_bank_details', ['ben_detail' => $ben_details]); */

        $ben_details = BeneficiaryPensions::find($id);

            // For Lot generated < 0 Date:15/09/2020 --- re updated on 12-11-2020 upon request to give edit power to Approver.
            if ($ben_details->lot_generated < -10) {
                return view('update-ben-details/edit_bank_details', ['ben_detail' => $ben_details, 'lot_msg' => 'This beneficiary under payment modification']);
            }
            else{
                // Redirect to state list if updating state wasn't existed
                if ($ben_details == null ) {
                    return redirect()->intended('/update-ben-details');
                }
                return view('update-ben-details/edit_bank_details', ['ben_detail' => $ben_details]);
            }

        }


        if($update_info=='stop_payment'){

           //$ben_details = BeneficiaryPensions::where('id','=',$id)->where('next_level_role_id','=',-99);
		   $ben_details = BeneficiaryPensions::find($id);
           $doc_type= DB::select(DB::raw("select * from m_attached_doc where id>=100"));
           
        // Redirect to state list if updating state wasn't existed
        if ($ben_details == null ) {
            return redirect()->intended('/update-ben-details');
        }
		//echo $ben_details->id;
		//echo $ben_details->ben_fname;
        return view('update-ben-details/stop_payment', ['ben_detail' => $ben_details,'doc_type'=>$doc_type]);

        }
        
    }

    public function updateBenDetails(Request $request, $id){
    	//$id = $request->id;

    	$this->validate($request, [
            'bank_name' => 'required|max:200',
            'branch_name' => 'required|max:200',
            'bank_ifsc' => 'required|max:11',
            'bank_code' => 'required|max:20',
            'mobile_no' => 'required|max:10',
            'remarks' => 'required|max:300'
        ]);
        $benDetails = BeneficiaryPensions::where('id',$id)->first();
        
        $new_bank_name = $request['bank_name'];
        $new_branch_name = $request['branch_name'];
        $new_bank_ifsc = trim($request['bank_ifsc']);
        $new_bank_code = trim($request['bank_code']);
        $new_mobile_no = trim($request['mobile_no']);

        // Duplicate Bank Account Check 
        $schemeId = $benDetails->scheme_id;
        if ($schemeId == 2 || $schemeId == 10 || $schemeId == 11) {
            $benDuplicateAcCount = BeneficiaryPensions::whereRaw("trim(bank_ifsc)=trim("."'".$new_bank_ifsc."'".")")->whereRaw("trim(bank_code)=trim("."'".$new_bank_code."'".")")
            ->where('is_rejected',0)
            ->where('scheme_id', $schemeId)->count('id');
            if($benDuplicateAcCount > 0)
            {
                $msg = 'This Bank A/c - '.$new_bank_code.' & IFSC - '.$new_bank_ifsc.' already exist in this scheme';
                return redirect('update-ben-details')->with('message',$msg)->with('id',$id);
            }
        }
        // echo 1;
        // die();
        $old_bank_name = $request['old_bank_name'];
        $old_branch_name = $request['old_branch_name'];
        $old_bank_ifsc = trim($request['old_bank_ifsc']);
        $old_bank_code = trim($request['old_bank_code']);
        $old_mobile_no = trim($request['old_mobile_no']);

        $old_value = [];
        $input = [];
        if ($new_bank_name != $old_bank_name) {
            $old_value['bank_name'] = $old_bank_name;
            $input['bank_name'] = $new_bank_name;
        }
        if ($new_branch_name != $old_branch_name) {
            $old_value['branch_name'] = $old_branch_name;
            $input['branch_name'] = $new_branch_name;
        }
        if ($new_bank_ifsc != $old_bank_ifsc) {
            $old_value['bank_ifsc'] = $old_bank_ifsc;
            $input['bank_ifsc'] = $new_bank_ifsc;
        }
        if ($new_bank_code != $old_bank_code) {
            $old_value['bank_code'] = $old_bank_code;
            $input['bank_code'] = $new_bank_code;
        }
        if ($new_bank_code != $old_bank_code) {
            $old_value['mobile_no'] = $old_mobile_no;
            $input['mobile_no'] = $new_mobile_no;
        }

        if (!empty($old_value) && !empty($input)) {
            $user_id = AuthChecker::getUserId();
        } 
        if (!empty($old_value) && !empty($input)) {
            $updateBenObj = new  UpdateBenDetails();
            $updateBenObj->original_application_id = $benDetails->id;
            $updateBenObj->dist_code = $benDetails->dist_code;
            $updateBenObj->scheme_id = $benDetails->scheme_id;
            $updateBenObj->remarks = $request->remarks;
            $updateBenObj->old_data = json_encode($old_value);
            $updateBenObj->new_data = json_encode($input);
            $updateBenObj->user_id = $user_id;
            $updateBenObj->update_code=1;
            $updateBenObj->save();
        }

		$update_ben = [
			'bank_name' => $request['bank_name'],
        	'branch_name' => $request['branch_name'],
        	'bank_ifsc' => trim($request['bank_ifsc']),
            'bank_code' => trim($request['bank_code']),
            'mobile_no' => trim($request['mobile_no']),
            'bank_edited' => 1
        ];

        $is_saved = BeneficiaryPensions::where('id', $id)->update($update_ben);
        if ($is_saved) {
        	return redirect('update-ben-details')->with('success','Updated Successfully!')->with('id',$id);
        }
    }

    public function loadBlockUlb($rural_urban, $district_code){
        if ($rural_urban == 1) {
        	//Urban
        	$results = UrbanBody::select('urban_body_code as code','urban_body_name as name')->where('district_code',$district_code)->get(['urban_body_code as code','urban_body_name as name']);
        	return response()->json($results);
        }
        elseif($rural_urban == 2) {
        	//Rural
        	$results = Taluka::select('block_code as code','block_name as name')->where('district_code',$district_code)->get(['block_code as code','block_name as name']);
        	return response()->json($results);
        } 
    }
	
   public function stopPayment(Request $request,$id){
        $user_id = AuthChecker::getUserId();
        $doc_types= DB::select(DB::raw("select * from m_attached_doc where id=".$request->stop_payment_reason));
        
        $this->validate($request, [
            'remarks'=>'required',
            'file_stop_payment' => 'mimes:'.$doc_types[0]->doc_type.'|max:'.$doc_types[0]->doc_size_kb.'|nullable'
        ]);

       // $update_info = substr($doc_type->doc_name,7);
		//echo 'what to update '.$update_info;
        $stop_details = BeneficiaryPensions::find($id);
        $remarks=$request->remarks;
        // if($stop_details->scheme_id == 1){
        if($request->hasFile('file_stop_payment')) {
			$input = [];
            $image= $request->file('file_stop_payment'); 
            $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = storage_path('app/stop_payment/'.$stop_details->dist_code.'/scheme_id_'.$stop_details->scheme_id);
            $image->move($destinationPath,$input['imagename']); 
            DB::insert("INSERT INTO pension.ben_docs(
                 ben_id, doc_type_id, doc_name, doc_type_name, is_active, created_at)
                VALUES ( ".$id.",".$doc_types[0]->id.",'https://jaibangla.wb.gov.in/images_stopped/".$input['imagename']."','".$doc_types[0]->doc_name."',true, now());");
        } else { 
               $input['imagename'] = 'noimage.jpg';
            }
            
//shiifted below        DB::insert("INSERT INTO public.update_ben_details(
//             original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code)
//            VALUES (".$id.",".$stop_details->dist_code.",".$stop_details->scheme_id.",".$user_id.",now(),'".$remarks."',2);");
               
        // }

        // if($stop_details->scheme_id == 2){
        //     if($request->hasFile('file_stop_payment')) {
        //     $image= $request->file('file_stop_payment'); 
        //     $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        //     $destinationPath = public_path('/stop_payment_scheme_id_2');
        //     //$image->move($destinationPath,$input['imagename']); 
            
        //     } else { 
        //         $input['imagename'] = 'noimage.jpg';
        //     }
        // }

        // if($stop_details->scheme_id == 3){
        //     if($request->hasFile('file_stop_payment')) {
        //     $image= $request->file('file_stop_payment'); 
        //     $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
        //     $destinationPath = public_path('/stop_payment_scheme_id_3');
        //     //$image->move($destinationPath,$input['imagename']); 
            
        //     } else { 
        //         $input['imagename'] = 'noimage.jpg';
        //     }
        // }
/*
        if (!empty($old_value) && !empty($input)) {
            $updateBenObj = new  UpdateBenDetails();
            $updateBenObj->original_application_id = $benDetails->id;
            $updateBenObj->dist_code = $benDetails->dist_code;
            $updateBenObj->scheme_id = $benDetails->scheme_id;
            $updateBenObj->remarks = $request->remarks;
            $updateBenObj->old_data = json_encode($old_value);
            $updateBenObj->new_data = json_encode($input);
            $updateBenObj->user_id = $user_id;
            $updateBenObj->save();
        }     */   
/*
        DB::table('stop_payment')->insert([
			'id' => $id,
            'ben_fname' => $stop_details->ben_fname,
            'ben_mname' => $stop_details->ben_mname,
            'ben_lname' => $stop_details->ben_lname,
            'bank_name' => $stop_details->bank_name,
            'mobile_no' => $stop_details->mobile_no,
            'branch_name' => $stop_details->branch_name,
            'bank_ifsc' => $stop_details->bank_ifsc,
            'bank_code' => $stop_details->bank_code,
            'file_stop_payment'=> $input['imagename'],
            'remarks'=> $request->remarks,
            'couse_of_stop_payment' =>$request->couse_of_stop_payment
        ]); 
			echo $id;
            echo $stop_details->ben_fname;
            echo $stop_details->ben_mname;
            echo $stop_details->ben_lname;
            echo $stop_details->bank_name;
            echo $stop_details->mobile_no;
            echo $stop_details->branch_name;
            echo $stop_details->bank_ifsc;
            echo $stop_details->bank_code;
            echo $input['imagename'];
            echo $request->remarks;
            echo $request->stop_payment_reason;
			
			echo $stop_details->dist_code;
            echo $stop_details->scheme_id;
            echo $request->remarks;
            echo $user_id;*/
        //echo "<pre>";print_r($data);die;


        //$update_next_val = ['next_level_role_id' => -99];
        $is_update = BeneficiaryPensions::where('id', $id)->update(['next_level_role_id' => -99,'is_rejected' =>1]);

        //$update_is_active = ['is_active' => -99];
       // $is_update_data = DB::table('ben_export')->where('pension_id', $id)->update(['is_active' => DB::raw("is_active - 100")]);
	//DB::table('ifms.transaction_lot_details')->where('pension_id', $id)->update(['is_active' => DB::raw("is_active - 100")]);
		
        //$sbi_trabscation_active = ['is_active' => -99];
       // $sbi_tran_data = DB::table('sbi.transaction_lot_details')->where('pension_id', $id)->update(['is_active' => DB::raw("is_active - 100")]);

        $payment_stop_in_lot=DB::select('SELECT public."payment_adjustment"('.$id.', -99)');

        if ($is_update) {
			$input_json = [];
			$input_json['stop_payment_reason'] = $request->stop_payment_reason;
			$input_json['pension.beneficiary.next_level_role_id'] = '-99';
			//if ($is_update_data) {
			//	$input_json['ben_export.is_active'] = '-99';
			//}
			//if ($sbi_tran_data) {
			//	$input_json['sbi.transaction_lot_details.is_active'] = '-99';
			//}
			
			DB::insert("INSERT INTO public.update_ben_details
			(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
            VALUES (".$id.",".$stop_details->dist_code.",".$stop_details->scheme_id.",".$user_id.",now(),'".$remarks."',2,'".json_encode($input_json)."' );");
            /*
			$updateBenObj = new  UpdateBenDetails();
            $updateBenObj->original_application_id = $id;
            $updateBenObj->dist_code = $benDetails->dist_code;
            $updateBenObj->scheme_id = $benDetails->scheme_id;
            $updateBenObj->remarks = $request->remarks;
            //$updateBenObj->old_data = json_encode($old_value);
            $updateBenObj->new_data = json_encode($input_json);
            $updateBenObj->user_id = $user_id;
            $updateBenObj->save();*/
			
            return redirect('update-ben-details')->with('success','Updated Successfully!')->with('id',$id);
        }
    }

   public function pauseBenPayment($id){
        $user_id = AuthChecker::getUserId();
        $stop_details = BeneficiaryPensions::find($id);
        $is_saved = BeneficiaryPensions::where('id',$id)->update(['next_level_role_id'=>-98]);
        $is_paused = DB::select('SELECT public."payment_adjustment"('.$id.', -98)');

        if ($is_saved) {
                        $input_json = [];
			$input_json['pension.beneficiary.next_level_role_id'] = '-98';
						
			DB::insert("INSERT INTO public.update_ben_details
			(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
            VALUES (".$id.",".$stop_details->dist_code.",".$stop_details->scheme_id.",".$user_id.",now(),'Pause Payment',3,'".json_encode($input_json)."' );");

            return redirect('update-ben-details')->with('success','Pause Successfully.')->with('id',$id);
        }
    }

    public function resumeBenPayment(Request $request){
        $last_yymm = $request->resume_month;
        $id = $request->ben_id;
        $user_id = AuthChecker::getUserId();
        $stop_details = BeneficiaryPensions::find($id);
        if ($request->lot_generate_no < 0) {
            $input = [
                'next_level_role_id' => 0,
                'last_paid_yymm' => $last_yymm
            ];
        $is_saved = BeneficiaryPensions::where('id',$id)->update($input);
        if ($is_saved) {
                        $input_json = [];
			$input_json['pension.beneficiary.next_level_role_id'] = '0';
                        $input_json['pension.beneficiary.last_paid_yymm'] = $last_yymm;
						
			DB::insert("INSERT INTO public.update_ben_details
			(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
            VALUES (".$id.",".$stop_details->dist_code.",".$stop_details->scheme_id.",".$user_id.",now(),'Resume Payment',4,'".json_encode($input_json)."' );");

            return redirect('update-ben-details')->with('success','Resume Successfully. This beneficiary is under modification. Please contact concerned Block Verifier.')->with('id',$id);
        }

        }
        else {
            $input = [
                'next_level_role_id' => 0,
                'lot_generated' => 0,
                'last_paid_yymm' => $last_yymm
            ];
            $is_saved = BeneficiaryPensions::where('id',$id)->update($input);
            if ($is_saved) {
                        $input_json = [];
			$input_json['pension.beneficiary.next_level_role_id'] = '0';
                        $input_json['pension.beneficiary.lot_generated'] = '0';
                        $input_json['pension.beneficiary.last_paid_yymm'] = $last_yymm;
						
			DB::insert("INSERT INTO public.update_ben_details
			(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
                 VALUES (".$id.",".$stop_details->dist_code.",".$stop_details->scheme_id.",".$user_id.",now(),'Resume Payment',4,'".json_encode($input_json)."' );");

                   
                return redirect('update-ben-details')->with('success','Resume Successfully.')->with('id',$id);
            }
        }
        //print_r($input);die();
        
    }
	
    public function stopPaymentReport(Request $request){
        $user_id = AuthChecker::getUserId();
        $scheme_id = Configduty::select('scheme_id')->distinct()->where('user_id','=',$user_id)->where('is_active',1)->get();
        if(request()->ajax())
        {
            $designation = Auth::user()->designation_id;
            // IF ALL SCHEME SELECT
            $scheme_arr=[];
            foreach ($scheme_id as $k) {
                array_push($scheme_arr,$k->scheme_id);
            }
            $s_id = implode(',', $scheme_arr);
            if ($designation == 'Approver') {
                $dutyObj = Configduty::where('user_id',$user_id)->first();
                $dist_code = $dutyObj->district_code;
                if(!empty($request->scheme))
                {
                    $query = "SELECT m.scheme_name,b.id,concat(b.ben_fname,' ',b.ben_mname,' ',ben_lname) AS name,b.block_ulb_name,b.gp_ward_name 
                    FROM pension.beneficiary b JOIN m_scheme m ON m.id=b.scheme_id WHERE b.next_level_role_id=-99 AND b.scheme_id IN(".$s_id.") AND b.created_by_dist_code=".$dist_code." AND b.scheme_id=".$request->scheme;
                }
                else
                {
                    $query = "SELECT m.scheme_name,b.id,concat(b.ben_fname,' ',b.ben_mname,' ',ben_lname) AS name,b.block_ulb_name,b.gp_ward_name 
                    FROM pension.beneficiary b JOIN m_scheme m ON m.id=b.scheme_id WHERE b.next_level_role_id=-99 AND b.scheme_id IN(".$s_id.") AND b.created_by_dist_code=".$dist_code;
                }
            }
            
                
            $data = DB::connection('pgsql_mis')->select($query);
            // print_r($query);die();          
            return datatables()->of($data)
            ->addColumn('id', function ($data){
              return $data->id;
            })
            ->addColumn('scheme_name', function ($data){
              return $data->scheme_name;
            })
            ->addColumn('name', function ($data){
              return $data->name;
            })
            ->addColumn('block_ulb_name', function ($data) {
              return $data->block_ulb_name;
            })
            ->addColumn('gp_ward_name', function ($data) {
              return $data->gp_ward_name;
            })
          
            ->rawColumns(['id','scheme_name','name','block_ulb_name','gp_ward_name'])
            ->make(true);
        }
        return view('report-stop-payment/stop_payment_report_index',['schemes' => $scheme_id]);
        
    }
   
}
