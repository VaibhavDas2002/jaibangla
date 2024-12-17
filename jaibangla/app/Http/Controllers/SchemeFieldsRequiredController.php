<?php
namespace App\Http\Controllers;

use App\Scheme;
use App\SchemeReqFields;
use App\RequiredFilds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\DB;

class SchemeFieldsRequiredController extends Controller
{
    // Show form to create or assign required fields to a scheme
    public function index()
    {
        $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
        $doc_fields = RequiredFilds::where('field_type', 2)->get(); // Document fields
        $normal_fields = RequiredFilds::where('field_type', 1)->get(); // Normal fields
        return view('scheme-req-field.index', [
            'schemes' => $schemes,
            'normal_fields' => $normal_fields,
            'doc_fields' => $doc_fields
        ]);
    }

    // Store the selected fields for the scheme
    public function store(Request $request)
    {
        // Validate the request (ensure fields are selected)
        $validator = Validator::make($request->all(), [
            'scheme_id' => 'required',
            'normal_fields' => 'nullable|array', // Validate normal fields array
            'doc_fields' => 'nullable|array', // Validate document fields array
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Save normal fields
            if ($request->has('normal_fields')) {
                foreach ($request->normal_fields as $normal_field) {
                    $scheme_req_fields = new SchemeReqFields();
                    $scheme_req_fields->scheme_id = $request->scheme_id;
                    $scheme_req_fields->field_type = $normal_field; // Set field type to 'normal'
                    // $scheme_req_fields->field_id = $normal_field; // Assuming you want to store the field ID
                    $scheme_req_fields->save();
                }
            }

            // Save document fields
            if ($request->has('doc_fields')) {
                foreach ($request->doc_fields as $doc_field) {
                    $scheme_req_fields = new SchemeReqFields();
                    $scheme_req_fields->scheme_id = $request->scheme_id;
                    $scheme_req_fields->field_type = $doc_field; // Set field type to 'document'
                    // $scheme_req_fields->field_id = $doc_field; // Assuming you want to store the field ID
                    $scheme_req_fields->save();
                }
            }

            // Flash success message to session
            Session::flash('success', 'Scheme fields saved successfully!');
            return redirect()->back();

        } catch (\Exception $e) {
            dd($e);
            // Log the error for debugging
            Log::error('Error saving scheme fields: ' . $e->getMessage());

            // Flash error message to session
            Session::flash('error', 'An error occurred while saving scheme fields. Please try again.');
            return redirect()->back();
        }
    }

    public function getData(Request $request)
    {
        // Fetch active schemes
        $schemes = Scheme::where('is_active', 1)->get();
    
        // Map and format data for the response
        $data = $schemes->map(function ($scheme, $index) {
            // Fetch required fields for the current scheme
            $requiredFields = SchemeReqFields::where('scheme_id', $scheme->id)->get();
    
            // Get field names from RequiredFields table
            $fieldNames = $requiredFields->map(function ($field) {
                $requiredField = RequiredFilds::find($field->field_type); // Fetch the field details
                return $requiredField ? $requiredField->name : null;
            })->filter()->implode(', '); // Filter out null values and join names with commas
    
            return [
                'sl_no' => $index + 1,
                'scheme_name' => $scheme->scheme_name,
                'required_fields' => $fieldNames,
                'action' => '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $scheme->id . '">Edit</button>',
            ];
        });
    
        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $schemes->count(),
            'recordsFiltered' => $schemes->count(),
            'data' => $data,
        ]);
    }
    

}
