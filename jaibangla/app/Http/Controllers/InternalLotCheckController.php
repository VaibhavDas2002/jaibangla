<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\lot_master;


class InternalLotCheckController extends Controller
{
    //
    public function ifmsStatus()
	{       
	    $lot_master = lot_master::orderBy('lot_no','desc')->get();	    
	    return view('internal-check/ifms-status', ['datas'=>$lot_master]);       
	}

	public function loadLotGenStatus($scheme_id,$lot_no){
		$datas = lot_master::where('scheme_id',$scheme_id)->where('lot_no',$lot_no)->get();
		return response()->json($datas);
	}
}
