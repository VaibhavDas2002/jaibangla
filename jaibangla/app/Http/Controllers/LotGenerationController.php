<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\ben_lot_month;
use App\PensionSc;
use App\lot_no_seeder;
use App\lot_master;
use App\Scheme;
use Auth;
use App\Configduty;
use App\BeneficiaryPensions;

class LotGenerationController extends Controller
{
    public function index()
    {   
        //sayantika department
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $is_active = $duty->is_active;
        
        
        //$schemes = Scheme::where('id',$duty->scheme_id)->get();
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();

        $districts = District::all();
        //$flag=0;
        return view('lot-generation/index', ['schemes'=>$schemes,'districts'=>$districts]);

       // return view('employee-report-drilldown/index');
    }

    public function loadlevel2d(Request $request,$level_name,$id)
    {
        //dd("hh");
        if($level_name=="District"){
        	$list=District::get(['district_code as id','district_name as name']);
        	//dd($level_name,$list);
        }
        elseif($level_name=="ULB"){
          $list=UrbanBody::where('district_code','=',$id)->get(['sub_district_code AS id','urban_body_name AS name']);
            // $list=DB::table('m_urban_body')->where('district_code','=',$id)->whereNotIn('urban_body_code',[801715,801739,801670,801673,801672])->get(['urban_body_code AS id','urban_body_name AS name']);

        }elseif($level_name=="Block"){
             $list=Taluka::where('district_code','=',$id)->get(['block_code AS id','block_name AS name']);
            // Taluka::get(['taluka_code AS id','taluka_name AS name']);
        }elseif($level_name=="All"){
        	$list=[array("id"=>-1,"name"=>"No Data")];
        }

        
       //dd($list);

        return response()->json($list);
    }

    public function loadlevel2(Request $request,$id,$code)
    {
        
      //$level_name=$request->level_name;
      $level_name=$code;
      //dd($level_name);
      if ($level_name=="District") {
          //$list=District::where('is_revenue_district','=','1')->get(['district_code AS id','district_name AS name']);
          $list=District::get(['district_code AS id','district_name AS name']);
            
      }elseif($level_name=="ULB"){
          $list=UrbanBody::where('district_code','=',$id)->get(['sub_district_code AS id','urban_body_name AS name']);
          //$list=UrbanBody::get(['urban_body_code AS id','urban_body_name AS name']);
            
      }else{
          //dd("sr");
          $list=Taluka::where('district_code',$id)->get(['block_code AS id','block_name AS name']);
      }
       

      return response()->json($list);
    }  

	public function loadcount(Request $request){

		$scheme_id=$request->reportlevel1_data;
		$location_code=$request->reportlevel2_data;
		$level=$request->reportlevel2d_data;
    $reportdistrict_data=$request->reportdistrict_data;

    //dd($scheme_id,$location_code,$level,$reportdistrict_data);
    if($scheme_id==0 && $location_code==0 && $level==0 && $reportdistrict_data==0){
     // dd("I m here");
      $user_id = Auth::user()->id;
      $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();
      $count=BeneficiaryPensions::where(function($query) use ($schemes){
          foreach($schemes as $scheme){
            $query->orwhere('scheme_id',$scheme->scheme_id);
          }
        })
      ->where('next_level_role_id',0)->where('lot_generated',0)
      ->where('payment_count',0)
      ->count();
      $datas=[ 'count'=>$count];
      return response()->json($datas);
    }
    else
    {
      if($level=='All'){
        $count=BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('lot_generated',0)
        ->where('next_level_role_id',0)
        ->where('payment_count',0)
        ->count();
      }
      elseif($level=='District'){

        $district_code=$location_code;
        $count=BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('created_by_dist_code',$district_code)
        ->where('lot_generated',0)
        ->where('next_level_role_id',0)
        ->where('payment_count',0)
        ->count();

      }elseif($level=='Block'){
        $block_code=$location_code;
        $count=BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('created_by_local_body_code',$block_code)
        ->where('lot_generated',0)
        ->where('next_level_role_id',0)
        ->where('payment_count',0)
        ->count();
      }elseif($level=='ULB'){
        $urban_body_code=$location_code;
        $count=BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('created_by_local_body_code',$urban_body_code)
        ->where('lot_generated',0)
        ->where('next_level_role_id',0)
        ->where('payment_count',0)
        ->count();
      }
      //dd($data);
      $datas=[ 'count'=>$count];
      return response()->json($datas);
    }

    	
    	
	}

 public function generatelot(Request $request){
        $inputs = request()->input('approvalcheck');
        $year_data=$request->year_data;
        $month_data=$request->month_data;
        //dd($inputs);
        $scheme_id=$request->scheme_id;
        $count=0;
       

        $lot_number_seeder=lot_no_seeder::where('scheme_id',$scheme_id)->first();//
        $lot_number=$lot_number_seeder->lot_no;
        
         foreach($inputs as $input){
          $count=$count+1;
          }

         $lot_data[]=[
          'scheme_id'=>$scheme_id,
          'lot_month'=>$month_data,
          'lot_year'=>$year_data,
          'lot_no'=>$lot_number_seeder->drn,
          'ben_count'=>$count
        ];


        $lot_master=lot_master::insert($lot_data);
      
        $seeder_update = [
            'lot_no' => $lot_number+1
          ];
            
       
        $is_seeder_updated=lot_no_seeder::where('scheme_id',$scheme_id)->update($seeder_update);

        foreach($inputs as $input){

        //$single_employee_details = BeneficiaryPensions::findOrFail($input);
        $single_employee_details = BeneficiaryPensions::where('id',$input)
                                  ->where('scheme_id',$scheme_id)
                                  ->where('lot_generated',0)
                                  ->where('next_level_role_id',0)
                                  ->where('payment_count',0)
                                  ->first();
        /***new way******/
        //$data=array();
        // $data[]=['applicant_id'=> $single_employee_details->id,
        //           'mobile_no'=> $single_employee_details->mobile_no,
        //           'bank_ifsc'=> $single_employee_details->bank_ifsc,
        //           'bank_ac'=> $single_employee_details->bank_code,
        //           'ben_fname'=> $single_employee_details->ben_fname,
        //           'ben_mname'=> $single_employee_details->ben_mname,
        //           'ben_lname'=> $single_employee_details->ben_lname,
        //           'scheme_id'=> $single_employee_details->scheme_id,
        //           'fin_year'=> $year_data,
        //           'fin_month'=>$month_data,
        //           'lot_no'=>$lot_no
        //         ];
        
        /****************/
        //where('id','=',$input)->first();//find($id);
       //dd($single_employee_details);
        //$data = $single_employee_details->replicate();

        //$data = $single_employee_details->toArray();
        /********OLD WAY****************************/
         $ben_lot_month=new ben_lot_month();
         $ben_lot_month->ben_id=$single_employee_details->benid;
         $ben_lot_month->mobile_no=$single_employee_details->mobile_no;
         $ben_lot_month->ifsc=$single_employee_details->bank_ifsc;
         $ben_lot_month->acc_no=$single_employee_details->bank_code;
         $ben_lot_month->name=$single_employee_details->fullname;
         //$ben_lot_month->scheme_id=$single_employee_details->scheme_id;
         $ben_lot_month->drn_part=$lot_number_seeder->drn;
         $ben_lot_month->amount=1000;
         $ben_lot_month->scheme_id=$single_employee_details->scheme_id;
         $ben_lot_month->dist_code=$single_employee_details->created_by_dist_code;
         $ben_lot_month->pension_id=$single_employee_details->id;
         //lot_no; drn_part
         //$ben_lot_month->fin_year=$year_data;
         //$ben_lot_month->fin_month=$month_data;

          $is_saved=$ben_lot_month->save();
          //$lot_number=$ben_lot_month->id;
          /**************************************************/
          // $input_update = ['lot_generated' => ]; 

          $single_employee_details->lot_generated=1;
          $single_employee_details->save();
          // $is_updated=PensionSc::where('id', $input)->update($input_update);   
       // $is_pushed=PushTable::firstOrCreate($data);
        //$i=$i+1;
        }
        //dd($data);
         //$is_saved=ben_lot_month::insert($data);
         if($is_saved){

            return redirect("lot-generation")->with('success', 'Lot Generation Successful')
            ->with('id', $lot_number);
            }
        //dd($inputs);
    }


public function convertdata($search){
    $converted=(int)$search;
    return $converted;
}

public function getdata(Request $request){

    DB::enableQueryLog();
		$columns = array( 
                            0 =>'id', 
                            1 =>'name',
                            2=> 'dob',
                            3=>'gender',
                            4=>'mobile_number_1',
                            5=>'email',
                            6=>'verification_status',
                            7=>'approval_status'
                           
                            
                        );
  	$user_id = Auth::user()->id;
    $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();
   
    // dd($schemes->scheme_id);
    $scheme_id=$request->level1;
		$location_code=$request->level2;
		$level1=$request->level2d;
    $reportdistrict_data=$request->reportdistrict;
    //$reportlevel3_data=$request->level3;
    $year=$request->year;
    $month=$request->month;

    //dd($scheme_id,$location_code,$level1,$reportdistrict_data,$year,$month);
        
    $flag=1;
    $constraints = [
        'level1' => $request['level1'],
        'level2' => $request['level2'],
    ];
        
       //dd($constraints);
    if($scheme_id==0 && $location_code==0 && $level1==0 && $reportdistrict_data==0){

      $totalData = BeneficiaryPensions::where(function($query) use ($schemes){
        foreach($schemes as $scheme){
          $query->orwhere('scheme_id',$scheme->scheme_id);
        }
      })
      ->where('next_level_role_id','=',0)
      ->where('lot_generated',0)
      ->where('payment_count',0)      
      ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
      ->count();
        //dd($totalData);
      $totalFiltered = $totalData; 
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');


      if(empty($request->input('search.value')))
      {
        $posts=BeneficiaryPensions::where(function($query) use ($schemes){
          foreach($schemes as $scheme){
            $query->orwhere('scheme_id',$scheme->scheme_id);
            }
        })
        ->where('next_level_role_id','=',0)
        ->where('lot_generated',0) 
        ->where('payment_count',0)        
        //->where(DB::raw("limit=?"))->addBinding($reportlevel3_data,'select')
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
        ->offset($start)
        ->limit($limit)->orderBy($order,$dir)->get();  
        //dd($posts);  
      }
      else
      {
        $search = $request->input('search.value'); 
        $newsearch=$this->convertdata($search);

        $posts =BeneficiaryPensions::where(function($query) use ($schemes){
          foreach($schemes as $scheme){
            $query->orwhere('scheme_id',$scheme->scheme_id);
          }
        })
        ->where('next_level_role_id','=',0)
        ->where('lot_generated',0)
        ->where('payment_count',0)
        ->where(function($query)use($newsearch,$search){
          $query->where('id','=',$newsearch)
                ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname)"),'ILIKE','%'.$search.'%')
                ->orWhere('gender', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                ->orWhere('mobile_no', '=',$newsearch);              
          })
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
        ->offset($start)->limit($limit)->orderBy($order,$dir)->get(); 
        //dd(DB::getQueryLog(),$posts); 

            
           
        $totalFiltered = BeneficiaryPensions::where(function($query) use ($schemes){
          foreach($schemes as $scheme){
            $query->orwhere('scheme_id',$scheme->scheme_id);
          }
        })
        ->where('next_level_role_id','=',0)
        ->where('lot_generated',0)
        ->where('payment_count',0)
        ->where(function($query)use($newsearch,$search){
          $query->where('id','=',$newsearch)
                ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname)"),'ILIKE','%'.$search.'%')
                ->orWhere('gender', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                ->orWhere('mobile_no', '=',$newsearch);        
          })
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->count();
 
        // dd(DB::getQueryLog(),$totalFiltered); 
      }
      $data = array();
      if(!empty($posts))
      {
        foreach ($posts as $post)
        {
          //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
          //$edit =  route('posts.edit',$post->id);
          $value=$post->id;
          $nestedData['id'] = $post->id;
          $nestedData['name'] =  $post->name;
          // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
          //$nestedData['email'] = $post->email;
          $nestedData['mobile_no'] = $post->mobile_no;
          $nestedData['bank_name'] = $post->bank_name;
          $nestedData['bank_ifsc'] = $post->bank_ifsc;
          $nestedData['dob'] = $post->dob;
          $nestedData['gender'] = $post->gender;
          $nestedData['check']="<input type='checkbox' name='approvalcheck[]' checked  value='{$value}'>";   
          $data[] = $nestedData;
        }
            //dd($data);
      }
         
      $json_data = array(
        "draw" => intval($request->input('draw')),  
        "recordsTotal" => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data" => $data   
      );
            
      echo json_encode($json_data);
    }             
    else
    {
    // dd($level1);
    if($level1=="District"){
      $district_code=$location_code;
      //dd("HI");
      $totalData = BeneficiaryPensions::Where('created_by_dist_code','=',$district_code)
      ->where('scheme_id',$scheme_id)
      ->where('next_level_role_id','=',0)
      ->where('lot_generated',0)
      ->where('payment_count',0)
      ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->count();
            
      $totalFiltered = $totalData; 
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if(empty($request->input('search.value')))
      {
	     $posts=BeneficiaryPensions::Where('created_by_dist_code','=',$district_code)
       ->where('scheme_id',$scheme_id)
       ->where('next_level_role_id','=',0)
       ->where('payment_count',0)
       ->where('lot_generated',0) 
                 // ->where(DB::raw("limit=?"))->addBinding($reportlevel3_data,'select')
                   ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
                   ->offset($start)
	                 ->limit($limit)->orderBy($order,$dir)->get();  
      }
      else
      {
        $search = $request->input('search.value'); 
       	$newsearch=$this->convertdata($search);
        //dd($district_code);
             
        $posts =BeneficiaryPensions::Where('created_by_dist_code','=',$district_code)
                      ->where('scheme_id',$scheme_id)
                      ->where('next_level_role_id','=',0)
                      ->where('lot_generated',0)
                      ->where('payment_count',0)
           			      ->where(function($query)use($newsearch,$search){
           				               $query->where('id','=',$newsearch)
           					                   ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                                                          ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                                       ->orWhere('gender', 'ILIKE','%'.$search.'%')
                                       ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                                       ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                                       ->orWhere('mobile_no', '=',$newsearch);
                              
           			                })
                      ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->get();
          
                      //dd(DB::getQueryLog(),$posts);     
      $totalFiltered = BeneficiaryPensions::Where('created_by_dist_code','=',$district_code)
              ->where('scheme_id',$scheme_id)
              ->where('next_level_role_id','=',0)
              ->where('lot_generated',0)
              ->where('payment_count',0)
              ->where(function($query)use($newsearch,$search){
            $query->where('id','=',$newsearch)
                  ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname)"),'ILIKE','%'.$search.'%')
                  ->orWhere('gender', 'ILIKE','%'.$search.'%')
                  ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                  ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                  ->orWhere('mobile_no', '=',$newsearch);
                              
                })
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->count();
          // dd(DB::getQueryLog(),$totalFiltered); 
      } 
      $data = array();
      if(!empty($posts))
      {
        foreach ($posts as $post)
        {
          //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
          //$edit =  route('posts.edit',$post->id);
          $value=$post->id;
          $nestedData['id'] = $post->id;
          $nestedData['name'] =  $post->name;
          // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
          //$nestedData['email'] = $post->email;
          $nestedData['mobile_no'] = $post->mobile_no;
          $nestedData['bank_name'] = $post->bank_name;
          $nestedData['bank_ifsc'] = $post->bank_ifsc;
          $nestedData['dob'] = $post->dob;
          $nestedData['gender'] = $post->gender;
          $nestedData['check']="<input type='checkbox' name='approvalcheck[]' checked  value='{$value}'>";
               
          $data[] = $nestedData;
        }
       //dd($data);
      }
          
      $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        );
            
      echo json_encode($json_data);
    }
    if($level1=="ULB"){

      $urban_body_code=$location_code;
      $totalData = BeneficiaryPensions::Where('created_by_local_body_code','=',$urban_body_code)
                  ->where('scheme_id',$scheme_id)
                  ->where('next_level_role_id','=',0)
                  ->where('lot_generated',0)
                  ->where('payment_count',0)
                  ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->count();
            
      $totalFiltered = $totalData; 
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');


      if(empty($request->input('search.value')))
      {
	     $posts=BeneficiaryPensions::Where('created_by_local_body_code','=',$urban_body_code)
              ->where('scheme_id',$scheme_id)
              ->where('next_level_role_id','=',0)
              ->where('lot_generated',0) 
              ->where('payment_count',0)
              // ->where(DB::raw("limit=?"))->addBinding($reportlevel3_data,'select')
              ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
              ->offset($start)
              ->limit($limit)->orderBy($order,$dir)->get(); 
 
      }
      else
      {
       	$search = $request->input('search.value'); 
       	$newsearch=$this->convertdata($search);

        //dd($urban_body_code);
        $posts= BeneficiaryPensions::Where('created_by_local_body_code','=',$urban_body_code)
                ->where('scheme_id',$scheme_id)
                ->where('next_level_role_id','=',0)
                ->where('lot_generated',0)
                ->where('payment_count',0)
                ->where(function($query)use($newsearch,$search){
                  $query->where('id','=',$newsearch)
                        ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                                ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                        ->orWhere('gender', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                        ->orWhere('mobile_no', '=',$newsearch);
                              
                })
                ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->get();
           /******OLD SEARCH CODE******/
           	// $posts =nhm_employee_details::where('body_code',$urban_body_code)
           	// 				->where('id','=',$newsearch)
            //                 ->orWhere(DB::raw("CONCAT(title,first_name,middle_name,
            //                     last_name)"),'ILIKE','%'.$search.'%')
            //                 ->orWhere('gender', 'ILIKE','%'.$search.'%')
            //                 ->orWhere('email', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('verification_status', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('approval_status', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('dob', 'ILIKE','%'.$search.'%')
            //                 ->orWhere('mobile_number_1', '=',$newsearch)
            //                 ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")->offset($start)
            //                 ->limit($limit)->orderBy($order,$dir)->get();
           

            
           
             // $totalFiltered = nhm_employee_details::where('body_code','=',$urban_body_code)
             // 				->where('id','=',$newsearch)
             //                ->orWhere(DB::raw("concat(title,first_name,middle_name,last_name)"),'ILIKE','%'.$search.'%')
             //                ->orWhere('gender', 'ILIKE','%'.$search.'%')
             //                ->orWhere('email', 'ILIKE','%'.$search.'%')
             //                //->orWhere('verification_status', 'ILIKE','%'.$search.'%')
             //                //->orWhere('approval_status', 'ILIKE','%'.$search.'%')
             //                //->orWhere('dob', 'ILIKE','%'.$search.'%')
             //                ->orWhere('mobile_number_1', '=',$newsearch)
             //                // ->orWhere('title', 'LIKE',"%{$search}%")
             //                 ->count();
 				/******OLD SEARCH CODE end******/
 		   $totalFiltered = BeneficiaryPensions::Where('created_by_local_body_code','=',$urban_body_code)
                ->where('scheme_id',$scheme_id)
                ->where('next_level_role_id','=',0)
                ->where('lot_generated',0)
                ->where('payment_count',0)
                ->where(function($query)use($newsearch,$search){
                  $query->where('id','=',$newsearch)
                        ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                                ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                        ->orWhere('gender', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                        ->orWhere('mobile_no', '=',$newsearch);
                              
                })
                ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->count();
                             // dd(DB::getQueryLog(),$totalFiltered); 
        } $data = array();
        if(!empty($posts))
        {
          foreach ($posts as $post)
          {
            //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
            //$edit =  route('posts.edit',$post->id);
            $value=$post->id;
            $nestedData['id'] = $post->id;
            $nestedData['name'] =  $post->name;
            // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
                //$nestedData['email'] = $post->email;
            $nestedData['mobile_no'] = $post->mobile_no;
            $nestedData['bank_name'] = $post->bank_name;
            $nestedData['bank_ifsc'] = $post->bank_ifsc;
            $nestedData['dob'] = $post->dob;
            $nestedData['gender'] = $post->gender;
            $nestedData['check']="<input type='checkbox' name='approvalcheck[]' checked  value='{$value}'>";
               
            $data[] = $nestedData;

          }
            //dd($data);
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data);
            
    }elseif($level1=="Block"){
      $block_code=$location_code;

      $totalData = BeneficiaryPensions::Where('created_by_local_body_code','=',$block_code)
                  ->where('scheme_id',$scheme_id)
                  ->where('next_level_role_id','=',0)
                  ->where('lot_generated',0)
                  ->where('payment_count',0)
                  ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->count();

      //$applications_received=DB::table('nhm_employee_details')->Where('body_code','=',$taluka_code)->count();
      $totalFiltered = $totalData; 
      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');

      if(empty($request->input('search.value')))
      {
	     $posts=BeneficiaryPensions::Where('created_by_local_body_code','=',$block_code)
              ->where('scheme_id',$scheme_id)
              ->where('next_level_role_id','=',0)
              ->where('lot_generated',0) 
              ->where('payment_count',0)
              // ->where(DB::raw("limit=?"))->addBinding($reportlevel3_data,'select')
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
        ->offset($start)
        ->limit($limit)->orderBy($order,$dir)->get();
    
      }
      else
      {
       	$search = $request->input('search.value'); 
       	$newsearch=$this->convertdata($search);

       	$posts =BeneficiaryPensions::Where('created_by_local_body_code','=',$block_code)
                ->where('scheme_id',$scheme_id)
                ->where('next_level_role_id','=',0)
                ->where('lot_generated',0)
                ->where('payment_count',0)
                ->where(function($query)use($newsearch,$search){
                  $query->where('id','=',$newsearch)
                        ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                          ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                        ->orWhere('gender', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                        ->orWhere('mobile_no', '=',$newsearch);
                              
                })
                ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)
                ->limit($limit)->orderBy($order,$dir)->get();



           	// /*********old search ********/
           	// $posts =nhm_employee_details::where('body_code','=',$taluka_code)
           	// 				->where('id','=',$newsearch)
            //                 ->orWhere(DB::raw("CONCAT(title,first_name,middle_name,
            //                     last_name)"),'ILIKE','%'.$search.'%')
            //                 ->orWhere('gender', 'ILIKE','%'.$search.'%')
            //                 ->orWhere('email', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('verification_status', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('approval_status', 'ILIKE','%'.$search.'%')
            //                // ->orWhere('dob', 'ILIKE','%'.$search.'%')
            //                 ->orWhere('mobile_number_1', '=',$newsearch)
            //                 ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")->offset($start)
            //                 ->limit($limit)->orderBy($order,$dir)->get();

            
            
           
             // $totalFiltered = nhm_employee_details::where('body_code','=',$taluka_code)
             // 				->where('id','=',$newsearch)
             //                ->orWhere(DB::raw("concat(title,first_name,middle_name,last_name)"),'ILIKE','%'.$search.'%')
             //                ->orWhere('gender', 'ILIKE','%'.$search.'%')
             //                ->orWhere('email', 'ILIKE','%'.$search.'%')
             //                //->orWhere('verification_status', 'ILIKE','%'.$search.'%')
             //                //->orWhere('approval_status', 'ILIKE','%'.$search.'%')
             //                //->orWhere('dob', 'ILIKE','%'.$search.'%')
             //                ->orWhere('mobile_number_1', '=',$newsearch)
             //                // ->orWhere('title', 'LIKE',"%{$search}%")
             //                 ->count();
            /*************old search end***********/
        $totalFiltered = BeneficiaryPensions::Where('created_by_local_body_code','=',$block_code)
                        ->where('scheme_id',$scheme_id)
                        ->where('next_level_role_id','=',0)
                        ->where('lot_generated',0)
                        ->where('payment_count',0)
                        ->where(function($query)use($newsearch,$search){
                  $query->where('id','=',$newsearch)
                      ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                                ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                      ->orWhere('gender', 'ILIKE','%'.$search.'%')
                      ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                      ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                      ->orWhere('mobile_no', '=',$newsearch);
                              
                })
                ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->count();
            
                             // dd(DB::getQueryLog(),$totalFiltered); 
      } $data = array();
      if(!empty($posts))
      {
        foreach ($posts as $post)
        {
          //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
          //$edit =  route('posts.edit',$post->id);
          $value=$post->id;
          $nestedData['id'] = $post->id;
          $nestedData['name'] =  $post->name;
          // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
          //$nestedData['email'] = $post->email;
          $nestedData['mobile_no'] = $post->mobile_no;
          $nestedData['bank_name'] = $post->bank_name;
          $nestedData['bank_ifsc'] = $post->bank_ifsc;
          $nestedData['dob'] = $post->dob;
          $nestedData['gender'] = $post->gender;
          $nestedData['check']="<input type='checkbox' name='approvalcheck[]' checked  value='{$value}'>";
               
          $data[] = $nestedData;

        }
            //dd($data);
      }
          
      $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );    
      echo json_encode($json_data);

           //   $result=[
           //  'applications_received'=>$applications_received,
            
           // ];
    }//ssssss
    elseif($level1=="All"){
    //$district_code=$location_code;
    //dd("HI");
    $totalData = BeneficiaryPensions::where('scheme_id',$scheme_id)
                ->where('next_level_role_id','=',0)
                ->where('lot_generated',0)
                ->where('payment_count',0)
                ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->count();
            
    $totalFiltered = $totalData; 
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');


    if(empty($request->input('search.value')))
    {
      $posts=BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('next_level_role_id','=',0)
        ->where('lot_generated',0) 
        ->where('payment_count',0)
        // ->where(DB::raw("limit=?"))->addBinding($reportlevel3_data,'select')
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")
        ->offset($start)
        ->limit($limit)->orderBy($order,$dir)->get();    
    }
    else
    {
      $search = $request->input('search.value'); 
      $newsearch=$this->convertdata($search);
      //dd($district_code);

      $posts =BeneficiaryPensions::where('scheme_id',$scheme_id)
        ->where('next_level_role_id','=',0)
        ->where('lot_generated',0)
        ->where('payment_count',0)
        ->where(function($query)use($newsearch,$search){
          $query->where('id','=',$newsearch)
                ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                                ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                ->orWhere('gender', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                ->orWhere('mobile_no', '=',$newsearch);
                              
                })
        ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->get();
          
      //dd(DB::getQueryLog(),$posts); 

      $totalFiltered = BeneficiaryPensions::where('scheme_id',$scheme_id)
            ->where('next_level_role_id','=',0)
            ->where('lot_generated',0)
            ->where('payment_count',0)
            ->where(function($query)use($newsearch,$search){
                  $query->where('id','=',$newsearch)
                        ->orWhere(DB::raw("CONCAT(ben_fname,' ',ben_mname,
                        ' ',ben_lname)"),'ILIKE','%'.$search.'%')
                        ->orWhere('gender', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_name', 'ILIKE','%'.$search.'%')
                        ->orWhere('bank_ifsc', 'ILIKE','%'.$search.'%')
                        ->orWhere('mobile_no', '=',$newsearch);
                              
                })
             ->selectRaw("CONCAT(' ',ben_fname,' ',ben_mname,' ',ben_lname) as name,id as id,dob as dob,gender as gender,mobile_no as mobile_no,bank_name,bank_ifsc")->offset($start)->limit($limit)->orderBy($order,$dir)->count();

            // dd(DB::getQueryLog(),$totalFiltered); 
    } $data = array();
    if(!empty($posts))
    {
       foreach ($posts as $post)
        {
        //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
        //$edit =  route('posts.edit',$post->id);
        $value=$post->id;
        $nestedData['id'] = $post->id;
        $nestedData['name'] =  $post->name;
        // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
                //$nestedData['email'] = $post->email;
        $nestedData['mobile_no'] = $post->mobile_no;
        $nestedData['bank_name'] = $post->bank_name;
        $nestedData['bank_ifsc'] = $post->bank_ifsc;
        $nestedData['dob'] = $post->dob;
        $nestedData['gender'] = $post->gender;
        $nestedData['check']="<input type='checkbox' name='approvalcheck[]' checked  value='{$value}'>";
               
        $data[] = $nestedData;

      }
            //dd($data);
    }
          
    $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
    echo json_encode($json_data);
    }             
     
   }
 }

}
