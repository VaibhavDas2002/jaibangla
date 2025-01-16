<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Scheme;
use App\DsPhase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Helpers\AuthChecker;
class SchemeGenSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $auth = AuthChecker::AdminChecker();
        if ($auth) {
            $scheme_gen_id = DB::table('m_scheme_gen_setting')->pluck('scheme_id')->toArray(); // Get all IDs as an array

            $schemes = Scheme::where('is_active', 1)
                ->whereNotIn('id', $scheme_gen_id) // Specify the column name for comparison
                ->orderBy('id')
                ->get();

            $ds_phases = DsPhase::all(); // Use `all()` for better readability if fetching all records
            $name_options = DB::table('public.m_name_valid_option')->where('is_active', true)->get();
            // dd($name_options);

            return view('scheme_gen_setting.index', [
                'schemes' => $schemes,
                'ds_phases' => $ds_phases,
                'name_options' => $name_options
            ]);
        }
    }
    public function store(Request $request)
    {


        try {
            // dd($request->all());
            $now = Carbon::now();

            $dsPhase = $request->has('ds_phase') ? json_encode($request->ds_phase) : null;
            $name_opts = $request->has('name_validation_opt') ? $this->to_pg_array($request->name_validation_opt) : null;

            DB::table('m_scheme_gen_setting')->insert([
                'scheme_id' => $request->scheme_id,
                'allow_entry' => $request->entry ?? false,  // Default to false if not provided
                'allow_verify' => $request->verify ?? false,
                'allow_approve' => $request->approve ?? false,
                'allow_ds_entry' => $request->ds_entry ?? false,
                'ds_phase' => $dsPhase,  // This will be null if no phases are selected
                'cap_exists' => $request->scheme_cap ?? false,
                'allow_normal_entry' => $request->normal_entry ?? false,
                'special_quota_exists' => $request->special_quota ?? false,  // Fixed typo here
                'allow_cmo' => $request->cmo_check ?? false,
                'allow_bank_failed_update' => $request->bank_failed ?? false,
                'allow_bank_name_update' => $request->name_validation_failed ?? false,
                'allow_bank_ac_update' => $request->account_validation_failed ?? false,
                'name_valid_opt' => $name_opts,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Session::flash('success', 'Scheme configuration saved successfully!');
            return redirect()->route('scheme-general-setting'); // Adjust to your route name

        } catch (QueryException $e) {
            Session::flash('error', 'An error occurred while saving the configuration. Please try again.');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            Session::flash('error', 'An unexpected error occurred. Please try again.');
            return redirect()->back()->withInput();
        }
    }


    public function getData(Request $request)
    {
        $data = DB::table('m_scheme_gen_setting')
            ->join('m_scheme', 'm_scheme_gen_setting.scheme_id', '=', 'm_scheme.id')
            ->select(
                'm_scheme.id as scheme_id',
                'm_scheme.scheme_name',
                'm_scheme_gen_setting.allow_entry',
                'm_scheme_gen_setting.allow_verify',
                'm_scheme_gen_setting.allow_approve',
                'm_scheme_gen_setting.allow_ds_entry',
                'm_scheme_gen_setting.cap_exists',
                'm_scheme_gen_setting.allow_cmo',
                'm_scheme_gen_setting.allow_bank_failed_update',
                'm_scheme_gen_setting.allow_bank_name_update',
                'm_scheme_gen_setting.allow_bank_ac_update',
                'm_scheme_gen_setting.name_valid_opt'
            )
            ->get();

        // Process the name_valid_opt column for each record
        $data = $data->map(function ($item) {
            $item->name_valid_opt = $item->name_valid_opt
                ? array_map('intval', array_filter(explode(',', trim($item->name_valid_opt, '{}'))))
                : [];
            return $item;
        });

        return response()->json(['data' => $data]);
    }



    public function getDetails($scheme_id)
    {
        try {
            // Fetch scheme details from the database
            $scheme = DB::table('m_scheme_gen_setting')
                ->join('m_scheme', 'm_scheme_gen_setting.scheme_id', '=', 'm_scheme.id')
                ->select(
                    'm_scheme.id as scheme_id',
                    'm_scheme.scheme_name',
                    'm_scheme_gen_setting.allow_entry as entry',
                    'm_scheme_gen_setting.allow_verify as verify',
                    'm_scheme_gen_setting.allow_approve as approve',
                    'm_scheme_gen_setting.allow_bank_failed_update as bank_failed',
                    'm_scheme_gen_setting.allow_bank_name_update as name_failed',
                    'm_scheme_gen_setting.allow_bank_ac_update as ac_failed',
                    'm_scheme_gen_setting.name_valid_opt',
                    'm_scheme_gen_setting.allow_ds_entry as ds_entry',
                    'm_scheme_gen_setting.cap_exists as capacity',
                    'm_scheme_gen_setting.ds_phase',
                    'm_scheme_gen_setting.allow_normal_entry as normal_entry',
                    'm_scheme_gen_setting.special_quota_exists as special_quota',
                    'm_scheme_gen_setting.allow_cmo as cmo'
                )
                ->where('m_scheme_gen_setting.scheme_id', $scheme_id)
                ->first();

            // Check if scheme exists
            if (!$scheme) {
                return response()->json(['error' => 'Scheme not found'], 404);
            }

            // Decode JSON values and handle array columns
            $scheme->ds_phase = $scheme->ds_phase ? json_decode($scheme->ds_phase) : [];

            // Parse PostgreSQL array into a PHP array
            $scheme->name_valid_opt = $scheme->name_valid_opt
                ? array_map('intval', array_filter(explode(',', trim($scheme->name_valid_opt, '{}'))))
                : [];

            // Debug the result
            // dd($scheme);

            return response()->json($scheme);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        try {
            $now = Carbon::now();

            // Ensure the scheme_id is provided and valid
            $schemeId = $request->scheme_id;

            // Fetch the existing scheme setting from the database
            $existingSetting = DB::table('m_scheme_gen_setting')->where('scheme_id', $schemeId)->first();

            if (!$existingSetting) {
                // If the scheme does not exist, redirect back with an error
                Session::flash('error', 'Scheme not found.');
                return redirect()->back();
            }

            // Get the ds_phase array if present, otherwise set it to null
            $dsPhase = $request->has('ds_phase') ? json_encode($request->ds_phase) : null;
            $name_opts = $request->has('name_validation_opt') ? $this->to_pg_array($request->name_validation_opt) : null;


            // Update the existing record in the database
            DB::table('m_scheme_gen_setting')->where('scheme_id', $schemeId)->update([
                'allow_entry' => $request->entry ?? false,
                'allow_verify' => $request->verify ?? false,
                'allow_approve' => $request->approve ?? false,
                'allow_bank_failed_update' => $request->bank_failed ?? false,
                'allow_bank_name_update' => $request->name_validation_failed ?? false,
                'allow_bank_ac_update' => $request->account_validation_failed ?? false,
                'name_valid_opt' => $name_opts,
                'allow_ds_entry' => $request->ds_entry ?? false,
                'ds_phase' => $dsPhase,  // This will be null if no phases are selected
                'cap_exists' => $request->scheme_cap ?? false,
                'allow_normal_entry' => $request->normal_entry ?? false,
                'special_quota_exists' => $request->special_quota ?? false,  // Fixed typo here
                'updated_at' => $now,  // Only updated_at is required for the update
                'allow_cmo' => $request->cmo_check ?? false,
            ]);

            // Flash success message
            Session::flash('success', 'Scheme configuration updated successfully!');
            return redirect()->route('scheme-general-setting'); // Adjust to your route name

        } catch (QueryException $e) {
            // If a query exception occurs
            Session::flash('error', 'An error occurred while updating the configuration. Please try again.');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            // If an unexpected error occurs
            Session::flash('error', 'An unexpected error occurred. Please try again.');
            return redirect()->back()->withInput();
        }
    }
    function to_pg_array($set)
    {
        settype($set, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($set as $t) {
            if (is_array($t)) {
                $result[] = to_pg_array($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
                if (!is_numeric($t)) // quote only non-numeric values
                    $t = '"' . $t . '"';
                $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}'; // format
    }


}
