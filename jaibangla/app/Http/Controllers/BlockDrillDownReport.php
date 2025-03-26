<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taluka;
use App\District;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
//sayantika 21-03-2020
use App\UrbanBody;
use App\SubDistrict;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuthChecker;
use App\DsPhase;
use App\Workflow;
use App\SchemeStepRank;
class BlockDrillDownReport extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = Scheme::where('is_active', 1)->get(['scheme_name as name', 'id as id']);
        //$districts = District::all();
        $district_code = $duty->district_code;

        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();

        $is_active = $duty->is_active;
        //dd($is_active);
        if ($is_active == 1) {

            return view('Block-Drilldown.index')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
        }
        if ($is_active == 0) {

            return redirect("/")->with('success', 'User Disabled');
        }
    }




    public function indexdist($district_code)
    {

        $district_name = District::where('district_code', $district_code)->pluck('district_name')
            ->first();
        $schemes = Scheme::get(['scheme_name as name', 'id as id']);


        return view('Block-Drilldown.index')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
    }

    public function getdata(Request $request)
    {
        if (request()->ajax()) {
            if (empty($request->level1a) && !empty($request->level2) && empty($request->level3)) {

                $district_code = $request->level2;
                $scheme_id = $request->level1a;
                $posts_taluka = Taluka::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_block.block_code', '=', 't.local_body_code')->addBinding($district_code, 'select')
                    ->addBinding($scheme_id, 'select') //->limit($limit)->orderBy($order,$dir)
                    ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_block.block_code as block_code', 'm_block.block_name as block_name');


                $data = SubDistrict::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_sub_district.sub_district_code', '=', 't.local_body_code')->addBinding($district_code, 'select')
                    ->addBinding($scheme_id, 'select') //->limit($limit)->orderBy($order,$dir)
                    ->unionAll($posts_taluka)
                    //->limit($limit)->orderBy($order,$dir)
                    ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_sub_district.sub_district_code as block_code', 'm_sub_district.sub_district_name as block_name')
                    ->get(['t.scheme_id', 't.application_submitted', 't.application_verified', 't.application_approved', 'block_code', 'block_name']);
            } elseif (!empty($request->level1a) && !empty($request->level2) && empty($request->level3)) {
                //dd($request->level1a,$request->level2);
                //dd("rural");
                $district_code = $request->level2;
                $scheme_id = $request->level1a;
                $posts_taluka = Taluka::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_block.block_code', '=', 't.local_body_code')->addBinding($district_code, 'select')->addBinding($scheme_id, 'select') //->limit($limit)->orderBy($order,$dir)
                    ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_block.block_code as block_code', 'm_block.block_name as block_name');

                $data = SubDistrict::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_sub_district.sub_district_code', '=', 't.local_body_code')->addBinding($district_code, 'select')
                    ->addBinding($scheme_id, 'select')
                    ->unionAll($posts_taluka)
                    ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_sub_district.sub_district_code as block_code', 'm_sub_district.sub_district_name as block_name')
                    ->get();
                //->get(['t.scheme_id','t.application_submitted as application_submitted','t.application_verified','t.application_approved','block_code','block_name']);
                //dd($datas);

            } elseif (!empty($request->level1a) && !empty($request->level2) && !empty($request->level3)) {
                //dd($request->level1a,$request->level2);
                $district_code = $request->level2;
                $scheme_id = $request->level1a;

                if ($request->level3 == "Rural") {

                    $data = Taluka::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_block.block_code', '=', 't.local_body_code')->addBinding($district_code, 'select')->addBinding($scheme_id, 'select') //->limit($limit)->orderBy($order,$dir)
                        ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_block.block_code as block_code', 'm_block.block_name as block_name')
                        ->get();
                    //dd("rural");

                } else {
                    $data = SubDistrict::where('district_code', $district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"), 'm_sub_district.sub_district_code', '=', 't.local_body_code')->addBinding($district_code, 'select')
                        ->addBinding($scheme_id, 'select')
                        //->unionAll($posts_taluka)
                        ->select('t.scheme_id', 't.application_submitted as application_submitted', 't.application_verified as application_verified', 't.application_approved as application_approved', 'm_sub_district.sub_district_code as block_code', 'm_sub_district.sub_district_name as block_name')
                        ->get();
                    //->get(['t.scheme_id','t.application_submitted as application_submitted','t.application_verified','t.application_approved','block_code','block_name']);
                    //dd($datas);

                }
            }
            return datatables()->of($data)
                ->addColumn('application_submitted', function ($data) {
                    if ($data->application_submitted != Null) {
                        return '<a href="' . route('block-drill-down-submiited', [$data->block_code, $data->scheme_id]) . '">' . $data->application_submitted . '</a>';
                    } else {
                        return 0;
                    }
                })
                ->addColumn('application_verified', function ($data) {
                    if ($data->application_verified != Null) {
                        return '<a href="' . route('block-drill-down-verified', [$data->block_code, $data->scheme_id]) . '">' . $data->application_verified . '</a>';
                    } else {
                        return 0;
                    }
                })
                ->addColumn('application_approved', function ($data) {
                    if ($data->application_approved != Null) {
                        return '<a href="' . route('block-drill-down-approved', [$data->block_code, $data->scheme_id]) . '">' . $data->application_approved . '</a>';
                    } else {
                        return 0;
                    }
                })
                ->rawColumns(['application_submitted', 'application_verified', 'application_approved'])
                ->make(true);
        }
        return view('Block-Drilldown.index')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
    }
    /****
  public function getdata1(Request $request){
  DB::enableQueryLog();
  
  if($request->level2!=Null){
     $district_code=$request->level2;
  }
  else{
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id','=',$user_id)->first();  
    $district_code=$duty->district_code;
   } 	   
 
        
    $columns = array( 
                            0 =>'block_name', 
                            1 =>'applications_submitted',
                            2=> 'approval_pending',
                            3=>'approved',   
                  );
  
  $scheme_id=$request->level1a;
       
  $constraints = [
      'level1a' => $request['level1a'],   
    ];
        
   
  //sayantika 21-03-2020
  $posts_taluka=Taluka::where('district_code',$district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"),'m_block.block_code','=','t.local_body_code')->addBinding($district_code,'select')->addBinding($scheme_id,'select')//->limit($limit)->orderBy($order,$dir)
      ->select('t.scheme_id','t.application_submitted as application_submitted','t.application_verified as application_verified','t.application_approved as application_approved','m_block.block_code as block_code','m_block.block_name as block_name');
      // ->get(['t.scheme_id','t.application_submitted','t.application_verified','t.application_approved',' block_code','block_name'])->count();

  $totalData=UrbanBody::where('district_code',$district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"),'m_urban_body.urban_body_code','=','t.local_body_code')->addBinding($district_code,'select')->addBinding($scheme_id,'select')//->limit($limit)->orderBy($order,$dir)
        ->select('t.scheme_id','t.application_submitted as application_submitted','t.application_verified as application_verified','t.application_approved as application_approved','m_urban_body.urban_body_code as block_code','m_urban_body.urban_body_name as block_name')
        ->unionAll($posts_taluka)
        ->get(['t.scheme_id','t.application_submitted','t.application_verified','t.application_approved','block_code','block_name'])->count();
        
  //$totalData=$posts_taluka+$posts_urban;    
  //dd($totalData);  
  $totalFiltered = $totalData; 
  $limit = $request->input('length');
  $start = $request->input('start');
  $order = $columns[$request->input('order.0.column')];
  $dir = $request->input('order.0.dir');

 //dd($totalData);
            
  if(empty($request->input('search.value')))
  { 
              //sayantika 21-03-2020
    $posts_taluka=Taluka::where('district_code',$district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"),'m_block.block_code','=','t.local_body_code')->addBinding($district_code,'select')->addBinding($scheme_id,'select')//->limit($limit)->orderBy($order,$dir)
      ->select('t.scheme_id','t.application_submitted as application_submitted','t.application_verified as application_verified','t.application_approved as application_approved','m_block.block_code as block_code','m_block.block_name as block_name');
      //->get(['t.scheme_id','t.application_submitted','t.application_verified','t.application_approved',' block_code','block_name'])->toArray();

      $posts=$posts_urban=UrbanBody::where('district_code',$district_code)->leftJoin(DB::raw("(select local_body_code,application_submitted,application_verified,application_approved,scheme_id from pension.pension_statistics as pension_statistics where dist_code=? and scheme_id=?)t"),'m_urban_body.urban_body_code','=','t.local_body_code')->addBinding($district_code,'select')->addBinding($scheme_id,'select')//->limit($limit)->orderBy($order,$dir)
        ->unionAll($posts_taluka)
        ->limit($limit)->orderBy($order,$dir)
        ->select('t.scheme_id','t.application_submitted as application_submitted','t.application_verified as application_verified','t.application_approved as application_approved','m_urban_body.urban_body_code as block_code','m_urban_body.urban_body_name as block_name')
        
        
        ->get(['t.scheme_id','t.application_submitted','t.application_verified','t.application_approved','block_code','block_name']);//->toArray();

     // $merged=array_merge($posts_taluka,$posts_urban);
     // $posts=collect($merged);
      //$totalData=$posts->count();
      //$totalFiltered=$totalData; 
//dd($posts);
      //$posts=$merged->all();
            //dd("taluka",$posts_taluka,"urban",$posts_urban,"merge",$merged);
//dd(DB::getQueryLog(),$posts);
                   
      
  }
  $data = array();
  if(!empty($posts))
  {//dd($posts);
    foreach ($posts as $post)
    {
     // foreach($value as $post){
      //dd($value['block_name']);
      $block_code=$post->block_code;
     
      $scheme_id=$post->scheme_id;

      $app_submitted=route('block-drill-down-submiited',[$block_code,$scheme_id]);
      $app_verified=route('block-drill-down-verified',[$block_code,$scheme_id]);
      $app_approved=route('block-drill-down-approved',[$block_code,$scheme_id]);

      if($post->application_submitted){
        $application_submitted=0;
        $nestedData['application_submitted'] ="{$application_submitted}";
        }else{
        $nestedData['application_submitted'] ="<a href='{$app_submitted}'>".$post->application_submitted."</a>";  //
        }
                
      if($post->application_verified==null){
        $application_verified=0;
        $nestedData['application_verified'] = "{$application_verified}";
        }else{
        $nestedData['application_verified'] = "<a href='{$app_verified}'>".$post->application_verified."</a>";
        }
                
      if($post->application_approved==null){
      $application_approved=0;
      $nestedData['application_approved'] = "{$application_approved}";

      }else{
      $nestedData['application_approved'] = "<a href='{$app_approved}'>".$post->application_approved."</a>";
      }
      
      $nestedData['block_name'] = $post->block_name;
              
      $data[] = $nestedData;

    //  }


    }
            //dd($data);
  }
      // dd($totalData,$totalFiltered);   
  $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
  echo json_encode($json_data);
}   
     *****/


    public function convertdata($search)
    {
        $converted = (int) $search;
        return $converted;
    }

    public function getlistsubmitted($block_code, $scheme_id)
    {

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();

        //$district_code=$duty->district_code;

        $block_name = Taluka::where('block_code', $block_code)->pluck('block_name')->first();
        $district_code = Taluka::where('block_code', $block_code)->pluck('district_code')->first();
        $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();

        if ($scheme_id == 1) {
            $pr1 = 'st';
        } elseif ($scheme_id == 2) {
            $pr1 = 'manabik';
        } else {
            $pr1 = 'sc';
        }

        $results = BeneficiaryPensions::where('created_by_local_body_code', $block_code)->where('scheme_id', $scheme_id)
            // ->select('id as id','ben_fname as first_name','ben_mname as middle_name','ben_lname as last_name','dob as dob','gender as gender','dist_name','assembly_name')
            ->orderby('pension.beneficiaries.id')->get(); //dd($results);
        //dd($results);

        return view(
            'Block-Drilldown.linelisting',
            [
                'results' => $results,
            ]
        )->with('level1a', $scheme_id)
            ->with('district_name', $district_name)
            ->with('block_name', $block_name)
            ->with('message', 'Applications Submitted')
            ->with('pr1', $pr1);
    }


    public function getlistapproved($block_code, $scheme_id)
    {

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();

        //$district_code=$duty->district_code;
        $block_name = Taluka::where('block_code', $block_code)->pluck('block_name')->first();
        $district_code = Taluka::where('block_code', $block_code)->pluck('district_code')->first();
        $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();

        if ($scheme_id == 1) {
            $pr1 = 'st';
        } elseif ($scheme_id == 2) {
            $pr = 'manabik';
        } else {
            $pr1 = 'sc';
        }



        $results = BeneficiaryPensions::where('created_by_local_body_code', $block_code)
            ->where('scheme_id', $scheme_id)->where('next_level_role_id', 0)
            // ->select('id as id','ben_fname as first_name','ben_mname as middle_name','ben_lname as last_name','dob as dob','gender as gender','dist_name','assembly_name')
            ->orderby('pension.beneficiaries.id')->get(); //dd($results);
        //dd($results);

        return view(
            'Block-Drilldown.linelisting',
            [
                'results' => $results,
            ]
        )->with('level1a', $scheme_id)
            ->with('district_name', $district_name)
            ->with('block_name', $block_name)
            ->with('message', 'Applications Approved')
            ->with('pr1', $pr1);
    }


    public function getlistverified($block_code, $scheme_id)
    {

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();

        //$district_code=$duty->district_code;
        $block_name = Taluka::where('block_code', $block_code)->pluck('block_name')->first();
        $district_code = Taluka::where('block_code', $block_code)->pluck('district_code')->first();
        $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();

        if ($scheme_id == 1) {
            $pr1 = 'st';
        } elseif ($scheme_id == 2) {
            $pr = 'manabik';
        } else {
            $pr1 = 'sc';
        }

        $results = BeneficiaryPensions::where('created_by_local_body_code', $block_code)
            ->where('scheme_id', $scheme_id)->where('is_verified', 1)->where('is_approved', 0)->where('is_rejected', 0)
            ->get();

        return view(
            'Block-Drilldown.linelisting',
            [
                'results' => $results,
            ]
        )->with('level1a', $scheme_id)
            ->with('district_name', $district_name)
            ->with('block_name', $block_name)
            ->with('message', 'Applications Verified')
            ->with('pr1', $pr1);
        ;
    }


    //Payment Drill Down 
    public function payment($type)
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        //$schemes = Scheme::get(['scheme_name as name', 'id as id']);
        $schemes = DB::select(DB::raw("select id,scheme_name as name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 ) and is_active=1 order by scheme_name"));
        //$districts = District::all();
        $district_code = $duty->district_code;

        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();

        // $is_active = $duty->is_active;
        return view('Block-Drilldown.payment')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('type', $type);
        //dd($is_active);
        // if ($is_active == 1) {


        // }
        // if ($is_active == 0) {

        //     return redirect("/")->with('success', 'User Disabled');
        // }
    }
    public function indexdistpayment($district_code, $type)
    {

        $district_name = District::where('district_code', $district_code)->pluck('district_name')
            ->first();
        $schemes = Scheme::get(['scheme_name as name', 'id as id']);


        return view('Block-Drilldown.payment')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('type', $type);
    }
    public function getpaymentdata(Request $request)
    {
        if (request()->ajax()) {
            $district_code = $request->level2;
            $scheme_id = $request->level1a;
            $level = $request->level3; // Rural/Urban
            $type = $request->type;

            $table_name = 'pension.beneficiaries';
            if ($scheme_id == 1) {
                $table_name = 'johar.beneficiaries';
            } else if ($scheme_id == 2) {
                $table_name = 'manabik.beneficiaries';
            } else if ($scheme_id == 3) {
                $table_name = 'bandhu.beneficiaries';
            }

            if (empty($district_code)) {

                $data = array();
            } else {

                $filter = array();
                $filter['dist_code'] = $district_code;
                // if(!empty($level)){
                //   $filter['rural_urban'] = $level;
                // }
                if (!empty($scheme_id)) {
                    $filter['scheme_id'] = $scheme_id;
                }
                $lot_generated = "-1";
                $bank_edited = "1";
                if ($type == 'RBI') {
                    $lot_generated = "-2";
                    //$bank_edited = "1";
                }
                if ($type == 'SBI') {
                    $lot_generated = "-3";
                }
                $filter['lot_generated'] = $lot_generated;
                $filter['bank_edited'] = $bank_edited;

                $data = array();

                if ($level != 'Rural') {
                    $fetcheddata = array();

                    $urbanquery = "select sub_district_name as block_name, 'Urban' as Level, b.failed, b.rectified
          FROM
          (select sub_district_name, 
          coalesce(count(b.id) FILTER(where lot_generated=:lot_generated and next_level_role_id=0),0) as failed,
          coalesce(count(b.id) FILTER(where bank_edited=:bank_edited and lot_generated=:lot_generated and next_level_role_id=0),0) as rectified
          from " . $table_name
                        . " b, m_sub_district ub  where rural_urban_id=1 and created_by_dist_code=:dist_code  and b.created_by_local_body_code=ub.sub_district_code
          group by ub.sub_district_name";
                    if (!is_null($scheme_id)) {
                        $urbanquery = $urbanquery . ', scheme_id having scheme_id=:scheme_id';
                    }
                    $urbanquery = $urbanquery . ") b
          order by sub_district_name";

                    $fetcheddata = DB::connection('pgsql_mis')->select($urbanquery, $filter);
                    $data = array_merge($data, $fetcheddata);
                }


                if ($level != 'Urban') {
                    $fetcheddata = array();
                    $ruralquery = "select b.block_name, 'Rural' as Level,
          b.failed,b.rectified
          FROM
          (select bl.block_name, 
          coalesce(count(id) FILTER(where lot_generated=:lot_generated and next_level_role_id=0),0) as failed,
          coalesce(count(id) FILTER(where bank_edited=:bank_edited and lot_generated=:lot_generated and next_level_role_id=0),0) as rectified
          from " . $table_name
                        . " b ,m_block bl where rural_urban_id=2 and created_by_dist_code=:dist_code and b.created_by_local_body_code=bl.block_code
                        and b.created_by_dist_code=bl.district_code
          group by bl.block_name";
                    if (!is_null($scheme_id)) {
                        $ruralquery = $ruralquery . ', scheme_id having scheme_id=:scheme_id';
                    }
                    $ruralquery = $ruralquery . ") b
          order by block_name";

                    $fetcheddata = DB::connection('pgsql_mis')->select($ruralquery, $filter);
                    $data = array_merge($data, $fetcheddata);
                }
            }

            return datatables()->of($data)
                ->addColumn('level', function ($data) {
                    // if($data->level!=Null){
                    return $data->level;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('block_name', function ($data) {
                    //if($data->block_name!=Null){
                    return $data->block_name;
                    //}
                    //else{
                    // return 0;
                    //}
                })
                ->addColumn('failed', function ($data) {
                    // if($data->failed!=Null){
                    return $data->failed;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('rectified', function ($data) {
                    //  if($data->rectified!=Null){
                    return $data->rectified;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('pending', function ($data) {
                    // if($data->rectified!=Null){
                    return $data->failed - $data->rectified;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->rawColumns(['level', 'block_name', 'failed', 'rectified', 'pending'])
                ->make(true);

            // 
            // ->make(true); 
        }
        return view('Block-Drilldown.payment')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('type', $type);
    }



    //Consolidated Report Block/Municipality wise
    public function consol_report()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $district_code = $duty->district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
        $c_time = Carbon::now();
        $year = $c_time->year;
        if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
            $first_part = $year - 1;
            $second_part = $year;
        } else {
            $first_part = $year;
            $second_part = $year + 1;
        }
        $select_year = $first_part . '-' . $second_part;
        $monthName = $c_time->format('F');
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('Block-Drilldown.block_consolidate_report')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('selected_year', $select_year)->with('selected_month', $monthName);
        ;
    }


    public function getconsol_reportData(Request $request)
    {
        $schemes = array();
        if (request()->ajax()) {
            $user_id = AuthChecker::getUserId();
            $scheme_id = $request->scheme_id;
            $rural_urban = $request->rural_urban;
            $year = $request->fin_year;
            $month = $request->month;
            $payment_option = $request->payment_option;
            $district_code = $request->district_code;
            $tld_table = 'transaction_lot_details';
            // if ($payment_option == 2) {
            //     $tld_table = 'transaction_lot_details_report';
            // }

            // New Changes on 10-01-2023 after breaking up transaction_lot_details_report table
            $year_arr = explode('-', $year);
            $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
            $tld_table = 'transaction_lot_details_report_' . $yyyy_val;
            // end changes on 10-01-2023

            $table_name = 'pension.beneficiaries';
            if (!is_null($scheme_id)) {
                $schemes_arr = Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
                $parameter['scheme_id'] = $scheme_id;
                $schema_name = $schemes_arr->short_code;
                //dd($schema_name);
                if (empty($schema_name))
                    $schema_name = 'pension';
                $table_name = $schema_name . '.beneficiaries';
            } else {
                $schemes_in_arr = Configduty::select('scheme_id')->where('user_id', '=', $user_id)->get();
                $schemes_in = array();
                // dd($schemes_in_arr);
                foreach ($schemes_in_arr as $schm) {
                    array_push($schemes_in, $schm->scheme_id);
                }
                //dd($schemes_in);
            }
            if (empty($district_code)) {

                $data = array();
            } else {
                $filter = array();
                $filter['dist_code'] = $district_code;
                // if(!empty($level)){
                //   $filter['rural_urban'] = $level;
                // }
                if (!is_null($scheme_id)) {
                    $filter['scheme_id'] = $scheme_id;
                }
                if (!empty($month)) {
                    $filter['lot_month'] = $month;
                }
                if (!empty($year)) {
                    $filter['lot_year'] = $year;
                }

                $data = array();

                if ($rural_urban != 'Rural') {
                    $fetcheddata = array();

                    $query = "select mb.urban_body_name as block_ulb_name, 'Urban' as Level,
            coalesce(count(distinct b.id),0) as applied,
            coalesce(count(distinct b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as verified,
            coalesce(count(distinct b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
            coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2)),0) as pushed_ifms,
            coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0),0) as mandate_generated
            FROM (select * from " . $table_name . " where rural_urban_id=1";
                    $query = $query . ' and dist_code= :dist_code';
                    if (!is_null($scheme_id)) {
                        $query = $query . ' and scheme_id = :scheme_id';
                    } else {
                        $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
                    }
                    $query = $query . ') b left join 
                    m_urban_body mb on mb.urban_body_code=b.block_ulb_code LEFT JOIN
                (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount 
                from ifms.' . $tld_table . ' e right join lot_master lm 
                on e.drn_part = lm.lot_no';
                    if (!is_null($year)) {
                        $query = $query . ' and lm.lot_year= :lot_year';
                    }
                    if (!is_null($month)) {
                        $query = $query . ' and lm.lot_month= :lot_month AND lm.ref_no>0 ';
                    }
                    if (!is_null($scheme_id)) {
                        $query = $query . ' and lm.scheme_id = :scheme_id';
                    } else {
                        $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
                    }
                    $query = $query . ') l
                on b.id = l.pension_id and b.scheme_id = l.scheme_id
                group by mb.urban_body_name order by mb.urban_body_name';
                    $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
                    //return response()->json(['status'=>$query, 'filter' => $filter]);
                    $data = array_merge($data, $fetcheddata);
                }

                if ($rural_urban != 'Urban') {
                    $fetcheddata = array();

                    $query = "select mb.block_name as block_ulb_name, 'Rural' as Level,
            coalesce(count(distinct b.id),0) as applied,
            coalesce(count(distinct b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as verified,
            coalesce(count(distinct b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
            coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0),0) as pushed_ifms,
            coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0 and l.ref_no>0),0) as mandate_generated
            FROM (select * from " . $table_name . " where rural_urban_id=2";
                    $query = $query . ' and dist_code= :dist_code';

                    if (!is_null($scheme_id)) {
                        $query = $query . ' and scheme_id = :scheme_id';
                    } else {
                        $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
                    }
                    $query = $query . ') b left join 
                    m_block mb on mb.block_code=b.block_ulb_code LEFT JOIN
                (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount 
                from ifms.' . $tld_table . ' e right join lot_master lm 
                on e.drn_part = lm.lot_no';
                    if (!is_null($year)) {
                        $query = $query . ' and lm.lot_year= :lot_year';
                    }
                    if (!is_null($month)) {
                        $query = $query . ' and lm.lot_month= :lot_month';
                    }
                    if (!is_null($scheme_id)) {
                        $query = $query . ' and lm.scheme_id = :scheme_id';
                    } else {
                        $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
                    }
                    $query = $query . ') l
                on b.id = l.pension_id and b.scheme_id = l.scheme_id 
                group by mb.block_name order by mb.block_name';
                    //return response()->json(['status'=>$query, 'filter' => $filter]);
                    $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
                    $data = array_merge($data, $fetcheddata);
                }
            }

            return datatables()->of($data)
                ->addColumn('level', function ($data) {
                    // if($data->level!=Null){
                    return $data->level;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('block_name', function ($data) {
                    //  if($data->block_ulb_name!=Null){
                    return $data->block_ulb_name;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('applied', function ($data) {
                    //   if($data->applied!=Null){
                    return $data->applied;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('verified', function ($data) {
                    //   if($data->verified!=Null){
                    return $data->verified;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('approved', function ($data) {
                    //    if($data->approved!=Null){
                    return $data->approved;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('pushed_ifms', function ($data) {
                    //  if($data->pushed_ifms!=Null){
                    return $data->pushed_ifms;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('mandate_generated', function ($data) {
                    //  if($data->mandate_generated!=Null){
                    return $data->mandate_generated;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('amount_booked', function ($data) {
                    //   if($data->mandate_generated!=Null){
                    return $data->mandate_generated * 1000;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->rawColumns(['district_name', 'applied', 'verified', 'approved', 'pushed_ifms', 'mandate_generated', 'amount_booked'])
                ->make(true);
        }
        return view('Block-Drilldown.block_consolidate_report')->with('schemes', $schemes);
    }

    public function indexdistconsol($district_code)
    {

        $user_id = AuthChecker::getUserId();
        $district_code = $district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
        $c_time = Carbon::now();
        $year = $c_time->year;
        if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
            $first_part = $year - 1;
            $second_part = $year;
        } else {
            $first_part = $year;
            $second_part = $year + 1;
        }
        $select_year = $first_part . '-' . $second_part;
        $monthName = $c_time->format('F');
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));

        return view('Block-Drilldown.block_consolidate_report')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('selected_year', $select_year)->with('selected_month', $monthName);
    }


    public function consol_report_sbi()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $district_code = $duty->district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
        $c_time = Carbon::now();
        $year = $c_time->year;
        if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
            $first_part = $year - 1;
            $second_part = $year;
        } else {
            $first_part = $year;
            $second_part = $year + 1;
        }
        $select_year = $first_part . '-' . $second_part;
        $monthName = $c_time->format('F');
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('Block-Drilldown.block_consolidate_report_sbi')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('selected_year', $select_year)->with('selected_month', $monthName);
    }


    public function getconsol_reportData_sbi(Request $request)
    {
        //DB::enableQueryLog();
        $schemes = array();
        if (request()->ajax()) {
            $user_id = AuthChecker::getUserId();
            $scheme_id = $request->scheme_id;
            $rural_urban = $request->rural_urban;
            $year = $request->fin_year;
            $month = $request->month;

            $district_code = $request->district_code;
            $schemes_in = array();

            if (!is_null($scheme_id)) {
                $schemes_arr = Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->where('is_active', 1)->first();
                $parameter['scheme_id'] = $scheme_id;
                $schema_name = $schemes_arr->short_code;
                //dd($schema_name);

                $table_name = $schema_name . '.beneficiaries';

            } else {
                $schemes_in_arr = Configduty::select(DB::raw('distinct scheme_id'))->where('user_id', '=', $user_id)->where('is_active', 1)->get();

                // dd($schemes_in_arr);
                foreach ($schemes_in_arr as $schm) {
                    array_push($schemes_in, $schm->scheme_id);
                }
                //  dd($schemes_in);
                $table_name = 'pension.beneficiaries';
            }
            if (empty($district_code)) {

                $data = array();
            } else {
                $filter = array();
                $filter['dist_code'] = $district_code;
                if (!empty($level)) {
                    $filter['rural_urban'] = $level;
                }
                if (!is_null($scheme_id)) {
                    $filter['scheme_id'] = $scheme_id;
                }

                if (!empty($month)) {
                    $filter['lot_month'] = $month;
                }
                if (!empty($year)) {
                    $filter['lot_year'] = $year;
                }
            }
            $data = array();
            //echo $rural_urban;die;
            if ($rural_urban == 'Rural') {
                $query = $this->getRuralSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id);
            } else if ($rural_urban == 'Urban') {
                $query = $this->getUrbanSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id);
            } else {
                $query = $this->getAllSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id);
            }

            //echo  $query ;die;
            $delete = DB::connection('pgsql_main_mis')->select(DB::raw("delete from  sbi.payment_report where user_id=" . $user_id));
            $insert = DB::connection('pgsql_main_mis')->select($query, $filter);
            $data = DB::connection('pgsql_main_mis')->select(DB::raw("select * from  sbi.payment_report where user_id=" . $user_id));
            return datatables()->of($data)
                ->addColumn('level', function ($data) {
                    // if($data->level!=Null){
                    return $data->level;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('block_name', function ($data) {
                    //  if($data->block_ulb_name!=Null){
                    return $data->block_ulb_name;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('applied', function ($data) {
                    //   if($data->applied!=Null){
                    return $data->applied;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('to_be_verified', function ($data) {
                    //   if($data->verified!=Null){
                    return $data->to_be_verified;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('to_be_approved', function ($data) {
                    //   if($data->verified!=Null){
                    return $data->to_be_approved;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('approved', function ($data) {
                    //    if($data->approved!=Null){
                    return $data->approved;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('current_applied', function ($data) {
                    //   if($data->applied!=Null){
                    return $data->current_applied;
                    // }
                    // else{
                    //   return 0;
                    // }
                })
                ->addColumn('current_to_be_verified', function ($data) {
                    //   if($data->verified!=Null){
                    return $data->current_to_be_verified;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('current_to_be_approved', function ($data) {
                    return $data->current_to_be_approved;
                })
                ->addColumn('current_approved', function ($data) {
                    //    if($data->approved!=Null){
                    return $data->current_approved;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('pushed_sbi', function ($data) {
                    //  if($data->pushed_ifms!=Null){
                    return $data->pushed_sbi;
                    // }else{
                    //     return 0;
                    //   }
                })
                ->addColumn('amount_booked', function ($data) {
                    //  if($data->pushed_ifms!=Null){
                    return $data->pushed_sbi * 1000;
                    // }else{
                    //     return 0;
                    //   }
                })

                ->rawColumns(['district_name', 'applied', 'to_be_verified', 'to_be_approved', 'approved', 'current_applied', 'current_to_be_verified', 'current_to_be_approved', 'current_approved', 'pushed_sbi', 'amount_booked'])
                ->make(true);
        }
        return view('Block-Drilldown.block_consolidate_report_sbi')->with('schemes', $schemes);
    }

    public function indexdistconsol_sbi($district_code)
    {

        $user_id = AuthChecker::getUserId();
        $district_code = $district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
        $c_time = Carbon::now();
        $year = $c_time->year;
        if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
            $first_part = $year - 1;
            $second_part = $year;
        } else {
            $first_part = $year;
            $second_part = $year + 1;
        }
        $select_year = $first_part . '-' . $second_part;
        $monthName = $c_time->format('F');
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('Block-Drilldown.block_consolidate_report_sbi')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code)->with('selected_year', $select_year)->with('selected_month', $monthName);
    }

    //WCD Consolidated Report Block/Municipality wise
    public function wcdconsol_report()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = Scheme::where('is_active', 1)->whereIn('id', [2, 10, 11])->get(['scheme_name as name', 'id as id']);
        $district_code = $duty->district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();

        return view('Block-Drilldown.block_consolidatewcd_report')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
    }


    public function getwcdconsol_reportData(Request $request)
    {
        //DB::enableQueryLog();
        if (request()->ajax()) {
            $user_id = AuthChecker::getUserId();
            $district_code = $request->level2;
            $scheme_id = $request->level1a;
            $pensioner_type = $request->level1c;
            $level = $request->level3;
            $data = array();

            if (!empty($district_code)) {
                $condition = " and b.dist_code='" . $district_code . "' ";

                if ($level != '') {
                    $condition = $condition . " and b.rural_urban_id=" . $level . " ";
                }

                if (($scheme_id == '') || ($scheme_id == 2)) {

                    $query = "select block_ulb_name, b.rural_urban_id, s.scheme_name,
              count(b.id) applied,
              sum(case when b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 then 1 else 0 end) verified,
              sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
              sum(case when b.is_rejected=1 then 1 else 0 end) rejected
              from pension.beneficiaries b, m_scheme s
              where s.id=b.scheme_id " . $condition .
                        " group by s.scheme_name,b.block_ulb_name,b.rural_urban_id
              order by b.rural_urban_id, b.block_ulb_name,s.scheme_name";

                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                }

                if (($scheme_id == '') || ($scheme_id == 10)) {
                    $query = "select block_ulb_name, b.rural_urban_id, s.scheme_name,
            count(b.id) applied,
            sum(case when b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 then 1 else 0 end) verified,
            sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when b.is_rejected=1 then 1 else 0 end) rejected
            from pension.beneficiaries b, m_scheme s
            where s.id=b.scheme_id " . $condition .
                        " group by s.scheme_name,b.block_ulb_name,b.rural_urban_id
            order by b.rural_urban_id, b.block_ulb_name,s.scheme_name";

                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                }

                if (($scheme_id == '') || ($scheme_id == 11)) {
                    $query = "select block_ulb_name, b.rural_urban_id, s.scheme_name,
            count(b.id) applied,
            sum(case when b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 then 1 else 0 end) verified,
            sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when b.is_rejected=1 then 1 else 0 end) rejected
            from pension.beneficiaries b, m_scheme s
            where s.id=b.scheme_id " . $condition .
                        " group by s.scheme_name,b.block_ulb_name,b.rural_urban_id
            order by b.rural_urban_id, b.block_ulb_name,s.scheme_name";

                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                }
            }

            return datatables()->of($data)
                ->addColumn('level', function ($data) {
                    if ($data->rural_urban_id == 2)
                        return "Rural";
                    else if ($data->rural_urban_id == 1)
                        return "Urban";
                    else
                        return "Unknown";
                })
                ->rawColumns(['level'])
                ->make(true);
        }
        return view('Block-Drilldown.block_consolidatewcd_report')->with('schemes', $schemes);
    }

    public function indexdistwcdconsol($district_code)
    {

        $district_name = District::where('district_code', $district_code)->pluck('district_name')
            ->first();
        $schemes = Scheme::get(['scheme_name as name', 'id as id']);


        return view('Block-Drilldown.block_consolidatewcd_report')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
    }
    function applicationstatreport(Request $request)
    {
        $heading_msg = '';
        date_default_timezone_set('Asia/Kolkata');
        $date = Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
        $date = $date->format('F j, Y g:i:a');
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        $designation_id = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $scheme_arr = array();
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));

        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::DashboardChecker() || AuthChecker::DDOChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
            $scheme_arr = array(10, 11);
        } else if (AuthChecker::ApproverPermission() || AuthChecker::VerifierPermission() || AuthChecker::StatusCheckerDistrictChecker() || AuthChecker::StatusCheckerFieldChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (!empty($roleObj['district_code'])) {
                    $district_code = $roleObj['district_code'];
                }
                if (!empty($roleObj['urban_body_code'])) {
                    $blockCode = $roleObj['urban_body_code'];
                }
                if (!empty($roleObj['taluka_code'])) {
                    $blockCode = $roleObj['taluka_code'];
                }
            }

            if (empty($district_code))
                return redirect("/")->with('error', 'User Disabled. ');
        } else {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        //dd($district_code);
        if (!empty($district_code)) {
            $district_visible = 0;
            $district_code_fk = $district_code;
        } else {
            $district_code_fk = NULL;
        }
        if (!empty($is_urban)) {
            $is_urban_visible = 0;
            $rural_urban_fk = $is_urban;
        } else {
            $rural_urban_fk = NULL;
        }
        if (!empty($blockCode)) {
            $block_visible = 0;
            $block_munc_corp_code_fk = $blockCode;
        } else {
            $block_munc_corp_code_fk = NULL;
        }
        $districts = District::get();
        $phase_list = DsPhase::orderBy('id')->get();
        return view(
            'Block-Drilldown.applicationstatereport',
            [
                'schemes' => $schemes,
                'phase_list' => $phase_list,
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'c_time' => $date
            ]
        );
    }
    function applicationstatreportpost(Request $request)
    {

        $scheme_code = $request->scheme_code;
        $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_code)->first();
        if (!empty($scheme_row->short_code)) {
            $table_schema = $scheme_row->short_code;
            $scheme_name = $scheme_row->scheme_name;
        } else {
            $table_schema = 'pension';
            $scheme_name = 'All';
        }
        $district = $request->district;
        $c_time = Carbon::now();
        //  $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";

        $district_condition = "";
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
            $district_condition = " and created_by_dist_code=" . $district;
        } else {
            $district_condition = "";
        }
        if (!empty($scheme_code)) {
            $scheme_condition = " and scheme_id=" . $scheme_code;
        } else {
            $scheme_condition = "";
        }
        $phase_condition = '';
        if (!empty($request->phase_code) && $request->phase_code > 0) {

            $phase_condition = ' and (ds_phase=' . $request->phase_code . ' or cur_mark_ds_phase=' . $request->phase_code . ')';
        }
        if ($request->phase_code == -1) {
            $phase_condition = 'and ds_phase IS NULL and sm_ds_mark IS NULL';
        }
        $from_date = $request->from_date;
        // dd($from_date);
        $to_date = $request->to_date;
        if (!empty($from_date) && !empty($to_date)) {
            $dateFilter = " AND created_at >= '" . $from_date . "'::date AND created_at <= '" . $to_date . "'::date";
        } else {
            $dateFilter = "";
        }
        // dd($dateFilter);
        $urban_code = $request->urban_code;
        $block = $request->block;
        if (!empty($block)) {
            if ($urban_code == 1) {
                $block_ulb = UrbanBody::where('urban_body_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->urban_body_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
        }

        $districtwise = $blockwise = $muncwise = $gpwise = $wardwise = 0;
        $rules = [
            'scheme_code' => 'nullable|integer',
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['scheme_code'] = 'Scheme';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $from_date_condition = "";
            $to_date_condition = "";
            $legacy_import_condition = "";
            $created_at_date_condition = "";
            $user_msg = "Applications Statistics";

            date_default_timezone_set('Asia/Kolkata');
            $date = Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
            $date = $date->format('F j, Y g:i:a');
            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $next_level_role_id_operator = SchemeStepRank::getSchemeParentId($scheme_code, 1);
            if ($scheme_code == 8 || $scheme_code == 9) {
                $next_level_role_id_verifier = SchemeStepRank::getSchemeParentId($scheme_code, 1);
            } else {

                $next_level_role_id_verifier = SchemeStepRank::getSchemeParentId($scheme_code, 2);
            }
            if (!empty($block)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_ward_code,urban_body_ward_name as location_name from m_urban_body_ward where urban_body_code=" . $block . "
                order by urban_body_ward_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
                    main.gp_ward_code
                    from pension.beneficiaries as main 
                    
                    where  main.block_ulb_code=" . $block . "  " . $scheme_condition . " " . $dateFilter . "
                    group by main.gp_ward_code
                ) as B ON A.urban_body_ward_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "Ward";
                    $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $blk_munc_name;
                } else {
                    $query = "select A.*,B.*
                from
                (
                select gram_panchyat_code,gram_panchyat_name as location_name from m_gp where block_code=" . $block . "
                order by gram_panchyat_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
main.gp_ward_code
                    from pension.beneficiaries as main 
                   
                    where  main.block_ulb_code=" . $block . "  " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.gp_ward_code
                ) as B ON A.gram_panchyat_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $blk_munc_name;
                }
            } else if (!empty($urban_code)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
                    main.block_ulb_code
                    from pension.beneficiaries as main 
                    where  main.created_by_dist_code=" . $district . "  " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Municipality";
                } else {
                    $query = "select A.*,B.*
                from
                (
                select block_code,block_name as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
main.block_ulb_code
                    from pension.beneficiaries as main 
                    where  main.created_by_dist_code=" . $district . "  " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.block_ulb_code
                ) as B ON A.block_code=B.block_ulb_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                }
            } else if (!empty($district)) {
                $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
block_ulb_code
                    from pension.beneficiaries as main 
                    where  main.created_by_dist_code=" . $district . "  " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";

                $data_part1 = DB::connection('pgsql_mis')->select($query);
                $data1 = array_merge($data, $data_part1);

                $query = "select A.*,B.*
                from
                (
                select block_code,block_name||'-B' as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                   select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
main.block_ulb_code
                    from pension.beneficiaries as main 
                    where  main.created_by_dist_code=" . $district . "  " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.block_ulb_code
                ) as B ON A.block_code=B.block_ulb_code";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data2 = array_merge($data, $data_part);
                $data = array_merge($data1, $data2);
                $heading_msg = 'Block/Munc Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                $column = "Block/Munc";
            } else {
                $query = "select A.*,B.*
                from
                (
                select district_code,district_name as location_name from m_district
                order by district_name
                ) as A LEFT JOIN
                (
                     select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=0 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_operator . ")) as fresh,
                    coalesce(count( distinct main.id) FILTER(WHERE is_verified=1 and is_approved=0 and is_rejected=0 and next_level_role_id=" . $next_level_role_id_verifier . ")) as verified,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1 or next_level_role_id<0)) as rejected,
                    main.created_by_dist_code
                    from pension.beneficiaries as main 
                    where  created_by_dist_code IS NOT NULL " . $scheme_condition . " " . $phase_condition . " " . $dateFilter . "
                    group by main.created_by_dist_code
                ) as B ON A.district_code=B.created_by_dist_code";
                //dd($query);
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data = array_merge($data, $data_part);
                $heading_msg = 'District Wise ' . $user_msg;
                $column = "Disttrict";
            }
            $heading_msg = $heading_msg . ' for Scheme:' . $scheme_name;
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg,
            'c_time' => $date
        ]);
    }

    function getAllSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id)
    {
        // New Changes on 10-01-2023 after breaking up transaction_lot_details_report table
        $year_arr = explode('-', $year);
        $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
        $tld_table = 'transaction_lot_details_report_' . $yyyy_val;
        // end changes on 10-01-2023
        $query = "insert into sbi.payment_report select block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved,sum(pushed_sbi) pushed_sbi," . $user_id . ",now() from( select mb.urban_body_name as block_ulb_name, 'Urban' as Level,
        coalesce(count( b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id >= 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
        FROM (select * from pension.beneficiaries where  rural_urban_id=1 and dist_code= :dist_code  ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b 
        left join m_urban_body mb on mb.urban_body_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code  from sbi.transaction_lot_details tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no 
         ";
        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " ) l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.urban_body_name 
        Union All
        select mb.block_name as block_ulb_name, 'Rural' as Level,
        coalesce(count(distinct b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi FROM (select * from pension.beneficiaries where rural_urban_id=2 and dist_code= :dist_code ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b  
        left join  m_block mb on mb.block_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code from sbi.transaction_lot_details tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no ";


        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " )l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.block_name 
        Union All
        select mb.urban_body_name as block_ulb_name, 'Urban' as Level,
        coalesce(count( b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
        FROM (select * from pension.beneficiaries where  rural_urban_id=1 and dist_code= :dist_code  ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b 
        left join m_urban_body mb on mb.urban_body_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code  from sbi." . $tld_table . " tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no 
         ";
        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " ) l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.urban_body_name 
        Union All
        select mb.block_name as block_ulb_name, 'Rural' as Level,
        coalesce(count(distinct b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi FROM (select * from pension.beneficiaries where rural_urban_id=2 and dist_code= :dist_code ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b  
        left join  m_block mb on mb.block_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code from sbi." . $tld_table . " tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no ";


        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " )l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.block_name  )t  where pushed_sbi<>0 group by block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved";

        return $query;
    }

    function getRuralSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id)
    {
        // New Changes on 10-01-2023 after breaking up transaction_lot_details_report table
        $year_arr = explode('-', $year);
        $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
        $tld_table = 'transaction_lot_details_report_' . $yyyy_val;
        // end changes on 10-01-2023
        $query = "insert into sbi.payment_report select block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved,sum(pushed_sbi) pushed_sbi," . $user_id . ",now() from(select mb.block_name as block_ulb_name, 'Rural' as Level,
        coalesce(count(distinct b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi FROM (select * from pension.beneficiaries where rural_urban_id=2 and dist_code= :dist_code ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b  
        left join  m_block mb on mb.block_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code from sbi.transaction_lot_details tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no ";


        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " )l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.block_name 
        Union All
        select mb.block_name as block_ulb_name, 'Rural' as Level,
        coalesce(count(distinct b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi FROM (select * from pension.beneficiaries where rural_urban_id=2 and dist_code= :dist_code ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b  
        left join  m_block mb on mb.block_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code from sbi." . $tld_table . " tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no ";


        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " )l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.block_name  )t  where pushed_sbi<>0 group by block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved";

        return $query;

    }

    function getUrbanSbiPaymentReport($table_name, $scheme_id, $schemes_in, $year, $month, $user_id)
    {
        // New Changes on 10-01-2023 after breaking up transaction_lot_details_report table
        $year_arr = explode('-', $year);
        $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
        $tld_table = 'transaction_lot_details_report_' . $yyyy_val;
        // end changes on 10-01-2023
        $query = "insert into sbi.payment_report select block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved,sum(pushed_sbi) pushed_sbi," . $user_id . ",now() from(select mb.urban_body_name as block_ulb_name, 'Urban' as Level,
        coalesce(count( b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
        FROM (select * from pension.beneficiaries where  rural_urban_id=1 and dist_code= :dist_code  ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b 
        left join m_urban_body mb on mb.urban_body_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code  from sbi.transaction_lot_details tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no 
         ";
        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " ) l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.urban_body_name 
        Union All
        select mb.urban_body_name as block_ulb_name, 'Urban' as Level,
        coalesce(count( b.id),0) as applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
        coalesce(count( b.id) FILTER(WHERE trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
        coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
        coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
        coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
        FROM (select * from pension.beneficiaries where  rural_urban_id=1 and dist_code= :dist_code  ";
        if (!is_null($scheme_id)) {
            $query = $query . ' and scheme_id = :scheme_id';
        } else {
            $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
        }

        $query = $query . ") b 
        left join m_urban_body mb on mb.urban_body_code=b.block_ulb_code 
        LEFT JOIN (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code  from sbi." . $tld_table . " tld right join sbi.transaction_lot tl on tld.lot_no = tl.lot_no 
         ";
        if (!is_null($year)) {
            $query = $query . ' and tl.lot_year= :lot_year';
        }
        if (!is_null($month)) {
            $query = $query . ' and tl.lot_month= :lot_month';
        }
        if (!is_null($scheme_id)) {
            $query = $query . ' and tl.scheme_id = :scheme_id';
        } else {
            $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
        }
        $query = $query . " ) l  on b.id = l.pension_id and b.scheme_id = l.scheme_id  group by mb.urban_body_name )t  where pushed_sbi<>0 group by block_ulb_name,Level,applied,to_be_verified,to_be_approved,approved,current_applied,current_to_be_verified,current_to_be_approved,current_approved";
        return $query;

    }
}
