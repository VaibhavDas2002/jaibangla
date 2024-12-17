<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use Config;
use Maatwebsite\Excel\Facades\Excel;

class DuplicateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export_excel(Request $request)
    {
        $scheme_code = $request->scheme_code;
        if ($scheme_code == 10 || $scheme_code == 11) {
            $district_code = NULL;
            $roleArray = $request->session()->get('role');
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 10) {
                    $district_code = $roleObj['district_code'];
                    break;
                }
            }
            if (empty($district_code)) {
                foreach ($roleArray as $roleObj) {
                    if ($roleObj['scheme_id'] == 11) {
                        $district_code = $roleObj['district_code'];
                        break;
                    }
                }
            }
            if (empty($district_code)) {
                return redirect("/")->with('success', 'User Disabled');
            } else {
                $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
                $scheme_name_row = Scheme::where('id', $scheme_code)->select('scheme_name', 'short_code')->first();
                $scheme_schema_name = $scheme_name_row->short_code;
                $title = $district_name . "_" . $scheme_name_row->scheme_name . "_Duplicates";
                $data = array();
                $query = "select block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
                ,id as \"Beneficiary_Id\"
                , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
                , b.bank_code  as \"Account_No\"
                , b.bank_ifsc as \"IFSC\"
                , case when b.legacy_import=true then 'Brief' else 'Normal' end \"DataEntryMode\"
                , case when next_level_role_id=0  then 'Approved' 
                  when (is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id is null then 'Not Yet Approved' end \"Approved_NotYetApproved\"
                from " . $scheme_schema_name . ".beneficiary b inner join
                (
                select bank_code,bank_ifsc from " . $scheme_schema_name . ".beneficiary
                where dist_code=" . $district_code . " and ((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id is null)
                and lot_generated=0 and payment_count=0
                group by bank_code,bank_ifsc
                having count(1)>1
                )d on b.bank_code=d.bank_code and b.bank_ifsc=d.bank_ifsc
                where dist_code=" . $district_code . " and ((b.is_verified=1 and b.is_approved=0 and b.is_rejected=0) or b.next_level_role_id=0 or b.next_level_role_id is null) 
                and lot_generated=0 and payment_count=0
                order by block_ulb_name,\"Account_No\"";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data = array_merge($data, $data_part);
                //dd($data);
                $excel_data[] = array('Block/Municipality', 'Beneficiary_Id', 'Name', 'Account_No', 'IFSC', 'DataEntryMode', 'Approved/NotYetApproved');

                foreach ($data as $row) {
                    $excel_data[] = array(
                        'Block/Municipality'  => $row->Block_Municipality,
                        'Beneficiary_Id'  => $row->Beneficiary_Id,
                        'Name'  => $row->Name,
                        'Account_No'  => $row->Account_No,
                        'IFSC'  => $row->IFSC,
                        'DataEntryMode'  => $row->DataEntryMode,
                        'Approved/NotYetApproved'  => $row->Approved_NotYetApproved
                    );
                }

                $scheme_name = $scheme_name_row->scheme_name;
                Excel::create('' . $title, function ($excel) use ($excel_data, $title, $scheme_name) {
                    $excel->setTitle('' . $title);
                    $excel->sheet('' . $scheme_name, function ($sheet) use ($excel_data) {
                        $sheet->fromArray($excel_data, null, 'A1', false, false);
                    });
                })->download('xlsx');
            }
        } else
            return redirect("/")->with('success', 'Scheme Not Valid');
    }
}
