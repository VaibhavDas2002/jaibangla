<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;
use App\Designation;
use App\Schemetype;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Users_audit_trail_admin;
class AdminController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-management';

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('Admin');
        
        
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function useredit(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('pcre.backtrack_limit', "10000000");
        ini_set('max_execution_time', 300);
        $designation_id = Auth::user()->designation_id;

        $code = 0;
        $fill_array = array();
        $old_files = array();
        $fill_array['order_no'] = '';
        $fill_array['pre_user_id'] = '';
        $fill_array['new_mobile_no'] = '';
        $fill_array['remarks'] = '';
        $fill_array['old_mobile_no'] = '';
        $issubmitted = 0;
        $valid = 1;
        $msg = '';
        $errors = array();
        $is_active = 0;
        $scheme_arr = Scheme::where('is_active', 1)->get();
        $designation_arr = Designation::get();
        $designation_id = Auth::user()->designation_id;
        if (!in_array($designation_id, array('Admin'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }


        if (isset($request->submit)) {
            if (!empty($request->order_no)) {
                $fill_array['order_no'] = $request->order_no;
            }
            if (!empty($request->pre_user_id)) {
                $fill_array['pre_user_id'] = $request->pre_user_id;
            }
            if (!empty($request->new_mobile_no)) {
                $fill_array['new_mobile_no'] = $request->new_mobile_no;
            }
            if (!empty($request->remarks)) {
                $fill_array['remarks'] = $request->remarks;
            }
            if (!empty($request->old_mobile_no)) {
                $fill_array['old_mobile_no'] = $request->old_mobile_no;
            }
            $issubmitted = 1;
            $rules = [
                'new_mobile_no' => 'required',
                'remarks' => 'required',
                'uploaded_file' => 'required'
            ];
            $attributes = array();
            $messages = array();
            $attributes['order_no'] = 'Order/Memo No.';
            $attributes['pre_user_id'] = 'Comma Separated User Id';
            $attributes['new_mobile_no'] = 'New Mobile No. List';
            $attributes['remarks'] = 'Remarks';
            $attributes['uploaded_file'] = 'Upload File';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
                
                $destinationPath = storage_path('app/useraudittrailadmin/');
                if ($request->hasFile('uploaded_file')) {
                    $doc_file = $request->file('uploaded_file');
                    $file_profile = time() . '.' . $doc_file->getClientOriginalExtension();
                    if ($doc_file->move($destinationPath, $file_profile)) {
                        try {
                            $trail = new Users_audit_trail_admin();
                            if (!empty($request->new_mobile_no)) {
                                if($this->isJson(json_encode(explode(",",  trim($request->new_mobile_no))))){
                                    $trail->new_mobile_no_list = json_encode(explode(",",  trim($request->new_mobile_no)));
                                }
                           
                            }
                            $trail->remarks = trim($request->remarks);
                            $trail->uploaded_file = $file_profile;
                            if (!empty($request->order_no)) {
                            $trail->order_no = trim($request->order_no);
                            }
                            if (!empty($request->pre_user_id)) {
                                if($this->isJson(json_encode(explode(",",  trim($request->pre_user_id))))){
                                    $trail->deactivated_user_id_list = json_encode(explode(",",  trim($request->pre_user_id)));
                                }
                           
                            }
                            if (!empty($request->schemelist)) {
                                if($this->isJson(json_encode($request->schemelist, JSON_FORCE_OBJECT))){
                                $trail->scheme_id_list = json_encode($request->schemelist, JSON_FORCE_OBJECT);
                                }
                            }
                            if (!empty($request->old_mobile_no)) {
                                if($this->isJson(json_encode(explode(",",  trim($request->old_mobile_no))))){
                                $trail->old_mobile_no_list = json_encode(explode(",",  trim($request->old_mobile_no)));
                                }
                            }
                            $is_saved2 = $trail->save();
                            $valid = 1;
                            $msg = 'User Modification trail has been uploaded Successfully';
                        } catch (\Exception $e) {
                            dd($e);
                            $valid = 0;
                            $msg = 'Error.. Please try later.';
                        }
                    } else {
                        $valid = 0;
                        $msg = 'Error.. Please try later.';
                    }
                }
            } else {
                $valid = 0;
                $errors = $validator->errors()->all();
            }
        }
        // dd($is_urban);
        return view(
            'Admin.usermanagement.upload',
            [
                'valid' => $valid,
                'msg' => $msg,
                'scheme_arr' => $scheme_arr,
                'fill_array' => $fill_array,
                'designation_arr' => $designation_arr,
                'errors' => $errors,
                'issubmitted' => $issubmitted
            ]
        );
    }

    
    function isJson($str) {
        $json = json_decode($str);
        return $json && $str != $json;
    }
}
