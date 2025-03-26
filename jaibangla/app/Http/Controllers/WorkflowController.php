<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Configduty;
use App\MapLavel;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

//Dynamic Doc
use App\BenDocsSc;
use App\BenDocsSt;
use App\BenDocsFisherman;
use App\BenDocsMSME;
use App\BenDocsTextile;

use App\BenDocsManabikWCD;
use App\BenDocsOAPWCD;
use App\BenDocsWPWCD;
use App\PensionOAPFarmer;

use App\BenDocsPurohitMonthlyICAD;
use App\PensionPurohitHousingICAD;

use App\DocumentType;
use App\SchemecodeStatic;
use App\Helpers\Helper;
use App\SubDistrict;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\BlkUrbanlEntryMapping;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Scheme;
use App\BenDocs;

use App\Helpers\AuthChecker;
class WorkflowController extends Controller
{
  protected $monthlySlug;
  protected $monthlySchemeCode;
  protected $monthlyMainTable;
  protected $monthlyMainTable1;
  protected $monthlyDocArchTable;
  protected $monthlyDocTable;
  protected $housingSlug;
  protected $housingSchemeCode;
  protected $housingMainTable;
  protected $housingMainTable1;
  protected $housingDocArchTable;
  protected $housingDocTable;
  protected $state_login_next_level_role_id_arr;
  

  public function __construct()
  {
    $this->middleware('auth');
    $arr = SchemecodeStatic::getpr1ListPurohit();
    $this->monthlySlug = $arr['monthly']['slug'];
    $this->monthlySchemeCode = $arr['monthly']['scheme_code'];
    $this->monthlyMainTable1 = $arr['monthly']['maintable'];
    $this->monthlyMainTable = "App\\" . $arr['monthly']['maintable'];
    $this->monthlyDocTable = "App\\" . $arr['monthly']['doctable'];
    $this->monthlyDocArchTable = "App\\" . $arr['monthly']['docarchtable'];

    $this->housingSlug = $arr['housing']['slug'];
    $this->housingSchemeCode = $arr['housing']['scheme_code'];
    $this->housingMainTable = "App\\" . $arr['housing']['maintable'];
    $this->housingMainTable1 = $arr['housing']['maintable'];
    $this->housingDocTable = "App\\" . $arr['housing']['doctable'];
    $this->housingDocArchTable = "App\\" . $arr['housing']['docarchtable'];
    $this->state_login_next_level_role_id_arr = Config::get('constants.state_login_next_level_role_id');
  }
  public function formEntryOption(Request $request)
  {
    $scheme_not_re = array(4, 12, 14, 15, 16, 18, 19);
    $user_id = AuthChecker::getUserId();
    $auth = AuthChecker::OperatorChecker();
    if (!$auth) {
      return redirect('/')->with('error', 'Not Allowded');
    }
    if ($auth) {
      $district_arr = Configduty::select('district_code')->where('user_id', $user_id)->where('is_active', 1)->first();
      if (empty($district_arr)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      if (empty($district_arr->district_code)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      $district_code = $district_arr->district_code;
      $return_arr = array();
      $schemes_arr_all = Scheme::where('is_active', 1)->orderBy('rank')->get();
      $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
      //dd($schemes);
      $i = 0;
      foreach ($schemes as $scheme_arr) {
        if (in_array($scheme_arr->id, $scheme_not_re)) {
          continue;
        }
        $return_arr[$i]['id'] = $scheme_arr->id;
        $return_arr[$i]['display_name'] = $scheme_arr->display_name;
        $return_arr[$i]['pr1_code'] = $scheme_arr->pr1_code;
        $return_arr[$i]['entry_url'] = $scheme_arr->entry_url;
        $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_arr->id)->where('district_code', $district_code)->first();
        if (empty($allowded_arr)) {
          $return_arr[$i]['active'] = 1;
        } else {
          $main_entry_allowded = intval($allowded_arr->main_entry);
          if ($main_entry_allowded == 0) {
            $return_arr[$i]['active'] = 0;
            continue;
            //$return_arr[$i]['active'] = 0;
          } else
            $return_arr[$i]['active'] = 1;
        }

        $i++;
      }
      // dd($return_arr);
      return view(
        'scheme-selection/entryOption',
        [
          'monthlySlug' => $this->monthlySlug,
          'housingSlug' => $this->housingSlug,
          'return_arr' => $return_arr,
        ]
      );
    }

  }
  public function formEntryOptionwtQuota(Request $request)
  {
    $scheme_not_re = array(4, 12, 14, 15, 16, 18, 19);
    $designation_id = Auth::user()->designation_id;
    $user_id = AuthChecker::getUserId();
    if ($designation_id != 'Operator') {
      return redirect('/')->with('error', 'Not Allowded');
    }
    $district_arr = Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban')->where('user_id', $user_id)->where('is_active', 1)->first();
    if (empty($district_arr)) {
      return redirect("/")->with('danger', 'User Disabled');
    }
    if (empty($district_arr->district_code)) {
      return redirect("/")->with('danger', 'User Disabled');
    }
    $district_code = $district_arr->district_code;
    $is_urban = $district_arr->is_urban;
    if ($is_urban == 1) {
      $block_ulb_code = $district_arr->urban_body_code;
    } else {
      $block_ulb_code = $district_arr->taluka_code;
    }
    $return_arr = array();
    $schemes_arr_all = Scheme::where('is_active', 1)->orderBy('rank')->get();
    $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
    //dd($schemes);
    $i = 0;
    foreach ($schemes as $scheme_arr) {
      if (in_array($scheme_arr->id, $scheme_not_re)) {
        continue;
      }

      $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_arr->id)->where('district_code', $district_code)->where('block_ulb_code', $block_ulb_code)->first();
      if (empty($allowded_arr)) {
        // $return_arr[$i]['active'] = 0;
        continue;
      } else {
        $main_entry_allowded = intval($allowded_arr->special_entry);
        if ($main_entry_allowded == 0) {
          //$return_arr[$i]['active'] = 0;
          continue;
          $return_arr[$i]['active'] = 0;
        } else
          $return_arr[$i]['active'] = 1;
      }
      $return_arr[$i]['display_name'] = $scheme_arr->display_name;
      $return_arr[$i]['pr1_code'] = $scheme_arr->pr1_code;
      $return_arr[$i]['entry_url'] = $scheme_arr->entry_url;
      $i++;
    }
    //dd($return_arr);
    return view(
      'scheme-selection/entryOptionwtQuota',
      [
        'return_arr' => $return_arr,
      ]
    );
  }
  public function shemeSelection(Request $request)
  {
    $scheme_not_re = array(4, 12, 14, 15, 16, 18, 19);
    $designation_id = Auth::user()->designation_id;
    $user_id = AuthChecker::getUserId();
    if ($designation_id == 'Verifier') {

      $district_arr = Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban')->where('user_id', $user_id)->where('is_active', 1)->first();
      if (empty($district_arr)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      if (empty($district_arr->district_code)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      $district_code = $district_arr->district_code;
      if ($district_arr->is_urban == 1) {
        $block_ulb_code = $district_arr->urban_body_code;
      } else {
        $block_ulb_code = $district_arr->taluka_code;
      }
      $return_arr = array();
      $schemes_arr_all = Scheme::where('is_active', 1)->orderBy('rank')->get();
      $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,verification_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
      // dd($schemes);
      $i = 0;
      foreach ($schemes as $scheme_arr) {
        if (in_array($scheme_arr->id, $scheme_not_re)) {
          continue;
        }
        $return_arr[$i]['display_name'] = $scheme_arr->display_name;
        $return_arr[$i]['pr1_code'] = $scheme_arr->pr1_code;
        $return_arr[$i]['verification_url'] = $scheme_arr->verification_url;
        $return_arr[$i]['id'] = $scheme_arr->id;
        $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_arr->id)->where('district_code', $district_code)->where('block_ulb_code', $block_ulb_code)->whereraw(" (main_verification=TRUE or special_verification=TRUE)")->count();
        if ($allowded_arr_cnt == 0) {
          $return_arr[$i]['active'] = 0;
          continue;
          //$return_arr[$i]['active'] = 0;
        } else
          $return_arr[$i]['active'] = 1;


        $i++;
      }
      //dd($return_arr);
      return view(
        'scheme-selection/VerificationOption',
        [
          'monthlySlug' => $this->monthlySlug,
          'housingSlug' => $this->housingSlug,
          'return_arr' => $return_arr,
        ]
      );
    } else if ($designation_id == 'Approver') {
      $district_arr = Configduty::select('district_code')->where('user_id', $user_id)->where('is_active', 1)->first();
      if (empty($district_arr)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      if (empty($district_arr->district_code)) {
        return redirect("/")->with('danger', 'User Disabled');
      }
      $district_code = $district_arr->district_code;
      $return_arr = array();
      $schemes_arr_all = Scheme::where('is_active', 1)->orderBy('rank')->get();
      $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,approval_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
      //dd($schemes);
      // if($user_id == 3382){
      //  dd($schemes);
      // }
      $i = 0;
      foreach ($schemes as $scheme_arr) {
        if (in_array($scheme_arr->id, $scheme_not_re)) {
          continue;
        }
        $return_arr[$i]['display_name'] = $scheme_arr->display_name;
        $return_arr[$i]['scheme_id'] = $scheme_arr->id;
        $return_arr[$i]['pr1_code'] = $scheme_arr->pr1_code;
        $return_arr[$i]['approval_url'] = $scheme_arr->approval_url;
        $allowded_arr_count = BlkUrbanlEntryMapping::where('scheme_id', $scheme_arr->id)->where('district_code', $district_code)->whereraw(" (main_approval=TRUE or special_approval=TRUE)")->count();

        if ($allowded_arr_count == 0) {
          unset($return_arr[$i]['display_name']);
          unset($return_arr[$i]['scheme_id']);
          unset($return_arr[$i]['pr1_code']);
          unset($return_arr[$i]['approval_url']);
          unset($return_arr[$i]);
          continue;
          $return_arr[$i]['active'] = 0;
        } else
          $return_arr[$i]['active'] = 1;


        $i++;
      }
      $oap_duty_check = Configduty::where('scheme_id', 10)->where('user_id', $user_id)->where('is_active', 1)->count();
      if ($oap_duty_check > 0) {

        $schemes_arr_oap = Scheme::where('is_active', 1)->where('id', 10)->first();
        $i = $i + 1;
        $return_arr[$i]['active'] = 1;
        $return_arr[$i]['display_name'] = $schemes_arr_oap->display_name;
        $return_arr[$i]['scheme_id'] = $schemes_arr_oap->id;
        $return_arr[$i]['pr1_code'] = $schemes_arr_oap->pr1_code;
        $return_arr[$i]['approval_url'] = 'oap-wcd-verified-rejection';
        // dd($return_arr);
      }
      //  if($user_id == 3382){
      //   dd($return_arr);
      //  }
      // dd($return_arr);
      return view(
        'scheme-selection/ApproverOption',
        [
          'monthlySlug' => $this->monthlySlug,
          'housingSlug' => $this->housingSlug,
          'return_arr' => $return_arr,
        ]
      );
    }
    //return view('scheme-selection/main', ['monthlySlug' => $this->monthlySlug, 'housingSlug' => $this->housingSlug]);
  }

  public function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj = Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name = $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name = strtolower($schema_name) . '.beneficiaries';
    } else {
      // $table_name =  'pension.beneficiary';
      $table_name = '';
    }
    return $table_name;
  }

  public function shemeSessionCheck(Request $request)
  {
    $user_id = AuthChecker::getUserId();

    $scheme_id = 0;
    $ben_table = "";
    if ($request->get('pr1')) {
      if ($request->get('pr1') == "farmers") {
        $scheme_id = 13;
        $ben_table = "PensionOAPFarmer";
      } else if ($request->get('pr1') == "sc") {
        $scheme_id = 3;
        $ben_table = "PensionSc";
      } else if ($request->get('pr1') == "st") {
        $scheme_id = 1;
        $ben_table = "PensionSt";
      } else if ($request->get('pr1') == "prachesta") {
        $scheme_id = 4;
        $ben_table = "Prachesta";
      } else if ($request->get('pr1') == "fisherman") {
        $scheme_id = 5;
        $ben_table = "PensionFisherman";
      } else if ($request->get('pr1') == "msme") {
        $scheme_id = 6;
        $ben_table = "PensionMSME";
      } else if ($request->get('pr1') == "textile") {
        $scheme_id = 7;
        $ben_table = "PensionTextile";
      } else if ($request->get('pr1') == "wcd") {
        $scheme_id = $request->get('wcd_type');
        if ($scheme_id == 2) {
          $ben_table = "PensionManabikWCD";
        } else if ($scheme_id == 10) {
          $ben_table = "PensionOAPWCD";
        } else if ($scheme_id == 11) {
          $ben_table = "PensionWPWCD";
        }
      }
      // else if($request->get('pr1')==$this->monthlySlug){
      //   $scheme_id=$this->monthlySchemeCode;
      //   $ben_table=$this->monthlyMainTable1;
      // }
      // else if($request->get('pr1')==$this->housingSlug){
      //   $scheme_id=$this->housingSchemeCode;
      //   $ben_table=$this->housingMainTable1;
      // }   
      else if ($request->get('pr1') == 'purohits') {
        $scheme_id = 17;
        $ben_table = "PensionPurohitMonthlyICAD";
      } else {
        return view('scheme-selection/main');
      }
    } else {
      return redirect("scheme-selection/main");
    }

    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $district_code = $roleObj['district_code'];
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $request->session()->put('scheme_id', $scheme_id);
        $request->session()->put('ben_table', $ben_table);
        $request->session()->put('is_first', $roleObj['is_first']);
        $request->session()->put('is_urban', $roleObj['is_urban']);
        $request->session()->put('role_id', $roleObj['id']);
        $request->session()->put('is_state_login', $roleObj['is_state_login']);
        if ($roleObj['is_urban'] == 1) {
          $block_ulb_code = $roleObj['urban_body_code'];
          $request->session()->put('bodyCode', $roleObj['urban_body_code']);
        } else {
          $block_ulb_code = $roleObj['taluka_code'];
          $request->session()->put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
    if ($is_active == 1) {
      if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id == 'Verifier') {
          $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $block_ulb_code)->where('district_code', $district_code)->whereraw(" (main_verification=TRUE or special_verification=TRUE)")->count();
          if ($allowded_arr_cnt == 0) {
            return false;
          }
        }
        if ($designation_id == 'Approver') {
          $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code', $district_code)->whereraw(" (main_approval=TRUE or special_approval=TRUE)")->count();
          if ($allowded_arr_cnt == 0) {
            return false;
          }
        }
      }

      return true;
    } else {
      return false;
    }
    return view('scheme-selection/main');
  }

  public function applicationdetails(Request $request)
  {

    // dd($request->all());
    if ($this->shemeSessionCheck($request)) {
      $designation_id = Auth::user()->designation_id;
      if (in_array($designation_id, array('StatusCheckerDistrict', 'StatusCheckerField'))) {
        return redirect('/')->with('error', 'Not Allowded');
      }
      $scheme_id = $request->session()->get('scheme_id');

      $ben_table = $request->session()->get('ben_table');
      // if ($scheme_id == 2) {
      //   $ben_table = "PensionManabikWCDReport";
      // } else if ($scheme_id == 10) {
      //   $ben_table = "PensionOAPWCDReport";
      // } else if ($scheme_id == 11) {
      //   $ben_table = "PensionWPWCDReport";
      // } else {
      //   $ben_table = $request->session()->get('ben_table');
      // }
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $urban_body_code = $request->session()->get('bodyCode');
      $taluka_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');
      $is_state_login = $request->session()->get('is_state_login');

      // dd(session());
      $user_id = AuthChecker::getUserId();
      $table_name = 'pension.beneficiaries';

      if ($table_name == '') {
        return redirect('/')->with('error', 'Scheme Not Found...');
      }
      $designation_id = Auth::user()->designation_id;
      if ($designation_id == 'Operator') {
        return redirect('/')->with('error', 'Not Allowded...');
      }

      // dd($table_name);
      if ($is_first) {   // First Level Verifier   	
        if ($mappingLevel == "State") {
          $level = "State";
          $districts = District::select(['district_code', 'district_name'])->get();
          $appPrefix = "App";
          $modelName = $appPrefix . "\\" . $ben_table;
          $levels = [
            2 => 'Rural',
            1 => 'Urban',
          ];
          if (request()->ajax()) {
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $query = DB::table($table_name)->where('next_level_role_id', $this->state_login_next_level_role_id_arr['entry'])
              ->where('is_state', TRUE)
              ->where('scheme_id', $scheme_id);
            if (!empty($request->district_code)) {
              $query = $query->where('created_by_dist_code', $request->district_code);
            }
            if (!empty($request->urban_code)) {
              $query = $query->where('block_ulb_type', $request->urban_code);
            }
            if (!empty($request->block_subdiv_code)) {
              $query = $query->where('created_by_local_body_code', $request->block_subdiv_code);
            }
            $limit = $request->input('length');
            $offset = $request->input('start');
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count('id');
              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id',
                'created_by_local_body_code',
                'created_by_dist_code',
                'dob',
                'assembly_name',
                'bank_code',
                'ben_fname',
                'ben_lname',
                'ben_mname',
                'gender',
                'ben_age',
                'block_ulb_name',
                'gp_ward_name',
                'bank_ifsc',
                'village_town_city',
                'scheme_id',
                'lot_generated',
                'payment_count',
                'next_level_role_id'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                $ben_id = substr($serachvalue, -7);
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id)
                    ->orWhere('bank_code', $serachvalue);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_local_body_code',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'like', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_local_body_code',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                  ]
                );
              }
              $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) {
                if ($data->scheme_id == 17) {
                  $action = '<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                } else
                  $action = '<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';



                return $action;
              })
              ->addColumn('id', function ($data) {
                // return $data->getBenidAttribute();
                return $data->id;
              })
              ->addColumn('name', function ($data) {
                // return $data->getName();
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
              })->addColumn('block_ulb_name', function ($data) {
                return trim($data->block_ulb_name);
              })->addColumn('gp_ward_name', function ($data) {
                return trim($data->gp_ward_name);
              })->addColumn('date_formated', function ($data) {
                return Carbon::parse($data->dob)->format('d/m/y');
              })->addColumn('bank_code', function ($data) {
                return trim($data->bank_code);
              })->rawColumns(['view', 'check', 'id', 'name'])
              ->make(true);
          }
          return view('processApplicationState/linelisting_stateverified', [
            'districts' => $districts,
            'scheme_id' => $scheme_id,
            'levels' => $levels
          ]);
        } else if ($mappingLevel == "District") {
          $appPrefix = "App";
          $modelName = $appPrefix . "\\" . $ben_table;
          $rows = DB::table($table_name)->where('next_level_role_id', null)->where('created_by_dist_code', $district_code)->orderBy('id', 'desc')->paginate(10);
          return view('pension_list', ['nhm_employee_details' => $rows]);
        } else if ($mappingLevel == "Subdiv") {
          if ($is_urban == 1) {
            $duty_level = "SubdivVerifier";
            $normal_verification_allowded = 0;
            $special_verification_allowded = 0;
            $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
            $verification_allowded = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code', $request->session()->get('distCode'))->where('block_ulb_code', $request->session()->get('bodyCode'))->first();
            $normal_verification_allowded = intval($verification_allowded->main_verification);
            $special_verification_allowded = intval($verification_allowded->special_verification);
            $urban_body_codes = [];
            $i = 0;
            foreach ($urban_bodys as $urban_body) {

              $urban_body_codes[$i] = $urban_body->urban_body_code;
              $i++;
            }
            if (request()->ajax()) {
              $limit = $request->input('length');
              $offset = $request->input('start');
              if (!empty($request->filter_1) && empty($request->filter_2)) {
                $body_code = $request->session()->get('bodyCode');
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                if ($scheme_id == 17) {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code)
                    ->where('block_ulb_code', $request->filter_1)
                    ->whereNotNull('temple_type');
                } else {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code)
                    ->where('block_ulb_code', $request->filter_1);
                }
              } elseif (!empty($request->filter_1) && !empty($request->filter_2)) {
                $body_code = $request->session()->get('bodyCode');
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                if ($scheme_id == 17) {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code)
                    ->where('block_ulb_code', $request->filter_1)
                    ->where('gp_ward_code', $request->filter_2)
                    ->whereNotNull('temple_type');
                } else {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code)
                    ->where('block_ulb_code', $request->filter_1)
                    ->where('gp_ward_code', $request->filter_2);
                }
              } else {
                $body_code = $request->session()->get('bodyCode');
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                if ($scheme_id == 17) {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code)
                    ->whereNotNull('temple_type');
                } else {
                  $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                    ->where('scheme_id', $scheme_id)
                    ->where('created_by_local_body_code', $body_code);
                }
              }
              // dd('Approver');
              if ($request->filter_quota != '') {
                if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
                  if ($scheme_id == 10) {
                    return redirect("/")->with('error', 'Verification temporary suspended.');
                  }
                  $query = $query->where('wt_special', $request->filter_quota);
                }
              }
              if ($scheme_id == 2) {
                if ($request->aadhar_exists == 0) {
                  $query = $query->where('aadhar_exits', 0);
                }
                if ($request->aadhar_exists == 1) {
                  $query = $query->whereNotNull('aadhar_no');
                }
              }
              $query = $query->whereNull('is_reverted');
              if ($scheme_id == 1 || $scheme_id == 3 || $scheme_id == 10) {
                $query = $query->whereNull('is_lb_imported');
              }
              $serachvalue = $request->search['value'];
              if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                  'id',
                  'created_by_dist_code',
                  'dob',
                  'assembly_name',
                  'bank_code',
                  'ben_fname',
                  'ben_lname',
                  'ben_mname',
                  'gender',
                  'ben_age',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'village_town_city',
                  'scheme_id',
                  'lot_generated',
                  'payment_count',
                  'next_level_role_id',
                  'aadhar_exits',
                  'dup_bank',
                  'dup_aadhar',
                  'dup_mobile',
                  'no_aadhar',
                  'no_mobile'
                ]);
                $filterRecords = count($data);
              } else {
                if (is_numeric($serachvalue)) {
                  //$ben_id = (int) substr($serachvalue, -10);
                  $ben_id = $serachvalue;
                  $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id);
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                      'aadhar_exits',
                      'dup_bank',
                      'dup_aadhar',
                      'dup_mobile',
                      'no_aadhar',
                      'no_mobile'
                    ]
                  );
                } else {
                  $query = $query->where(function ($query1) use ($serachvalue) {
                    $query1->where('ben_fname', 'like', $serachvalue . '%')
                      ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                      ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                      ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                      'aadhar_exits',
                      'dup_bank',
                      'dup_aadhar',
                      'dup_mobile',
                      'no_aadhar',
                      'no_mobile'
                    ]
                  );
                }
                $filterRecords = count($data);
                // dd($filterRecords);
              }
              return datatables()->of($data)->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('view', function ($data) {
                  if ($data->scheme_id == 17) {
                    $action = '&nbsp; &nbsp;<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                  } else
                    $action = '&nbsp; &nbsp;<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                  if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
                    $action = $action . '&nbsp; &nbsp;<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                  }
                  if ($data->scheme_id == 10) {
                    $action = $action . '&nbsp; &nbsp;<button type="button" id="revertbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-info revert">Revert</button>&nbsp; &nbsp;';
                    $action = $action . '&nbsp; &nbsp;<button type="button" id="rejectbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-danger reject">Reject</button>&nbsp; &nbsp;';

                  }
                  return $action;
                })
                ->addColumn('id', function ($data) {
                  // return $data->getBenidAttribute();
                  return $data->id;
                })
                ->addColumn('name', function ($data) {
                  // return $data->getName();
                  return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })->addColumn('dup_no_info', function ($data) {
                  $dup_no_info = array();
                  if ($data->dup_bank == 1) {
                    array_push($dup_no_info, 'Duplicate Bank Account Number');
                  }
                  if ($data->dup_aadhar == 1) {
                    array_push($dup_no_info, 'Duplicate Aadhaar Number.');
                  }
                  if ($data->dup_mobile == 1) {
                    array_push($dup_no_info, 'Duplicate Mobile Number.');
                  }
                  if ($data->no_aadhar == 1) {
                    array_push($dup_no_info, 'Aadhaar Number Incorrect.');
                  }
                  if ($data->no_mobile == 1) {
                    array_push($dup_no_info, 'Mobile Number Incorrect.');
                  }
                  if (count($dup_no_info) > 0) {
                    $comma_separated = implode(",", $dup_no_info);
                    return $comma_separated;
                  } else {
                    return '';
                  }
                })
                ->rawColumns(['view', 'id', 'name', 'dup_no_info'])
                ->make(true);
            }
            if ($scheme_id == 17) {
              //Purohits
              return view('PurohitICAD/linelisting_verified_subdiv')
                ->with('duty_level', $duty_level)
                ->with('urban_bodys', $urban_bodys)
                ->with('dist_code', $district_code);
            } else {
              $aadhar_filer_visible = 0;
              $pr1 = $request->pr1;
              $wcd_type = $request->wcd_type;
              if ($pr1 == 'wcd' && $wcd_type == 2) {
                $aadhar_filer_visible = 1;
              }
              return view('linelisting_verified_subdiv')
                ->with('duty_level', $duty_level)
                ->with('urban_bodys', $urban_bodys)
                ->with('dist_code', $district_code)
                ->with('normal_verification_allowded', $normal_verification_allowded)
                ->with('special_verification_allowded', $special_verification_allowded)
                ->with('aadhar_filer_visible', $aadhar_filer_visible)->with('scheme_id', $scheme_id);
            }
          } else {
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            if ($scheme_id == 17) {
              $rows = DB::table($table_name)->where('next_level_role_id', null)->where('created_by_dist_code', $district_code)
                ->where('scheme_id', $scheme_id)
                ->where('created_by_local_body_code', $taluka_code)
                ->whereNotNull('temple_type')
                ->orderBy('id', 'desc')->paginate(10);
            } else {
              $rows = DB::table($table_name)->where('next_level_role_id', null)->where('created_by_dist_code', $district_code)
                ->where('created_by_local_body_code', $taluka_code)
                ->orderBy('id', 'desc')->paginate(10);
            }
            return view('pension_list', ['nhm_employee_details' => $rows]);
          }
        } else if ($mappingLevel == "Block") {
          // dd('ok');


          $duty_level = "BlockVerifier";
          $normal_verification_allowded = 0;
          $special_verification_allowded = 0;
          $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
          $verification_allowded = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code', $request->session()->get('distCode'))->where('block_ulb_code', $request->session()->get('bodyCode'))->first();
          $normal_verification_allowded = intval($verification_allowded->main_verification);
          $special_verification_allowded = intval($verification_allowded->special_verification);
          if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            if (!empty($request->filter_1)) {
              $body_code = $request->session()->get('bodyCode');
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              if ($scheme_id == 17) {
                $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)
                  ->whereNotNull('temple_type')->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $body_code)
                  ->where('gp_ward_code', $request->filter_1);
              } else {
                $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)
                  ->where('created_by_local_body_code', $body_code)->where('created_by_dist_code', $district_code)
                  ->where('gp_ward_code', $request->filter_1);
              }
            } else {
              $body_code = $request->session()->get('bodyCode');
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              if ($scheme_id == 17) {
                $query = DB::table($table_name)->where('next_level_role_id', null)->where('is_state', FALSE)
                  ->where('scheme_id', $scheme_id)
                  ->whereNotNull('temple_type')->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $body_code);
              } else {
                $query = DB::table($table_name)->whereNull('next_level_role_id')->where('is_state', FALSE)->where('created_by_dist_code', $district_code)
                  ->where('scheme_id', $scheme_id)
                  ->where('created_by_local_body_code', $body_code);
              }
            }
            if ($request->filter_quota != '') {
              if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
                //$query = $query->where('wt_special', $request->filter_quota);
              }
            }
            if ($scheme_id == 2) {
              //dd($request->aadhar_exists);
              if ($request->aadhar_exists == 0) {
                $query = $query->where('aadhar_exits', 0);
              }
              if ($request->aadhar_exists == 1) {
                $query = $query->whereNotNull('aadhar_no');
              }
            }
            $query = $query->whereNull('is_reverted');
            if ($scheme_id == 1 || $scheme_id == 3 || $scheme_id == 10) {
              if ($scheme_id == 10) {
                return redirect("/")->with('error', 'Verification temporary suspended.');
              }
              $query = $query->whereNull('is_lb_imported');
            }
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count();

              // if($body_code==3063)
              // {

              // $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->toSql();
              // dd($data);

              // }

              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id',
                'created_by_dist_code',
                'dob',
                'assembly_name',
                'bank_code',
                'ben_fname',
                'ben_lname',
                'ben_mname',
                'gender',
                'ben_age',
                'block_ulb_name',
                'gp_ward_name',
                'bank_ifsc',
                'village_town_city',
                'scheme_id',
                'lot_generated',
                'payment_count',
                'next_level_role_id',
                'aadhar_exits',
                'dup_bank',
                'dup_aadhar',
                'dup_mobile',
                'no_aadhar',
                'no_mobile'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                //$ben_id = (int) substr($serachvalue, -10);
                $ben_id = $serachvalue;
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                    'aadhar_exits',
                    'dup_bank',
                    'dup_aadhar',
                    'dup_mobile',
                    'no_aadhar',
                    'no_mobile'
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'like', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                    'aadhar_exits',
                    'dup_bank',
                    'dup_aadhar',
                    'dup_mobile',
                    'no_aadhar',
                    'no_mobile'
                  ]
                );
              }
              $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) {
                if ($data->scheme_id == 17) {
                  $action = '&nbsp; &nbsp;<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View&nbsp; &nbsp;</a>';

                } else
                  $action = '&nbsp; &nbsp;<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View&nbsp; &nbsp;</a>';

                if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
                  $action = $action . '&nbsp; &nbsp;<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp; &nbsp;';
                }
                if ($data->scheme_id == 10) {
                  $action = $action . '<button type="button" id="revertbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-info revert">Revert</button>&nbsp; &nbsp;';
                  $action = $action . '<button type="button" id="rejectbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-danger reject">Reject</button>&nbsp; &nbsp;';

                }

                return $action;
              })
              ->addColumn('id', function ($data) {
                // return $data->getBenidAttribute();
                return $data->id;
              })
              ->addColumn('name', function ($data) {
                // return $data->getName();
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
              })->addColumn('dup_no_info', function ($data) {
                $dup_no_info = array();
                if ($data->dup_bank == 1) {
                  array_push($dup_no_info, 'Duplicate Bank Account Number');
                }
                if ($data->dup_aadhar == 1) {
                  array_push($dup_no_info, 'Duplicate Aadhaar Number.');
                }
                if ($data->dup_mobile == 1) {
                  array_push($dup_no_info, 'Duplicate Mobile Number.');
                }
                if ($data->no_aadhar == 1) {
                  array_push($dup_no_info, 'Aadhaar Number Incorrect.');
                }
                if ($data->no_mobile == 1) {
                  array_push($dup_no_info, 'Mobile Number Incorrect.');
                }
                if (count($dup_no_info) > 0) {
                  $comma_separated = implode(",", $dup_no_info);
                  return $comma_separated;
                } else {
                  return '';
                }

              })
              ->rawColumns(['view', 'id', 'name', 'dup_no_info'])
              ->make(true);
          }
          //dd($special_verification_allowded);
          $scheme_capacity_arr = array();

          $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
          if ($scheme_capacity_arr['visible'] == 1) {
            if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
              $approveBtnvisible = 0;
            } else {
              $approveBtnvisible = 1;
            }
          } else {
            $approveBtnvisible = 1;
          }

          if ($scheme_id == 17) {
            return view('PurohitICAD/linelisting_verified')
              ->with('duty_level', $duty_level)->with('gps', $gps)
              ->with('dist_code', $district_code)->with('normal_verification_allowded', $normal_verification_allowded)->with('special_verification_allowded', $special_verification_allowded);
          } else {
            $aadhar_filer_visible = 0;
            $pr1 = $request->pr1;
            $wcd_type = $request->wcd_type;
            if ($pr1 == 'wcd' && $wcd_type == 2) {
              $aadhar_filer_visible = 1;
            }
            return view('linelisting_verified')
              ->with('duty_level', $duty_level)->with('gps', $gps)
              ->with('dist_code', $district_code)
              ->with('normal_verification_allowded', $normal_verification_allowded)
              ->with('special_verification_allowded', $special_verification_allowded)
              ->with('aadhar_filer_visible', $aadhar_filer_visible)->with('scheme_id', $scheme_id)
              ->with('scheme_capacity_arr ', $scheme_capacity_arr);
          }
        }
      } else {
        //dd('ok');
        $approveBtnvisible = 1;
        $scheme_capacity_arr = array();
        $distCode = $request->session()->get('distCode');
        $main_approval_allowded = 1;
        $special_approval_allowded = 1;
        $main_approval_allowded = BlkUrbanlEntryMapping::where('main_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $request->session()->get('distCode'))->count();
        $special_approval_allowded = BlkUrbanlEntryMapping::where('special_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $request->session()->get('distCode'))->count();
        if ($main_approval_allowded == 1) {
          $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
          if ($scheme_capacity_arr['visible'] == 1) {
            if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
              $approveBtnvisible = 0;
            } else {
              $approveBtnvisible = 1;
            }
          } else {
            $approveBtnvisible = 1;
          }
        } else {
          $scheme_capacity_arr['visible'] = 0;
        }
        if ($special_approval_allowded == 1) {
          $scheme_capacity_arr_wt = Helper::getCapacityWtQuotaDistrict($scheme_id, $distCode);
          if ($scheme_capacity_arr_wt['visible'] == 1) {
            if ($scheme_capacity_arr_wt['approved'] >= $scheme_capacity_arr_wt['capacity']) {
              $approveBtnvisible = 0;
            } else {
              $approveBtnvisible = 1;
            }
          } else {
            $approveBtnvisible = 1;
          }
        } else {
          $scheme_capacity_arr_wt['visible'] = 0;
        }
        // dd($scheme_capacity_arr_wt);
        if ($mappingLevel == "State") {

          $district_list = Cache::rememberForever('master_districts', function () {
            return District::select(
              'id',
              'district_code',
              'district_name',
              'rch_district_code',
              'is_revenue_district',
              'state_code',
              'district_status'
            )->get();
          });
          $duty_level = "StateApprover";
          // $levels = [
          //   2 => 'Rural',
          //   1 => 'Urban',
          // ];
          if ($is_state_login) {

            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $levels = [
              2 => 'Rural',
              1 => 'Urban',
            ];
            if (request()->ajax()) {
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              $query = DB::table($table_name)->where('next_level_role_id', $this->state_login_next_level_role_id_arr['verified'])
                ->where('is_state', TRUE)
                ->where('scheme_id', $scheme_id);
              if (!empty($request->district_code)) {
                $query = $query->where('created_by_dist_code', $request->district_code);
              }
              if (!empty($request->urban_code)) {
                $query = $query->where('block_ulb_type', $request->urban_code);
              }
              if (!empty($request->block_subdiv_code)) {
                $query = $query->where('created_by_local_body_code', $request->block_subdiv_code);
              }
              $limit = $request->input('length');
              $offset = $request->input('start');
              $serachvalue = $request->search['value'];
              if (empty($serachvalue)) {
                $totalRecords = $query->count('id');
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                  'id',
                  'created_by_local_body_code',
                  'created_by_dist_code',
                  'dob',
                  'assembly_name',
                  'bank_code',
                  'ben_fname',
                  'ben_lname',
                  'ben_mname',
                  'gender',
                  'ben_age',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'village_town_city',
                  'scheme_id',
                  'lot_generated',
                  'payment_count',
                  'next_level_role_id'
                ]);
                $filterRecords = count($data);
              } else {
                if (is_numeric($serachvalue)) {
                  //$ben_id = (int) substr($serachvalue, -10);
                  $ben_id = $serachvalue;
                  $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id)
                      ->orWhere('bank_code', $serachvalue);
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_local_body_code',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                    ]
                  );
                } else {
                  $query = $query->where(function ($query1) use ($serachvalue) {
                    $query1->where('ben_fname', 'like', $serachvalue . '%')
                      ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                      ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                      ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_local_body_code',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                    ]
                  );
                }
                $filterRecords = count($data);
              }
              return datatables()->of($data)->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('view', function ($data) {
                  if ($data->scheme_id == 17) {
                    $action = '<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                  } else
                    $action = '<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';



                  return $action;
                })
                ->addColumn('id', function ($data) {
                  // return $data->getBenidAttribute();
                  return $data->id;
                })
                ->addColumn('name', function ($data) {
                  // return $data->getName();
                  return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })->addColumn('block_ulb_name', function ($data) {
                  return trim($data->block_ulb_name);
                })->addColumn('gp_ward_name', function ($data) {
                  return trim($data->gp_ward_name);
                })->addColumn('date_formated', function ($data) {
                  return Carbon::parse($data->dob)->format('d/m/y');
                })->addColumn('bank_code', function ($data) {
                  return trim($data->bank_code);
                })->addColumn('check', function ($data) use ($approveBtnvisible) {
                  if ($approveBtnvisible)
                    return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';
                  else
                    return '';
                })->rawColumns(['view', 'check', 'id', 'name'])
                ->make(true);
            }
            return view('processApplicationState/linelisting_stateapproved', [
              'districts' => $district_list,
              'scheme_id' => $scheme_id,
              'levels' => $levels,
              'scheme_capacity_arr' => $scheme_capacity_arr
            ]);
          } else {
            if (request()->ajax()) {
              $limit = $request->input('length');
              $offset = $request->input('start');
              $condition = array();
              $condition['next_level_role_id'] = $role_id;
              $condition['is_state'] = FALSE;
              if (!empty($request->district_code))
                $condition['created_by_dist_code'] = $request->district_code;
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              $query = DB::table($table_name)->where('scheme_id', $scheme_id)->where('next_level_role_id', $role_id)->where('scheme_id', $scheme_id)
                ->where($condition);
              //$data->approveBtnvisible = $approveBtnvisible;
              $serachvalue = $request->search['value'];
              if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                  'id',
                  'created_by_dist_code',
                  'dob',
                  'assembly_name',
                  'bank_code',
                  'ben_fname',
                  'ben_lname',
                  'ben_mname',
                  'gender',
                  'ben_age',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'village_town_city',
                  'scheme_id',
                  'lot_generated',
                  'payment_count',
                  'next_level_role_id'
                ]);
                $filterRecords = count($data);
              } else {
                if (is_numeric($serachvalue)) {
                  //$ben_id = (int) substr($serachvalue, -10);
                  $ben_id = $serachvalue;
                  $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id)
                      ->orWhere('bank_code', $serachvalue);
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                    ]
                  );
                } else {
                  $query = $query->where(function ($query1) use ($serachvalue) {
                    $query1->where('ben_fname', 'like', $serachvalue . '%')
                      ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                      ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                      ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id',
                      'created_by_dist_code',
                      'dob',
                      'assembly_name',
                      'bank_code',
                      'ben_fname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname',
                      'gender',
                      'ben_age',
                      'ben_mname',
                    ]
                  );
                }
                $filterRecords = count($data);
              }
              return datatables()->of($data)->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('view', function ($data) {
                  if ($data->scheme_id == 17)
                    $action = '<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                  else
                    $action = '<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                  if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
                    $action = $action . '<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                  }

                  return $action;
                })
                ->addColumn('check', function ($data) use ($approveBtnvisible) {
                  if ($approveBtnvisible)
                    return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';
                  else
                    return '';
                })
                ->addColumn('id', function ($data) {
                  // return $data->getBenidAttribute();
                  return $data->id;
                })
                ->addColumn('name', function ($data) {
                  // return $data->getName();
                  return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })->addColumn('dist_name', function ($data) use ($district_list) {
                  $district_list = $district_list->where('district_code', $data->created_by_dist_code)->first();
                  return $district_list->district_name;
                })
                ->rawColumns(['view', 'check', 'id', 'name'])
                ->make(true);
            } else {
              $districts = District::select(['district_code', 'district_name'])->get();
              if ($scheme_id == 17) {
                return view('PurohitICAD/linelisting_stateapproved')
                  ->with('approveBtnvisible', $approveBtnvisible)
                  ->with('scheme_capacity_arr', $scheme_capacity_arr)
                  ->with('duty_level', $duty_level)
                  ->with('districts', $districts);
              }
            }
          }
        } else if ($mappingLevel == "District") {
          $duty_level = 'DistrictApprover';

          $levels = [
            2 => 'Rural',
            1 => 'Urban',
          ];
          if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            if (!empty($request->filter_1) && !empty($request->filter_2)) {
              if ($request->filter_1 == '2') {
                $body_code = $request->session()->get('bodyCode');
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
                  ->where('scheme_id', $scheme_id)
                  ->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $request->filter_2)->where('is_rejected', 0);
              } else {
                $body_code = $request->session()->get('bodyCode');
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
                  ->where('scheme_id', $scheme_id)
                  ->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $request->filter_2)->where('is_rejected', 0);
              }
            } else {
              $body_code = $request->session()->get('bodyCode');
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
                ->where('scheme_id', $scheme_id)
                ->where('created_by_dist_code', $district_code)->where('is_rejected', 0);
            }
            if ($request->filter_quota != '') {
              if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
                $query = $query->where('wt_special', $request->filter_quota);
              }
            }
            if ($scheme_id == 2) {
              if ($request->aadhar_exists == 0) {
                $query = $query->where('aadhar_exits', 0);
              }
              if ($request->aadhar_exists == 1) {
                $query = $query->whereNotNull('aadhar_no');
              }
            }
            // For LB 03-04-2023
            if ($scheme_id == 1 || $scheme_id == 3 || $scheme_id == 10) {

              //return redirect("/")->with('error', 'Approval temporary suspended.');

              $query = $query->whereNull('is_lb_imported');
            }
            if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {

              return redirect("/")->with('error', 'Approval temporary suspended.');

            }
            if ($scheme_id == 10) {

              $query = $query->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))");
              if ($request->sm_ds_flag != '') {
                if ($request->sm_ds_flag == 1) {
                  $query = $query->where('sm_flag', 1);
                }
                if ($request->sm_ds_flag == 2) {
                  $query = $query->whereraw(" ((sm_ds_mark=1 and mark_ds_phase=8) or ds_phase=8) ");
                }
                if ($request->sm_ds_flag == 3) {
                  $query = $query->whereraw(" ((sm_ds_mark=1 and mark_ds_phase=9) or ds_phase=9) ");
                }
                if ($request->sm_ds_flag == 4) {
                  $query = $query->whereraw(" (ds_phase=10 or  sm_ds_mark_ix=1) ");
                }
              }
            }
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count();
              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id',
                'created_by_dist_code',
                'dob',
                'assembly_name',
                'bank_code',
                'ben_fname',
                'ben_lname',
                'ben_mname',
                'gender',
                'ben_age',
                'block_ulb_name',
                'gp_ward_name',
                'bank_ifsc',
                'village_town_city',
                'scheme_id',
                'lot_generated',
                'payment_count',
                'next_level_role_id',
                'sm_flag',
                'dup_bank',
                'dup_aadhar',
                'dup_mobile',
                'no_aadhar',
                'no_mobile'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                //$ben_id = (int) substr($serachvalue, -10);
                $ben_id = $serachvalue;
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id);

                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                    'sm_flag',
                    'dup_bank',
                    'dup_aadhar',
                    'dup_mobile',
                    'no_aadhar',
                    'no_mobile'
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'like', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname',
                    'gender',
                    'ben_age',
                    'ben_mname',
                    'dup_bank',
                    'dup_aadhar',
                    'dup_mobile',
                    'no_aadhar',
                    'no_mobile'
                  ]
                );
              }
              $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) {
                if ($data->scheme_id == 17) {
                  $action = '<a href="' . route('showApplicantDetailsPurohit', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                } else
                  $action = '<a href="' . route('showApplicantDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
                  $action = $action . '<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                }

                return $action;
              })
              ->addColumn('check', function ($data) use ($approveBtnvisible) {
                if ($approveBtnvisible)
                  if ($data->dup_bank == 1 || $data->dup_aadhar || $data->dup_mobile || $data->no_aadhar || $data->no_mobile) {
                    return '';
                  } else {
                    return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';

                  } else
                  return '';
              })
              ->addColumn('id', function ($data) {
                // return $data->getBenidAttribute();
                return $data->id;
              })
              ->addColumn('name', function ($data) {
                // return $data->getName();
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
              })
              ->rawColumns(['view', 'check', 'id', 'name'])
              ->make(true);
          }
          $aadhar_filer_visible = 0;
          $pr1 = $request->pr1;
          $wcd_type = $request->wcd_type;
          if ($pr1 == 'wcd' && $wcd_type == 2) {
            $aadhar_filer_visible = 1;
          }
          if ($scheme_id == 17) {
            return view('PurohitICAD/linelisting_approved')
              ->with('duty_level', $duty_level)->with('levels', $levels)
              ->with('approveBtnvisible', $approveBtnvisible)
              ->with('scheme_capacity_arr', $scheme_capacity_arr)
              ->with('dist_code', $district_code)->with('scheme_id', $scheme_id);
          } else {
            return view('linelisting_approved')->with('duty_level', $duty_level)
              ->with('levels', $levels)->with('approveBtnvisible', $approveBtnvisible)
              ->with('scheme_capacity_arr', $scheme_capacity_arr)->with('scheme_capacity_arr_wt', $scheme_capacity_arr_wt)
              ->with('dist_code', $district_code)->with('main_approval_allowded', $main_approval_allowded)
              ->with('special_approval_allowded', $special_approval_allowded)
              ->with('aadhar_filer_visible', $aadhar_filer_visible)->with('scheme_id', $scheme_id);
          }
        } else {
          if ($is_urban == 1) {
            $duty_level = "ULB";
          } else {
            $duty_level = "Block";
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $rows = $data = DB::table($table_name)->where('next_level_role_id', $role_id)->where('created_by_local_body_code', $taluka_code)->orderBy('id', 'desc')->paginate(10);
            if ($scheme_id == 17) {
              return view('PurohitICAD/linelisting_approved', ['datas' => $rows, 'dist_code' => $district_code]);
            } else {
              return view('linelisting_approved', ['datas' => $rows, 'dist_code' => $district_code]);
            }
          }
        }
      }
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function loadWard(Request $request, $municipality)
  {
    dd($municipality);
    $wards = Ward::where('urban_body_code', $municipality)->get(['urban_body_ward_code as id', 'urban_body_ward_name as name']);
    // dd($wards);
    return response()->json($wards);

  }


  public function MassEmployeeApproval(Request $request)
  {
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');

    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $is_state_login = $request->session()->get('is_state_login');
    $c_time = date('Y-m-d H:i:s', time());
    $inputs = request()->input('approvalcheck');

    $table_name = $this->getSchemaName($scheme_id);
    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }

    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
      return redirect("/")->with('error', 'Approval temporary suspended.');
    }
    if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {

      if (isset($request->wq)) {
        $wq = $request->wq;
      } else {
        $wq = 0;
      }
      if ($wq == '') {
        return redirect("/")->with('danger', 'Security Issue.. Please try Again');
      }
      if (isset($request->created_by_local_body_code)) {
        $created_by_local_body_code_post = $request->created_by_local_body_code;
      }
      if ($created_by_local_body_code_post == '') {
        return redirect("/")->with('danger', 'Security Issue.. Please try Again');
      }
      $wq_condition = array();
      if ($wq == 1) {
        $wq_condition['wt_special'] = 1;
        $allowded_count = BlkUrbanlEntryMapping::where('special_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $district_code)->count();
        if ($allowded_count == 0) {
          return redirect("/")->with('danger', 'Approval with Special Quota is temporarily suspended');
        }
      } else {
        $wq_condition['wt_special'] = 0;
        $allowded_count = BlkUrbanlEntryMapping::where('main_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $district_code)->count();
        if ($allowded_count == 0) {
          return redirect("/")->with('danger', 'Approval is temporarily suspended');
        }
      }
      $scheme_capacity_arr = array();
      if ($wq == 1) {
        //dd($created_by_local_body_code_post);
        $scheme_capacity_arr = Helper::getCapacityWtQuota($scheme_id, $district_code, $created_by_local_body_code_post);
        // dd($scheme_capacity_arr);   
        if ($scheme_capacity_arr['visible'] == 1) {
          $scheme_capacity_arr['total_data'] = $scheme_capacity_arr['approved'];
          if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications (Special Quota) " . $scheme_capacity_arr['total_data'] . " exceeds the Special Quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('error', $errorMsgCap);
          }
          $total_check = $scheme_capacity_arr['total_data'] + count($inputs);
          if ($total_check >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications (Special Quota) plus the applications which you have selected is: " . $total_check . " which exceeds the  Special Quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('danger', $errorMsgCap);
          }
        }
      } else {
        /*
        $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
        if ($scheme_capacity_arr['visible'] == 1) {
          if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('error', $errorMsgCap);
          }
          $total_check = $scheme_capacity_arr['total_data'] + count($inputs);
          if ($total_check >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications plus the applications which you have selected is: " . $total_check . " which exceeds the quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('danger', $errorMsgCap);
          }
        }
       */
      }
    }
    $user_id = AuthChecker::getUserId();
    $ben_table = $request->session()->get('ben_table');
    $id = $request->benId;
    $Verified = "Verified";
    $Rejected = "Rejected";
    $comments = $request->comments;
    $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
    if ($is_state_login) {
      $next_level_role_id = 0;
    } else {
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();
      $next_level_role_id = $role->parent_id;
    }


    try {
      $payment_start_date = date('Y-m-d');
      if ($scheme_id == 10 && $request->oap_smsd == 1 && $payment_start_date < '2024-04-01') {
        $payment_start_date = '2024-04-01';

      }
      DB::beginTransaction();
      $k = 0;
      if ($scheme_id == 11) {
        $input_update = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'approval_date' => $c_time, 'payment_start_date' => $payment_start_date, 'approved_by' => $user_id, 'wp_phase' => 2];
      } else {
        $input_update = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'approval_date' => $c_time, 'payment_start_date' => $payment_start_date, 'approved_by' => $user_id];
      }
      foreach ($inputs as $input) {
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
          if ($scheme_id == 10) {
            $is_pushed = DB::table($table_name)->where('id', $input)->whereNull('is_lb_imported')->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))")->where($wq_condition)->update($input_update);
          } else {
            $is_pushed = DB::table($table_name)->where('id', $input)->where($wq_condition)->update($input_update);
          }
        } else {
          $is_pushed = DB::table($table_name)->where('id', $input)->update($input_update);
        }
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $input;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->comment_message = $comments;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->created_by_dist_code = $district_code;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'AA';
        $is_saved_log = $accept_reject_model->save();
        if ($is_pushed && $is_saved_log) {
          $k++;
        }
      }
      if ($k == count($inputs)) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Succesfully Approved!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('danger', 'Some error.Please try again');
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect("/")->with('danger', 'Some error.Please try again');
    }
  }

  public function showSingleEmployeeReport(Request $request)
  {
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = AuthChecker::getUserId();
    $table_name = $this->getSchemaName($scheme_id);

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }

    $id = $request->id;
    $appPrefix = "App";
    $modelName = $appPrefix . "\\" . $ben_table;
    $row = DB::table($table_name)->where('id', '=', $id)->first(); //find($id);
    if ($scheme_id == 13) {
      $docs = collect([]);
    } else if ($scheme_id == 3) {
      $docs = BenDocsSc::where('ben_id', $id)->get();
    } else if ($scheme_id == 1) {
      $docs = BenDocsSt::where('ben_id', $id)->get();
    } else if ($scheme_id == 4) {
      $docs = BenDocsPrachesta::where('ben_id', $id)->get();
    } else if ($scheme_id == 5) {
      $docs = BenDocsFisherman::where('ben_id', $id)->get();
    } else if ($scheme_id == 6) {
      $docs = BenDocsMSME::where('ben_id', $id)->get();
    } else if ($scheme_id == 7) {
      $docs = BenDocsTextile::where('ben_id', $id)->get();
    } else if ($scheme_id == 2) {
      $docs = BenDocsManabikWCD::where('ben_id', $id)->get();
    } else if ($scheme_id == 10) {
      $docs = BenDocsOAPWCD::where('ben_id', $id)->get();
    } else if ($scheme_id == 11) {
      $docs = BenDocsWPWCD::where('ben_id', $id)->get();
    } else if ($scheme_id == 17) {
      $docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();
    }
    // else if($scheme_id==$this->monthlySchemeCode){
    //   $docs = $this->monthlyDocTable::where('ben_id',$id)->get(); 
    // }
    // else if($scheme_id==$this->housingSchemeCode){
    //   $docs = $this->housingDocTable::where('ben_id',$id)->get(); 
    // }
    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
    if ($row->block_ulb_code != "") {
      if ($row->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
        $block_name = $block->urban_body_name;
      } else {
        $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
        $block_name = $block->block_name;
      }
    }
    if ($row->gp_ward_code != "") {
      if ($row->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
        $gp_name = $gp_ward->urban_body_ward_name;
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        $gp_name = $gp->gram_panchyat_name;
      }
    }
    $doc_profile_image = DocumentType::get()
      ->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }
    if ($scheme_id == 13)
      return view('farmer/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    else if ($scheme_id == 5)
      return view('fisherman/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    elseif ($scheme_id == 6)
      return view('msme/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    elseif ($scheme_id == 7)
      return view('textile/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    elseif ($scheme_id == 17)
      return view('PurohitICAD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    // elseif($scheme_id ==$this->monthlySchemeCode)
    //   return view('PurohitICAD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // elseif($scheme_id ==$this->housingSchemeCode)
    //   return view('PurohitICAD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    else
      return view('pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
  }


  public function showApplicantDetails(Request $request)
  {
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $table_name = $this->getSchemaName($scheme_id);

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }
    $approveBtnvisible = 1;

    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $is_state_login = $request->session()->get('is_state_login');
    //dd($is_state_login);
    $user_id = AuthChecker::getUserId();
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->id;
    $appPrefix = "App";
    $modelName = $appPrefix . "\\" . $ben_table;
    if ($is_state_login) {
      $row = DB::table($table_name)->where('id', '=', $id)->where('is_state', TRUE)->first();
    } else {
      if ($mappingLevel == 'Department' || $mappingLevel == 'State') {
        $row = DB::table($table_name)->where('id', '=', $id)->first();
      } else
        $row = DB::table($table_name)->where('id', '=', $id)->where('created_by_dist_code', $district_code)->first();
    }
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if ($is_state_login) {
      $district_state = District::where('district_code', '=', $row->created_by_dist_code)->get(['district_code', 'district_name'])->first();
      $district_state_name = trim($district_state->district_name);
      if ($row->block_ulb_type == 1) {
        $sdo_state = SubDistrict::where('sub_district_code', '=', $row->created_by_local_body_code)->get(['sub_district_code', 'sub_district_name'])->first();
        $block_subdiv_state_name = trim($sdo_state->sub_district_name);
      } else {
        $block_state = Taluka::where('block_code', '=', $row->created_by_local_body_code)->first();
        $block_subdiv_state_name = trim($block_state->block_name);
      }
    } else {
      $district_state_name = '';
      $urban_code_state_name = '';
      $block_subdiv_state_name = '';
    }
    $housingrecord = '';
    if ($scheme_id == 13) {
      $row = PensionOAPFarmer::find($id);
      //$docs = collect([]);
      // $docs = BenDocsOAPFarmer::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 3) {
      //$row = PensionSc::find($id);           
      //$docs = BenDocsSc::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 1) {
      //$row = PensionSt::find($id);
      //$docs = BenDocsSt::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 4) {
      //$row = Manabik::find($id);          
      // $docs = BenDocsPrachesta::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 5) {
      //$row = Manabik::find($id);          
      //$docs = BenDocsFisherman::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 6) {
      //$row = Manabik::find($id);          
      //$docs = BenDocsMSME::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 7) {
      //$row = Manabik::find($id);          
      // $docs = BenDocsTextile::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 2) {
      //$row = Manabik::find($id);          
      // $docs = BenDocsManabikWCD::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 10) {
      //$row = Manabik::find($id);          
      // $docs = BenDocsOAPWCD::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 11) {
      //$row = Manabik::find($id);          
      // $docs = BenDocsWPWCD::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    } else if ($scheme_id == 17) {
      if ($row->housing_code != '') {
        $code = $row->housing_code;
        $housingrecord = PensionPurohitHousingICAD::where('id', '=', $row->housing_code)->first();
        if ($housingrecord->next_level_role_id == -1) {
          $housingrecord = '';
        }
      }
      // $docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();

    }
    // else if($scheme_id==$this->monthlySchemeCode){       
    //   $docs = $this->monthlyDocTable::where('ben_id',$id)->get();
    // }
    // else if($scheme_id==$this->housingSchemeCode){
    //   //$row = Manabik::find($id);          
    //   $docs = $this->hosingDocTable::where('ben_id',$id)->get();
    // }
    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
    $block_name = "";
    if ($row->block_ulb_code != "") {
      if ($row->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
        if (!empty($block)) {
          $block_name = $block->urban_body_name;
        }
      } else {
        if (!empty($row->block_ulb_code)) {
          $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
          if (!empty($block)) {
            $block_name = $block->block_name;
          } else {
            $block_name = '';
          }
        } else {
          $block_name = '';
        }
      }
    }
    $gp_name = "";
    if ($row->gp_ward_code != "") {
      if ($row->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
        if (!empty($gp_ward)) {
          $gp_name = $gp_ward->urban_body_ward_name;
        }
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        if (!empty($gp)) {
          $gp_name = $gp->gram_panchyat_name;
        }
      }
    }
    $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }
    $scheme_capacity_arr = array();
    $distCode = $request->session()->get('distCode');
    if ($row->wt_special == 1) {
      $designation_id = Auth::user()->designation_id;
      if ($designation_id == 'Verifier') {
        $scheme_capacity_arr = Helper::getCapacityWtQuota($scheme_id, $distCode, $request->session()->get('bodyCode'));
      }
      if ($designation_id == 'Approver') {
        $scheme_capacity_arr = Helper::getCapacityWtQuotaDistrict($scheme_id, $distCode);
      }
      if ($scheme_capacity_arr['visible'] == 1) {
        $scheme_capacity_arr['total_data'] = $scheme_capacity_arr['approved'];
        if ($scheme_capacity_arr['approved'] >= $scheme_capacity_arr['capacity']) {
          $approveBtnvisible = 0;
        } else {
          $approveBtnvisible = 1;
        }
      } else {
        $approveBtnvisible = 1;
      }
    } else {
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $approveBtnvisible = 0;
        } else {
          $approveBtnvisible = 1;
        }
      } else {
        $approveBtnvisible = 1;
      }
    }
    if ($scheme_id == 13)
      return view('farmer/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    else if ($scheme_id == 5)
      return view('fisherman/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    else if ($scheme_id == 2) {

      return view('MANABIKWCD.pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    } else if ($scheme_id == 10) {
      //dd($row->wt_special);
      return view('OAPWCD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    } else if ($scheme_id == 11) {
      // dd($reject_revert_cause_list);
      return view('WPWCD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    } else if ($scheme_id == 6)
      return view('msme/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    else if ($scheme_id == 7) {
      return view('textile/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    } else if ($scheme_id == 17) {
      $duty = Configduty::where('user_id', '=', Auth::user()->id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

      return view('PurohitICAD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'parent_id' => $role->parent_id,
        'scheme_id' => $scheme_id,
        'housingrecord' => $housingrecord,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    }
    // else if($scheme_id== $this->monthlySchemeCode){
    //   return view('PurohitICAD/pension_view_details', [
    //     'scheme_id' => $scheme_id,
    //     'monthlySchemeCode' => $this->monthlySchemeCode,
    //     'housingSchemeCode' =>  $this->housingSchemeCode,
    //     'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // }
    // else if($scheme_id==$this->housingSchemeCode){
    //   return view('PurohitICAD/pension_view_details', ['scheme_id' => $scheme_id,
    //   'monthlySchemeCode' => $this->monthlySchemeCode,
    //   'housingSchemeCode' =>  $this->housingSchemeCode,
    //   'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // }
    else
      return view('pension_view_details', [
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
  }

  public function verifydata(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    if (empty($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }
    $user_id = AuthChecker::getUserId();
    $designation_id = Auth::user()->designation_id;
    if ($designation_id == 'Operator') {
      return redirect("/")->with('danger', 'Not Allowded');
    }
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->benId;
    $scheme_id = $request->scheme_id;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    // dd($duty_obj);
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $condition_arr = array();
    $condition_arr['id'] = $id;
    if ($duty_obj->mapping_level == "Department") {
      $created_by_local_body_code = NULL;
      $created_by_dist_code = NULL;
    } else {
      $condition_arr['created_by_dist_code'] = $duty_obj->district_code;
      $created_by_dist_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "District") {
        $created_by_local_body_code = NULL;
      }
    }
    $row = DB::table($schema . '.beneficiaries')->where('scheme_id', $scheme_id)
      ->where($condition_arr)->first();
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $c_time = date('Y-m-d H:i:s', time());
    $Verified = "Verified";
    $Rejected = 1;
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $created_by_dist_code;
    $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
    $accept_reject_model->ip_address = request()->ip();
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty_obj->mapping_level)->first();
    $next_level_role_id = $role->parent_id;

    if ($_POST['submit'] == 'Verify') {
      if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
        if ($scheme_id == 10)
          return redirect("/")->with('error', 'Verification temporary suspended.');
        $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $created_by_local_body_code)->where('district_code', $created_by_dist_code)->first();
        $verification_allowded = intval($allowded_arr->main_verification);
        if ($verification_allowded == 0) {
          return redirect("/")->with('danger', 'Verification is temporarily suspended');
        }
      }
      if ($row->dup_bank == 1) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Bank Account Number..');
      }
      if ($row->dup_aadhar == 1) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Aadhaar Number.');
      }
      if ($row->dup_mobile == 1) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Mobile Number.');
      }
      if ($row->no_aadhar == 1) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Aadhaar Number Incorrect.');
      }
      if ($row->no_mobile == 1) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Mobile Number Incorrect.');
      }
      $accept_reject_model->op_type = 'AV';


      $input = [
        'is_verified' => 1,
        'next_level_role_id' => $next_level_role_id,
        'comments' => $comments,
        'verification_date' => $c_time,
        'verified_by' => $user_id
      ];

      DB::beginTransaction();

      $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->whereNotNull('bank_code')->whereNull('next_level_role_id')->update($input);

      $is_saved_log = $accept_reject_model->save();
      //dd($is_status_updated);
      if ($is_status_updated && $is_saved_log) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Forwarded Succesfully!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    } else if ($_POST['submit'] == 'Revert') {

      $accept_reject_model->op_type = 'AREVERT';


      $input = ['next_level_role_id' => NULL, 'is_verified' => 0, 'is_approved' => 0, 'is_reverted' => 1];

      DB::beginTransaction();

      $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);

      $is_saved_log = $accept_reject_model->save();
      //dd($is_status_updated);
      if ($is_status_updated && $is_saved_log) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    } else if ($_POST['submit'] == 'Reject') {
      $is_state_login = 0;
      try {
        $accept_reject_model->op_type = 'AR';
        $input = [
          'verification_rejected' => $Rejected,
          'comments' => $comments,
          'next_level_role_id' => -1,
          'is_approved' => 2,
          'is_verified' => 2,
          'is_rejected' => 1,
          'rejected_date' => $c_time,
          'rejected_by' => $user_id,
          'is_clean' => 10
        ];
        $appPrefix = "App";
        // $modelName = $appPrefix . "\\" . $ben_table;
        DB::beginTransaction();
        if ($is_state_login == 1) {
          $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('is_state', TRUE)->update($input);
        } else {
          $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);
        }
        $is_saved_log = $accept_reject_model->save();
        $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        if (in_array($scheme_id, $scheme_dedup_list)) {
          $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $created_by_dist_code . ")");
          //dd($free_pending_bank_duplicate_arr);
          $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
          if (!empty(trim($row->mobile_no))) {
            $sp_mobile = $row->mobile_no;
          } else {
            $sp_mobile = 0;
          }
          $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
          in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
          in_old_bank_code => '" . $row->bank_code . "', 
          in_old_aadhar_no => '" . $row->aadhar_no . "', 
          in_old_mobile_no => " . $sp_mobile . "
          )");
          $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
        } else {
          $reject_dup_adjustment = 1;
          $free_pending_bank_duplicate_data = 1;
        }
        if ($is_status_updated && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
          DB::commit();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
        }
      } catch (\Exception $e) {
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    }
  }


  public function approvedata(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');

    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $is_state_login = $request->session()->get('is_state_login');
    $c_time = date('Y-m-d H:i:s', time());
    $table_name = 'pension.beneficiaries';

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }
    $user_id = AuthChecker::getUserId();
    $appPrefix = "App";
    $modelName = $appPrefix . "\\" . $ben_table;
    $id = $request->benId;
    $Verified = "Verified";
    $Rejected = 1;
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $district_code;
    $accept_reject_model->ip_address = request()->ip();
    $user_id = AuthChecker::getUserId();
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
    if ($is_state_login == 1) {
      $next_level_role_id = 0;
      $row = DB::table($table_name)->where('id', '=', $id)->where('is_state', TRUE)->first();
    } else {
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();
      $next_level_role_id = $role->parent_id;
      $row = DB::table($table_name)->where('id', '=', $id)->where('next_level_role_id', $role->id)->first();
    }
    if (empty($row)) {
      return redirect("/")->with('danger', 'Application id Not Found');
    }

    if ($_POST['submit'] == 'Approve') {

      $accept_reject_model->op_type = 'AA';
      if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
        return redirect("/")->with('error', 'Approval temporary suspended.');
      }
      if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {

        $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $row->created_by_local_body_code)->where('district_code', $district_code)->first();
        if ($row->wt_special == 1) {
          $approval_allowded = intval($allowded_arr->special_approval);
          if ($approval_allowded == 0) {
            return redirect("/")->with('danger', 'Special Approval  without Quota  is temporarily suspended');
          }
        } else {
          $approval_allowded = intval($allowded_arr->main_approval);
          if ($approval_allowded == 0) {
            return redirect("/")->with('danger', 'Approval is temporarily suspended');
          }
        }
      }
      if ($row->wt_special == 1) {
        $scheme_capacity_arr = Helper::getCapacityWtQuotaDistrict($scheme_id, $district_code);
        $scheme_capacity_arr['total_data'] = $scheme_capacity_arr['approved'];
        if ($scheme_capacity_arr['visible'] == 1) {
          if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications (Special Quota) " . $scheme_capacity_arr['total_data'] . " exceeds the Special quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('danger', $errorMsgCap);
          }
        }
      } else {
        $scheme_capacity_arr = array();
        /* $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
         if ($scheme_capacity_arr['visible'] == 1) {
           if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
             $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
             return redirect("/")->with('danger', $errorMsgCap);
           }
         }*/
      }
      $payment_start_date = date('Y-m-d');
      if ($scheme_id == 10 && $row->ds_phase == 10 && $payment_start_date < '2024-04-01') {
        $payment_start_date = '2024-04-01';

      }
      if ($scheme_id == 11) {
        $input = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'comments' => $comments, 'payment_start_date' => $payment_start_date, 'approval_date' => $c_time, 'approved_by' => $user_id, 'wp_phase' => 2];
      } else
        $input = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'comments' => $comments, 'payment_start_date' => $payment_start_date, 'approval_date' => $c_time, 'approved_by' => $user_id];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      if ($is_state_login == 1) {
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('is_state', TRUE)->whereNotNull('bank_code')->update($input);
      } else {
        if ($scheme_id == 10) {
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNull('is_lb_imported')->whereNotNull('bank_code')->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))")->update($input);
        } else
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNotNull('bank_code')->update($input);
      }
      $is_saved_log = $accept_reject_model->save();
      if ($is_status_updated && $is_saved_log) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Approved Succesfully!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    } else if ($_POST['submit'] == 'Reject') {
      $accept_reject_model->op_type = 'AR';
      $input = [
        'approval_rejected' => $Rejected,
        'comments' => $comments,
        'next_level_role_id' => -1,
        'is_approved' => 2,
        'is_verified' => 2,
        'is_rejected' => 1,
        'rejected_date' => $c_time,
        'rejected_by' => $user_id,
        'is_clean' => 10
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      if ($is_state_login == 1) {
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('is_state', TRUE)->update($input);
      } else {
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
      }
      $is_saved_log = $accept_reject_model->save();
      $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
      if (in_array($scheme_id, $scheme_dedup_list)) {
        $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
        //dd($free_pending_bank_duplicate_arr);
        $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
        if (!empty(trim($row->mobile_no))) {
          $sp_mobile = $row->mobile_no;
        } else {
          $sp_mobile = 0;
        }
        $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
          in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
          in_old_bank_code => '" . $row->bank_code . "', 
          in_old_aadhar_no => '" . $row->aadhar_no . "', 
          in_old_mobile_no => " . $sp_mobile . "
          )");
        $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
      } else {
        $reject_dup_adjustment = 1;
        $free_pending_bank_duplicate_data = 1;
      }
      if ($is_status_updated && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    } else if ($_POST['submit'] == 'Revert') {
      $accept_reject_model->op_type = 'AE';
      $input = [
        'approval_rejected' => 3,
        'comments' => $comments,
        'next_level_role_id' => NULL,
        'is_verified' => 0,
        'is_approved' => 0,
        'is_reverted' => 1
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      if ($is_state_login == 1) {
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('is_state', TRUE)->update($input);
      } else {
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
      }
      $is_saved_log = $accept_reject_model->save();
      if ($is_status_updated && $is_saved_log) {
        DB::commit();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
      } else {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
    }
  }

  //Purohits Verification/Approval
  public function verifyPurohitdata(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = AuthChecker::getUserId();
    $table_name = $this->getSchemaName($scheme_id);

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }
    $c_time = date('Y-m-d H:i:s', time());
    $id = $request->benId;
    $Verified = "Verified";
    $Rejected = 1;

    $pensionverificationstatus = $request->pensionverification;
    $pensioncomments = $request->pensionverificationcomment;

    $housingverificationstatus = $request->housingverification;
    $housingcomments = $request->housingverificationcomment;
    $housingBenId = $request->housingBenId;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $user_id = AuthChecker::getUserId();
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $district_code;
    $accept_reject_model->ip_address = request()->ip();
    $pension_verification_status = '';
    $pension_rejection_status = '';
    if ($pensionverificationstatus) {
      $accept_reject_model->op_type = 'AV';
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('danger', $errorMsgCap);
        }
      }
      //Verified
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

      $input = ['is_verified' => 1, 'next_level_role_id' => $role->parent_id, 'comments' => $pensioncomments, 'verification_date' => $c_time, 'verified_by' => $user_id];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        $is_saved_log = $accept_reject_model->save();
        $pension_verification_status = DB::table($table_name)->where('id', $id)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    } else {
      $accept_reject_model->op_type = 'AR';
      $input = [
        'verification_rejected' => $Rejected,
        'comments' => $pensioncomments,
        'next_level_role_id' => -1,
        'is_approved' => 2,
        'is_verified' => 2,
        'is_rejected' => 1,
        'rejected_date' => $c_time,
        'rejected_by' => $user_id,
        'is_clean' => 10
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        $is_saved_log = $accept_reject_model->save();
        $pension_rejection_status = DB::table($table_name)->where('id', $id)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    }

    $housing_verification_status = '';
    $housing_rejection_status = '';
    $ben_table = 'PensionPurohitHousingICAD';
    $scheme_id = 18;

    if ($housingverificationstatus) {
      $accept_reject_model->op_type = 'AV';
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('danger', $errorMsgCap);
        }
      }
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();
      //Verified
      $input = ['is_verified' => 1, 'next_level_role_id' => $role->parent_id, 'comments' => $housingcomments];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        $is_saved_log = $accept_reject_model->save();
        $pension_verification_status = DB::table($table_name)->where('id', $housingBenId)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    } else {
      //$accept_reject_model->op_type = 'AR';
      $input = [
        'verification_rejected' => $Rejected,
        'comments' => $housingcomments,
        'next_level_role_id' => -1,
        'is_approved' => 2,
        'is_verified' => 2,
        'is_rejected' => 1,
        'rejected_date' => $c_time,
        'rejected_by' => $user_id,
        'is_clean' => 10
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        //$is_saved_log = $accept_reject_model->save();
        $pension_rejection_status = DB::table($table_name)->where('id', $housingBenId)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    }
    return redirect()->intended('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Verification Completed!');
  }

  public function approvePurohitdata(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');

    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');

    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = AuthChecker::getUserId();
    $table_name = $this->getSchemaName($scheme_id);

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }
    $c_time = date('Y-m-d H:i:s', time());
    $id = $request->benId;
    $Verified = "Verified";
    $Rejected = 1;


    $pensionapprovalstatus = $request->pensionapproval;
    $pensioncomments = $request->pensionapprovalcomment;

    $housingapprovalstatus = $request->housingapproval;
    $housingcomments = $request->housingapprovalcomment;
    $housingBenId = $request->housingBenId;

    $user_id = AuthChecker::getUserId();
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $district_code;
    $accept_reject_model->ip_address = request()->ip();
    $pension_approval_status = '';
    $pension_rejection_status = '';
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if ($pensionapprovalstatus) {
      $accept_reject_model->op_type = 'AA';
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('danger', $errorMsgCap);
        }
      }
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

      //Approved
      $input = ['is_approved' => 1, 'next_level_role_id' => $role->parent_id, 'comments' => $pensioncomments, 'payment_start_date' => date('Y-m-d'), 'approval_date' => $c_time, 'approved_by' => $user_id,];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        $is_saved_log = $accept_reject_model->save();
        $pension_approval_status = DB::table($table_name)->where('id', $id)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    } else {
      //Rejected
      $accept_reject_model->op_type = 'AR';
      $input = [
        'approval_rejected' => $Rejected,
        'comments' => $pensioncomments,
        'next_level_role_id' => -1,
        'is_approved' => 2,
        'is_verified' => 2,
        'is_rejected' => 1,
        'rejected_date' => $c_time,
        'rejected_by' => $user_id,
        'is_clean' => 10
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        $is_saved_log = $accept_reject_model->save();
        $pension_rejection_status = DB::table($table_name)->where('id', $id)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    }

    $housing_approval_status = '';
    $housing_rejection_status = '';
    $ben_table = 'PensionPurohitHousingICAD';
    $scheme_id = 18;

    if ($housingapprovalstatus) {
      //$accept_reject_model->op_type = 'AA';
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('danger', $errorMsgCap);
        }
      }
      //Verified
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

      $input = ['next_level_role_id' => $role->parent_id, 'comments' => $housingcomments];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        // $is_saved_log = $accept_reject_model->save();
        $pension_approval_status = DB::table($table_name)->where('id', $housingBenId)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    } else {
      //Rejected
      // $accept_reject_model->op_type = 'AR';
      $input = [
        'approval_rejected' => $Rejected,
        'comments' => $housingcomments,
        'next_level_role_id' => -1,
        'is_approved' => 2,
        'is_verified' => 2,
        'is_rejected' => 1,
        'rejected_date' => $c_time,
        'rejected_by' => $user_id,
        'is_clean' => 10
      ];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      try {
        //$is_saved_log = $accept_reject_model->save();
        $pension_rejection_status = DB::table($table_name)->where('id', $housingBenId)->update($input);
      } catch (\Exception $e) {
        DB::rollback();
        return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
      }
      DB::commit();
    }
    return redirect()->intended('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Approval process Completed!');

    // $comments=$request->comments;

    // $user_id = AuthChecker::getUserId();        
    // $duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
    // $role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();


    // if ($_POST['submit'] == 'Approve') {
    //   $input = ['next_level_role_id' => $role->parent_id,'comments' => $comments]; 
    //   $appPrefix = "App";
    //   $modelName=$appPrefix . "\\" . $ben_table;            
    //   $is_status_updated=$modelName::where('id', $id)->update($input);            
    //   if($is_status_updated){
    //       return redirect()->intended('workflow')->with('message','Approved Succesfully!');            
    //   }
    // }else if ($_POST['submit'] == 'Reject') {
    //   $input = [
    //   'approval_rejected' => $Rejected,'comments' => $comments,'next_level_role_id' => -1];
    //   $appPrefix = "App";
    //   $modelName=$appPrefix . "\\" . $ben_table; 
    //   $is_status_updated=$modelName::where('id', $id)->update($input);  
    //   //->update($input);
    //   if($is_status_updated){
    //       return redirect()->intended('workflow')->with('message','Rejected Succesfully!');
    //   } 
    // }
  }
  public function MassApprovalPurohits(Request $request)
  {
    $this->shemeSessionCheck($request);
    $scheme_id = $request->session()->get('scheme_id');
    $table_name = $this->getSchemaName($scheme_id);

    if ($table_name == '') {
      return redirect('/')->with('error', 'Scheme Not Found...');
    }
    $scheme_capacity_arr = array();
    $distCode = $request->session()->get('distCode');
    $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
    if ($scheme_capacity_arr['visible'] == 1) {
      if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
        $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
        return redirect("/")->with('danger', $errorMsgCap);
      }
    }
    $ben_table = $request->session()->get('ben_table');
    $mappingLevel = $request->session()->get('level');
    $district_code = $request->session()->get('distCode');
    $is_first = $request->session()->get('is_first');
    $is_urban = $request->session()->get('is_urban');
    $urban_body_code = $request->session()->get('bodyCode');
    $taluka_code = $request->session()->get('bodyCode');
    $role_id = $request->session()->get('role_id');
    $user_id = AuthChecker::getUserId();
    $c_time = date('Y-m-d H:i:s', time());
    $id = $request->benId;
    $Verified = "Verified";
    $Rejected = "Rejected";
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $district_code;
    $accept_reject_model->ip_address = request()->ip();
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();
    $inputs = request()->input('approvalcheck');
    if ($scheme_capacity_arr['visible'] == 1) {
      $total_check = $scheme_capacity_arr['total_data'] + count($inputs);
      if ($total_check >= $scheme_capacity_arr['capacity']) {
        $errorMsgCap = "Total no. of Approved applications plus the applications which you have selected is: " . $total_check . " which exceeds the quota " . $scheme_capacity_arr['capacity'];
        return redirect("/")->with('danger', $errorMsgCap);
      }
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    DB::beginTransaction();
    try {
      foreach ($inputs as $input) {
        $input_update = ['is_approved' => 1, 'next_level_role_id' => $role->parent_id, 'approval_date' => $c_time, 'payment_start_date' => date('Y-m-d'), 'approved_by' => $user_id];
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $comments = $request->comments;
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $input;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->comment_message = $comments;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->created_by_dist_code = $district_code;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'AA';
        $is_saved_log = $accept_reject_model->save();
        $is_pushed = DB::table($table_name)->where('id', $input)->whereNotNull('bank_code')->update($input_update);
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
    }
    DB::commit();
    return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Succesfull!');
  }
  public function showApplicantDetailsCommon(Request $request)
  {
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }

    $approveBtnvisible = 1;
    $verifyBtnvisible = 0;
    $user_id = AuthChecker::getUserId();
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->id;
    $scheme_id = $request->scheme_id;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $condition_arr = array();
    $condition_arr['id'] = $id;
    if ($duty_obj->mapping_level == "Department") {
      $created_by_local_body_code = NULL;
      $created_by_dist_code = NULL;
    } else {
      $condition_arr['created_by_dist_code'] = $duty_obj->district_code;
      $created_by_dist_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "District") {
        $created_by_local_body_code = NULL;
      }
    }
    $row = DB::table($schema . '.beneficiaries')
      ->where($condition_arr)->first();
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $is_state_login = 0;
    $district_state_name = '';
    $urban_code_state_name = '';
    $block_subdiv_state_name = '';



    $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $created_by_dist_code)->orderBy('document_type')->get();

    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
    $block_name = "";
    if ($row->block_ulb_code != "") {
      if ($row->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
        if (!empty($block)) {
          $block_name = $block->urban_body_name;
        }
      } else {
        if (!empty($row->block_ulb_code)) {
          $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
          if (!empty($block)) {
            $block_name = $block->block_name;
          } else {
            $block_name = '';
          }
        } else {
          $block_name = '';
        }
      }
    }
    $row->block_name = $block_name;
    $gp_name = "";
    if ($row->gp_ward_code != "") {
      if ($row->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
        if (!empty($gp_ward)) {
          $gp_name = $gp_ward->urban_body_ward_name;
        }
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        if (!empty($gp)) {
          $gp_name = $gp->gram_panchyat_name;
        }
      }
    }
    $row->gp_name = $gp_name;

    $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }
    $scheme_capacity_arr = array();
    $scheme_capacity_arr['visible'] = 0;
    $is_dup_msg = array();
    if ($row->dup_bank == 1) {
      array_push($is_dup_msg, 'Duplicate Bank Account Number..');
      $approveBtnvisible = 0;
    }
    if ($row->dup_aadhar == 1) {
      array_push($is_dup_msg, 'Duplicate Aadhaar Number.');
      $approveBtnvisible = 0;
    }
    if ($row->dup_mobile == 1) {
      array_push($is_dup_msg, 'Duplicate Mobile Number.');
      $approveBtnvisible = 0;
    }
    if ($row->no_aadhar == 1) {
      array_push($is_dup_msg, 'Aadhaar Number Incorrect.');
      $approveBtnvisible = 0;
    }
    if ($row->no_mobile == 1) {
      array_push($is_dup_msg, 'Mobile Number Incorrect.');
      $approveBtnvisible = 0;
    }
    //dd($is_dup_msg);
    if ($scheme_id == 13)
      return view('farmer/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg
      ]);
    else if ($scheme_id == 5)
      return view('fisherman/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg
      ]);
    else if ($scheme_id == 2) {

      return view('MANABIKWCD.pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    } else if ($scheme_id == 10) {

      return view('OAPWCD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    } else if ($scheme_id == 11) {
      // dd($reject_revert_cause_list);
      return view('WPWCD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    } else if ($scheme_id == 6)
      return view('msme/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    else if ($scheme_id == 7) {
      return view('textile/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    } else if ($scheme_id == 17) {
      $duty = Configduty::where('user_id', '=', Auth::user()->id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty->mapping_level)->first();

      return view('PurohitICAD/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'parent_id' => $role->parent_id,
        'scheme_id' => $scheme_id,
        'housingrecord' => $housingrecord,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
    }
    // else if($scheme_id== $this->monthlySchemeCode){
    //   return view('PurohitICAD/pension_view_details', [
    //     'scheme_id' => $scheme_id,
    //     'monthlySchemeCode' => $this->monthlySchemeCode,
    //     'housingSchemeCode' =>  $this->housingSchemeCode,
    //     'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // }
    // else if($scheme_id==$this->housingSchemeCode){
    //   return view('PurohitICAD/pension_view_details', ['scheme_id' => $scheme_id,
    //   'monthlySchemeCode' => $this->monthlySchemeCode,
    //   'housingSchemeCode' =>  $this->housingSchemeCode,
    //   'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // }
    else
      return view('pension_view_details', [
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg

      ]);
  }
}
