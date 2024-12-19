<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BeneficiaryPensions;
use Auth;
use App\Configduty;
use DB;
use App\Helpers\AuthChecker;

//use Datatables;

class RetainerToPensionerController extends Controller
{
	public function __construct(){
    	$this->middleware('auth');
    	set_time_limit(180);
    }

    public function index(){
    	// $user_id = AuthChecker::getUserId();
     //    $dutyObj = Configduty::where('user_id',$user_id)->first();
     //    $dist_code = $dutyObj->district_code;
     //    $data = DB::select(DB::raw("select *,to_char(dob, 'DD-MM-YYYY') as date_of_birth from lokprasar_retainer.beneficiary where scheme_id=8 and dist_code=".$dist_code." and DATE_PART('year', AGE(current_date, dob)) >= 60 and next_level_role_id=0 and lot_generated <> 1  order by dob;"));
    	// return view('retainer-to-pensioner/linelisting',['results' => $data]);
      return view('retainer-to-pensioner/index_linelisting');
    }

    public function retainerToPensionerList(Request $request) {
      if ($request->ajax()) {
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id',$user_id)->first();
        $dist_code = $dutyObj->district_code;
        $data = DB::select(DB::raw("select *,to_char(dob, 'DD-MM-YYYY') as date_of_birth from pension.beneficiaries where scheme_id=8 and dist_code=".$dist_code." and DATE_PART('year', AGE(current_date, dob)) >= 60 and next_level_role_id=0 and lot_generated <> 1  order by dob;"));
        // dd($data);
        return datatables()->of($data)
        ->addColumn('view', function ($data) {
          $day = date('d');
          if($day >= 01 && $day < 11) { 
            return '<button onclick=editFunction('.$data->id.') class="btn btn-info">Retainer To Pensioner</button>';
          }
          else {
            return '';
          }
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname.' '.$data->ben_mname.' '.$data->ben_lname;
        })
        ->addColumn('father_name', function ($data) {
          return $data->father_fname.' '.$data->father_mname.' '.$data->father_lname;
        })
        ->addColumn('ration_card', function ($data) {
          return $data->ration_card_cat.' - '.$data->ration_card_no;
        })
        ->rawColumns(['view', 'name', 'father_name', 'ration_card'])
        ->make(true);
      }
    }

    public function store(Request $request){
      // $id = $request->ben_id;
      // $user_id=Auth::user()->id;
      // $id=15686;
      // //$result = DB::select('SELECT lokprasar_retainer.retainer_to_pensioner_migration('.$id.','.$user_id.')');
      // return redirect('retainer-to-pensioner')->with('msg','Retainer Id: '.$id.' has been changed to Pensioner successfully.');
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in form submit.');
        return response()->json($response, $statusCode);
      }
      try {
        DB::beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
        $id = $request->ben_id;
        $user_id=Auth::user()->id;
        $result = DB::select('SELECT lokprasar_retainer.retainer_to_pensioner_migration('.$id.','.$user_id.')');
        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', 8)->where('ben_id', $id)->update(['is_eligible' => false, 'is_rejected' => 40, 'rejected_at' => DB::raw("now()")]);
        $msg = "Retainer Id: ".$id." has been changed to Pensioner Id: ".$result[0]->retainer_to_pensioner_migration." successfully.";
        DB::commit();
        DB::connection('pgsql_paywrite')->commit();
        $response = array(
          'status' => 1, 'msg' => $msg,
          'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
        );
      } catch(\Exception $e) {
        DB::rollback();
        DB::connection('pgsql_paywrite')->rollback();
        $response = array(
          'exception' => true,
          'exception_message' => $e->getMessage(),
        );
        $statusCode = 400;
      } finally {
        return response()->json($response, $statusCode);
      }
    }

    public function generateReport(){
    	$user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id',$user_id)->first();
        $dist_code = $dutyObj->district_code;
    	if (request()->ajax()){
	        $data = DB::select(DB::raw("select ben_fname||' '||ben_mname||' '||ben_lname as ben_name,father_fname||' '||father_mname||' '||father_lname as father_name,ration_card_cat||'-'||ration_card_no ration_card,block_ulb_name,id,bank_ifsc,bank_code,to_char(transferred_at,'DD/MM/YYYY') as transferred_at,pensioner_id,epic_voter_id from pension.beneficiaries where scheme_id=8 and dist_code=".$dist_code." and next_level_role_id in(-95,-96) and pensioner_id is not null;"));
	        return datatables()->of($data)->make(true);
        }
    	return view('retainer-to-pensioner/retainer_to_pensioner_report');
    }
}
