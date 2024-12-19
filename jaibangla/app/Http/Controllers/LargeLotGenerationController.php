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

class LargeLotGenerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "store"]); 
    }

    public function getPendingCount($scheme_id,$lotmonth,$category){
        if($lotmonth=="April"){
            $created_month='04';
        }else if($lotmonth=="May"){
            $created_month='05';
        }else if($lotmonth=="June"){
            $created_month='06';
        }else if($lotmonth=="July"){
            $created_month='07';
        }else if($lotmonth=="August"){
            $created_month='08';
        }else if($lotmonth=="September"){
            $created_month='09';
        }
        if($category=="ALL"){
            $benCount=BeneficiaryPensions::where('scheme_id',$scheme_id)
                ->where('lot_generated',0)
                ->where('next_level_role_id',0)
                //->where('caste',$category)
                ->whereMonth('created_at', '=', $created_month)  //category
                ->where('payment_count',0)
                ->count();
        }else{
            $benCount=BeneficiaryPensions::where('scheme_id',$scheme_id)
                ->where('lot_generated',0)
                ->where('next_level_role_id',0)
                ->where('caste',$category)
                ->whereMonth('created_at', '=', $created_month)  //category
                ->where('payment_count',0)
                ->count();
        }
        
       return response()->json($benCount);
    }

    public function index()
    { 
    	$user_id = AuthChecker::getUserId();
        //$duty = Configduty::where('user_id','=',$user_id)->where('is_active',1)       					->get('scheme_id')->toArray();

        $duty = Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();

        //$scheme = Scheme::whereIn('id',$duty)->get();					
        $scheme = Scheme::where('id',$duty->scheme_id)->first();                                             
        $benCount=BeneficiaryPensions::where('scheme_id',$scheme->id)
        		->where('lot_generated',0)
        		->where('next_level_role_id',0)
                ->where('payment_count',0)
        		->count();
        return view('lot-generation/large-lot-index', ['scheme'=>$scheme,'benCount'=>$benCount]);
    }


    public function store(Request $request)
    {
        $this->validateInput($request);
        $scheme_id = $request->scheme;
        $in_lot_size = $request->lot_size;
        $in_fin_year = $request->year;
        $in_lot_month = $request->month;
        $in_category = $request->category;
        

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)
        					->where('is_active',1)
        					->first();
        $assignedScheme = $duty->scheme_id;

        if((Auth::user()->designation_id_old == "DDO")&&($assignedScheme==$scheme_id)&&($in_category=="ALL")){
    		$new_lot_no = DB::statement('SELECT generate_large_lot(?, ?, ?, ?)', [$in_fin_year,$in_lot_month, $scheme_id, $in_lot_size]);
	        if(strlen($new_lot_no)>0){
	        	$lot = lot_master::where('scheme_id',$scheme_id)        		
	        		->where('lot_month',$in_lot_month)
	        		->orderBy('created_at', 'desc')
	        		->first();
	        	return redirect("large-lot-generation")
	        	->with('success', 'Lot Generated Sussceefully')
	            ->with('id', $lot->lot_no);
	        }else{
	        	return redirect("large-lot-generation")
	        	->with('success', 'Please Try Again Later')
	            ->with('id', $new_lot_no);
	        }

        }else if((Auth::user()->designation_id_old == "DDO")&&($assignedScheme==$scheme_id)&&($in_category != "ALL")){
            $new_lot_no = DB::statement('SELECT generate_large_lot_category_wise(?, ?, ?, ? , ? )', [$in_fin_year,$in_lot_month, $scheme_id, $in_lot_size, $in_category ]);
            if(strlen($new_lot_no)>0){
                $lot = lot_master::where('scheme_id',$scheme_id)                
                    ->where('lot_month',$in_lot_month)
                    ->orderBy('created_at', 'desc')
                    ->first();
                return redirect("large-lot-generation")
                ->with('success', 'Lot Generated Sussceefully')
                ->with('id', $lot->lot_no);
            }else{
                return redirect("large-lot-generation")
                ->with('success', 'Please Try Again Later')
                ->with('id', $new_lot_no);
            }

        }else{
        	return redirect("large-lot-generation")
        	->with('success', 'Please Try Again Later')
            ->with('id', 0);
        }

        
        
    }

    private function validateInput($request) {
        $this->validate($request, [
        'scheme' => 'required',
        'year' => 'required',
        'month' => 'required',
        'lot_size' => 'required',
        'category' => 'required'        
    	]);
    }


}
