<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use App\nhm_level_place;
//use App\nhm_posting_level;
//use App\nhm_health_facility;
use App\nhm_health_facility;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\User;
use App\SubDistrict;
use Auth;

class NHMPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::All();
        //$nhm_level_places = nhm_level_place::paginate(5);
        //$nhm_level_places=nhm_health_facility::orderBy('facility_name','ASC')->paginate(20);
         

         $nhm_level_places=DB::table('m_health_facility')
        ->leftjoin('m_district','m_health_facility.district_code','=','m_district.district_code')
        ->leftjoin('m_taluka','m_health_facility.taluka_code','=','m_taluka.taluka_code')
        ->leftjoin('m_urban_body','m_health_facility.taluka_code','=','m_urban_body.urban_body_code')
        ->orderBy('facility_name','ASC')->select('m_health_facility.id as id','m_health_facility.facilty_code',
            'm_health_facility.facility_name','m_health_facility.district_code','m_health_facility.taluka_code','m_health_facility.facility_type','m_taluka.taluka_name',
            'm_district.district_name','m_urban_body.urban_body_name')->paginate(100);
       
        return view('system-mgmt/nhmlevelplaces/index', ['nhm_level_places' => $nhm_level_places])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        $nhm_districts = District::All();
        return view('system-mgmt/nhmlevelplaces/create',['nhm_districts' =>$nhm_districts])->with('users',$users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $users = User::All();
        $this->validateInput($request);

        $nhm_health_facility=new nhm_health_facility();
        
        $nhm_health_facility->facility_name=$request->health_facility_name;
        $nhm_health_facility->facilty_code=$request->health_facility_code;
        $nhm_health_facility->facility_type=$request->health_facility_type;
        $nhm_health_facility->district_code=$request->district;
        $nhm_health_facility->taluka_code=$request->location;
         
        $is_saved=$nhm_health_facility->save();
       
        // nhm_health_facility::create([
        //     'facility_name' => $request['health_facility_name'],
        //     'facilty_code'=> $request['health_facility_code'],
        //     'facility_type'=>$request['health_facility_type'],
        //     'district_code'=>$request['district'],
        //     'taluka_code'=>$request['location'],
        // ]);

        if($is_saved){
        return redirect()->intended('system-management/nhmPlace')->with('users',$users);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::All();
        $nhm_level_place = nhm_health_facility::find($id);

        $location=$nhm_level_place->taluka_code;

        if($location!= null){
            $location_detail=UrbanBody::where('urban_body_code','=',$location)->select('urban_body_code as location_code','urban_body_name as location_name')->first();
            //dd( $location_detail);
            if($location_detail==null){
               $location_detail=Taluka::where('taluka_code','=',$location)->select('taluka_code as location_code','taluka_name as location_name')->first();  
            }
        }else{
            $location_detail=null;
        }


        //dd( $location_detail);
        $nhm_districts = District::All();
        // Redirect to department list if updating department wasn't existed
        if ($nhm_level_place == null) {
            return redirect()->intended('/system-management/nhmPlace');
        }

        return view('system-mgmt/nhmlevelplaces/edit', ['nhm_level_place' => $nhm_level_place,'nhm_districts'=>$nhm_districts,'location_detail'=>$location_detail])->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $users = User::All();
        $nhm_level_place = nhm_health_facility::findOrFail($id);
        $this->validateInput($request);
        $input = [

            'facility_name' => $request['health_facility_name'],
            'facilty_code'=> $request['health_facility_code'],
            'facility_type'=>$request['health_facility_type'],
            'district_code'=>$request['district'],
            'taluka_code'=>$request['location'],
            
        ];
        $is_updated=nhm_health_facility::where('id', $id)->update($input);
        
        return redirect()->intended('system-management/nhmPlace')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::All();
        $is_deleted=nhm_health_facility::where('id', $id)->delete();

        //$is_deleted=nhm_health_facility::find($id)->delete();
        //dd($is_deleted);
        if($is_deleted){
        return redirect()->intended('system-management/nhmPlace')->with('users',$users);
        }
    }

 

    public function search(Request $request) {
        $users = User::All();
       
        $facilityname=$request->facilityname;
        $facilitytype=$request->facilitytype;
        $district=$request->district;
        
       // dd($facilityname);
        $constraints = [
            'Facility Name' => $request['facilityname'],
            'Facility Type' => $request['facilitytype'],
            'District' => $request['district'],
            ];
       //dd($constraints);
       $newConstratints = [
        'facility_name' => strtolower(str_replace(' ','',$facilityname)),
        'facility_type' => strtolower(str_replace(' ','',$facilitytype)),
        'district_name' => strtolower(str_replace(' ','',$district))
        ];

         $nhm_level_places=DB::table('m_health_facility')
        ->leftJoin('m_district','m_health_facility.district_code','=','m_district.district_code')
        ->leftJoin('m_taluka','m_health_facility.taluka_code','=','m_taluka.taluka_code')
        ->leftjoin('m_urban_body','m_health_facility.taluka_code','=','m_urban_body.urban_body_code')
        ->where('m_health_facility.facility_name','ilike','%'.$facilityname.'%')
        ->Where('m_health_facility.facility_type','ilike','%'.$facilitytype.'%')
        ->Where('m_district.district_name','ilike','%'.$district.'%')
        ->select('m_health_facility.id as id','m_health_facility.facilty_code',
            'm_health_facility.facility_name','m_health_facility.district_code','m_health_facility.taluka_code','m_health_facility.facility_type','m_taluka.taluka_name',
            'm_district.district_name','m_urban_body.urban_body_name')->get();

        //$nhm_level_places->appends(['nhmPlace.search' => $newConstratints]);
        //dd($nhm_level_places);
        
        // $fields = array_keys($newConstratints);
        // $index = 0;
        // foreach ($newConstratints as $newConstratint) {
        //     if ($newConstratint != null) {
        //         $nhm_level_places = $query->where( $fields[$index], 'ilike', '%'.$newConstratint.'%')->paginate(20);
        //     }

        //     $index++;
        // }

        //return view('system-mgmt/nhmlevelplaces/index', compact('nhm_level_places','constraints'));
       
        //$nhm_level_places->appends($newConstratints);
    
      //dd($newConstratints);
       //$nhm_level_places = $this->doSearchingQuery($constraints,$newConstratints);
       return view('system-mgmt/nhmlevelplaces/search', ['nhm_level_places' => $nhm_level_places, 'searchingVals' => $constraints])->with('users',$users);
       
    }

        private function doSearchingQuery($constraints,$newConstratints) {
        //$query = nhm_level_place::query();
        $query=DB::table('m_health_facility')
        ->leftjoin('m_district','m_health_facility.district_code','=','m_district.district_code')
        ->leftjoin('m_taluka','m_health_facility.taluka_code','=','m_taluka.taluka_code');

      
        
        $fields = array_keys($newConstratints);
        $index = 0;
        foreach ($newConstratints as $newConstratint) {
            if ($newConstratint != null) {
                $result = $query->where( $fields[$index], 'ilike', '%'.$newConstratint.'%');
            }

            $index++;
        }


       
        return $result->paginate(20)->appends($newConstratints);
    }

     public function loadlocationHealthFacilityEdit($district_code) {
    
        $urban_bodys = UrbanBody::where('district_code', '=', $district_code)->get(['urban_body_code as id', 'urban_body_name as name']);
        $talukas = Taluka::where('district_code', '=', $district_code)->get(['taluka_code as id', 'taluka_name as name']);

        $merged = $urban_bodys->merge($talukas);
        $result = $merged->all();
       
       return response()->json($result);
       
    }

    
    private function validateInput($request) {
        $this->validate($request, [
        'health_facility_name' => 'required|max:120',
        'health_facility_code'=> 'required|max:120|',
        'health_facility_type'=>'required|max:60',
        'district'=>'required|max:120',
        'location'=>'required|max:140',
        ]);
    }
}
