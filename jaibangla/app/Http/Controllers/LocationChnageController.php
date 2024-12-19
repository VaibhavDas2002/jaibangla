<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\GP;
use App\Scheme;
use App\User;
use Redirect;
use Auth;
use Config;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Location_change_mapping;
use App\Location_change_mapping_track;
use App\Helpers\AuthChecker;

class LocationChnageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    

    public function applicationStatusList(Request $request)
    {
        //return redirect("/")->with('error', 'Not Allowded');
        
        if(empty($request->scheme_id)){
            return redirect('/')->with('error', 'Scheme Not Found');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_id=$request->scheme_id;
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old != 'Approver') {
            return redirect('/')->with('error', 'Not Allowded');
        }
        $duty= Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->where('scheme_id', $scheme_id)->first();
        if(empty($duty)){
            return redirect('/')->with('error', 'Not Allowded');
        }
        $district_code = $duty->district_code;
        $mapping_checking= Location_change_mapping::where('district_code', $district_code)->where('scheme_id', $scheme_id)->count();
        if($mapping_checking==0){
            return redirect('/')->with('error', 'Not Allowded');
        }
        $allowded_blk_ulb= Location_change_mapping::where('district_code', $district_code)->where('scheme_id', $scheme_id)->pluck('block_subdiv_code')->toArray();
        if($mapping_checking==0){
            return redirect('/')->with('error', 'Not Allowded');
        }
        $map_block_list=Taluka::where('district_code', $district_code)->whereIn('block_code',$allowded_blk_ulb)->get();
        $block_list=Taluka::where('district_code', $district_code)->get();
       // dd( $map_block_list);
        $report_type_name = 'Approved Beneficiary List';
        $errormsg_arr = Config::get('constants.errormsg');
        $sessiontimeoutmessage = $errormsg_arr['sessiontimeOut'];
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        $scheme_schema = $scheme_row->short_code;
        if (!empty($scheme_schema)) {
            $table=$scheme_row->short_code;
        }
        else{
            $table='pension'; 
        }
        $report_type_name = $report_type_name .' of the Scheme '.$scheme_row->scheme_name;
        $scheme_length = NULL;
        $id_length = NULL;
        $condition = array();
        $condition["created_by_dist_code"] = $district_code;
         // temporary
        $condition["rural_urban_id"] = 2;
        if (request()->ajax()) 
        {
            $rural_urbanid = $request->rural_urbanid;
            $urban_body_code = $request->urban_body_code;
            $gp_ward_code = $request->gp_ward_code;
            if (!empty($rural_urbanid)) {
                $condition["rural_urban_id"] = $rural_urbanid;
            }
            if (!empty($urban_body_code)) {
                $condition["created_by_local_body_code"] = $urban_body_code;
            }
            if (!empty($gp_ward_code)) {
                $condition["gp_ward_code"] = $gp_ward_code;
            }
        $limit = $request->input('length');
        $offset = $request->input('start');
        $totalRecords = 0;
        $filterRecords = 0;

            $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                        'id', 'created_by_dist_code','created_by_local_body_code',
                        'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                        'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'is_state', 'house_premise_no', 'mobile_no', 'post_office', 'pincode'
                    ]);
                    //dd($data);
            }
            else{
                if (is_numeric($serachvalue)) {
                    $ben_id = substr($serachvalue, -7);
                    $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                        $query1->where('id', $ben_id)
                            ->orWhere('bank_code', $serachvalue);
                    });
                    $totalRecords = $query->count('id');
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                        [
                            'id', 'created_by_dist_code','created_by_local_body_code',
                            'bank_code',
                            'ben_fname',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'ben_lname', 'gender', 'ben_age', 'ben_mname', 'is_state', 'house_premise_no', 'mobile_no', 'post_office', 'pincode'
                        ]
                    );
                }
                else{
                    $query = $query->where(function ($query1) use ($serachvalue) {
                        if (strtoupper(trim($serachvalue)) == 'STATE') {
                            $query1->where('is_state', TRUE);
                        } else {
                            $query1->where('ben_fname', 'like', $serachvalue . '%')
                                ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                                ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                                ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                        }
                    });
                    $totalRecords = $query->count('id');
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                        [
                            'id', 'created_by_dist_code','created_by_local_body_code',
                            'bank_code',
                            'ben_fname',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'ben_lname', 'gender', 'ben_age', 'ben_mname', 'is_state', 'house_premise_no', 'mobile_no', 'post_office', 'pincode'
                        ]
                    );
                }
               
            }
            $filterRecords = count($data);
           
           $k= datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('beneficiary_id', function ($data) {
                return $data->id;
              })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox"  class="checkboxall" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
              })
            ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                return $app_id;
            })
            ->addColumn('ben_name', function ($data) {
                // return $data->getName();
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->addColumn('bank_ifsc', function ($data) {
                return trim($data->bank_ifsc);
            })
            ->addColumn('bank_code', function ($data) {
                return trim($data->bank_code);
            })
            ->addColumn('mobile_no', function ($data) use ($block_list) {
                return trim($data->mobile_no);
               
            })->addColumn('created_by_local_body_code_name', function ($data) use ($block_list) {
                $block_arr=$block_list->where('block_code',$data->created_by_local_body_code)->first();
                return trim($block_arr->block_name);
            })
            ->addColumn('block_ulb_name', function ($data) {
                return trim($data->block_ulb_name);
            })
            ->addColumn('gp_ward_name', function ($data) {
                return trim($data->gp_ward_name);
            })
            ->addColumn('action', function ($data) use ($scheme_id, $report_type_name) {
                $val = '<button  class="btn btn-primary ben_view_button" role="button" id="'.$data->id.'" >Edit Block</button>';
                return $val;
            })
            ->rawColumns(['check','ben_name',  'bank_ifsc', 'bank_code',  'mobile_no', 'block_ulb_name','gp_ward_name','action'])
            ->make(true);
           // dd($k);
           return $k;
        }
        else{
            return view(
                'location_change.index',
                [
                  'scheme' => $scheme_id,
                  'district_code' => $duty->district_code,
                  'report_type_name' => $report_type_name,
                  'report_type_name' => $report_type_name,
                  'allowded_blk_ulb' => $allowded_blk_ulb,
                  'map_block_list' => $map_block_list,
                  'sessiontimeoutmessage' => $sessiontimeoutmessage,
                ]
              );
           }
 
        
    }

    public function update(Request $request)
    {
        //return redirect("/")->with('error', 'Not Allowded');
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old != 'Approver') {
            return redirect('/')->with('error', 'Not Allowded');
        }
        $beneficiary_id=trim($request->beneficiary_id);
        if(empty($request->scheme_id)){
            return redirect('/')->with('error', 'Scheme Not Found');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $rules = [
            'beneficiary_id' => 'required|integer',
            'scheme_id' => 'required|integer',
            'new_block_ulb_code' => 'required|integer',
        ];
        $attributes = array();
        $messages = array();
        $attributes['beneficiary_id'] = 'Beneficiary Id';
        $attributes['scheme_id'] = 'Scheme Id';
        $attributes['new_block_ulb_code'] = 'Block/SubDivision Code';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if (!$validator->passes()) {
            //dd($validator->errors());
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('errors', $validator->errors());
        }
        $beneficiary_id=trim($request->beneficiary_id);
        $scheme_id=trim($request->scheme_id);
        $new_block_ulb_code=trim($request->new_block_ulb_code);
        $new_gp_ward_code=trim($request->new_gp_ward_code);
        $user_id = AuthChecker::getUserId();
        $duty= Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->where('scheme_id', $scheme_id)->first();
        if(empty($duty)){
            return redirect('/')->with('error', 'Not Allowded');
        }
        $district_code = $duty->district_code;
        $mapping_checking= Location_change_mapping::where('district_code', $district_code)->where('block_subdiv_code', $new_block_ulb_code)->where('scheme_id', $scheme_id)->count();
        if($mapping_checking==0){
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', 'Not Allowded');
        }
        $block_check=Taluka::where('district_code', $district_code)->where('block_code',$new_block_ulb_code)->first();
        if(empty($block_check)){
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', 'Invalid Block/SubDivision');

        }      
        $errormsg_arr = Config::get('constants.errormsg');
        $sessiontimeoutmessage = $errormsg_arr['sessiontimeOut'];
        $roolbackmessage = $errormsg_arr['roolback'];
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        $scheme_schema = $scheme_row->short_code;
        if (!empty($scheme_schema)) {
            $table=$scheme_row->short_code;
        }
        else{
            $table='pension'; 
        }
        $condition = array();
        $condition["created_by_dist_code"] = $district_code;
        $condition["id"] = $beneficiary_id;
        $condition["next_level_role_id"] = 0;
        $row = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->select('id','rural_urban_id','block_ulb_code','gp_ward_code','created_by_local_body_code')->where($condition)->first();
        if(empty($row)){
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', 'Beneficiary Id Not Found');
        }
        if(trim($row->created_by_local_body_code)==$new_block_ulb_code){
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', 'You Have Choose Same Block..Please Try Different');

        }
        $update_arr=array();
        $update_arr['created_by_local_body_code']=$new_block_ulb_code;
        $track_arr=array();
        $track_arr['created_by_dist_code']=$district_code;
        $track_arr['user_id']=$user_id;
        $track_arr['beneficiary_id']=$beneficiary_id;
        $track_arr['scheme_id']=$scheme_id;
        $track_arr['op_type']=2;
        $track_arr['created_at']=date("Y-m-d h:i:s");
        $track_arr['old_created_by_local_body_code']=$row->created_by_local_body_code;
        $track_arr['new_created_by_local_body_code']=$new_block_ulb_code;
        if($row->rural_urban_id==2){
                $gp_check=GP::where('gram_panchyat_code',$row->gp_ward_code)->first();
                if(!empty($gp_check)){
                    $new_block_add_arr=Taluka::where('block_code',$gp_check->block_code)->first();
                    if(!empty($new_block_add_arr)){
                        $track_arr['old_block_ulb_code']=$row->block_ulb_code;
                        $track_arr['new_block_ulb_code']=$gp_check->block_code;
                        $update_arr['block_ulb_code']= $gp_check->block_code;
                        $update_arr['block_ulb_name']=$new_block_add_arr->block_name;
                    }

                }
        }
        DB::beginTransaction();
        $update1=DB::table('' . $table . '.beneficiary')
        ->where('id', $beneficiary_id)  
        ->where('next_level_role_id', 0)
        ->where('created_by_dist_code', $district_code)
        ->update($update_arr);  
        if( $update1){
            $location_change_mapping_track=new Location_change_mapping_track();
           
            $track_status = $location_change_mapping_track->insert($track_arr);
            if( $track_status){
                DB::commit();
                $return_text = "Beneficiary informations successfully updated with Beneficiary Id:" . $beneficiary_id;
                return redirect("/location_change?scheme_id=".$request->scheme_id)->with('success', $return_text);
            }
            else{
                DB::rollback();
                return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', $roolbackmessage);

            }
          }
          else{
            DB::rollback();
            return redirect("/location_change?scheme_id=".$request->scheme_id)->with('error', $roolbackmessage);

          }
    }
}
