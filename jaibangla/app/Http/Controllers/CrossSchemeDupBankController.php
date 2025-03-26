<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AuthChecker;
use App\Configduty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Scheme;
use App\SchemeConfig;
use App\SchemeGenSetting;

class CrossSchemeDupBankController extends Controller
{
  public function __construct()
  {
    set_time_limit(120);
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
  }

  public function ApproverIndex(Request $request)
  {
    $auth = AuthChecker::ApproverPermission();
    if ($auth) {

      $user_id = Auth::user()->id;
      $schemes = DB::select(DB::raw('SELECT id,scheme_name FROM m_scheme where id in (select scheme_id from duty_assignement where user_id = ' . $user_id . ')'));
      $levels = [
        2 => 'Rural',
        1 => 'Urban'
      ];
      // dd($schemes);

      $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect()->route('/')->with('danger', 'Not Allowded');
      }
      $dist_code = $duty_obj ? $duty_obj->district_code : null;

      return view(
        'CrossScheme.Approver-index',
        [
          'schemes' => $schemes,
          'levels' => $levels,
          'dist_code' => $dist_code
        ]
      );
    } else {
      return redirect()->route('/')->with('danger', 'Not Allowded');
    }
  }

  public function crossSchemeListAjax(Request $request)
  {
    // dd($request->all());
    try {
      $scheme_id = $request->scheme_id;

      if (empty($scheme_id)) {
        return $response = [
          'status' => 1,
          'msg' => 'No Beneficiary Found.',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!',
        ];
      } else {
        $scheme_details = SchemeGenSetting::where('scheme_id', $scheme_id)->where('is_cross', 1)->first();
        if ($scheme_details) {
          $cross_scheme = json_decode($scheme_details->cross_scheme, true); // Decode JSONB into an array
          if ($cross_scheme == null) {
            $cross_scheme = [];
          } else {
            $cross_scheme = Scheme::whereIn('id', $cross_scheme)->get();
            $response = [
              'status' => 1,
              'cross_scheme' => $cross_scheme,
            ];
            $statusCode = 200;
          }

        } else {
          $response = [
            'status' => 1,
            'msg' => 'No Cross Scheme Found.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!',
          ];
          $statusCode = 200;
        }

        //  $cross_scheme = $scheme_details->cross_scheme;
        //  dd($cross_scheme);
      }
    } catch (\Exception $e) {
      dd($e);
      $response = [
        'exception' => true,
        'exception_message' => $e->getMessage(),
      ];
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }

  }

  public function crossSchemeDupListAjax(Request $request)
  {
    try {
      $cross_scheme_id = $request->cross_scheme;
      $scheme_id = $request->scheme;
      $rural_urban_id = $request->rural_urban_id;
      $block_ulb_code = $request->block_ulb_code;
      $aadhar_filter = (int) $request->aadhar_filter;
      $scheme = Scheme::where('id', $scheme_id)->first();
      $cross_schemes = Scheme::whereIn('id', $cross_scheme_id)->get();
      // Convert multiple cross-scheme names into a string
      $cross_scheme_names = $cross_schemes->pluck('scheme_name')->implode(' & ');

      $schemeNames = "{$scheme->scheme_name} & {$cross_scheme_names}";
      $user_msg = 'Cross Scheme Bank Duplicate List of ' . $schemeNames;
      $query = $this->getBankDupRows($cross_scheme_id, $scheme_id, $aadhar_filter, $block_ulb_code);

      dd($query);
      $result = DB::connection('pgsql_mis')->select($query);
      return datatables()->of($result)
        ->addColumn('aadhar_no', function ($result) {
          $mask_aadhar = '';
          $aadhar = trim($result->aadhar_no);
          if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
            $mask_aadhar = '********' . substr($aadhar, 8, 4);
          } else {
            $mask_aadhar = $aadhar;
          }
          return $mask_aadhar;
        })
        ->addColumn('payment_status', function ($result) {
          $html = '';
          if ($result->is_pause_resume_reject == 1) {
            $html = '<span class="text-danger"><b>Payment Autostopped</b></span>';
          }
          if ($result->is_pause_resume_reject == -99) {
            $html = '<span class="text-danger"><b>Rejected</b></span>';
          }
          return $html;
        })
        ->addColumn('action', function ($result) {
          $action = '<div style="display: flex; gap: 5px;">';
          if ($result->is_pause_resume_reject >= 0) {
            $action .= '<button class="btn btn-danger btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_1"><i class="glyphicon glyphicon-edit"></i>Reject</button>';
          }
          if ($result->is_pause_resume_reject == 1) {
            $action .= '<button class="btn btn-primary btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_2"><i class="glyphicon glyphicon-edit"></i>Resume</button>';
          }
          if ($result->is_pause_resume_reject == 0) {
            $action .= '<button class="btn btn-info btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_3"><i class="glyphicon glyphicon-edit"></i>Pause</button>';
          }
          $action .= '</div>';
          return $action;
        })
        ->rawColumns(['aadhar_no', 'payment_status', 'action'])
        ->make(true);
    } catch (\Exception $e) {
      dd($e);
    }
  }


  public function getBankDupRows($cross_scheme_id, $scheme_id, $aadhar_filter, $blk_ulb_code = null)
  {
    
    try {
      $user_id = Auth::user()->id;
      $duty_obj = Configduty::where('user_id', $user_id)->first();
      $district_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == 'Subdiv') {
        $blk_Ulb_code = $duty_obj->urban_body_code;
      } elseif ($duty_obj->mapping_level == 'Block') {
        $blk_Ulb_code = $duty_obj->block_code;
      } else {
        $blk_Ulb_code = null;
      }

      $local_body_code = $blk_Ulb_code ? " AND created_by_local_body_code = $blk_Ulb_code" : '';

      // Convert array to comma-separated string for SQL
      $merge_scheme = implode(',', [$scheme_id, $cross_scheme_id]);

      // dd($aadhar_filter);
      if ($aadhar_filter == 1) {
        $data = "SELECT d.district_name, scheme_name, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, scheme_id, is_pause_resume_reject 
                 FROM (
                     SELECT dist_code, scheme_id, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, is_pause_resume_reject 
                     FROM data_check.cross_scheme_bank_details 
                     WHERE bank_code IN (
                         SELECT bank_code FROM data_check.cross_scheme_bank_details 
                         WHERE bank_code IN (
                             SELECT bank_code FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id = $scheme_id AND next_level_role_id = 0
                             INTERSECT
                             SELECT bank_code FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id = $cross_scheme_id
                         ) 
                         AND scheme_id IN ($merge_scheme)
                     ) 
                     AND scheme_id IN ($merge_scheme) 
                     AND cross_bank_dup = 1 
                     AND cross_bank_aadhar_dup = 0 
                     $local_body_code 
                     ORDER BY bank_code
                 ) a
                 JOIN (SELECT scheme_name, id FROM m_scheme) b ON a.scheme_id = b.id
                 JOIN (SELECT district_code, district_name FROM m_district) d ON d.district_code = a.dist_code";
      } elseif ($aadhar_filter == 2) {
        $data = "SELECT d.district_name, scheme_name, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, scheme_id, is_pause_resume_reject 
                 FROM (
                     SELECT dist_code, scheme_id, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, is_pause_resume_reject 
                     FROM data_check.cross_scheme_bank_details 
                     WHERE bank_code IN (
                         SELECT bank_code FROM data_check.cross_scheme_bank_details 
                         WHERE bank_code IN (
                             SELECT bank_code FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id = $scheme_id AND next_level_role_id = 0
                             INTERSECT
                             SELECT bank_code FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id in $cross_scheme_id
                         ) 
                         AND aadhar_no IN (
                             SELECT aadhar_no FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id = $scheme_id AND next_level_role_id = 0
                             INTERSECT
                             SELECT aadhar_no FROM data_check.cross_scheme_bank_details 
                             WHERE scheme_id in $cross_scheme_id 
                         ) 
                         AND scheme_id IN ($merge_scheme)
                     ) 
                     AND scheme_id IN ($merge_scheme) 
                     AND cross_bank_aadhar_dup = 1 
                     $local_body_code 
                     ORDER BY bank_code
                 ) a
                 JOIN (SELECT scheme_name, id FROM m_scheme) b ON a.scheme_id = b.id
                 JOIN (SELECT district_code, district_name FROM m_district) d ON d.district_code = a.dist_code";
      }

      // dd($data);

      return $data;
    } catch (\Exception $e) {
      dd($e);
    }
  }


}
