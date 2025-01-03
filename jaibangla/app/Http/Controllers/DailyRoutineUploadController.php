<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Config;
use App\BankDetails;
use App\DocumentType;
class DailyRoutineUploadController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index(Request $request)
    {
        $change_request = DB::select(DB::raw("select * from public.m_cr_type where code in('01','02')"));
        $comunnication_type= DB::select(DB::raw("select * from public.m_cr_type where code in('0201','0202','0203')"));
        $schemes = Scheme::where('is_active', 1)->get();
        return view('daily-routine/index',['change_request' => $change_request,'comunnication_type' => $comunnication_type,'schemes'=>$schemes]);
    }
    public function getDataUpload(Request $request)
    {
        // dd($request->all());
        $designation = Auth::user()->designation_id;
        if ($request->ajax()) {
            $category = strtoUpper(trim($request->category));
            $comunication_type= $request->comun_type;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $query = DB::table('change_request_track');
            if (!is_null($comunication_type)) {
                $query->where('cr_communication_type', $comunication_type);
            } else {
                $query->where('cr_communication_type', $category);
            }
            if (!is_null($from_date) && !is_null($to_date)) {
                $query->whereBetween('implemented_date', [$from_date, $to_date]);
            }
            $data = $query->get();
            // dd($data);
            $imagesData = DB::connection('pgsql_encwrite')->table('jb_doc.chenge_request_document')
            ->whereIn('requirement_id', $data->pluck('id'))
            ->whereIn('document_type', $data->pluck('cr_communication_type'))
            ->get()
            ->groupBy('requirement_id');
            return datatables()->of($data)
            ->addColumn('id', function ($data) {
              return $data->id;
            })
            ->addColumn('subject', function ($data) {
                return $data->subject;
            })
            ->addColumn('download', function ($value) use ($imagesData) {
                $imageData = $imagesData[$value->id] ?? null; // Get images for the current requirement_id
                $links = collect();
                if ($imageData) {
                    foreach ($imageData as $image) {
                        $url = "data:image/jpeg;base64," . $image->attched_document;
                        $links->push('<a href="' . $url . '" download="image_' . $image->id . '.jpg"><i class="fa fa-download"></i></a>');
                    }
                    return $links->implode('  '); // Join all links with a space or any other separator
                } else {
                    return 'No image';
                }
            })
            ->rawColumns(['id', 'subject', 'description','implemented_date','download'])
            ->make(true);
            
        }
    }
    public function postDataUpload(Request $request)
    { 
        //    dd($request->all());
        $response = [];
        $statusCode = 200;
        try{
        $category_type=$request->category_type;
        $comunication_type=$request->comun_type_id;
        $implement_date=$request->implement_date;
        $schemes=$request->scheme;
        // dd($schemes);
        // $schemearray = array();
        // foreach ($schemes as $scheme) {
        //     array_push($schemearray, (int) $scheme);
        // }
        $schemeslist = "{" .  $schemes . "}";
        //  dd($schemeslist);
        $subject=$request->subject;
        $description=$request->description;
        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        
          $updateDetails=[];
          $updateDetails['created_at']=date('Y-m-d H:i:s');
          $updateDetails['subject']=$subject;
          $updateDetails['description']=$description;
          $updateDetails['implemented_date']=$implement_date;
          $updateDetails['schemes']=$schemeslist;
          if(!empty($comunication_type)){
            $updateDetails['cr_communication_type']=$comunication_type;
          }else{
            $updateDetails['cr_communication_type']=$category_type;
          }
          $is_saved =DB::table('public.change_request_track')->insertGetId($updateDetails);
        if ($request->hasFile('raise_file')) {
            $files = $request->file('raise_file');
            foreach ($files as $file) {
            $img_data = file_get_contents($file);
            $extension = $file->getClientOriginalExtension();
            $mime_type = $file->getMimeType();
            $base64 = base64_encode($img_data);
            $updateimage=[];
            if($is_saved){
                $updateimage['attched_document']=$base64;
                $updateimage['implemented_date']=$implement_date;
                $updateimage['created_at']=date('Y-m-d H:i:s');
                $updateimage['ip_address']=request()->ip();
                $updateimage['document_extension']=$extension;
                $updateimage['document_mime_type']=$mime_type;
                $updateimage['schemes']=$schemeslist;
                $updateimage['requirement_id']=$is_saved;
                if(!empty($comunication_type)){
                    $updateimage['document_type']=$comunication_type;
                  }else{
                    $updateimage['document_type']=$category_type;
                }      
                $is_insert =DB::connection('pgsql_encwrite')->table('jb_doc.chenge_request_document')->insert($updateimage);
            }
            } 
        }
       
        if ($is_insert) {
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            $response = array(
                'status' => 1, 'msg' => 'Document Update Successfully.',
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
              );
          } else {
          DB::rollback();
          DB::connection('pgsql_encwrite')->rollback();
          $response = array(
          'status' => 3, 'msg' => 'Somethimg went wrong!!',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }
    }catch (\Exception $e) {
          dd($e);
        DB::rollback();
        $response = array(
          'exception' => true,
          // 'exception_message' => $e->getMessage(),
          'exception_message' => 'Something went wrong. May be session time out logout and login again......',
        );
         $statusCode = 400;
      } finally {
        return response()->json($response, $statusCode);
      }
}
}
