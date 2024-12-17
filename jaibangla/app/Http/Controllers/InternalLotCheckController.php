<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
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
		
		return response()->json($datas);
	}
}
