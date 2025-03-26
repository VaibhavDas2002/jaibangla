<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use App\Helpers\AuthChecker;

class CommonReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(200);
    date_default_timezone_set('Asia/Kolkata');
  }

  /* Report For WCD OAP Different Age De-activated Case */
  public function wcdAgeDiffStopPaymentIndex(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 and scheme_id in(2,10,11))"));
    return view('common_report.wcd_age_stop_payment_report', ['report' => $schemes]);
  }
  public function wcdAgeDiffStopPaymentGetData(Request $request)
  {
    $selectscheme = $request->selectscheme;
    $selectyear = $request->selectyear;
    $schemeObj = Scheme::where('id', $selectscheme)->first();
    $tablename = $schemeObj->short_code . '.beneficiary';
    $query = "";
    if ($selectscheme == 10) {
      $query = "select m.district_name,
      sum(case when (extract(year from current_date)-extract(year from dob))<60  then 1  else 0 end) as age_below_60,
      sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
      sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
      sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
      sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
      sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
      from " . $tablename . " op join m_district m on m.district_code=op.created_by_dist_code where next_level_role_id=0 group by m.district_name order by m.district_name";
      $data = DB::select($query);
      return view('common_report/oap_age_cohort_report', ['result' => $data]);
    } else if ($selectscheme == 11) {
      $query = "select m.district_name,
      sum(case when (extract(year from current_date)-extract(year from dob))<20  then 1  else 0 end) as age_below_20,
      sum(case when (extract(year from current_date)-extract(year from dob))>=20 and (extract(year from current_date)-extract(year from dob))<30 then 1  else 0 end) as age_20_30,
      sum(case when (extract(year from current_date)-extract(year from dob))>=30 and (extract(year from current_date)-extract(year from dob))<40 then 1  else 0 end) as age_30_40,
      sum(case when (extract(year from current_date)-extract(year from dob))>=40 and (extract(year from current_date)-extract(year from dob))<50 then 1  else 0 end) as age_40_50,
      sum(case when (extract(year from current_date)-extract(year from dob))>=50 and (extract(year from current_date)-extract(year from dob))<60 then 1  else 0 end) as age_50_60,
      sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
      sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
      sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
      sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
      sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
      from wp_wcd.beneficiary b join m_district m on m.district_code=b.created_by_dist_code where next_level_role_id=0 group by m.district_name order by m.district_name";
      $data = DB::select($query);
      return view('common_report/wp_age_cohort_report', ['result' => $data]);
    } else if ($selectscheme == 2) {
      $query = "select m.district_name,
      sum(case when (extract(year from current_date)-extract(year from dob))<10  then 1  else 0 end) as age_below_10,
      sum(case when (extract(year from current_date)-extract(year from dob))>=10 and (extract(year from current_date)-extract(year from dob))<20 then 1  else 0 end) as age_10_20,
      sum(case when (extract(year from current_date)-extract(year from dob))>=20 and (extract(year from current_date)-extract(year from dob))<30 then 1  else 0 end) as age_20_30,
      sum(case when (extract(year from current_date)-extract(year from dob))>=30 and (extract(year from current_date)-extract(year from dob))<40 then 1  else 0 end) as age_30_40,
      sum(case when (extract(year from current_date)-extract(year from dob))>=40 and (extract(year from current_date)-extract(year from dob))<50 then 1  else 0 end) as age_40_50,
      sum(case when (extract(year from current_date)-extract(year from dob))>=50 and (extract(year from current_date)-extract(year from dob))<60 then 1  else 0 end) as age_50_60,
      sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
      sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
      sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
      sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
      sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
      from manabik.beneficiary b join m_district m on m.district_code=b.created_by_dist_code where next_level_role_id=0 group by m.district_name order by m.district_name";
      $data = DB::select($query);
      return view('common_report/manabik_age_cohort_report', ['result' => $data]);
    }
  }
  public function wcdStopPaymentReport(Request $request)
  {
    if ($request->ajax()) {
      $selectscheme = $request->selectscheme1;
      $selectyear = $request->selectyear;
      $schemeObj = Scheme::where('id', $selectscheme)->first();
      $tablename = $schemeObj->short_code . '.beneficiary';
      $query = "";
      $query = "select m.district_name,count(1) as total from " . $tablename . " op join m_district m on m.district_code=op.created_by_dist_code
        where is_rejected=1 and next_level_role_id<>-97 group by m.district_name order by m.district_name";
      $data = DB::connection('pgsql_mis')->select($query);
      return datatables()->of($data)
        ->make(true);
    }
  }
  function viewEncloser(Request $request)
  {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    try {
      $created_by_dist_code = $request->created_by_dist_code;
      $beneficiary_id = $request->beneficiary_id;
      $doc_type_id = $request->document_type;
      $scheme_id = $request->scheme_id;
      if (empty($created_by_dist_code)) {
        $return_text = 'District Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($created_by_dist_code)) {
        $return_text = 'District Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (empty($scheme_id)) {
        $return_text = 'Scheme Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($scheme_id)) {
        $return_text = 'Scheme Id Not Valid';
        // return redirect("/")->with('error',  $return_text);
      }
      if (empty($beneficiary_id)) {
        $return_text = 'Beneficiary Id Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($beneficiary_id)) {
        $return_text = 'Beneficiary Id Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (empty($doc_type_id)) {
        $return_text = 'Doc Type Id Parameter Not Valid';
        //  return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($doc_type_id)) {
        $return_text = 'Doc Type Id Not Valid';
        // return redirect("/")->with('error',  $return_text);
      }
      //dd($return_text);
      $user_id = AuthChecker::getUserId();
      $designation_id = Auth::user()->designation_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        $return_text = 'Scheme Not Valid';
        return redirect("/")->with('error', $return_text);
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        $return_text = 'Not Allowed';
        return redirect("/")->with('error', $return_text);
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $condition = array();
      $condition['beneficiary_id'] = $beneficiary_id;
      $condition['created_by_dist_code'] = $created_by_dist_code;
      $condition['document_type'] = $doc_type_id;
      if ($designation_id == 'Verifier') {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        //$condition['created_by_local_body_code'] = $created_by_local_body_code;
      }
      $doc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where($condition)->first();
      //dd($doc);

      if (!empty($doc)) {
       
        $mime_type = $doc->document_mime_type;
        $file_extension = $doc->document_extension;
        if ($file_extension != 'png' && $file_extension != 'jpg' && $file_extension != 'jpeg' && $file_extension != 'pdf') {
            if ($mime_type == 'image/png') {
                $file_extension = 'png';
            } else if ($mime_type == 'image/jpeg') {
                $file_extension = 'jpg';
            } else if ($mime_type == 'application/pdf') {
                $file_extension = 'pdf';
            }
        }
        try {
            if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
                $resultimg = str_replace("data:image/" . $file_extension . ";base64,", "", $doc->attched_document);
                //dd($resultimg);
                $file_name = $doc->document_type . '_' . $doc->beneficiary_id;

                header('Content-Disposition: attachment;filename="' . $file_name . '.' . $file_extension . '"');
                header('Content-Type: ' . $mime_type);
                ob_clean();
                echo base64_decode($resultimg);
            } else if (strtoupper($file_extension) == 'PDF') {
                $decoded = base64_decode($doc->attched_document);
                $file_name = $doc->document_type . '_' . $doc->beneficiary_id . '.pdf';
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename=' . $file_name);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . strlen($decoded));
                ob_clean();
                flush();
                echo $decoded;
                exit;
            }
        } catch (\Exception $e) {
            $return_text = 'Some error. please try again.';
            return redirect("/")->with('error',  $return_text);
        }

      } else {
        $return_text = 'File Not Found';
        return redirect("/")->with('error', $return_text);
      }
    } catch (\Exception $e) {
      dd($e);
      //return redirect("/")->with('error',  'Some error.please try again ......');
    }



  }
}
