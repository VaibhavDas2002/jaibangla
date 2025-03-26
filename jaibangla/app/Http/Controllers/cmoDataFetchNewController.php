<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Helpers\APICurl;
use App\Helpers\JWTToken;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Configduty;
use App\getModelFunc;
use App\UrbanBody;
use App\GP;
use App\MapLavel;
use Maatwebsite\Excel\Facades\Excel;
use App\DocumentType;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\DsPhase;
use App\Scheme;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Traits\TraitCMOValidate;
class cmoDataFetchNewController extends Controller
{
    use TraitCMOValidate;
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index(){
        return view('Cmo_data_fetching/index');
    }
    public function dataFetch(Request $request){
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            ini_set('memory_limit', '-1');
            $from_date = Carbon::parse($request->from_date)->format('Y-m-d 00:00:00');
            $to_date = Carbon::parse($request->to_date)->format('Y-m-d 00:00:00');
            $cmo_data = $this->pullNewCmo($from_date,$to_date);
            $data = json_decode($cmo_data->getContent(), true);
            $status = $data['status'];
            if($status == 200){
                return $response = [
                    'status' => 1,
                    'msg' => 'Data Fetch Successfully',
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success',
                ];
            }else if($status == 400){
                return $response = [
                    'status' => 3,
                    'msg' => 'No Record Found',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }else if($status == 500){
                return $response = [
                    'status' => 3,
                    'msg' => 'Curl Error Occured',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }else if($status == 300){
                $message = $data['message'];
                return $response = [
                    'status' => 3,
                    'msg' => $message,
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            dd($e);
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' =>
                //     'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }
}
