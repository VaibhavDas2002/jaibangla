<?php

namespace App\Helpers;

use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\DB;
use App\SchemeGenSetting;

class PermissionManagement
{
    /**
     * Check entry permissions for a given scheme.
     *
     * @param int $scheme_id
     * @return array|bool
     */
    public static function EntryChecker($scheme_id)
    {

        $entry = DB::table('m_scheme_gen_setting')
            ->where('scheme_id', $scheme_id)
            ->value('allow_entry');

        if ($entry) {
            return true;
        }

        return false;
    }

    public static function VerifyCheker($scheme_id)
    {
        if (AuthChecker::VerifierPermission()) {
            $verify = DB::table('m_scheme_gen_setting')
                ->where('scheme_id', $scheme_id)
                ->value('allow_verify');
            if ($verify) {
                return true;
            }
        }
        return false;
    }
    public static function ApproveCheker($scheme_id)
    {
        if (AuthChecker::ApproverPermission()) {
            $verify = DB::table('m_scheme_gen_setting')
                ->where('scheme_id', $scheme_id)
                ->value('allow_approve');
            if ($verify) {
                return true;
            }
        }
        return false;
    }

    public static function CmoCheck($scheme_id)
    {
        $verify = DB::table('m_scheme_gen_setting')
            ->where('scheme_id', $scheme_id)
            ->value('allow_cmo');
        if ($verify) {
            return true;
        }
        return false;
    }
    public static function DsEntryCheker($scheme_id)
    {
        $ds_entry = DB::table('m_scheme_gen_setting')
            ->where('scheme_id', $scheme_id)
            ->value('allow_ds_entry');

        if ($ds_entry) {
            return true;
        }
        return false;
    }
    public static function Dsmarking($scheme_id)
    {

        $entry = DB::table('m_scheme_gen_setting')
            ->where('scheme_id', $scheme_id)
            ->value('ds_marking');

        if ($entry) {
            return true;
        }

        return false;
    }


    public static function CMOmarking($scheme_id)
    {

        $entry = DB::table('m_scheme_gen_setting')
            ->where('scheme_id', $scheme_id)
            ->value('cmo_marking');

        if ($entry) {
            return true;
        }

        return false;
    }


}
