<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DocumentType;
use App\Scheme;
use App\Schemetype;
use App\SchemeDocMap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;


class DocumentTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $document_class = "active";


        if (request()->ajax()) {
            $scheme_id = $request->scheme_id;

            $limit = $request->input('length');
            $offset = $request->input('start');
            //$serachvalue = $request->search['value'];
            $docs = array();
            $totalRecords = 0;
            $filterRecords = 0;
            $action = "";
            if (empty($serachvalue)) {
                $docs = DocumentType::orderBy("doc_name")->offset($offset)->limit($limit)->get([
                    'id', 'doc_name',
                    'doc_type', 'doc_size_kb', 'doucument_group', 'is_active', 'is_profile_pic'
                ]);
                $totalRecords = DocumentType::count();
                $filterRecords = count($docs);
            }

            return datatables()
                ->of($docs)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('is_active', function ($docs) {
                    return ($docs->is_active == true) ? '<button class="glyphicon glyphicon-ok" onClick="toggleActivate(' . $docs->id . ',1)"></button>'
                        : '<button class="glyphicon glyphicon-remove" onClick="toggleActivate(' . $docs->id . ',1)"></button>';
                })
                ->addColumn('is_profile_pic', function ($docs) {
                    return ($docs->is_profile_pic == true) ? '<button class="glyphicon glyphicon-ok" onClick="toggleActivate(' . $docs->id . ',2)"></button>'
                        : '<button class="glyphicon glyphicon-remove" onClick="toggleActivate(' . $docs->id . ',2)"></button>';
                })
                ->addColumn('doucument_group', function ($docs) {
                    return   $this->getGroupName($docs->doucument_group);
                })
                ->addColumn('action', function ($docs) {

                    $action = '<button class="btn btn-warning ben_view_button" onClick="UpdateDocument(' . $docs->id . ')"><i class="fa fa-edit"></i></button>&nbsp;';
                    $action .= '<button class="btn btn-danger ben_delete_button" onClick="deleteDocument(' . $docs->id . ')"><i class="fa fa-trash"></i></button>';
                    return $action;
                })
                ->rawColumns(['is_active', 'is_profile_pic', 'doucument_group', 'action'])
                ->make(true);
        }

        // $docs = DocumentType::orderBy("doc_name")->get();
        // $docs_arr=array();
        // $i=0;
        // foreach($docs as $document){
        //     $docs_arr[$i]['id']=$document->id;
        //     $docs_arr[$i]['doc_name']=$document->doc_name;
        //     $docs_arr[$i]['doc_type']=$document->doc_type;
        //     $docs_arr[$i]['doc_size_kb']=$document->doc_size_kb;
        //     $docs_arr[$i]['is_active']=$document->is_active;
        //     $docs_arr[$i]['group_name']=$this->getGroupName($document->doucument_group);
        //     $i++;
        // }
        return view('document-mgmt/index')->with("document_class", $document_class);;;
    }
    public function documentToggleActivate(Request $request)
    {
        $document_id = $request[trim('document_id')];
        $action_type = $request[trim('action_type')]; //active - IS_ACTIVE , first - IS_FIRST

        $mytime = Carbon::now();

        $documentItem = DocumentType::where('id', $document_id)->first();

        if ($action_type == 1) { //is_active - 1
            $statusCode = TRUE;
            if ($documentItem->is_active) {
                $statusCode = FALSE;
            }
            DocumentType::where('id', $document_id)->update(['is_active' => $statusCode]);
        }
        if ($action_type == 2) { //is_profile_pic - 2
            $toggleStatus = TRUE;
            if ($documentItem->is_profile_pic) {
                $toggleStatus = FALSE;
            }
            DocumentType::where('id', $document_id)->update(['is_profile_pic' => $toggleStatus]);
        }


        return "success";
    }


    public function deleteDocument(Request $request)
    {

        $document_id = $request['item_id'];


        DocumentType::where('id', $document_id)->delete();

        return "success";
    }

    public function documentSaveUpdate(Request $request)
    {
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
        $this->validate($request, [
            'doc_name' => 'required',
            'doc_type' => 'required',
            'doc_size_kb' => 'required|integer',

        ], [
            'doc_name.required' => 'Please enter doc name',
            'doc_type.required' => 'Please enter doc type',
            'doc_size_kb.required' => 'Please enter doc size',
            'doc_size_kb.integer' => 'doc size should be in integer',
        ]);
        try {
            $edit_code = $request->edit_code;
            $doucument_group_explode = $request['doucument_group'];
            $doucument_group = "{" . $doucument_group_explode . "}";
            if ($edit_code == "") {
                $msg = 'DocumentType Created Succesfully';
                DocumentType::create([
                    'doc_name' => $request['doc_name'],
                    'doc_type' => $request['doc_type'],
                    'doc_size_kb' => $request['doc_size_kb'],
                    'doucument_group' => $doucument_group,
                    'is_active' => true
                ]);
            } else {
                $msg = 'Document Type "' . $request['doc_name'] . '" Updated Succesfully!';
                $input = [
                    'doc_name' => $request['doc_name'],
                    'doc_type' => $request['doc_type'],
                    'doc_size_kb' => $request['doc_size_kb'],
                    // 'is_active' => $request['is_active'] == 'on' ? 1 : 0,
                    'doucument_group' => $doucument_group,
                ];

                DocumentType::where('id', $edit_code)
                    ->update($input);
            }
            $response = array('status' => 1, 'msg' => $msg);
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function editDocument(Request $request)
    {
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
        try {
            $editId = $request->editId;
            $docs = DocumentType::select('doc_name', 'doc_type', 'doc_size_kb', 'id', 'doucument_group')->where('id', $editId)->first();

            $response = array('status' => 1, 'docs' => $docs);
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('document-mgmt/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$this->validateInput($request);
        DocumentType::create([
            'doc_name' => $request['doc_name'],
            'doc_type' => $request['doc_type'],
            'doc_size_kb' => $request['max_size'],
            'doucument_group' => json_encode($request['doucument_group']),
            'is_active' => true
        ]);

        return redirect()->intended('document-mgmt')->with('message', 'DocumentType Created Succesfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $docs = DocumentType::find($id);
        // Redirect to city list if updating city wasn't existed
        if ($docs == null) {
            return redirect()->intended('document-mgmt');
        }



        return view('document-mgmt/edit', ['docs' => $docs]);
    }

    /*
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $doc = DocumentType::findOrFail($id);
        $doucument_group = "{" . join(",", $request['doucument_group']) . "}";
        $input = [
            'doc_name' => $request['doc_name'],
            'doc_type' => $request['doc_type'],
            'doc_size_kb' => $request['doc_size_kb'],
            'doucument_group' => $doucument_group,
            'is_active' => $request['is_active'] == 'on' ? 1 : 0,

        ];
        //print_r($input);die();

        DocumentType::where('id', $id)
            ->update($input);

        return redirect()->intended('document-mgmt')->with('message', 'Document Type "' . $request['doc_name'] . '" Updated Succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DocumentType::where('id', $id)->delete();
        return redirect()->intended('document-mgmt');
    }

    public function assigndocumenttoscheme()
    {
        $schemes = Scheme::orderby('id')->get();
        $groupArr = Config::get('constants.document_group');
        //Only Scheme Type=Pension selected 
        $scheme_type = Schemetype::orderby('scheme_type')->get();
        $docs = DocumentType::orderBy("doc_name")->get();

        $scheme_doc_map = SchemeDocMap::orderby('scheme_code')->get()->where("scheme_type_code", "1");
        //print_r($scheme_doc_map)

        //print(sizeof($scheme_doc_map));
        // print($scheme_doc_map[0]->doc_list_man);
        // $mandatory_list = json_decode($scheme_doc_map[0]->doc_list_man);
        // print_r($mandatory_list);
        //die();
        //$maparr=$array('' => , );
        // print("<br>");
        // foreach($scheme_doc_map as $docmap){
        //     print($docmap->scheme_code."=>".$docmap->doc_list_man.'=>'.$docmap->doc_list_opt);
        //     print("<br>");
        // }
        // for($scheme_doc_map as $map){
        //     print($map);

        // }
        // die();

        return view('document-mgmt.assign.index')
            ->with('schemes', $schemes)
            ->with('scheme_type', $scheme_type)
            ->with('docs', $docs)
            ->with('docgroup', $groupArr)
            ->with('scheme_doc_map', $scheme_doc_map);
    }

    public function ajaxschemeChnageRequest($id)
    {
        if (Input::get('scheme_type_Id')) {
            $scheme_type_Id = Input::get('scheme_type_Id');
            // print_r($scheme_type_Id);exit();
            $scheme_Id_name = Scheme::where('scheme_type', '=', $scheme_type_Id)->get();
            return $scheme_Id_name;
        }
    }
    public function ajaxschemenameRequest($id)
    {
        if (Input::get('schemes_name')) {
            $schemes_name = Input::get('schemes_name');
            $scheme_name = SchemeDocMap::where('scheme_code', '=', $schemes_name)->get();
            // print_r($scheme_name);die();
            return $scheme_name;
        }
    }

    public function documentsetupforScheme(Request $request)
    {
        // // echo "scheme type - ".$request['scheme_type'],'\n schemes_name -'.$request['schemes_name'];
        $id = SchemeDocMap::where('scheme_type_code', $request['scheme_type'])
            ->where('scheme_code', $request['schemes_name'])
            ->first();

        if ($id) {
            SchemeDocMap::where('scheme_type_code', $request['scheme_type'])
                ->where('scheme_code', $request['schemes_name'])
                ->update(['doc_list_man' => json_encode($request['doc_mand']), 'doc_list_man_group' => json_encode($request['doc_group']), 'doc_list_opt' => json_encode($request['doc_opt'])]);
        } else {
            SchemeDocMap::create([
                'scheme_type_code' => $request['scheme_type'],
                'scheme_code' => $request['schemes_name'],
                'doc_list_man' => json_encode($request['doc_mand']),
                'doc_list_opt' => json_encode($request['doc_opt'])
            ]);
        }
        return redirect()->intended('scheme-doc-map')->with('message', 'Scheme Document Mapping Successful');
    }
    public function getGroupName($groupId)
    {
        if (!empty($groupId)) {
            $groupArr = Config::get('constants.document_group');
            $arr = array();
            $postgresStr = trim($groupId, "{}");
            $elmts = explode(",", $postgresStr);
            foreach ($groupArr as $key => $value) {
                if (in_array($key, $elmts)) {
                    array_push($arr, $value);
                }
            }
            $groupDescription = implode(',', $arr);
            //print_r($groupDescription);
        } else
            $groupDescription = "NA";
        return $groupDescription;
    }
}
