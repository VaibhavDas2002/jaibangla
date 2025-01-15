<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Scheme;

class AuthChecker
{
    /**
     * Checks and processes the operator's data or permissions.
     *
     * @return mixed
     */
    public static function OperatorChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Operator') {
            return true;
        } else {
            return false;
        }
    }

    public static function DelegatedOperatorChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'DelegatedOperator') {
            return true;
        } else {
            return false;
        }
    }


    public static function VerifierChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Verifier') {
            return true;
        } else {
            return false;
        }
    }

    public static function DelegatedVerifierChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'DelegatedVerifier') {
            return true;
        } else {
            return false;
        }
    }

    public static function ApproverChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Approver') {
            return true;
        } else {
            return false;
        }
    }

    public static function DelegatedApproverChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'DelegatedApprover') {
            return true;
        } else {
            return false;
        }
    }

    public static function HODChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'HOD') {
            return true;
        } else {
            return false;
        }
    }

    public static function AdminChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Admin') {
            return true;
        } else {
            return false;
        }
    }
    public static function HOPChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'HOP') {
            return true;
        } else {
            return false;
        }
    }
    public static function CorpChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Corp') {
            return true;
        } else {
            return false;
        }
    }
    public static function SPDashboardChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'SPDashboard') {
            return true;
        } else {
            return false;
        }
    }
    public static function SPNodalChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'SPNodal') {
            return true;
        } else {
            return false;
        }
    }

    public static function DDOChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'DDO') {
            return true;
        } else {
            return false;
        }
    }


    public static function StatusCheckerFieldChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'StatusCheckerField') {
            return true;
        } else {
            return false;
        }
    }

    public static function StatusCheckerDistrictChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'StatusCheckerDistrict') {
            return true;
        } else {
            return false;
        }
    }
    public static function DashboardChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'Dashboard') {
            return true;
        } else {
            return false;
        }
    }
    public static function MisStateChecker()
    {
        $user = Auth::user();
        if (empty($user)) {
            return redirect('/login');
        }
        $designation_id = $user->designation_id;
        if ($designation_id === 'MisState') {
            return true;
        } else {
            return false;
        }
    }


    public static function ReportChecker()
    {
        // Fixed logic to call the static methods properly using `self::`
        if (self::OperatorChecker() || self::VerifierChecker() || self::ApproverChecker()) {
            return true;
        } else {
            return false;
        }
    }
    public static function ReportCheckerCommon()
    {
        // Fixed logic to call the static methods properly using `self::`
        if (self::OperatorChecker() || self::VerifierChecker() || self::ApproverChecker() || self::HODChecker() || self::AdminChecker() || self::HOPChecker()) {
            return true;
        } else {
            return false;
        }
    }
    public static function WorkflowChecker()
    {
        if (self::OperatorChecker() || self::VerifierChecker() || self::ApproverChecker()) {
            return true;
        } else {
            return false;
        }
    }
    public static function getUserId()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->id;
        } else {
            return redirect('/login');
        }
    }

    // public static function getDesignationId()
    // {

    //     $designation_id = Auth::user()->designation_id;
    //     return $designation_id;
    // }
    // public static function getDesignation()
    // {
    //     $designation_id = Auth::user()->designation_id;

    //     if (in_array($designation_id, [13, 24])) {
    //         return 'Operator';
    //     } elseif (in_array($designation_id, [14, 25])) {
    //         return 'Verifier';
    //     } elseif (in_array($designation_id, [15, 26])) {
    //         return 'Approver';
    //     } elseif (in_array($designation_id, [17])) {
    //         return 'HOD';
    //     } elseif (in_array($designation_id, [12])) {
    //         return 'Admin';
    //     } elseif (in_array($designation_id, [18])) {
    //         return 'DDO';
    //     } else {
    //         return null;
    //     }
    // }


    public static function OperatorPermission()
    {
        if (self::OperatorChecker() || self::DelegatedOperatorChecker())
            return true;
        else
            return false;
    }

    public static function VerifierPermission()
    {
        if (self::DelegatedVerifierChecker() || self::VerifierChecker())
            return true;
        else
            return false;
    }

    public static function ApproverPermission()
    {
        if (self::DelegatedApproverChecker() || self::ApproverChecker())
            return true;
        else
            return false;
    }

}

