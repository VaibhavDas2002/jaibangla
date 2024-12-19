<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\lot_master;
use Auth;
use App\Configduty;
use App\Helpers\AuthChecker;

class RepeatLotController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $lot_month="August";
        $user_id = AuthChecker::getUserId();        
        $scheme_id = Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
        $lots = lot_master::where('scheme_id',$scheme_id->scheme_id)
        		//->whereNotNull('ref_no')
                ->where('ref_no','>',0)
                ->where('voucher_no','>',0)
                ->whereNotNull('voucher_date')
                ->where('token_no','>',0)
                ->whereNotNull('token_date')
        		->where('lot_month',$lot_month)
        		->where('repeat_lot',0)
                ->orderBy('lot_no','desc')
        		->get();
        return view('lot-generation/repeat-lot', ['scheme_id'=>$scheme_id,'lots'=>$lots]);
    }

    public function store(Request $request)
    {
        $this->validateInput($request);
        $scheme_id = $request->scheme_id;
        $in_lot_no = $request->lot_no;
        $in_fin_year = $request->year;
        $in_lot_month = $request->month;
        $new_lot_month="September";

        $new_lot_no = DB::statement('SELECT repeat_lot(?, ?, ?, ?)', [$scheme_id, $in_lot_no, $in_fin_year,$in_lot_month]);
        if(strlen($new_lot_no)>0){
        	$lot = lot_master::where('scheme_id',$scheme_id)        		
        		->where('lot_month',$new_lot_month)
        		->orderBy('created_at', 'desc')
        		->first();
        	return redirect("repeat-lot-generation")
        	->with('success', 'Lot Generated Sussceefully')
            ->with('id', $lot->lot_no);
        }else{
        	return redirect("repeat-lot-generation")
        	->with('success', 'Please Try Again Later')
            ->with('id', $new_lot_no);
        }
        
    }

    private function validateInput($request) {
        $this->validate($request, [
        'scheme_id' => 'required',
        'year' => 'required',
        'month' => 'required',
        'lot_no' => 'required'
    	]);
    }


}
