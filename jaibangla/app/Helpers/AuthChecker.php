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
        if ($designation_id === 13) {
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
        if ($designation_id === 14) {
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
        if ($designation_id === 15) {
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
        if ($designation_id === 17) {
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
        if ($designation_id === 12) {
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
        if ($designation_id === 17) {
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

    public static function getUserId()
    {
        $user = Auth::user();
        return $user->id;
    }

    public static function getDesignationId()
    {
        $designation_id = Auth::user()->designation_id_old;
        return $designation_id;
    }
    public static function getDesignation()
    {
        $designation_id = Auth::user()->designation_id;

        if (in_array($designation_id, [13, 24])) {
            return 'Operator';
        } elseif (in_array($designation_id, [14, 25])) {
            return 'Verifier';
        } elseif (in_array($designation_id, [15, 26])) {
            return 'Approver';
        } elseif (in_array($designation_id, [17])) {
            return 'HOD';
        } elseif (in_array($designation_id, [12])) {
            return 'Admin';
        } elseif (in_array($designation_id, [18])) {
            return 'DDO';
        } else {
            return null;
        }
    }

}

