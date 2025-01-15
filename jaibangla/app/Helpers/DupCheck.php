<?php


namespace App\Helpers;
use App\LotGenerationFunctionMaster;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\SchemeCapacity;
use App\Scheme;
use App\SchemeConfig;
use App\NextLevelRoleId;
use App\BlkUrbanlEntryMapping;
use App\BenEntry;
use phpDocumentor\Reflection\Types\Integer;

class DupCheck
{
    public static function getDupCheckBank($scheme_id, $bank_code)
    {
        if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 1 || $scheme_id == 3) {
            try {
                $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
                $schema = $scheme_obj->short_code;
                $duplicate_bank = DB::select("select id from " . $schema . ".beneficiaries where (next_level_role_id = 0 or next_level_role_id > 0 or next_level_role_id is null) and trim(bank_code)='" . $bank_code . "'");
                if (!empty($duplicate_bank)) {
                    $beneficiary_id = $duplicate_bank[0]->id;
                    return $beneficiary_id;
                }
            } catch (\Exception $e) {
                // dd($e);
                return redirect("/")->with('error', 'Something Went Wrong!!');
            }
        }
        if ($scheme_id == 20) {
            $serverip = 'http://172.25.154.28';
            $post_url = $serverip . '/api/dupCheckBankLb';
            $curl = curl_init($post_url);
            $headers = array(
                'Content-Type: application/json'
            );
            $data = array("bank_code" => $bank_code, "scheme_id" => $scheme_id);
            $data_string = json_encode($data);
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            // dd($post_response);
            if ($post_response) {
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if ($httpcode == 200) {
                    $post_response_lb = json_decode($post_response);
                    $is_dup = $post_response_lb->is_dup;
                    if ($is_dup == 1) {
                        $beneficiary_id = $post_response_lb->application_id;
                        return $beneficiary_id;
                    }
                }
            }
        }
    }
    public static function getDupCheckAadhar($scheme_id, $aadhar_no)
    {
        if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 1 || $scheme_id == 3) {
            try {
                $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
                $schema = $scheme_obj->short_code;
                $duplicate_aadhar = DB::select("select id from " . $schema . ".beneficiaries where (next_level_role_id = 0 or next_level_role_id > 0 or next_level_role_id is null) and trim(aadhar_no)='" . $aadhar_no . "'");
                if (!empty($duplicate_aadhar)) {
                    $beneficiary_id = $duplicate_aadhar[0]->id;
                    return $beneficiary_id;
                }
            } catch (\Exception $e) {
                return redirect("/")->with('error', 'Something Went Wrong!!');
            }
        }
        if ($scheme_id == 20) {
            $serverip = 'http://172.25.154.28';
            $post_url = $serverip . '/api/dupCheckAadharLb';
            $curl = curl_init($post_url);
            $headers = array(
                'Content-Type: application/json'
            );
            $data = array("aadhar_no" => $aadhar_no, "scheme_id" => $scheme_id);
            $data_string = json_encode($data);
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            // dd($post_response);
            if ($post_response) {
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if ($httpcode == 200) {
                    $post_response_lb = json_decode($post_response);
                    // dd($post_response_lb);
                    $is_dup = $post_response_lb->is_dup;
                    if ($is_dup == 1) {
                        $beneficiary_id = $post_response_lb->application_id;
                        return $beneficiary_id;
                    }
                }
            }
        }
    }

    public static function dupBankCheck($bank_code)
    {

        $entry = BenEntry::where('bank_code', $bank_code)->where('is_clean', 1)
            ->first();

        if ($entry) {
            return true;
        } else {
            return false;
        }
    }
    public static function dupAadharCheck($aadhar_no)
    {
        $entry = BenEntry::where('aadhar_no', $aadhar_no)->where('is_clean', 1)
            ->first();
        if ($entry) {
            return true;
        } else {
            return false;
        }
    }
    public static function dupMobileCheck($mobile_no)
    {
        $entry = BenEntry::where('mobile_no', $mobile_no)->where('is_clean', 1)
            ->first();
        if ($entry) {
            return true;
        } else {
            return false;
        }
    }
    public static function dupCasteCheck($caste_certificate_no)
    {
        $entry = BenEntry::where('caste_certificate_no', $caste_certificate_no)->where('is_clean', 1)
            ->first();
        if ($entry) {
            return true;
        } else {
            return false;
        }
    }

    public static function dupBankCheckSame($scheme_id, $bank_code, $id = null)
    {
        $is_normal = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', false)
            ->where('field_type', 4)
            ->first();
        if ($is_normal) {
            if ($id === null) {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('bank_code', $bank_code)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('bank_code', $bank_code)
                    ->where('id', '!=', $id)
                    ->first();

                return $entry !== null;
            }


        }
    }

    public static function dupAadharCheckSame($scheme_id, $aadhar_no, $id = null)
    {
        $is_normal = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', false)
            ->where('field_type', 1)
            ->first();
        if ($is_normal) {
            if ($id === null) {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('aadhar_no', $aadhar_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('aadhar_no', $aadhar_no)
                    ->where('id', '!=', $id)
                    ->first();

                return $entry !== null;
            }
        }
    }

    public static function dupMobileCheckSame($scheme_id, $mobile_no, $id = null)
    {
        $is_normal = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', false)
            ->where('field_type', 2)
            ->first();
        if ($is_normal) {
            if ($id === null) {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('mobile_no', $mobile_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('mobile_no', $mobile_no)
                    ->where('id', '!=', $id)
                    ->first();

                return $entry !== null;
            }
        }
    }

    public static function dupCasteCheckSame($scheme_id, $catse_certificate_no, $id = null)
    {
        $is_normal = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', false)
            ->where('field_type', 3)
            ->first();
        if ($is_normal) {
            if ($id === null) {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('caste_certificate_no', $catse_certificate_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::where('scheme_id', $scheme_id)
                    ->where('caste_certificate_no', $catse_certificate_no)
                    ->where('id', '!=', $id)
                    ->first();

                return $entry !== null;
            }
        }
    }




    // 
    public static function dupBankCheckCross($scheme_id, $bank_code, $id = null)
    {
        $is_cross = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', true)
            ->where('field_type', 4)
            ->first();

        if ($is_cross) {
            $cross_schemes = $is_cross->cross_scheme;
            $cross = explode(',', trim($cross_schemes, '{}'));
            $cross_s = [];
            foreach ($cross as $scheme)
                array_push($cross_s, (int) $scheme);
            if ($id === null) {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('bank_code', $bank_code)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('bank_code', $bank_code)
                    ->where('id', '!=', $id)
                    ->first();
                return $entry !== null;
            }

        } else {
            return false;
        }

    }

    public static function dupAadharCheckCross($scheme_id, $aadhar_no, $id = null)
    {
        $is_cross = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', true)
            ->where('field_type', 1)
            ->first();

        if ($is_cross) {
            $cross_schemes = $is_cross->cross_scheme;
            $cross = explode(',', trim($cross_schemes, '{}'));
            $cross_s = [];
            foreach ($cross as $scheme)
                array_push($cross_s, (int) $scheme);
            if ($id === null) {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('aadhar_no', $aadhar_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('aadhar_no', $aadhar_no)
                    ->where('id', '!=', $id)
                    ->first();
                return $entry !== null;
            }

        } else {
            return false;
        }
    }

    public static function dupMobileCheckCross($scheme_id, $mobile_no, $id = null)
    {
        $is_cross = SchemeConfig::where('scheme_id', $scheme_id)
        ->where('is_cross', true)
        ->where('field_type', 2)
        ->first();

        if ($is_cross) {
            $cross_schemes = $is_cross->cross_scheme;
            $cross = explode(',', trim($cross_schemes, '{}'));
            $cross_s = [];
            foreach ($cross as $scheme)
                array_push($cross_s, (int) $scheme);
            if ($id === null) {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('mobile_no', $mobile_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('mobile_no', $mobile_no)
                    ->where('id', '!=', $id)
                    ->first();
                return $entry !== null;
            }

        } else {
            return false;
        }
    }


    public static function dupCasteCheckCross($scheme_id, $catse_certificate_no, $id = null)
    {
        $is_cross = SchemeConfig::where('scheme_id', $scheme_id)
            ->where('is_cross', true)
            ->where('field_type', 3)
            ->first();

        if ($is_cross) {
            $cross_schemes = $is_cross->cross_scheme;
            $cross = explode(',', trim($cross_schemes, '{}'));
            $cross_s = [];
            foreach ($cross as $scheme)
                array_push($cross_s, (int) $scheme);
            if ($id === null) {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('caste_certificate_no', $catse_certificate_no)
                    ->first();

                return $entry !== null;
            } else {
                $entry = BenEntry::wherein('scheme_id', $cross_s)
                    ->where('caste_certificate_no', $catse_certificate_no)
                    ->where('id', '!=', $id)
                    ->first();
                return $entry !== null;
            }

        } else {
            return false;
        }
    }




}





