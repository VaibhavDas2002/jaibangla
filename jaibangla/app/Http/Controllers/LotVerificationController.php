<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\District;
use App\Taluka;
use App\ben_lot_month;
use App\PensionSc;
use App\lot_no_seeder;
use App\lot_master;


use Excel;
use DOMDocument;
use Response;
use Carbon;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
///////////03-04-2020 start/////////
use App\Configduty;
use Auth;
 ///////////03-04-2020 end/////////
class LotVerificationController extends Controller
{   
     ///////////03-04-2020 start/////////
    public function selectYearMonth(){
        $user_id = Auth::user()->id;
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();
        return view('lot-verification/selectYearMonth',['schemes'=>$schemes]);
    }
     ///////////03-04-2020 end/////////

    public function index(Request $request)
    {
        //dd($request->lot_year ,$request->lot_month);
       //old code
        ///////////03-04-2020 start/////////
        $lot_master = lot_master::where('lot_year',$request->lot_year)
                    ->where('lot_month',$request->lot_month)->where('scheme_id',$request->scheme)->get();
         ///////////03-04-2020 end/////////
        if(empty($lot_master)){
            return redirect("/")->with('success','PLEASE GENERATE LOT ');
        }
        
        //dd($lot_master);
        return view('lot-verification/index', ['datas'=>$lot_master]);

       /** if(request()->ajax())
        { 
          $data = lot_master::all(); 
          return datatables()->of($data)
          ->addColumn('ben_count', function ($data){
                    return '<a href="'. route('push-to-ifms.showlist',['lot_no'=>$data->lot_no,
                        'scheme_id'=>$data->scheme_id]) .'" class="btn btn-xs btn-primary">'.$data->ben_count.'</a>';
                    })
          ->addColumn('scheme_name', function ($data) {
                                return $data->Scheme->scheme_name;
                                })
            // ->addColumn('check', function ($data) {
            //             return '<input type="checkbox" name="lot_numbers[] checked hidden">';
            //             })
          ->rawColumns(['ben_count','scheme_name'])
          ->make(true);
        }
        return view('push-ifms/index');**/
    }



     public function showlist(Request $request)
    {
        $lot_no=$request->lot_no;
        //$scheme_id=$request->scheme_id;

        $ben_lot_month = ben_lot_month::where('drn_part',$lot_no)->get();
        //$districts = District::all();
        //$flag=0;
        //dd($ben_lot_month);
        return view('linelisting_showlist', ['datas'=>$ben_lot_month]);

       // return view('employee-report-drilldown/index');
    }

    
}
