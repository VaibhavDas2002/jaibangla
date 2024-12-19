<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Configduty;
use App\Scheme;
use App\lot_master;
use App\Helpers\AuthChecker;

class LargeLotGenerationSbiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "store"]); 
    }
//not used from this controller
//being used from LargeLotGenerationController
    public function getPendingCount($scheme_id,$lotmonth){
        if($lotmonth=="April"){
            $created_month='04';
        }else if($lotmonth=="May"){
            $created_month='05';
        }
        else if($lotmonth=="June"){
            $created_month='06';
        }
        else if($lotmonth=="July"){
            $created_month='07';
        }
        else if($lotmonth=="August"){
            $created_month='08';
        }
        else if($lotmonth=="September"){
            $created_month='09';
        }
        else if($lotmonth=="October"){
            $created_month='10';
        }
        else if($lotmonth=="November"){
            $created_month='11';
        }
        else if($lotmonth=="December"){
            $created_month='12';
        }
        else if($lotmonth=="January"){
            $created_month='01';
        }
        else if($lotmonth=="February"){
            $created_month='02';
        }
        else if($lotmonth=="March"){
            $created_month='03';
        }
        $benCount=BeneficiaryPensions::where('scheme_id',$scheme_id)
                ->where('lot_generated',0)
                ->where('next_level_role_id',0)
                ->whereMonth('created_at', '=', $created_month)
                ->where('payment_count',0)
                ->count();
       return response()->json($benCount);
    }
//end use
    public function index()
    { 
    	$user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)
        					->where('is_active',1)
        					->first();

        $scheme = Scheme::where('id',$duty->scheme_id)->first();					
        $benCount=BeneficiaryPensions::where('scheme_id',$scheme->id)
        		->where('lot_generated',0)
        		->where('next_level_role_id',0)
                ->where('payment_count',0)
        		->count();
        return view('lot-generation/large-lot-sbi-index', ['scheme'=>$scheme,'benCount'=>$benCount]);
    }


    public function store(Request $request)
    {
        $this->validateInput($request);
        $scheme_id = $request->scheme;
        $in_lot_size = $request->lot_size;
        $in_fin_year = $request->year;
        $in_lot_month = $request->month;
        

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)
        					->where('is_active',1)
        					->first();
        $assignedScheme = $duty->scheme_id;

        if((Auth::user()->designation_id_old == "DDO")&&($assignedScheme==$scheme_id)){
    		$new_lot_no = DB::statement('SELECT sbi.generate_large_lot(?, ?, ?, ?, ?, ?)', [$in_fin_year,$in_lot_month, $scheme_id, $in_lot_size, '', '']);
	        if(strlen($new_lot_no)>0){
	        	$lot = lot_master::where('scheme_id',$scheme_id)        		
	        		->where('lot_month',$in_lot_month)
	        		->orderBy('created_at', 'desc')
	        		->first();
	        	return redirect("large-lot-generation-sbi")
	        	->with('success', 'Lot Generated Sussceefully')
	            ->with('id', $lot->lot_no);
	        }else{
	        	return redirect("large-lot-generation-sbi")
	        	->with('success', 'Please Try Again Later')
	            ->with('id', $new_lot_no);
	        }

        }else{
        	return redirect("large-lot-generation-sbi")
        	->with('success', 'Please Try Again Later')
            ->with('id', 0);
        }

        
        
    }

    private function validateInput($request) {
        $this->validate($request, [
        'scheme' => 'required',
        'year' => 'required',
        'month' => 'required',
        'lot_size' => 'required'
    	]);
    }


}
