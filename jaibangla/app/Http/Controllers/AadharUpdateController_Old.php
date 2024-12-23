<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;

use App\PensionSc;
use App\PensionSt;
use App\PensionFisherman;
use App\PensionMSME;
use App\PensionTextile;
use App\PensionManabikWCD;
use App\PensionOAPWCD;
use App\PensionWPWCD;
use App\PensionOAPFarmer;
use App\PensionPurohitMonthlyICAD;
use App\PensionPurohitHousingICAD;
use App\PensionOAPST;
use App\Helpers\AuthChecker;

//Dynamic Doc
use App\BenDocsSc;
use App\BenDocsSt;
use App\BenDocsFisherman;
use App\BenDocsMSME;
use App\BenDocsTextile;
use App\BenDocsManabikWCD;
use App\BenDocsOAPWCD;
use App\BenDocsWPWCD;
use App\BenDocsOAPFarmer;
use App\BenDocsPurohitMonthlyICAD;
use App\BenDocsPurohitHousingICAD;

use App\BenDocsArcSc;
use App\BenDocsArcSt;
use App\BenDocsArcFisherman;
use App\BenDocsArcMSME;
use App\BenDocsArcTextile;
use App\BenDocsArcManabikWCD;
use App\BenDocsArcOAPWCD;
use App\BenDocsArcWPWCD;
use App\BenDocsArcOAPFarmer;
use App\BenDocsArcPurohitMonthlyICAD;
use App\BenDocsArcPurohitHousingICAD;

use App\SchemecodeStatic;

use App\DocumentType;
use App\SchemeDocMap;
use App\Scheme;
//Dynamic Doc End
use App\Manabik;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\MapLavel;
use Redirect;
use Auth;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class AadharUpdateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $arr = SchemecodeStatic::getpr1ListPurohit();
        // print_r($arr);die;
        $this->monthlySlug = $arr['monthly']['slug'];
        $this->monthlySchemeCode = $arr['monthly']['scheme_code'];
        $this->monthlyMainTable = "App\\" . $arr['monthly']['maintable'];
        $this->monthlyDocTable = "App\\" . $arr['monthly']['doctable'];
        $this->monthlyDocArchTable = "App\\" . $arr['monthly']['docarchtable'];

        $this->housingSlug = $arr['housing']['slug'];
        $this->housingSchemeCode = $arr['housing']['scheme_code'];
        $this->housingMainTable = "App\\" . $arr['housing']['maintable'];
        $this->housingDocTable = "App\\" . $arr['housing']['doctable'];
        $this->housingDocArchTable = "App\\" . $arr['housing']['docarchtable'];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('pr1')) {
            if ($request->get('pr1') == "sc") {
                $scheme_id = 3;
            } else if ($request->get('pr1') == "st") {
                $scheme_id = 1;
            } else if ($request->get('pr1') == $this->monthlySlug) {
                $scheme_id = $this->monthlySchemeCode;
            } else if ($request->get('pr1') == $this->housingSlug) {
                $scheme_id = $this->housingSchemeCode;
            }
        }
        $is_active = 0;

        // $base_url=url('/');
        // echo $base_url.'/images/';exit;        

        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $request->session()->put('level', $roleObj['mapping_level']);
                $request->session()->put('distCode', $roleObj['district_code']);
                if ($roleObj['is_urban'] == 1) {
                    $request->session()->put('blockCode', $roleObj['urban_body_code']);
                } else {
                    $request->session()->put('blockCode', $roleObj['taluka_code']);
                }
                break;
            }
        }
        if ($is_active == 1) {
            $districts = District::all();
            //return view('pension_details')->with('districts',$districts); 

            //Document Dynamic
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
            $doc_list_man = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_man']));
            $doc_list_opt = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_opt']));
            $doc_profile_image = DocumentType::get()
                ->where("is_profile_pic", true)->first();

            $doc_profile_image_id = 999;
            if ($doc_profile_image) {
                $doc_profile_image_id = $doc_profile_image->id;
            }
            //echo "<pre>";print_r($doc_profile_image_id); echo "</pre>";die();  
            return view('pension_details', [
                'districts' => $districts,
                'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id
            ]);
        }
        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            return redirect("/")->with('success', 'User Disabled');
        }
    }

    //###################  Opeartor ################################
    public function schemeList(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if ($designation_id_old != 'Operator') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $query = "select d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name from public.duty_assignement d 
                join m_scheme s on d.scheme_id = s.id 
                join users u on u.id = d.user_id 
                where d.user_id = " . $user_id . "
                group by d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name
                order by s.scheme_name";
        $userObj = DB::connection('pgsql_mis')->select($query);
        if (count($userObj) > 0) {
            return view('portal.scheme_aadhar_update', ['user_id' => $user_id, 'designation_id_old' => $designation_id_old, 'scheme_id' => $scheme_id, 'userObj' => $userObj]);
        } else {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
    }
    public function applicationlistAadharUpdate(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        //dd($designation_id_old);
        if ($designation_id_old != 'Operator') {
            return redirect("/")->with('error', 'Not Allowed');
        }


        if ($scheme_id) {
            if ($scheme_id == 13) {
                $scheme_id = 13;
                $model_name = 'App\\PensionOAPFarmer';
            } else if ($scheme_id == 3) {
                $scheme_id = 3;
                $model_name = 'App\\PensionSc';
            } else if ($scheme_id == 1) {
                $scheme_id = 1;
                $model_name = 'App\\PensionSt';
            } else if ($scheme_id == 5) {
                $scheme_id = 5;
                $model_name = 'App\\PensionFisherman';
            } else if ($scheme_id == 6) {
                $scheme_id = 6;
                $model_name = 'App\\PensionMSME';
            } else if ($scheme_id == 7) {
                $scheme_id = 7;
                $model_name = 'App\\PensionTextile';
            } else if ($scheme_id == 2) {

                $model_name = 'App\\PensionManabikWCD';
            } else if ($scheme_id == 10) {
                $model_name = 'App\\PensionOAPWCD';
            } else if ($scheme_id == 11) {
                $model_name = 'App\\PensionWPWCD';
            } else if ($scheme_id == 17) {
                $model_name =  'App\\PensionPurohitMonthlyICAD';
            } else if ($scheme_id == 18) {
                $model_name = 'App\\PensionPurohitHousingICAD';
            } else {
                return redirect("/")->with('success', 'User Disabled');
            }
            $is_active = 0;
            $roleArray = $request->session()->get('role');
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $distCode = $roleObj['district_code'];
                    $is_urban = $roleObj['is_urban'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($is_active == 0) {
                return redirect("/")->with('success', 'User Disabled');
            }

            // echo $distCode.','.$is_urban.','.$blockCode.','.$user_id; exit;
            // $rows = $model_name::where(['rural_urban_id' => $is_urban, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'aadhar_no' => null, 'aadhar_edit_role_id' => null, 'next_level_role_id' => 0])->where(function ($query) use ($user_id) {
            //     $query->where('created_by', '=', $user_id)
            //         ->orWhereNull('created_by');
            // })->get();//orderBy('id', 'desc');
            // ->pagiante()->appends(request()->query());
            $rows = $model_name::where('created_by_dist_code', '=', $distCode)
                ->where('created_by_local_body_code', '=', $blockCode)
                ->where('scheme_id', '=', $scheme_id)
                ->where('next_level_role_id', '=', '0')
                // ->whereNull('aadhar_edit_role_id')
                ->whereraw("(aadhar_edit_role_id IS NULL or aadhar_edit_role_id=-6 or aadhar_edit_role_id=-7)")
                ->whereraw("(aadhar_no IS NULL or LENGTH(aadhar_no)!=12)")
                ->orderBy('id', 'desc')
                ->get();
            // ->toSql();
            // print($rows);die();
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }

        return view('aadhar-update/pension_list_aadhar_update', ['nhm_employee_details' => $rows, 'scheme_id' => $scheme_id, 'list_type' => '0']);
    }

    public function applicationeditviewAadharUpdate(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $id = $request->id;
        //dd($user_id);
        $scheme_id = (int) $request->scheme_id;

        $designation_id_old = Auth::user()->designation_id_old;

        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {
            return redirect("/")->with('error', 'Applicant ID Not Valid');
        }
        $row = array();
        if ($scheme_id == 13) {
            // $row = PensionSc::find($id);
            $model_name = 'App\\PensionOAPFarmer';
        } else if ($scheme_id == 3) {
            // $row = PensionSc::find($id);
            $model_name = 'App\\PensionSc';
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_name = 'App\\PensionSt';
        } else if ($scheme_id == 5) {
            //  $row = PensionFisherman::find($id);
            $model_name = 'App\\PensionFisherman';
        } else if ($scheme_id == 6) {
            //$row = PensionMSME::find($id);
            $model_name = 'App\\PensionMSME';
        } else if ($scheme_id == 7) {
            // $row = PensionTextile::find($id);
            $model_name = 'App\\PensionTextile';
        } else if ($scheme_id == 2) {
            // $row = PensionManabikWCD::find($id);
            $model_name = 'App\\PensionManabikWCD';
        } else if ($scheme_id == 10) {
            // $row = PensionOAPWCD::find($id);
            $model_name = 'App\\PensionOAPWCD';
        } else if ($scheme_id == 11) {
            // $row = PensionWPWCD::find($id);
            $model_name = 'App\\PensionWPWCD';
        } else if ($scheme_id == 17) {
            // $row = $this->monthlyMainTable::find($id);
            $model_name = 'App\\PensionPurohitMonthlyICAD';
        } else if ($scheme_id == 18) {
            //$row = $this->housingMainTable::find($id);
            $model_name = 'App\\PensionPurohitHousingICAD';
        }
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        //dd($distCode);
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        if ($scheme_id == 17) {
            $query = $model_name::where(['id' => $id,  'scheme_id' => $scheme_id])->whereraw("(aadhar_edit_role_id IS NULL or aadhar_edit_role_id=-6 or aadhar_edit_role_id=-7)");
        } else {
            $query = $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->whereraw("(aadhar_edit_role_id IS NULL or aadhar_edit_role_id=-6 or aadhar_edit_role_id=-7)");
        }

        $query = $query->where('next_level_role_id',  0);

        $row = $query->first();
        //dd($row);
        // echo "<pre>"; print_r($row); exit;
        if (empty($row)) {
            return redirect("/")->with('error', 'Applicant Id not found');
        }
        $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);

        // $blocks= Taluka::where('district_code','=',$row->dist_code)->get(['block_code', 'block_name']);

        // $gps = GP::where('block_code','=',$row->block_ulb_code)->get(['gram_panchyat_code','gram_panchyat_name']);

        //Document Dynamic
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();
        // $scheme_name = Config::get('constants.schemeurl.'. $doc_id_list->scheme_code .'');

        if (!empty($doc_id_list->doc_list_man))
            $doc_list_man = DocumentType::get()->whereIn("id", json_decode($doc_id_list->doc_list_man));
        else
            $doc_list_man = collect([]);
        if (!empty($doc_id_list->doc_list_opt))
            $doc_list_opt = DocumentType::get()->whereIn("id", json_decode($doc_id_list->doc_list_opt));
        else
            $doc_list_opt = collect([]);
        $doc_profile_image = DocumentType::get()
            ->where("is_profile_pic", true)->first();

        $doc_profile_image_id = 999;
        if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
        }

        $document_msg = "";

        return view('aadhar-update/aadhar_edit', ['row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
    }


    public function aadharUpdatePost(Request $request, $id)
    {
        $base_url = url('/');
        $id = $request->id;
        // echo $id;die();
        $scheme_id = (int) $request->scheme_id;

        //echo $id.'/'.$scheme_id; 
        //dd($scheme_id);
        $designation_id_old = Auth::user()->designation_id_old;
        if (!is_int($scheme_id)) {

            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {

            return redirect("/")->with('error', 'Applicant ID Not Valid');
        }
        $created_by = Auth::user()->id;
        $is_active = 0;
        $mapping_level = NULL;
        $roleArray = $request->session()->get('role');

        //echo "<pre>"; print_r($roleArray);exit;
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }

        if ($scheme_id == 13) {
            $model_name = 'App\\PensionOAPFarmer';
            $model_ben_docs = 'App\\BenDocsOAPFarmer';
            $img_folder = 'images_farmer';
            $img_keep_folder = 'keep_farmer';
        } else if ($scheme_id == 3) {
            // $row = PensionSc::find($id);
            $model_name = 'App\\PensionSc';
            $model_ben_docs = 'App\\BenDocsSc';
            $img_folder = 'images';
            $img_keep_folder = 'keep';
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_name = 'App\\PensionSt';
            $model_ben_docs = 'App\\BenDocsSt';
            $img_folder = 'images';
            $img_keep_folder = 'keep';
        } else if ($scheme_id == 5) {
            //  $row = PensionFisherman::find($id);
            $model_name = 'App\\PensionFisherman';
            $model_ben_docs = 'App\\BenDocsFisherman';
            $img_folder = 'images';
            $img_keep_folder = 'keep';
        } else if ($scheme_id == 6) {
            //$row = PensionMSME::find($id);
            $model_name = 'App\\PensionMSME';
            $model_ben_docs = 'App\\BenDocsMSME';
            $img_folder = 'images';
            $img_keep_folder = 'keep';
        } else if ($scheme_id == 7) {
            // $row = PensionTextile::find($id);
            $model_name = 'App\\PensionTextile';
            $model_ben_docs = 'App\\BenDocsTextile';
            $img_folder = 'images';
            $img_keep_folder = 'keep';
        } else if ($scheme_id == 2) {
            // $row = PensionManabikWCD::find($id);
            $model_name = 'App\\PensionManabikWCD';
            $model_ben_docs = 'App\\BenDocsManabikWCD';
            $img_folder = 'images_wcd';
            $img_keep_folder = 'keep_wcd';
        } else if ($scheme_id == 10) {
            // $row = PensionOAPWCD::find($id);
            $model_name = 'App\\PensionOAPWCD';
            $model_ben_docs = 'App\\BenDocsOAPWCD';
            $img_folder = 'images_wcd';
            $img_keep_folder = 'keep_wcd';
        } else if ($scheme_id == 11) {
            // $row = PensionWPWCD::find($id);
            $model_name = 'App\\PensionWPWCD';
            $model_ben_docs = 'App\\BenDocsWPWCD';
            $img_folder = 'images_wcd';
            $img_keep_folder = 'keep_wcd';
        } else if ($scheme_id == 17) {
            // $row = $this->monthlyMainTable::find($id);
            $model_name = 'App\\PensionPurohitMonthlyICAD';
            $model_ben_docs = 'App\\BenDocsPurohitMonthlyICAD';
            $img_folder = 'keep_ICAD';
            $img_keep_folder = 'keep_ICAD';
        } else if ($scheme_id == 18) {
            //$row = $this->housingMainTable::find($id);
            $model_name = 'App\\PensionPurohitHousingICAD';
            $model_ben_docs = 'App\\BenDocsPurohitHousingICAD';
            $img_folder = 'keep_ICAD';
            $img_keep_folder = 'keep_ICAD';
        }
        // echo $model_name;exit;
        $query = $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
        // if ($designation_id_old == 'Verifier') {
        //     $query = $query->whereNull('next_level_role_id');
        // } else if ($designation_id_old == 'Approver') {
        //     $query = $query->where('next_level_role_id', '>', 0);
        // } else {
        //     $query = $query->whereNull('next_level_role_id');
        // }
        $row = $query->first();
        //echo $row->scheme_id; exit;

        if (empty($row->scheme_id)) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        //dd($row);exit;  

        // $this->validateInput($request,  $scheme_id);

        $attributes = array();
        $messages   = array();

        $rules = [
            'aadhar_no' => 'required|numeric|digits:12',
            // 'aadhar_no' => 'required|max:60|unique:'.$model_name.',aadhar_no,'.$id,           
        ];

        $attributes['aadhar_no'] = 'Aadhaar Number';

        $rules['aadhar_document'] =  'required|mimes:jpg,jpeg,png,pdf|max:500';



        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->passes()) {

            $post_aadhar_no = $request->aadhar_no;
            if ($this->isAadharValid($post_aadhar_no) == false) {
                $return_text = 'Aadhaar Number Invalid';
                $return_msg = array("" . $return_text);

                return redirect("application-aadhar-update-view?id=" . urlencode($id) . "&scheme_id=" . urlencode($scheme_id))->with('errors', $return_msg)->withInput(Input::all());
            }

            $aadharCount = $model_name::whereRaw("trim(aadhar_no)=trim(" . "'" . $request->aadhar_no . "'" . ")")->where('id', '!=', $id)->count();

            if ($aadharCount > 0) {

                $return_text = 'Aadhaar Number Already Exist';
                $return_msg = array("" . $return_text);

                return redirect("application-aadhar-update-view?id=" . urlencode($id) . "&scheme_id=" . urlencode($scheme_id))->with('errors', $return_msg)->withInput(Input::all());
            }

            $input = [
                'aadhar_no'  => trim($request->aadhar_no),
                'created_by' => $created_by,
                'created_by_level' => $mapping_level,
                'aadhar_edit_role_id' => 1,
            ];

            $pr1 = "";


            if ($request->hasFile('aadhar_document')) {

                $doc_file = $request->file('aadhar_document');
                $file_passport = $doc_file->getClientOriginalName();
                $file_type = $doc_file->getClientOriginalExtension();
                $file_profile = "doc_6" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
                $destinationPath = storage_path('app/' . $img_keep_folder);
                $doc_file->move($destinationPath, $file_profile);
                //array_push($uploaded_doc,$file_profile); 
                //echo 345; exit;               
            }

            DB::beginTransaction();
            try {
                $schema_name = $this->getSchemaName($scheme_id);
                /* 
                    Archive  Aadhar card document if exists in ben_docs table
                    */
                $ben_docs_count = $model_ben_docs::where(['doc_type_id' => 6, 'ben_id' => $id, 'doc_type_name' => 'Copy of Aadhar Card'])->count();
                if ($ben_docs_count > 0) {
                    $queryInsertToArchive = "INSERT INTO " . $schema_name . "_arc(ben_id, doc_type_id, doc_name, doc_type_name, created_at, updated_at,deleted_at )
                        SELECT ben_id, doc_type_id, doc_name, doc_type_name, created_at, updated_at,deleted_at 
                        FROM " . $schema_name . " WHERE ben_id=" . $id . " AND doc_type_id=6";
                    // dd($queryInsertToArchive);
                    $isArchiveDone = DB::select($queryInsertToArchive);
                    if ($isArchiveDone) {
                        $model_ben_docs::where(['doc_type_id' => 6, 'ben_id' => $id, 'doc_type_name' => 'Copy of Aadhar Card'])->delete();
                    }
                }
                /* Archive End */
                $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 0])
                    ->update($input);

                $ben_docs = new $model_ben_docs();
                // echo $model_ben_docs;//exit;
                $ben_docs->ben_id = $id;
                $ben_docs->doc_type_id = 6;
                $ben_docs->doc_name = $base_url . '/' . $img_folder . '/' . $file_profile;
                //$ben_docs->doc_name = $base_url.'/jaibangla/storage/app/keep_wcd/'.$doc;                
                $ben_docs->doc_type_name = 'Copy of Aadhar Card';
                $ben_docs->is_active = true;
                $ben_docs->save();
            } catch (\Exception $e) {
                DB::rollback();
                if ($designation_id_old == 'Operator')
                    return redirect("application-list-aadhar-update?" . $scheme_id)->with('error', 'Some error.Please try again')
                        ->with('id',   $row->getBenidAttribute());
                else {
                    return redirect('/')->with('error', 'Some error.Please try again');
                }
            }
            DB::commit();
            if ($designation_id_old == 'Operator')
                return redirect("application-list-aadhar-update?scheme_id=" . $scheme_id)->with('success', 'Application Updated Successfully')
                    ->with('id',   $row->getBenidAttribute());
            else {
                return redirect('/')->with('success', 'Application Updated Successfully');
            }
        } else {
            $return_msg = $validator->errors()->all();
            //echo "<pre>"; print_r( $return_msg ); exit;           

            return redirect("application-aadhar-update-view?id=" . urlencode($id) . "&scheme_id=" . urlencode($scheme_id))->with('errors', $return_msg)->withInput();
        }
    }

    ///////////////////////////////////////////////////////////////////////

    public function isAadharValid($num)
    {
        settype($num, "string");
        $expectedDigit = substr($num, -1);
        $actualDigit = $this->CheckSumAadharDigit(substr($num, 0, -1));
        return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
    }

    function CheckSumAadharDigit($partial)
    {
        $dihedral = array(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
            array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
            array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
            array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
            array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
            array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
            array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
            array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
            array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
            array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
        );
        $permutation = array(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
            array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
            array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
            array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
            array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
            array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
            array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
            array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
        );

        $inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
        settype($partial, "string");
        $partial = strrev($partial);
        $digitIndex = 0;
        for ($i = 0; $i < strlen($partial); $i++) {
            $digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
        }
        return $inverse[$digitIndex];
    }


    //####################### Verifier #########################################   


    public function shemeSelectionAadharUpdateVerifier(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if ($designation_id_old != 'Verifier') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $query = "select d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name from        public.duty_assignement d 
                    join m_scheme s on d.scheme_id = s.id 
                    join users u on u.id = d.user_id 
                    where d.user_id =" . $user_id . " order by scheme_id";
        $verifierObj = DB::connection('pgsql_mis')->select($query);
        if (count($verifierObj) > 0) {
            return view('portal/scheme_aadhar_update_verifier', ['user_id' => $user_id, 'designation_id_old' => $designation_id_old, 'scheme_id' => $scheme_id, 'verifierObj' => $verifierObj]);
        } else {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
    }

    public function aadharUpdateListVerifer(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if (!is_int($scheme_id)) {

            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }

        if ($designation_id_old != 'Verifier') {

            return redirect("/")->with('error', 'Not Allowed');
        }

        if ($scheme_id) {
            if ($scheme_id == 13) {
                $scheme_id = 13;
                $model_name = 'App\\PensionOAPFarmer';
            } else if ($scheme_id == 3) {
                $scheme_id = 3;
                $model_name = 'App\\PensionSc';
            } else if ($scheme_id == 1) {
                $scheme_id = 1;
                $model_name = 'App\\PensionSt';
            } else if ($scheme_id == 5) {
                $scheme_id = 5;
                $model_name = 'App\\PensionFisherman';
            } else if ($scheme_id == 6) {
                $scheme_id = 6;
                $model_name = 'App\\PensionMSME';
            } else if ($scheme_id == 7) {
                $scheme_id = 7;
                $model_name = 'App\\PensionTextile';
            } else if ($scheme_id == 2) {

                $model_name = 'App\\PensionManabikWCD';
            } else if ($scheme_id == 10) {
                $model_name = 'App\\PensionOAPWCD';
            } else if ($scheme_id == 11) {
                $model_name = 'App\\PensionWPWCD';
            } else if ($scheme_id == 17) {

                $model_name =  'App\\PensionPurohitMonthlyICAD';
            } else if ($scheme_id == 18) {

                $model_name = 'App\\PensionPurohitHousingICAD';
            } else {
                return redirect("/")->with('success', 'User Disabled');
            }
            $is_active = 0;
            $roleArray = $request->session()->get('role');
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $distCode = $roleObj['district_code'];
                    $is_urban = $roleObj['is_urban'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($is_active == 0) {
                return redirect("/")->with('success', 'User Disabled');
            }
            $rows = $model_name::where(['created_by_local_body_code' => $blockCode, 'aadhar_edit_role_id' => 1])->where(function ($query) use ($user_id) {
            })->orderBy('id', 'desc')
                ->get();
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }

        return view('aadhar-update/pension_list_aadhar_update_verifier', ['nhm_employee_details' => $rows, 'scheme_id' => $scheme_id, 'list_type' => '0']);
    }

    public function showAadharApplicantDetails(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        $id = $request->id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        if ($designation_id_old != 'Verifier') {
            return redirect("/")->with('error', 'Not Allowed');
        }

        if ($scheme_id == 13) {
            // $row = PensionOAPFarmer::find($id);
            $modelName = 'App\\PensionOAPFarmer';
            $model_ben_docs = 'App\\BenDocsOAPFarmer';
            $docs = PensionOAPFarmer::where('ben_id', $id)->get();
        } else if ($scheme_id == 3) {
            //$row = PensionSc::find($id); 
            $modelName = 'App\\PensionSc';
            $model_ben_docs = 'App\\BenDocsSc';
            $docs = BenDocsSc::where('ben_id', $id)->get();
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_ben_docs = 'App\\BenDocsSt';
            $modelName = 'App\\PensionSt';
            $docs = BenDocsSt::where('ben_id', $id)->get();
        } else if ($scheme_id == 4) {
            //$row = Manabik::find($id);          
            $docs = BenDocsPrachesta::where('ben_id', $id)->get();
        } else if ($scheme_id == 5) {
            //$row = Manabik::find($id);   
            $modelName = 'App\\PensionFisherman';
            $model_ben_docs = 'App\\BenDocsFisherman';
            $docs = BenDocsFisherman::where('ben_id', $id)->get();
        } else if ($scheme_id == 6) {
            //$row = Manabik::find($id);  
            $modelName = 'App\\PensionMSME';
            $model_ben_docs = 'App\\BenDocsMSME';
            $docs = BenDocsMSME::where('ben_id', $id)->get();
        } else if ($scheme_id == 7) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionTextile';
            $model_ben_docs = 'App\\BenDocsTextile';
            $docs = BenDocsTextile::where('ben_id', $id)->get();
        } else if ($scheme_id == 2) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionManabikWCD';
            $model_ben_docs = 'App\\BenDocsManabikWCD';
            $docs = BenDocsManabikWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 10) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionOAPWCD';
            $model_ben_docs = 'App\\BenDocsOAPWCD';
            $docs = BenDocsOAPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 11) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionWPWCD';
            $model_ben_docs = 'App\\BenDocsWPWCD';
            $docs = BenDocsWPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 17) {
            $modelName =  'App\\PensionPurohitMonthlyICAD';
            $model_ben_docs = 'App\\BenDocsPurohitMonthlyICAD';
            $docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();
        } else if ($scheme_id == 18) {
            $modelName =  'App\\PensionPurohitHousingICAD';
            $model_ben_docs = 'App\\BenDocsPurohitHousingICAD';
            $docs = BenDocsPurohitHousingICAD::where('ben_id', $id)->get();
        }

        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mappingLevel = $roleObj['mapping_level'];
                $district_code = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                    $taluka_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled');
        }

        $approveBtnvisible = 1;

        $id = $request->id;
        //$appPrefix = "App";
        //$modelName = $appPrefix . "\\" . $ben_table;
        $row = $modelName::where('id', '=', $id)->first();
        $aadhar_doc = [];
        $aadhar_doc =  $model_ben_docs::where('ben_id', '=', $id)->where('doc_type_id', '=', 6)->orderby('id', 'desc')->first();
        $housingrecord = '';


        if ($row->dist_code != "") {
            $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
            $district_name = $district->district_name;
        }
        $block_name = "";
        if ($row->block_ulb_code != "") {
            if ($row->rural_urban_id == 1) {
                $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                $block_name = $block->urban_body_name;
            } else {
                $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
                //$block_name = '';
                $block_name = $block->block_name;
            }
        }
        $gp_name = "";
        if ($row->gp_ward_code != "") {
            if ($row->rural_urban_id == 1) {
                $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                $gp_name =  $gp_ward->urban_body_ward_name;
            } else {
                $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                $gp_name =  $gp->gram_panchyat_name;
            }
        }
        $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
        $doc_profile_image_id = 999;
        if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
        }
        return view('aadhar-update/pension_view_details_verify', [
            'approveBtnvisible' => $approveBtnvisible,
            'scheme_capacity_arr' => array(), 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id, 'aadhar_doc' => $aadhar_doc
        ]);
    }

    public function aadharVerifyData(Request $request)
    {

        $scheme_id = $request->scheme_id;
        $user_id = AuthChecker::getUserId();
        $id = $request->benId;

        if ($scheme_id == 13) {
            // $row = PensionOAPFarmer::find($id);
            $modelName = 'App\\PensionOAPFarmer';
            $model_ben_docs = 'App\\BenDocsOAPFarmer';
            //$docs = PensionOAPFarmer::where('ben_id', $id)->get();
        } else if ($scheme_id == 3) {
            //$row = PensionSc::find($id); 
            $modelName = 'App\\PensionSc';
            $model_ben_docs = 'App\\BenDocsSc';
            // $docs = BenDocsSc::where('ben_id', $id)->get();
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_ben_docs = 'App\\BenDocsSt';
            $modelName = 'App\\PensionSt';
            // $docs = BenDocsSt::where('ben_id', $id)->get();
        } else if ($scheme_id == 4) {
            //$row = Manabik::find($id);          
            //$docs = BenDocsPrachesta::where('ben_id', $id)->get();
        } else if ($scheme_id == 5) {
            //$row = Manabik::find($id);   
            $modelName = 'App\\PensionFisherman';
            $model_ben_docs = 'App\\BenDocsFisherman';
            //$docs = BenDocsFisherman::where('ben_id', $id)->get();
        } else if ($scheme_id == 6) {
            //$row = Manabik::find($id);  
            $modelName = 'App\\PensionMSME';
            $model_ben_docs = 'App\\BenDocsMSME';
            //$docs = BenDocsMSME::where('ben_id', $id)->get();
        } else if ($scheme_id == 7) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionTextile';
            $model_ben_docs = 'App\\BenDocsTextile';
            //$docs = BenDocsTextile::where('ben_id', $id)->get();
        } else if ($scheme_id == 2) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionManabikWCD';
            $model_ben_docs = 'App\\BenDocsManabikWCD';
            //$docs = BenDocsManabikWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 10) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionOAPWCD';
            $model_ben_docs = 'App\\BenDocsOAPWCD';
            //$docs = BenDocsOAPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 11) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionWPWCD';
            $model_ben_docs = 'App\\BenDocsWPWCD';
            //$docs = BenDocsWPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 17) {
            $modelName =  'App\\PensionPurohitMonthlyICAD';
            $model_ben_docs = 'App\\BenDocsPurohitMonthlyICAD';
            //$docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();            
        } else if ($scheme_id == 18) {
            $modelName =  'App\\PensionPurohitHousingICAD';
            $model_ben_docs = 'App\\BenDocsPurohitHousingICAD';
            //$docs = BenDocsPurohitHousingICAD::where('ben_id', $id)->get();
        }

        $comments = $request->comments;


        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();

        $role = MapLavel::where(column: 'scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty->mapping_level)->first();

        if ($_POST['submit'] == 'Verify') {
            $input = ['aadhar_edit_role_id' => 2, 'aadhar_edit_comments' => $comments];

            $is_status_updated = $modelName::where('id', $id)->update($input);
            if ($is_status_updated) {

                return redirect()->intended("aadhar-update-list-verifier?scheme_id=" . urlencode($scheme_id))->with('message', 'Forwarded Succesfully!');
            }
        } else if ($_POST['submit'] == 'Revert') {
            $input = ['aadhar_no' => null, 'aadhar_edit_role_id' => -6, 'aadhar_edit_comments' => $comments];

            $is_revert = $modelName::where('id', $id)->update($input);

            if ($is_revert) {
                return redirect()->intended("aadhar-update-list-verifier?scheme_id=" . urlencode($scheme_id))->with('message', 'Reverted Succesfully!');
            }
        } else if ($_POST['submit'] == 'Reject') {
            $input = [
                'aadhar_no' => null, 'aadhar_edit_role_id' => -4, 'aadhar_edit_comments' => $comments,
            ];

            $is_status_updated = $modelName::where('id', $id)->update($input);

            if ($is_status_updated) {

                return redirect()->intended("aadhar-update-list-verifier?scheme_id=" . urlencode($scheme_id))->with('message', 'Rejected Succesfully!');
            }
        }
    }


    //#####################  Approver ############################

    public function shemeSelectionAadharUpdateApprover(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if ($designation_id_old != 'Approver') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $query = "select d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name from        public.duty_assignement d 
                    join m_scheme s on d.scheme_id = s.id 
                    join users u on u.id = d.user_id 
                    where d.user_id =" . $user_id . " order by scheme_id";
        $approverObj = DB::connection('pgsql_mis')->select($query);
        if (count($approverObj) > 0) {
            return view('portal/scheme_aadhar_update_approver', ['user_id' => $user_id, 'designation_id_old' => $designation_id_old, 'scheme_id' => $scheme_id, 'approverObj' => $approverObj]);
        } else {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        // return view('portal/scheme_aadhar_update_approver');
    }

    public function aadharUpdateListApprover(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        //dd($designation_id_old);
        if ($designation_id_old != 'Approver') {
            return redirect("/")->with('error', 'Not Allowed');
        }

        if ($scheme_id) {
            if ($scheme_id == 13) {
                $scheme_id = 13;
                $model_name = 'App\\PensionOAPFarmer';
            } else if ($scheme_id == 3) {
                $scheme_id = 3;
                $model_name = 'App\\PensionSc';
            } else if ($scheme_id == 1) {
                $scheme_id = 1;
                $model_name = 'App\\PensionSt';
            } else if ($scheme_id == 5) {
                $scheme_id = 5;
                $model_name = 'App\\PensionFisherman';
            } else if ($scheme_id == 6) {
                $scheme_id = 6;
                $model_name = 'App\\PensionMSME';
            } else if ($scheme_id == 7) {
                $scheme_id = 7;
                $model_name = 'App\\PensionTextile';
            } else if ($scheme_id == 2) {

                $model_name = 'App\\PensionManabikWCD';
            } else if ($scheme_id == 10) {
                $model_name = 'App\\PensionOAPWCD';
            } else if ($scheme_id == 11) {
                $model_name = 'App\\PensionWPWCD';
            } else if ($scheme_id == 17) {

                $model_name =  'App\\PensionPurohitMonthlyICAD';
            } else if ($scheme_id == 18) {

                $model_name = 'App\\PensionPurohitHousingICAD';
            } else {
                return redirect("/")->with('success', 'User Disabled');
            }
            $is_active = 0;
            $roleArray = $request->session()->get('role');
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $distCode = $roleObj['district_code'];
                    $is_urban = $roleObj['is_urban'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($is_active == 0) {
                return redirect("/")->with('success', 'User Disabled');
            }
            $rows = $model_name::where(['created_by_dist_code' => $distCode, 'aadhar_edit_role_id' => 2])->where(function ($query) use ($user_id) {
            })->orderBy('id', 'desc')
                ->get();
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }

        return view('aadhar-update/pension_list_aadhar_update_approver', ['nhm_employee_details' => $rows, 'scheme_id' => $scheme_id, 'list_type' => '0']);
    }

    public function showAadharApplicantDetailsApprover(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        $id = $request->id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        //dd($designation_id_old);
        if ($designation_id_old != 'Approver') {
            return redirect("/")->with('error', 'Not Allowed');
        }

        if ($scheme_id == 13) {
            // $row = PensionOAPFarmer::find($id);
            $modelName = 'App\\PensionOAPFarmer';
            $model_ben_docs = 'App\\BenDocsOAPFarmer';
            $docs = PensionOAPFarmer::where('ben_id', $id)->get();
        } else if ($scheme_id == 3) {
            //$row = PensionSc::find($id); 
            $modelName = 'App\\PensionSc';
            $model_ben_docs = 'App\\BenDocsSc';
            $docs = BenDocsSc::where('ben_id', $id)->get();
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_ben_docs = 'App\\BenDocsSt';
            $modelName = 'App\\PensionSt';
            $docs = BenDocsSt::where('ben_id', $id)->get();
        } else if ($scheme_id == 4) {
            //$row = Manabik::find($id);          
            $docs = BenDocsPrachesta::where('ben_id', $id)->get();
        } else if ($scheme_id == 5) {
            //$row = Manabik::find($id);   
            $modelName = 'App\\PensionFisherman';
            $model_ben_docs = 'App\\BenDocsFisherman';
            $docs = BenDocsFisherman::where('ben_id', $id)->get();
        } else if ($scheme_id == 6) {
            //$row = Manabik::find($id);  
            $modelName = 'App\\PensionMSME';
            $model_ben_docs = 'App\\BenDocsMSME';
            $docs = BenDocsMSME::where('ben_id', $id)->get();
        } else if ($scheme_id == 7) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionTextile';
            $model_ben_docs = 'App\\BenDocsTextile';
            $docs = BenDocsTextile::where('ben_id', $id)->get();
        } else if ($scheme_id == 2) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionManabikWCD';
            $model_ben_docs = 'App\\BenDocsManabikWCD';
            $docs = BenDocsManabikWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 10) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionOAPWCD';
            $model_ben_docs = 'App\\BenDocsOAPWCD';
            $docs = BenDocsOAPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 11) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionWPWCD';
            $model_ben_docs = 'App\\BenDocsWPWCD';
            $docs = BenDocsWPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 17) {
            $modelName =  'App\\PensionPurohitMonthlyICAD';
            $model_ben_docs = 'App\\BenDocsPurohitMonthlyICAD';
            $docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();
        } else if ($scheme_id == 18) {
            $modelName =  'App\\PensionPurohitHousingICAD';
            $model_ben_docs = 'App\\BenDocsPurohitHousingICAD';
            $docs = BenDocsPurohitHousingICAD::where('ben_id', $id)->get();
        }

        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mappingLevel = $roleObj['mapping_level'];
                $district_code = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                    $taluka_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled');
        }

        $approveBtnvisible = 1;

        $id = $request->id;
        //$appPrefix = "App";
        //$modelName = $appPrefix . "\\" . $ben_table;
        $row = $modelName::where('id', '=', $id)->first();
        $aadhar_doc = [];
        $aadhar_doc =  $model_ben_docs::where('ben_id', '=', $id)->where('doc_type_id', '=', 6)->orderby('id', 'desc')->first();

        $housingrecord = '';


        if ($row->dist_code != "") {
            $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
            $district_name = $district->district_name;
        }
        $block_name = "";
        if ($row->block_ulb_code != "") {
            if ($row->rural_urban_id == 1) {
                $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                $block_name = $block->urban_body_name;
            } else {
                $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();

                //$block_name ="";
                $block_name = $block->block_name;
            }
        }
        $gp_name = "";
        if ($row->gp_ward_code != "") {
            if ($row->rural_urban_id == 1) {
                $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                $gp_name =  $gp_ward->urban_body_ward_name;
            } else {
                $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                $gp_name =  $gp->gram_panchyat_name;
            }
        }

        return view('aadhar-update/pension_view_details_approve', [
            'approveBtnvisible' => $approveBtnvisible,
            'scheme_capacity_arr' => array(), 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => '', 'aadhar_doc' => $aadhar_doc
        ]);
    }

    public function aadharApproveData(Request $request)
    {

        $scheme_id = $request->scheme_id;
        $user_id = AuthChecker::getUserId();
        $id = $request->benId;

        if ($scheme_id == 13) {
            // $row = PensionOAPFarmer::find($id);
            $modelName = 'App\\PensionOAPFarmer';
            $model_ben_docs = 'App\\BenDocsOAPFarmer';
            //$docs = PensionOAPFarmer::where('ben_id', $id)->get();
        } else if ($scheme_id == 3) {
            //$row = PensionSc::find($id); 
            $modelName = 'App\\PensionSc';
            $model_ben_docs = 'App\\BenDocsSc';
            // $docs = BenDocsSc::where('ben_id', $id)->get();
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_ben_docs = 'App\\BenDocsSt';
            $modelName = 'App\\PensionSt';
            // $docs = BenDocsSt::where('ben_id', $id)->get();
        } else if ($scheme_id == 4) {
            //$row = Manabik::find($id);          
            //$docs = BenDocsPrachesta::where('ben_id', $id)->get();
        } else if ($scheme_id == 5) {
            //$row = Manabik::find($id);   
            $modelName = 'App\\PensionFisherman';
            $model_ben_docs = 'App\\BenDocsFisherman';
            //$docs = BenDocsFisherman::where('ben_id', $id)->get();
        } else if ($scheme_id == 6) {
            //$row = Manabik::find($id);  
            $modelName = 'App\\PensionMSME';
            $model_ben_docs = 'App\\BenDocsMSME';
            //$docs = BenDocsMSME::where('ben_id', $id)->get();
        } else if ($scheme_id == 7) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionTextile';
            $model_ben_docs = 'App\\BenDocsTextile';
            //$docs = BenDocsTextile::where('ben_id', $id)->get();
        } else if ($scheme_id == 2) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionManabikWCD';
            $model_ben_docs = 'App\\BenDocsManabikWCD';
            //$docs = BenDocsManabikWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 10) {
            //$row = Manabik::find($id);
            $modelName = 'App\\PensionOAPWCD';
            $model_ben_docs = 'App\\BenDocsOAPWCD';
            //$docs = BenDocsOAPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 11) {
            //$row = Manabik::find($id); 
            $modelName = 'App\\PensionWPWCD';
            $model_ben_docs = 'App\\BenDocsWPWCD';
            //$docs = BenDocsWPWCD::where('ben_id', $id)->get();
        } else if ($scheme_id == 17) {
            $modelName =  'App\\PensionPurohitMonthlyICAD';
            $model_ben_docs = 'App\\BenDocsPurohitMonthlyICAD';
            //$docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();            
        } else if ($scheme_id == 18) {
            $modelName =  'App\\PensionPurohitHousingICAD';
            $model_ben_docs = 'App\\BenDocsPurohitHousingICAD';
            //$docs = BenDocsPurohitHousingICAD::where('ben_id', $id)->get();
        }



        $comments = $request->comments;

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();

        //dd($duty);
        $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty->mapping_level)->first();

        if ($_POST['submit'] == 'Approve') {

            $input = ['aadhar_edit_role_id' => 3, 'aadhar_edit_comments' => $comments];

            $is_status_updated = $modelName::where('id', $id)->update($input);
            if ($is_status_updated) {

                return redirect()->intended("aadhar-update-list-approver?scheme_id=" . urlencode($scheme_id))->with('message', 'Approved Succesfully!');
            }
        } else if ($_POST['submit'] == 'Revert') {
            $input = [
                'aadhar_no' => null, 'aadhar_edit_role_id' => -7, 'aadhar_edit_comments' => $comments,
            ];
            $is_status_revet = $modelName::where('id', $id)->update($input);
            if ($is_status_revet) {
                return redirect()->intended("aadhar-update-list-approver?scheme_id=" . urlencode($scheme_id))->with('message', 'Reverted Succesfully!');
            }
        } else if ($_POST['submit'] == 'Reject') {

            $input = [
                'aadhar_no' => null, 'aadhar_edit_role_id' => -5, 'aadhar_edit_comments' => $comments,
            ];
            //echo $modelName; exit;
            $is_status_updated = $modelName::where('id', $id)->update($input);
            //->update($input);
            if ($is_status_updated) {

                return redirect()->intended("aadhar-update-list-approver?scheme_id=" . urlencode($scheme_id))->with('message', 'Rejected Succesfully!');
            }
        }
    }
    public function countListIndex(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        if ($designation_id_old != 'Approver') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $query = "select d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name from        public.duty_assignement d 
                    join m_scheme s on d.scheme_id = s.id 
                    join users u on u.id = d.user_id 
                    where d.user_id =" . $user_id;
        $userObj = DB::connection('pgsql_mis')->select($query);
        if (count($userObj) > 0) {
            return view('aadhar-update/count_list_aadhar_update_approver', ['user_id' => $user_id, 'designation_id_old' => $designation_id_old, 'scheme_id' => $scheme_id, 'userObj' => $userObj]);
        } else {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        // return view('aadhar-update/count_list_aadhar_update_approver');
    }
    public function aadharUpdateCountListApprover(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $rural_urban = $request->rural_urban;
        // echo $rural_urban;die;
        $user_id = AuthChecker::getUserId();
        $distObj = Configduty::where('user_id', $user_id)->first();
        // echo $scheme_id; 
        // echo $user_id; 
        // echo $dist_code->district_code; exit;
        if ($request->ajax()) {
            if ($rural_urban == 'urban') {
                $query = "select d.sub_district_name as bsm,d.sub_district_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_sub_district d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $distObj->district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.sub_district_code
                    where d.district_code = " . $distObj->district_code . "
                    group by d.sub_district_name,d.sub_district_code
                    order by d.sub_district_name";
            } elseif ($rural_urban == 'rural') {
                $query = "select d.block_name as bsm,d.block_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_block d left join(
                        select s.scheme_name,b.created_by_local_body_code, count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $distObj->district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.block_code
                    where d.district_code = " . $distObj->district_code . "
                    group by d.block_name,d.block_code
                    order by d.block_name";
            } else {
                $query = "select p.bsm,p.bsm_code,
                    sum(coalesce(p.applied,0)) as applied,
                    sum(coalesce(p.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(p.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(p.edited,0)) as edited,
                    sum(coalesce(p.verified,0)) as verified,
                    sum(coalesce(p.approved,0)) as approved
                    from (
                    select d.block_name as bsm,d.block_code as bsm_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_block d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $distObj->district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.block_code
                    where d.district_code = " . $distObj->district_code . "
                    group by d.block_name,d.block_code

                    Union all

                    select d.sub_district_name,d.sub_district_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_sub_district d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $distObj->district_code . " and b.next_level_role_id = 0 
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.sub_district_code
                    where d.district_code = " . $distObj->district_code . "
                    group by d.sub_district_name,d.sub_district_code
                        )p 
                        group by p.bsm,p.bsm_code
                        order by p.bsm";
            }
            // echo $query;die();
            $updateAadhar = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($updateAadhar)
                // ->addIndexColumn()
                ->addColumn('total_blank_aadhar_count', function ($updateAadhar) {
                    $blank_aadhar = $updateAadhar->blank_aadhar_count;
                    $aadhar_no = $updateAadhar->aadhar_no_less_than_12;
                    $total = $blank_aadhar + $aadhar_no;
                    return $total;
                })
                ->rawColumns(['blank_aadhar_count'])
                ->make(true);
        } else {
            return view('aadhar-update/count_list_aadhar_update_approver');
        }
    }
    public function countListIndexforHod(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_id = (int) $request->scheme_id;
        $district_name = District::get();
        if ($designation_id_old != 'HOD') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $query = "select d.user_id,u.designation_id_old,d.scheme_id,s.scheme_name from        public.duty_assignement d 
                    join m_scheme s on d.scheme_id = s.id 
                    join users u on u.id = d.user_id 
                    where d.user_id =" . $user_id;
        $userObj = DB::connection('pgsql_mis')->select($query);
        if (count($userObj) > 0) {
            return view('aadhar-update/count_list_aadhar_update_hod', ['user_id' => $user_id, 'designation_id_old' => $designation_id_old, 'scheme_id' => $scheme_id, 'userObj' => $userObj, 'district_name' => $district_name]);
        } else {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
    }
    public function aadharUpdateCountListHod(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district_code = $request->district_code;
        $rural_urban = $request->rural_urban;
        $user_id = AuthChecker::getUserId();
        $distObj = Configduty::where('user_id', $user_id)->first();
        // echo $scheme_id; 
        // echo $user_id; 
        // echo $dist_code->district_code; exit;
        if ($request->ajax()) {
            if ($rural_urban == 'rural') {
                $query = "select d.block_name as bsm,d.block_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_block d left join(
                        select s.scheme_name,b.created_by_local_body_code, count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.block_code
                    where d.district_code = " . $district_code . "
                    group by d.block_name,d.block_code
                    order by d.block_name";
            } elseif ($rural_urban == 'urban') {
                $query = "select d.sub_district_name as bsm,d.sub_district_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_sub_district d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.sub_district_code
                    where d.district_code = " . $district_code . "
                    group by d.sub_district_name,d.sub_district_code
                    order by d.sub_district_name";
            } elseif ($rural_urban == 'all') {
                $query = "select p.bsm,p.bsm_code,
                    sum(coalesce(p.applied,0)) as applied,
                    sum(coalesce(p.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(p.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(p.edited,0)) as edited,
                    sum(coalesce(p.verified,0)) as verified,
                    sum(coalesce(p.approved,0)) as approved
                    from (
                    select d.block_name as bsm,d.block_code as bsm_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_block d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $district_code . " and b.next_level_role_id = 0
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.block_code
                    where d.district_code = " . $district_code . "
                    group by d.block_name,d.block_code

                    Union all

                    select d.sub_district_name,d.sub_district_code,
                    sum(coalesce(b.applied,0)) as applied,
                    sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                    sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                    sum(coalesce(b.edited,0)) as edited,
                    sum(coalesce(b.verified,0)) as verified,
                    sum(coalesce(b.approved,0)) as approved
                    from public.m_sub_district d left join(
                        select s.scheme_name,b.created_by_local_body_code, 
                        count(b.id) applied,
                        sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                        sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                        sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                        sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                        sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                        from pension.beneficiaries b 
                        JOIN m_scheme s ON s.id=b.scheme_id
                        where b.scheme_id = " . $scheme_id . " and b.created_by_dist_code = " . $district_code . " and b.next_level_role_id = 0 
                        group by s.scheme_name,b.created_by_local_body_code
                    )b on b.created_by_local_body_code = d.sub_district_code
                    where d.district_code = " . $district_code . "
                    group by d.sub_district_name,d.sub_district_code
                        )p 
                        group by p.bsm,p.bsm_code
                        order by p.bsm";
            } elseif (!empty($district_code)) {
                $query = "select d.district_name as bsm,d.district_code,
                sum(coalesce(b.applied,0)) as applied,
                sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                sum(coalesce(b.edited,0)) as edited,
                sum(coalesce(b.verified,0)) as verified,
                sum(coalesce(b.approved,0)) as approved
                from m_district d left join(
                    select s.scheme_name,b.scheme_id,b.dist_code, count(b.id) applied,
                    sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                    sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                    sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                    sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                    sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                    from pension.beneficiaries b 
                    JOIN m_scheme s ON s.id=b.scheme_id
                    where b.scheme_id = " . $scheme_id . " and b.next_level_role_id = 0
                    group by s.scheme_name,b.scheme_id,b.dist_code
                )b on b.dist_code = d.district_code
                where d.district_code = " . $district_code . "
                group by d.district_name,d.district_code
                order by d.district_name";
            } else {
                $query = "select d.district_name as bsm,d.district_code,
                sum(coalesce(b.applied,0)) as applied,
                sum(coalesce(b.blank_aadhar_count,0)) as blank_aadhar_count,
                sum(coalesce(b.aadhar_no_less_than_12,0)) as aadhar_no_less_than_12,
                sum(coalesce(b.edited,0)) as edited,
                sum(coalesce(b.verified,0)) as verified,
                sum(coalesce(b.approved,0)) as approved
                from m_district d left join(
                    select s.scheme_name,b.scheme_id,b.dist_code, count(b.id) applied,
                    sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
                    sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
                    sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
                    sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
                    sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved
                    from pension.beneficiaries b 
                    JOIN m_scheme s ON s.id=b.scheme_id
                    where b.scheme_id = " . $scheme_id . " and b.next_level_role_id = 0
                    group by s.scheme_name,b.scheme_id,b.dist_code
                )b on b.dist_code = d.district_code
                group by d.district_name,d.district_code
                order by d.district_name";
            }

            // echo $query;die();
            $updateAadhar = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($updateAadhar)
                ->addIndexColumn()
                ->addColumn('total_blank_aadhar_count', function ($updateAadhar) {
                    $blank_aadhar = $updateAadhar->blank_aadhar_count;
                    $aadhar_no = $updateAadhar->aadhar_no_less_than_12;
                    $total = $blank_aadhar + $aadhar_no;
                    return $total;
                })
                ->rawColumns(['total_blank_aadhar_count'])
                ->make(true);
        } else {
            return view('aadhar-update/count_list_aadhar_update_hod');
        }
    }

    private function validateInput($request, $scheme_id)
    {

        $this->validate($request, [
            //'first_name' => 'required|string|max:200',
            'aadhar_no' => 'required|string|max:200',
            'doc_6' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        ]);

        // echo 12444422; exit;
    }
    /**********************************************************/
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name = $sObj->short_code;
            // dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name = strtolower($schema_name) . '.ben_docs';
        } else {
            $table_name =  'pension.ben_docs';
        }
        // dd($table_name);
        return $table_name;
    }
}
