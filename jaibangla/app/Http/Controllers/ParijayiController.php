<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
Use App\Taluka;
Use App\District;
Use App\GP;
Use App\Ward;
use App\BeneficiaryPensions;
use Auth;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\SubDistrict;
use App\Parijayi;
use App\ParijayiExport;
use App\ParijayiLot;
use App\parijayi_lot_seeder;
use App\ApplicationStatus;
use App\StatusCode;
use App\BankResponse;
use Illuminate\Support\Collection;
use Excel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParijayiController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($app_type)
    {
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $district_code=$duty->district_code;
        $district_name=District::where('district_code',$district_code)->pluck('district_name')->first();
    
        return view('Parijayi.index')->with('district_name',$district_name)->with('district_code',$district_code)->with('app_type',$app_type);

    }
    public function getData(Request $request){
        //DB::enableQueryLog();
      if(request()->ajax())
      {
        $user_id = Auth::user()->id;
        $district_code=$request->level1;
        $district_name=$request->level2;
        $lottype = $request->lottype;
        $serachvalue = $request->search['value'];
        //Application TYPE - 'F':FRESH, 'R':REJECTED, 'A':APPROVED
        $app_type = $request->application_type;

        if(($lottype!=null)||($app_type!='L')){

        //Urban/Rural
        $level=$request->level3;
        //LocalBody
        $localBody=$request->level1a;
        
        $flag=1;
        $totalRecords = 0;
        $data = array(); 

    //if(empty($serachvalue)){      
          $status="";
          if($app_type=='F'){ //FRESH APPLICATION
            $status = ' where status < 2 and duplicate_status is not null';
          }else if($app_type=='A'){ //APPROVED APPLICATION
            $status = ' where status > 1 and status <6';  
          }else if($app_type== 'L'){
            if($lottype == 'ICICI'){ 
              $status = " where status = 2 and duplicate_status in('S','R') and ifsc_code like 'ICIC%' and payment_type='NEFT'";
            }else if($lottype == 'NONICICI'){
              $status = " where status = 2 and duplicate_status in('S','R') and ifsc_code not like 'ICIC%' and payment_type='NEFT'";
            }else{
              $status = " where status = 2 and duplicate_status in('S','R') ";
            }
          }else if($app_type=='R'){ //REJECTED APPLICATION
            $status = ' where status > 5';
          } else{
            $status = ' where 1=1';
          } 

    if(empty($serachvalue)){      
         //RecordsCount
         $mainquery = " select count(id) from wbmt_beneficiary ".$status;
         if(!empty($district_code)){
           $mainquery = $mainquery." and district_code = ".$district_code;
         }
         if(!empty($level)){
           //'Rural'
           if($level == 2){
             if(!empty($localBody)){
               $mainquery = $mainquery." and block_code = ".$localBody;
             }
           }
           //'Urban'
           if($level == 1){
             if(!empty($localBody)){
               $mainquery = $mainquery." and municipality_code = ".$localBody;
             }
           }
         }
         $totalRecordsResult = DB::connection('pgsql_sp')->select($mainquery);

         
         if($totalRecordsResult){
            $totalRecords = $totalRecordsResult[0]->count;
         }
    }
        
 // WORKING QUERY
          
          $limit = $request->input('length');
          $offset = $request->input('start');

          $query = "select b.*, s.message, '".$app_type."'as app_type from (";
          
          $mainquery = " select * from wbmt_beneficiary ".$status;
          if(!empty($district_code)){
            $mainquery = $mainquery." and district_code = ".$district_code;
          }
          if(!empty($level)){
            //'Rural'
            if($level == 2){
              if(!empty($localBody)){
                $mainquery = $mainquery." and block_code = ".$localBody;
              }
            }
            //'Urban'
            if($level == 1){
              if(!empty($localBody)){
                $mainquery = $mainquery." and municipality_code = ".$localBody;
              }
            }
          }
          $query = $query.$mainquery;
    if(empty($serachvalue)){      
          if($limit >= 0){
            $query = $query." limit ".$limit." offset ".$offset;
          }
          $query = $query.") b left join wbmt_status_code s on b.status = s.code order by b.beneficiary_id";
          $data = DB::connection('pgsql_sp')->select($query);
    }else{
	      	$query = $query." and beneficiary_id='".$serachvalue."'";
              
            $query = $query.") b left join wbmt_status_code s on b.status = s.code";

            $data = DB::connection('pgsql_sp')->select($query);
            $totalRecords = count($data);
    }      

          
          return datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($totalRecords)
            ->skipPaging()
            ->addColumn('check', function ($data) {
                if($data->app_type == 'F' || $data->app_type == 'L'){
                    return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="'.$data->beneficiary_id.'">';
                }else{
                  return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="'.$data->beneficiary_id.'" disabled>';
                }
            })
            ->addColumn('ben_id', function ($data) {
                    return $data->beneficiary_id;
            })
            ->addColumn('ben_name', function ($data) {
                    return $data->ben_name;
            })
            ->addColumn('dob', function ($data) {
                      return $data->dob;
                    })
            ->addColumn('gender', function ($data) {
                      return $data->gender;
                    })
            ->addColumn('mob_no', function ($data) {
                      return $data->mobile_no;
              })
              ->addColumn('aadhar', function ($data) {
                    return $data->aadhar_no;
              })
              ->addColumn('status', function ($data) {
                    if($data->app_type == 'F'){
                      return '<span class="label label-primary">NEW</span><span class="badge">'.$data->duplicate_status.'</span>';
                    }elseif($data->app_type == 'A' || $data->app_type == 'L'){
                      return '<span class="label label-success">'.$data->message.'</span>';
                    }elseif($data->app_type == 'R'){
                      return '<span class="label label-danger">'.$data->message.'</span>';
                    }
                })
                ->addColumn('action', function ($data) {
                      $val = '<button class="btn btn-primary ben_view_button">View</button>';
                      if($data->app_type == 'F'){
                        $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
                      }
                      return $val;
                  })
              ->rawColumns(['check','ben_id','ben_name','dob','gender','mob_no','aadhar','status','action'])
              ->make(true); 

            }
            return json_encode(array('data'=>''));
            // return; datatables()
            // ->of("")
            // ->setTotalRecords(0)
            // ->setFilteredRecords(0)
            // ->skipPaging();
      }
      return view('Parijayi.index')->with('district_name',$district_name)->with('district_code',$district_code); 
    }     
    
    public function bulkApprove(Request $request)
    {
	      set_time_limit(0);


      // DB::transaction(function()
      // {
        $user_id = Auth::user()->id;

        $inputs_json = $request->approvalcheck;
        $inputs = json_decode($inputs_json, true);

        
        DB::beginTransaction();
        try{
          foreach($inputs as $input){  
            
            $ben = Parijayi::select('id','duplicate_status')->where('beneficiary_id', $input)->where('status','<',3)->first();
            $dup_status = $ben->duplicate_status;
            if($ben->duplicate_status == 'D') {
              $dup_status = 'R';
            }

            $input_update = ['status' => '2', 'duplicate_status' => $dup_status]; 
            Parijayi::where('beneficiary_id', $input)->whereIn('status',[1,-1])->update($input_update);   
            //$ben = Parijayi::select('id')->where('beneficiary_id', $input)->where('status',2)->first();
            //application_id','status_code','by_user'
            $newStatus = ApplicationStatus::create(['application_id' => $ben->id, 'status_code' => 2 ,'by_user' => $user_id]);   
            $newStatus->save();
          }
        }catch(\Exception $e){
          DB::rollback();
        } 
        DB::commit();
      //});
        return count($inputs);
    }

    public function rejectApplication(Request $request)
    {
      $ben_id = $request->ben_id;
     
      $scheme_id = $request->session()->get('scheme_id');
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');

      $role_id=$request->session()->get('role_id');
      $user_id = Auth::user()->id;
     
      $reject_reason = $request->reject_reason;
      DB::beginTransaction();
      try{
        $input_update = ['status' => $reject_reason]; 
        Parijayi::where('beneficiary_id', $ben_id)->whereIn('status',[1,-1])->update($input_update);
        
        $ben = Parijayi::select('id')->where('beneficiary_id', $ben_id)->where('status',$reject_reason)->first();
        //application_id','status_code','by_user'
        $newStatus = ApplicationStatus::create(['application_id' => $ben->id, 'status_code' => $reject_reason,'by_user' => $user_id]);   
        $newStatus->save();
      }catch(\Exception $e){
        DB::rollback();
      } 
      DB::commit();
    }
    

    public function printSingleBeneficiary(Request $request)
    {
        $ben_id = $request->ben_id; 
        
        $ben = Parijayi::where('id',$ben_id)->first();
        $localBody="";
        if($ben->rural_urban=='Rural'){
          $localBody = GP::where('gram_panchyat_code',$ben->gp_code)->pluck('gram_panchyat_name');
        }else{  
          $localBody = Ward::where('urban_body_ward_code',$ben->ward_code)->pluck('urban_body_ward_name');
        }
        return view('Parijayi.print_ben_dtl')->with('ben', $ben)
                                            ->with('localBody', $localBody);
    }

    public function getStatusCode(Request $request)
    {
      $statusCode = StatusCode::select('code','message')->where('code','>',5)->get();
      return $statusCode;
    }

    //LOT GENERATION
    public function lot_generation()
    {
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();


        $districts = District::select('district_code','district_name')->get();

        $lot_details = ParijayiLot::where('lot_created_by',$user_id)->where('lot_status',3)->first();
        if(!$lot_details)
          $lot_details = new ParijayiLot();
        $old_lot_details = ParijayiLot::where('lot_created_by',$user_id)->where('lot_status',4)
                            ->orderBy('updated_at', 'desc')->limit(6)->get();  

        $total_ben = ParijayiLot::where('lot_created_by',$user_id)->where('lot_status',4)->sum('ben_count');                  
        // app_type=L for Lot Generation
        return view('Parijayi.lot_generation')->with('districts',$districts)
                ->with('app_type','L')->with('lot_details',$lot_details)
                ->with('old_lots',$old_lot_details)
                ->with('total_ben',$total_ben);
    }


/*
*
*  WORKING ON LOT GENERATION UPDATE IN APPLICATION STATUS TABLE
*
*/


    public function createNewLot(Request $request)
    {
      $user_id = Auth::user()->id;  
      $lotCount = ParijayiLot::where('lot_created_by',$user_id)->where('lot_status',3)->get()->count();
      $lottype = $request->lottype;
      if($lotCount<2){ // Maximum open lots 1
        //Fetch LOT Number
        $lot_number_seeder=parijayi_lot_seeder::first();//
        $lot_number=$lot_number_seeder->lot_number;
        DB::beginTransaction();
        try{
          $newlot = ParijayiLot::create([
            'lot_created_by' => $user_id,
            'lot_no' => 'SP'.$lot_number,
            'ben_count' => 0,
            'lot_type' => $lottype
          ]);
          
          $success = $newlot->save();
          
          if($success){
            //Update seeder
            $seeder_update = [
              'lot_no' => $lot_number+1
            ];
            $is_seeder_updated=parijayi_lot_seeder::where('scheme_id',12)->update($seeder_update);
          }
        }catch(\Exception $e){
          DB::rollback();
        } 
        DB::commit();
      }

      return redirect('parijayi_generate_lot');
    }

    public function bulkAddToLot(Request $request)
    {
      set_time_limit(0);

      $user_id = Auth::user()->id;  
      $lot_no = $request->lot_no;
      $benList_json = $request->approvalcheck;
      $benList = array_unique(json_decode($benList_json, true)); 

      $amount = 1000;
      $scheme_id = 12;
      $status = 3; // Assigned to Lot

        //Update Beneficiary Status as 'assigned to lot - 3'
        $input_update = ['status' => 3]; 
        Parijayi::whereIn('beneficiary_id', $benList)->where('status',2)->update($input_update);      
      
      //Get Lot Details
      $lot_details = ParijayiLot::where('lot_created_by',$user_id)->where('lot_no',$lot_no)->first();
      DB::beginTransaction();
      try{
        
        //Copy beneficiaries to ben_export with status as 'assigned to lot -3'
        $benList = Parijayi::whereIn('beneficiary_id', $benList)->where('status',3)->get();
        $benExport = array();
        $ben_status = array();
        foreach($benList as $i=>$ben)
        {
          $benExport[$i] = array();
          $benExport[$i]['name'] = $ben->ben_name;
          $benExport[$i]['bank_name'] = $ben->bank_name;
          $benExport[$i]['ifsc'] = $ben->ifsc_code;
          $benExport[$i]['acc_no'] = $ben->account_no;
          $benExport[$i]['amount'] = $amount;
          $benExport[$i]['ben_id'] = $ben->beneficiary_id;
          $benExport[$i]['mobile_no'] = $ben->mobile_no;
          $benExport[$i]['lot_number'] = $lot_no;
          $benExport[$i]['scheme_id'] = $scheme_id;
          $benExport[$i]['status'] = $status;

          $ben_status[$i] = array();
          $ben_status[$i]['application_id']= $ben->id;
          $ben_status[$i]['status_code']= $status; // Assigned to lot 3
          $ben_status[$i]['by_user']= $user_id;
        }
        ParijayiExport::insert($benExport);
        ApplicationStatus::insert($ben_status);
        
        $numberofBeneficiary = sizeof($benExport);

        
        $input_update = ['ben_count' => $lot_details->ben_count+$numberofBeneficiary]; 
        ParijayiLot::where('lot_no', $lot_no)->update($input_update); 
      }catch(\Exception $e){
        DB::rollback();
      } 
      DB::commit();

      return $lot_details->ben_count+$numberofBeneficiary;
    }

    public function processLot(Request $request)
    {
      set_time_limit(0);

      $user_id = Auth::user()->id;  
      $lot_no = $request->lot_no;
      $lot_type = $request->lot_type;
      $status = 4; // For Processed Lot
      
      //select Beneficiary id (primary_internal) for the lot process
      $ben_list = Parijayi::whereIn('beneficiary_id', function($query) use($lot_no){
        $query->select ('ben_id')
        ->from(with(new ParijayiExport)->getTable())
        ->where('status',3)
        ->where('lot_number',$lot_no);
      })->pluck('id');
        
      // Export to Excel Data
      //$export_data = ParijayiExport::select('amount','sender_account_type','sender_acc_no','sender_name','sender_sms_eml','sender_email','sender_scheme_name','ifsc','ben_account_type','acc_no','name','sender_to_rcvr_info')
      //              ->where('status',3)->where('lot_number',$lot_no)->get();
      if($lot_type == 'NONICICI'){
        $query = 'select row_number() over (order by ben_id) as "Sr. No."'.",''".' as "TRAN.ID",amount as "AMOUNT",sender_account_type as "SENDER ACCOUNT TYPE",TRIM(sender_acc_no) AS "SENDER ACCOUNT NO",
        TRIM(sender_name) AS "SENDER NAME",TRIM(sender_sms_eml) AS "SMS EML",TRIM(sender_email) AS "Detail",TRIM(sender_scheme_name) AS "OoR7002 (SENDER NAME)",TRIM(ifsc) AS "BENEFICIARY IFSC",
        ben_account_type AS "BENEFICIARY ACCOUNT TYPE",TRIM(acc_no) AS "BENEFICIARY ACCOUNT NO",TRIM(name) AS "BENEFICIARY ACCOUNT NAME",TRIM(sender_to_rcvr_info) AS "SENDER TO RECEIVER INFORMATION", ben_id as "Beneficiary ID"
        from ben_export where lot_number'."='".$lot_no."'";  
        $data = DB::connection('pgsql_sp')->select($query);   
        $excelData = json_decode( json_encode($data), true);       
        Excel::create($lot_no, function($excel) use($excelData) {
          $excel->sheet('Payment_Mandate', function ($sheet) use ($excelData) {
            $sheet->setOrientation('landscape');
            $sheet->fromArray($excelData, NULL, 'A4');
          });
        })->store('xlsx',storage_path('app/sp_lot_mandate/'));
      }else if($lot_type == 'ICICI'){
        $query = 'select row_number() over (order by ben_id) as "S.No", TRIM(name) AS "Name", TRIM(acc_no) AS "Account Number", 
        amount as "Amount",'. "''".' as "Remarks", ben_id as "Beneficiary ID"
        from ben_export where lot_number'."='".$lot_no."'";  
        $data = DB::connection('pgsql_sp')->select($query);   
        $excelData = json_decode( json_encode($data), true);       
        Excel::create($lot_no, function($excel) use($excelData) {
          $excel->sheet('Payment_Mandate', function ($sheet) use ($excelData) {
            $sheet->setOrientation('landscape');
            $sheet->fromArray($excelData, NULL, 'A4');
          });
        })->store('xlsx',storage_path('app/sp_lot_mandate/'));
      }
      //->download('xlsx');

      DB::beginTransaction();
      try{
        //update beneficiary status with 4
        Parijayi::where('status',3)->update(['status'=>$status, 'lot_generated'=>$status]);

        //update ben_export status with 4
        ParijayiExport::where('status',3)
                ->update(['status'=>$status]);

        //Close Lot with flag 4
        ParijayiLot::where('lot_no',$lot_no)->update(['lot_status'=>$status,'file_name'=>$lot_no.'.xlsx']);
        //insert Application Status Log with 4
        $ben_status = array();
        foreach($ben_list as $i=>$ben)
        {
          $ben_status[$i] = array();
          $ben_status[$i]['application_id']= $ben;
          $ben_status[$i]['status_code']= $status; // Assigned to lot 3
          $ben_status[$i]['by_user']= $user_id;
        }
        ApplicationStatus::insert($ben_status);

      }catch(\Exception $e){
        DB::rollback();
      } 
      DB::commit();
      
      return redirect('parijayi_generate_lot');

    }

    // public function exportBenDataExcel($lotno)
    // {
      
    // }

    //Get Filter Dropdown
    public function getLocalBody(Request $request)
    {
      //UrbanBody/Taluka
      $urban_rural = $request->urban_rural;
      $district_code = $request->district_code;

      if($urban_rural == 1){
        $body = UrbanBody::where('district_code', '=', $district_code)->get(['urban_body_code AS id', 'urban_body_name AS name']);
      }else{
        $body = Taluka::where('district_code', '=', $district_code)->get(['block_code AS id', 'block_name AS name']);
      }
      return response()->json($body);

    }


    //Import Bank Response
    public function importBankResponse()
    {
      //$data = BankResponse::get();
      $lot_nos =  ParijayiLot::where('lot_status',4)->whereNull('ack_status')->orderBy('lot_no')->pluck('lot_no');
      return view('Parijayi.bank_response_import')->with('lot_nos',$lot_nos);
    }
    
    public function importBankResponseByLot(Request $request)
    {
      $lot_no = $request->lotno;
      $data = BankResponse::where('lot_no',$lot_no)->get();
      return datatables()
             ->of($data)
             ->make(true); 
    }

    public function importBankResponseFile(Request $request){
      set_time_limit(0);

      $this->validate($request,[
        'select_file' => 'required|mimes:xls,xlsx'
      ]);
      $path = $request->file('select_file')->getRealPath();
      $file_name = pathinfo($request->file('select_file')->getClientOriginalName(),PATHINFO_FILENAME);
      $split_name = explode("-", $file_name );
      if(sizeof($split_name)==2){   
        $lot_no = $split_name[1];
        $data = Excel::load($path)->get();

        // echo '<pre>';
        // print_r($data)
        // echo '</pre>';
        // die();

        $lot_detail =  ParijayiLot::where('lot_no',$lot_no)->first();

        if($lot_detail['ack_status']==5){
          return back()->withErrors("File '".$file_name."' Already Imported");
        }

        $insert_data = array();  
        if($data->count()>0){
          foreach($data->toArray() as $row){
              $insert_data[] =array(
                'lot_no' => $lot_no,
                'sr_no' => $row['sr_no'],
                'sequence_no' => $row['sequence_no'],
                'transaction_ref'  => $row['transaction_ref'],
                'amount'  => $row['amount'],
                'value_date'  => $row['value_date'],
                'sending_branch_ifsc'  => $row['sending_branch_ifsc'],
                'sender_ac_type' => $row['sender_ac_type'],
                'sender_ac_no' => $row['sender_ac_no'],
                'sender_ac_name' => $row['sender_ac_name'],
                'benf_branch' => $row['benf_branch'],
                'benf_ac_type'  => $row['benf_ac_type'],
                'benf_ac_no'  => $row['benf_ac_no'],
                'benf_ac_name'  => $row['benf_ac_name'],
                'txn_status'  => $row['txn_status'],
                'originator_of_remittance' => $row['originator_of_remittance'],
                'sender_to_receiver_information' => $row['sender_to_receiver_information'],
                'reason_code'  => $row['status'],
                'reason' => $row['reason']
              );
          }
          if(!empty($insert_data)){
            BankResponse::insert($insert_data);
          }
        }
        else{
          return back()->withErrors('File Name related error');
        }
      }

      return back()->with('success','Bank Response file for Lot #'.$lot_no.' Imported successfully');

    }

    public function processBankResponse(Request $request)
    {
      $user_id = Auth::user()->id;

      $lot_no = $request->lot_no;
      $lot = ParijayiLot::where('lot_no',$lot_no)->first();

      if($lot['ack_status']==5){
        return 'Lot #'.$lot_no.' already processed.';
      }else{
        DB::beginTransaction();
        try{
          $allUsersCount=DB::connection('pgsql_sp')->select("SELECT process_bank_response(".$user_id.", '".$lot_no."')");
          //ParijayiLot::update('ack_status','Received')->where('lot_no',$lot_no);
        }catch(\Exception $e){
          DB::rollback();
          return 'Error faced while processing Lot #'.$lot_no;
        } 
        DB::commit();
        return 'Lot #'.$lot_no.' processed successfully.';
      }
    }
}
