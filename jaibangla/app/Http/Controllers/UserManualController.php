<?php

namespace App\Http\Controllers;

use App\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\Designation;
use App\UserManual;
use App\GP;
use App\Taluka;
use App\UrbanBody;
use App\PensionLBWCDTemp;
use App\PensionSC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use File;
use Illuminate\Support\Facades\Storage;
use App\Configduty;
use App\DataSourceCommon;
use App\Helpers\AuthChecker;


class UserManualController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function uploaddd(Request $request)
    {
        //dd($request->all());
    ini_set('memory_limit', '-1');
    ini_set('pcre.backtrack_limit', "10000000");
    ini_set('max_execution_time', 300);
    $designation_id = Auth::user()->designation_id;
    $code = 0;
    $fill_array = array();
    $old_files = array();
    $fill_array['file_name'] = '';
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
        $scheme_ids = $request->scheme_id;
        if (in_array('all', $scheme_ids)) {
            $scheme_ids = $scheme_arr->pluck('id')->toArray();
           // dd($scheme_ids);
        }
    
        $designation_ids = $request->designation_id;
        if (in_array('all', $designation_ids)) {
            $designation_ids = $designation_arr->pluck('name')->toArray();
            //dd($designation_ids);
        }

        $fill_array['file_name'] = $request->file_name;

        $rules = [
            'scheme_id' => 'required|array',
            'designation_id' => 'required|array',
            'file_name' => 'required|string',
            'uploaded_file' => 'required|mimetypes:application/pdf',
        ];
        $attributes = array();
        $messages = array();
        $attributes['file_name'] = 'Manual Name';
        $attributes['uploaded_file'] = 'Upload File';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->passes()) {
            $destinationPath = storage_path('app/userManual');
            if ($request->hasFile('uploaded_file')) {
                $doc_file = $request->file('uploaded_file');
                $file_profile = "user_manual_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
                if ($doc_file->move($destinationPath, $file_profile)) {
                    foreach ($scheme_ids as $scheme_id) {
                        foreach ($designation_ids as $designation_id) {
                            $issubmitted = 1;
                            $count_data = UserManual::where('scheme_id', $scheme_id)->where('designation_id', $designation_id)->count();
                            try {
                                if ($count_data > 0) {
                                    $input = [
                                        'is_active' => 0
                                    ];
                                    UserManual::where('scheme_id', $scheme_id)->where('designation_id', $designation_id)->where("is_active", 1)->update($input);
                                }
                                $manual = new UserManual();
                                $manual->scheme_id = $scheme_id;
                                $manual->designation_id = $designation_id;
                                $manual->file_name = trim($fill_array['file_name']);
                                $manual->uploaded_file = $file_profile;
                                $manual->is_active = 1;
                                $is_saved2 = $manual->save();
                                if ($is_saved2) {
                                    $valid = 1;
                                    $msg = 'User Manual has been uploaded Successfully';
                                } else {
                                    $valid = 0;
                                    $msg = 'Error.. Please try later.';
                                }
                            } catch (\Exception $e) {
                                $valid = 0;
                                $msg = 'Error.. Please try later.';
                            }
                        }
                    }
                } else {
                    $valid = 0;
                    $msg = 'The Upload File failed to upload.';
                }
            }
        } else {
            $valid = 0;
            $errors = $validator->errors()->all();
        }
    }

    return view(
        'UserManual.upload',
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

    
public function upload(Request $request)
{
    ini_set('memory_limit', '-1');
    ini_set('pcre.backtrack_limit', "10000000");
    ini_set('max_execution_time', 300);
    $designation_id = Auth::user()->designation_id;
    $code = 0;
    $fill_array = array();
    $old_files = array();
    $fill_array['file_name'] = '';
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

    $file_profile = '';

    if (isset($request->submit)) {
        $scheme_ids = $request->scheme_id;
        if (in_array('all', $scheme_ids)) {
            $scheme_ids = $scheme_arr->pluck('id')->toArray();
        }
    
        $designation_ids = $request->designation_id;
        if (in_array('all', $designation_ids)) {
            $designation_ids = $designation_arr->pluck('name')->toArray();
        }

        $fill_array['file_name'] = $request->file_name;

        $rules = [
            'scheme_id' => 'required|array',
            'designation_id' => 'required|array',
            'file_name' => 'required|string',
            'uploaded_file' => 'required|mimetypes:application/pdf',
        ];
        $attributes = array();
        $messages = array();
        $attributes['file_name'] = 'Manual Name';
        $attributes['uploaded_file'] = 'Upload File';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->passes()) {
            $destinationPath = storage_path('app/userManual');
            if ($request->hasFile('uploaded_file')) {
                $doc_file = $request->file('uploaded_file');
                $file_profile = "user_manual_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
                if ($doc_file->move($destinationPath, $file_profile)) {
                    foreach ($scheme_ids as $scheme_id) {
                        foreach ($designation_ids as $designation_id) {
                            $issubmitted = 1;
                            $count_data = UserManual::where('scheme_id', $scheme_id)->where('designation_id', $designation_id)->count();
                            try {
                                if ($count_data > 0) {
                                    $input = [
                                        'is_active' => 1
                                    ];
                                    UserManual::where('scheme_id', $scheme_id)->where('designation_id', $designation_id)->where("is_active", 1)->update($input);
                                }
                                $manual = new UserManual();
                                $manual->scheme_id = $scheme_id;
                                $manual->designation_id = $designation_id;
                                $manual->file_name = trim($fill_array['file_name']);
                                $manual->uploaded_file = $file_profile;
                                $manual->is_active = 1;
                                $is_saved2 = $manual->save();
                                if ($is_saved2) {
                                    $valid = 1;
                                    $msg = 'User Manual has been uploaded Successfully';
                                } else {
                                    $valid = 0;
                                    $msg = 'Error.. Please try later.';
                                }
                            } catch (\Exception $e) {
                                $valid = 0;
                                $msg = 'Error.. Please try later.';
                            }
                        }
                    }
                } else {
                    $valid = 0;
                    $msg = 'The Upload File failed to upload.';
                }
            }
        } else {
            $valid = 0;
            $errors = $validator->errors()->all();
        }
    }

    return view(
        'UserManual.upload',
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



    function get(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();
        $schemearray = array();
		$report = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));

		foreach ($report as $reportVal) {
			array_push($schemearray, $reportVal->id);
		}
        $userManuals = UserManual::where('designation_id', $designation_id)->where('is_active', 1)->whereIn('scheme_id', $schemearray)->get();
        $result = [];
        foreach ($userManuals as $userManual) {
            $scheme_id = $userManual->scheme_id;
            $scheme = DB::table('m_scheme')
                ->where('id', $scheme_id)
                ->where('is_active', 1)
                ->first();
    
            if ($scheme) {
                $schemeName = $scheme->scheme_name;
    
                if (!isset($result[$schemeName])) {
                    $result[$schemeName] = [
                        'scheme_name' => $schemeName,
                        'manualData' => [],
                    ];
                }
    
                $result[$schemeName]['manualData'][] = $userManual->toArray();
            }
        }

        $result = array_values($result);
        return view('UserManual.list', ['data' => $result]);
    }
    
    
    public function downloadstaticpdf(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        if (!in_array($designation_id, array('Admin', 'Operator', 'Approver', 'Verifier','Corp'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $file_name = $request->file_name;
        if (empty($file_name)) {
            return redirect("/get-user-manual")->with('error', 'File Name not Passed');
        }
        $file_name1 = 'userManual/' . $file_name;
        $exists = Storage::disk('local')->has($file_name1);
        if ($exists) {
            return response()->download(storage_path('app/userManual//' . $file_name));
        } else {
            return redirect("/get-user-manual")->with('error', 'File not Found');
        }
    }
    // ***************************************************************************************************************************************************

    // function gettt(Request $request)
    // {
    //     $designation_id = Auth::user()->designation_id;
    //     $user_id = AuthChecker::getUserId();
    //     $data = UserManual::where('designation_id', $designation_id)->where('is_active', 1)->get();
    //     $result = array();
    //     $scheme_in = array();
    
    //     foreach ($data as $row) {
           
    //         $scheme_arr = DB::select(DB::raw("select id,is_active,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
            
    //         foreach ($scheme_arr as $scheme) {
    //             if (isset($scheme->scheme_name) && $scheme->is_active == 1) {
    //                 if (!in_array($scheme->id, $scheme_in)) {
    //                     $result[] = [
    //                         'scheme_name' => $scheme->scheme_name,
    //                         'manualData' => [$row->toArray()],
    //                     ];
    //                     array_push($scheme_in, $scheme->id);
    //                 } else {
    //                     $index = array_search($scheme->id, array_column($result, 'scheme_name'));
    //                     if ($index !== false) {
    //                         $result[$index]['manualData'][] = $row->toArray();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    
    //     //dd($result);
    
    //     return view(
    //         'UserManual.list',
    //         [
    //             'data' => $result
    //         ]
    //     );
    // }
    // ***************************************************************************************************************************************************


    // function get(Request $request)
    // {
    //     $designation_id = Auth::user()->designation_id;
    //     $user_id = AuthChecker::getUserId();
    //     $data = UserManual::where('designation_id', $designation_id)->where('is_active', 1)->get();
    //     $result = array();
    //     $scheme_in = array();
    //     $arrayData = array();
    //     $i = 0;
    
    //     foreach ($data as $row) {
    //         // Fetch the scheme_arr data for the current user
    //         $scheme_arr = DB::select(DB::raw("select id,is_active,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
            
    //         foreach ($scheme_arr as $scheme) {
    //             if (isset($scheme->scheme_name) && $scheme->is_active == 1) {
    //                 if (!in_array($scheme->id, $scheme_in)) {
    //                     $result[$i]['scheme_name'] = $scheme->scheme_name;
    //                     $arrayData = array();
    //                     array_push($scheme_in, $scheme->id);
    //                     array_push($arrayData, $row->toArray());
    //                     $result[$i]['manualData'] = $arrayData;
    //                     $i++;
    //                 } else {
    //                     array_push($arrayData, $row->toArray());
    //                     $result[$i]['manualData'] = $arrayData;
    //                 }
    //             }
    //         }
    //     }
    //     dd($result);
    //     return view(
    //         'UserManual.list',
    //         [
    //             'data' => $result
    //         ]
    //     );
    // }


    
    

    // ***************************************************************************************************************************************************

    // public function uploaddddd(Request $request)
    // {
    //     ini_set('memory_limit', '-1');
    //     ini_set('pcre.backtrack_limit', "10000000");
    //     ini_set('max_execution_time', 300);
    //     $designation_id = Auth::user()->designation_id;

    //     $code = 0;
    //     $fill_array = array();
    //     $old_files = array();
    //     $fill_array['scheme_id'] = '';
    //     $fill_array['designation_id'] = '';
    //     $fill_array['file_name'] = '';
    //     $issubmitted = 0;
    //     $valid = 1;
    //     $msg = '';
    //     $errors = array();
    //     $is_active = 0;
    //     $scheme_arr = Scheme::where('is_active', 1)->get();
    //     $designation_arr = Designation::get();
    //     $designation_id = Auth::user()->designation_id;
    //     if (!in_array($designation_id, array('Admin'))) {
    //         return redirect("/")->with('error', 'Not Allowed');
    //     }


    //     if (isset($request->submit)) {
    //         if (!empty($request->scheme_id)) {
    //             $fill_array['scheme_id'] = $request->scheme_id;
    //         }
    //         if (!empty($request->designation_id)) {
    //             $fill_array['designation_id'] = $request->designation_id;
    //         }
    //         if (!empty($request->file_name)) {
    //             $fill_array['file_name'] = $request->file_name;
    //         }
    //         $issubmitted = 1;
    //         $rules = [
    //             'scheme_id' => 'required|integer',
    //             'designation_id' => 'required',
    //             'file_name' => 'required',
    //             'uploaded_file' => 'required|mimetypes:application/pdf'
    //         ];
    //         $attributes = array();
    //         $messages = array();
    //         $attributes['scheme_id'] = 'Scheme';
    //         $attributes['designation_id'] = 'Designation';
    //         $attributes['file_name'] = 'Manual Name';
    //         $attributes['uploaded_file'] = 'Upload File';
    //         $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    //         if ($validator->passes()) {
    //             $destinationPath = storage_path('app/userManual/');
    //             if ($request->hasFile('uploaded_file')) {
    //                 $doc_file = $request->file('uploaded_file');
    //                 $file_profile = "user_manual_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
    //                 if ($doc_file->move($destinationPath, $file_profile)) {
    //                     $count_data = UserManual::where('scheme_id', $request->scheme_id)->where('designation_id', $request->designation_id)->count();
    //                     try {
    //                         if ($count_data > 0) {
    //                             $input = [
    //                                 'is_active' => 0
    //                             ];
    //                             $is_update1 =  UserManual::where('scheme_id', $request->scheme_id)->where('designation_id', $request->designation_id)->where("is_active", 1)->update($input);
    //                         }
    //                         $manual = new UserManual();
    //                         $manual->scheme_id = $request->scheme_id;
    //                         $manual->designation_id = $request->designation_id;
    //                         $manual->file_name = trim($request->file_name);
    //                         $manual->uploaded_file = $file_profile;
    //                         $manual->is_active = 1;
    //                         $is_saved2 = $manual->save();
    //                         $valid = 1;
    //                         $msg = 'User Manual has been uploaded Successfully';
    //                     } catch (\Exception $e) {
    //                         $valid = 0;
    //                         $msg = 'Error.. Please try later.';
    //                     }
    //                 } else {
    //                     $valid = 0;
    //                     $msg = 'Error.. Please try later.';
    //                 }
    //             }
    //         } else {
    //             $valid = 0;
    //             $errors = $validator->errors()->all();
    //         }
    //     }
    //     // dd($is_urban);
    //     return view(
    //         'UserManual.upload',
    //         [
    //             'valid' => $valid,
    //             'msg' => $msg,
    //             'scheme_arr' => $scheme_arr,
    //             'fill_array' => $fill_array,
    //             'designation_arr' => $designation_arr,
    //             'errors' => $errors,
    //             'issubmitted' => $issubmitted
    //         ]
    //     );
    // }
}
