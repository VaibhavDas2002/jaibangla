<?php

namespace App\Http\Controllers;

use App\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\DB;
use App\PensionLBWCD;
use App\Ward;
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
use Illuminate\Support\Facades\View;

class LakkhiBhandarWCD extends Controller
{
    protected $scheme_id;

    public function __construct()
    {
        $this->middleware('auth');
        $this->scheme_id = 20;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $district_code = NULL;
        $code = 0;
        if (!empty($request->code))
            $code = $request->code;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray(); 
               $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Approver') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            $district_code = $roleObj['district_code'];
            if (!empty($district_code)) {
                break;
            }
        }
        //$district_code = 310;
        if (empty($district_code)) {
            return redirect("/")->with('error', 'User Disaled');
        }
        $data = array();
        $query = "select block_code as code,block_name||'-B' as location_name from m_block
        where district_code=" . $district_code . "
        UNION
        select urban_body_code as code,urban_body_name||'-M' as location_name from m_urban_body
       where district_code=" . $district_code . "";
        $data_part = DB::connection('pgsql_mis')->select($query);
        $data = array_merge($data, $data_part);
        return view(
            'LokkhiBhandarWCD.download_pdf.index',
            [
                'blk_munc' => $data,
                'district_code' => $district_code,
                'code' => $code
            ]
        );
    }
    function download(Request $request)
    {
        $rules = [
            'urban_code' => 'required',
            'block' => 'required',
            'gp_ward' => 'required'
        ];
        $attributes = array();
        $messages = array();
        $attributes['urban_code'] = 'Select Rural/ Urban';
        $attributes['block'] = 'Select Block/Municipality';
        $attributes['gp_ward'] = 'Select GP/Ward No';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            if ($request->urban_code == 1) {
                $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
                $gp_ward_name = $gp_ward->urban_body_ward_name;
            } else {
                $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
                $gp_ward_name   = $gp_ward->gram_panchyat_name;
            }
            if ($request->code == 1) {
                $location = storage_path('app/public/LakkhiBhandaar_1.pdf');
                // Optional: serve the file under a different filename:
                $filename = 'LakkhiBhandaar.pdf';
                // optional headers
                $headers = [];
                return response()->download($location, $filename, $headers);
            } else {
                $scheme_model = 'App\\PensionLBWCDTemp';
                $data = $scheme_model::take(2)->get();
                // dd($data);
                $view = View::make('LokkhiBhandarWCD/download_pdf/HtmlToPDF')->with('data', $data);
                $html_content = $view->render();


                TCPDF::SetAuthor('System');
                TCPDF::SetTitle('Lakkhi Bhandaar');
                TCPDF::SetSubject('Report of System');
                TCPDF::AddPage('P', 'A4');
                TCPDF::writeHTML($html_content, true, false, true, false, '');
                //PDF::lastPage();
                $c_time = time();
                TCPDF::Output('LakkhiBhandaar_' . $c_time . '_' . $gp_ward_name . '.pdf', 'D');
                exit;
            }
        } else {
            return redirect("/lkwcd-download-pdf")->with('errors', $validator->errors()->all());
        }
    }
    public function index1(Request $request)
    {
        $scheme_model = 'App\\PensionLBWCDTemp';
        $data = $scheme_model::get();
        return view(
            'LokkhiBhandarWCD.download_pdf.index1',
            [
                'data' => $data,
            ]
        );
    }
    public function indexadmin(Request $request)
    {
        $district_list = Cache::rememberForever('master_districts', function () {
            return District::select(
                'id',
                'district_code',
                'district_name',
                'rch_district_code',
                'is_revenue_district',
                'state_code',
                'district_status'
            )->get();
        });
        $urban_list = Cache::rememberForever('master_urbanbodies', function () {
            return UrbanBody::select('id', 'district_code', 'urban_body_code', 'urban_body_name', 'sub_district_code', 'urban_body_status')->get();
        });
        $block_list = Cache::rememberForever('master_blocks', function () {
            return Taluka::select(
                'district_code',
                'sub_division_code',
                'block_code',
                'block_name',
                'status',
                'district_id',
                'sub_division_id'
            )->get();
        });
        $ward_list = Cache::rememberForever('master_wards', function () {
            return Ward::select(
                'id',
                'urban_body_id',
                'urban_body_code',
                'urban_body_ward_code',
                'urban_body_ward_no',
                'urban_body_ward_name',
                'ward_status'
            )->get();
        });
        $gp_list = Cache::rememberForever('master_gps', function () {
            return GP::select('district_code', 'sub_division_code', 'block_code', 'gram_panchyat_code', 'gram_panchyat_name', 'status')->get();
        });
        $code = 0;
        $fill_array = array();
        $fill_array['district_code'] = '';
        $fill_array['urban_code'] = '';
        $fill_array['block'] = '';
        $fill_array['gp_ward'] = '';
        $fill_array['limit_no'] = 20;
        $issubmitted = 0;
        $valid = 1;
        $msg = '';
        $errors = array();
        $block_munc = collect([]);
        $gp_ward = collect([]);
        $old_files = array();
        $total_data = 0;
        $already_pdf_data = 0;
        $limit_array = array(20, 50, 100, 500);
        if (!empty($request->code))
            $code = $request->code;
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Admin') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        if (isset($request->submit)) {
            $issubmitted = 1;
            $rules = [
                'district_code' => 'required',
                'urban_code' => 'required',
                'block' => 'required',
                'gp_ward' => 'required',
                'limit_no' => 'required'
            ];
            $attributes = array();
            $messages = array();
            $attributes['district_code'] = 'Select District';
            $attributes['urban_code'] = 'Select Rural/ Urban';
            $attributes['block'] = 'Select Block/Municipality';
            $attributes['gp_ward'] = 'Select GP/Ward No';
            $attributes['limit_no'] = 'Select Limit';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
                $pdf_directory1 = '/lakkhi_bhandar_pdf/' . $request->district_code . '/' . $request->block . '/' . $request->gp_ward;
                $pdf_directory2 = 'lakkhi_bhandar_pdf/' . $request->district_code . '/' . $request->block . '/' . $request->gp_ward . '/';
                $condition = array();
                $condition['hof'] = true;
                $condition['gender'] = 'Female';

                if (!empty($request->district_code)) {
                    $fill_array['district_code'] = $request->district_code;
                    $condition['dist_code'] = $request->district_code;
                }
                if (!empty($request->urban_code)) {
                    $fill_array['urban_code'] = $request->urban_code;
                    if ($request->urban_code == 1)
                        $block_munc = $urban_list->where('district_code', $request->district_code);
                    else
                        $block_munc = $block_list = $block_list->where('district_code', $request->district_code);
                }
                if (!empty($request->block)) {
                    $fill_array['block'] = $request->block;
                    $condition['block_ulb_code'] = $request->block;
                    if ($request->urban_code == 1)
                        $gp_ward = $ward_list->where('urban_body_code', $request->block);
                    else
                        $gp_ward = $gp_list->where('block_code', $request->block);
                }
                if (!empty($request->gp_ward)) {
                    $fill_array['gp_ward'] = $request->gp_ward;
                    $condition['gp_ward_code'] = $request->gp_ward;
                }
                if (!empty($request->limit_no)) {
                    $fill_array['limit_no'] = $request->limit_no;
                }
                $scheme_model = 'App\\PensionLBWCDTemp';
                $limit = $request->limit_no;
                //$condition['pdf_is_download'] = false;
                $pdf_data = $scheme_model::where($condition)->where('pdf_is_download', false)->where('ben_age', '>', 18)->take($limit)->get();
                //dd($pdf_data);
                if (count($pdf_data) > 0) {
                    $id_in = $pdf_data->pluck('id')->toArray();
                    if (!Storage::exists($pdf_directory1)) {
                        Storage::makeDirectory($pdf_directory1, 0775, true);
                    }
                    $view = View::make('LokkhiBhandarWCD/download_pdf/HtmlToPDFAdmin')->with('data', $pdf_data);
                    $html_content = $view->render();
                    TCPDF::AddPage('P', 'A4');
                    TCPDF::writeHTML($html_content, true, false, true, false, '');
                    //PDF::lastPage();
                    $c_time = time();
                    $c_time1 = $c_time . '.pdf';
                    DB::beginTransaction();
                    try {
                        $input = [
                            'pdf_is_download' => true,
                            'file_name' => $c_time
                        ];
                        PensionLBWCDTemp::whereIn('id', $id_in)->update($input);
                        TCPDF::Output(storage_path('app') . $pdf_directory1 . '/' . $c_time1, 'F');
                        DB::commit();
                        $msg = $limit . ' Beneficiary Added to the PDF';
                    } catch (\Exception $e) {
                        DB::rollback();
                        $msg = 'Error.. Please try later.';
                        //dd($e);
                    }
                    // $condition['pdf_is_download'] = NULL;
                    $total_data = $scheme_model::where($condition)->where('ben_age', '>', 18)->count();

                    $already_pdf_data = $scheme_model::where($condition)->where('pdf_is_download', true)->where('ben_age', '>', 18)->count();
                    $files = Storage::disk('local')->files($pdf_directory2);
                    if (count($files) > 0) {
                        $old_files = $files;
                    }
                } else {
                    $total_data = $scheme_model::where($condition)->where('ben_age', '>', 18)->count();

                    $already_pdf_data = $scheme_model::where($condition)->where('pdf_is_download', true)->where('ben_age', '>', 18)->count();
                    $files = Storage::disk('local')->files($pdf_directory2);
                    if (count($files) > 0) {
                        $old_files = $files;
                    }
                    $msg = "No data to Add in PDF";
                }
            } else {
                $valid = 0;
                $errors = $validator->errors()->all();
            }
        }
        return view(
            'LokkhiBhandarWCD.download_pdf.indexadmin',
            [
                'districts' => $district_list,
                'block_munc' => $block_munc,
                'gp_ward' => $gp_ward,
                'code' => $code,
                'valid' => $valid,
                'msg' => $msg,
                'fill_array' => $fill_array,
                'errors' => $errors,
                'issubmitted' => $issubmitted,
                'old_files' => $old_files,
                'limit_array' => $limit_array,
                'total_data' => $total_data,
                'already_pdf_data' => $already_pdf_data
            ]
        );
    }
    public function downloadstaticpdf(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        if (!in_array($designation_id, array('Admin', 'Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $file_name = $request->file_name;
        if (empty($file_name)) {
            return redirect("/lkwcd-download-pdf-admin")->with('error', 'File Name not Passed');
        }
        $exists = Storage::disk('local')->has($file_name);
        if ($exists) {
            return response()->download(storage_path('app/' . $file_name));
        } else {
            return redirect("/lkwcd-download-pdf-admin")->with('error', 'File not Found');
        }
    }
    public function listpdf(Request $request)
    {
        $urban_list = Cache::rememberForever('master_urbanbodies', function () {
            return UrbanBody::select('id', 'district_code', 'urban_body_code', 'urban_body_name', 'sub_district_code', 'urban_body_status')->get();
        });
        $gp_list = Cache::rememberForever('master_gps', function () {
            return GP::select('district_code', 'sub_division_code', 'block_code', 'gram_panchyat_code', 'gram_panchyat_name', 'status')->get();
        });
        $ward_list = Cache::rememberForever('master_wards', function () {
            return Ward::select(
                'id',
                'urban_body_id',
                'urban_body_code',
                'urban_body_ward_code',
                'urban_body_ward_no',
                'urban_body_ward_name',
                'ward_status'
            )->get();
        });
        $scheme_id = $this->scheme_id;
        $errors = array();
        $msg = "";
        $valid = 1;
        $pdf_files = array();
        $fill_array = array();
        $fill_array['block'] = '';
        $fill_array['gp_ward'] = '';
        $issubmitted = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                $designation_id = Auth::user()->designation_id;
        if (!in_array($designation_id, array('Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $is_active = 1;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disaled');
        }
        if (isset($request->submit)) {
            $issubmitted = 1;
            $district_code = $request->district_code;
            $urban_code = $request->urban_code;
            $block = $request->block;
            $gp_ward = $request->gp_ward;
            if ($urban_code == 1) {
                $rules = [
                    'block' => 'required',
                    'gp_ward' => 'required'
                ];
                $attributes = array();
                $messages = array();
                $attributes['block'] = 'Select Municipality';
                $attributes['gp_ward'] = 'Select Ward No';
            } else {
                $rules = [
                    'gp_ward' => 'required'
                ];
                $attributes = array();
                $messages = array();
                $attributes['gp_ward'] = 'Select GP No';
            }
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
                $valid = 1;
                if (!empty($request->block)) {
                    $fill_array['block'] = $request->block;
                    $gp_ward_arr = $ward_list->where('urban_body_code', $request->block);
                }
                if (!empty($request->gp_ward)) {
                    $fill_array['gp_ward'] = $request->gp_ward;
                }
                if ($urban_code == 1) {
                    $pdf_block = $block;
                } else {
                    $pdf_block = $request->block_code;
                }
                $pdf_directory = '/lakkhi_bhandar_pdf/' . $district_code . '/' . $pdf_block . '/' . $gp_ward . '/';
                $files = Storage::disk('local')->files($pdf_directory);
                if (count($files) > 0) {
                    $pdf_files = $files;
                }
            } else {
                $valid = 0;
                $errors = $validator->errors()->all();
            }
        }
        if (strtoupper(trim($level)) == 'BLOCK') {
            $gp_ward_txt = 'GP';
            $munc_arr = array();
            $gp_ward_arr = $gp_list->where('block_code', $blockCode);
            $level = "BLOCK";
            $urban_code = 2;
        } else if (strtoupper(trim($level)) == 'SUBDIV') {
            $gp_ward_txt = 'WARD';
            $munc_arr = $urban_list->where('district_code', $request->district_code);
            $level = "SUBDIV";
            $urban_code = 1;
        }
        $data = array();

        return view(
            'LokkhiBhandarWCD.download_pdf.listpdf',
            [
                'urban_code' => $urban_code,
                'level' => $level,
                'gp_ward_txt' => $gp_ward_txt,
                'gp_ward_arr' => $gp_ward_arr,
                'munc_arr' => $munc_arr,
                'block_code' => $blockCode,
                'district_code' => $distCode,
                'valid' => $valid,
                'msg' => $msg,
                'fill_array' => $fill_array,
                'errors' => $errors,
                'issubmitted' => $issubmitted,
                'pdf_files' => $pdf_files
            ]
        );
    }
}
