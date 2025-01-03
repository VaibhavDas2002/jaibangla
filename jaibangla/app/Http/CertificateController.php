<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\applicationModel;
use App\Policestation;
use App\PicUpload;
use App\User;
use Illuminate\Support\Facades\DB;
use PDF;
use TCPDF;
//use CUSTOMPDF;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use TCPDF2DBarcode;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
        //$this->middleware('ACP');
        $this->middleware('Admin');
        //$this->middleware('DCP');
    }

    public function index(){
     $users = User::All();
     $applictions = applicationModel::where('current_status', '=', 'APPROVEDBYDCP')->get();
     return view('application.application-certificate-checking-status')
     ->with('applications',$applictions)->with('users',$users);
    }

    public function search(Request $request) {
        $users = User::All();
        $this->validateInput($request);
        $application_id = $request->input('application_id');
        $applictions = applicationModel::where('current_status', '=', 'APPROVEDBYDCP')->get();
       return view('application.certificate-search', ['applictions' => $applictions])->with('users',$users);
    }

    public function UploadCertificate($id){
    $users = User::All();
    $applications = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'APPROVEDBYDCP')->first();
    return view('application.certificate-upload', ['applications' => $applications])->with('users',$users);
    }

   public function UploadpdfSignCertificate(Request $request,$id){
    $users = User::All();
    $applications = applicationModel::where('application_id', $id)->first();

        $path = $request->file('signedFile');
        $file_profile = $path->getClientOriginalName();
        $applications->signed_certificate = $file_profile;
        $applications->current_status = 'READY';
     

        $destinationPath = storage_path('app\\keep\\').$applications->application_id;
        $fileStore = $path->move($destinationPath, $file_profile);
        $applications->save();
        return view('application.certificate-message')->with('message',"Uploded Digital signed Certificate !")->with('users',$users);

        //return back('application.certificate-upload')->with('applications',$applications)->with('users',$users);

   }

   public function readyCertificate(){
    $users = User::All();
    $application = applicationModel::where('current_status', '=', 'READY')->get();
    
     
     /*echo "<pre>";
     print_r($application);
     echo "</pre>";
     die();*/

    return view('application.readyCertificate')
     ->with('applications',$application)->with('users',$users);
   }

   public function downloadCertificate($id){
     $users = User::All();
    $application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'READY')->first();

    return view('application.downloadCertificate')
     ->with('applications',$application)->with('users',$users);
  
   }

    public function Downloadsearch(Request $request) {
        $users = User::All();
        //$this->validateInput($request);
        $application_id = $request->input('application_id');
        $applictions = applicationModel::where('current_status', '=', 'READY')->get();
       return view('application.download-certificate-search', ['applictions' => $applictions])->with('users',$users);
    }

    public function printCertificate($id)
    {
        $application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'APPROVEDBYDCP')->first();

        $police_station = Policestation::where('id','=',$application->police_station_name )->first();
        $gendar ='';
        $spouse_name = '';
         if($application->gender == 'm'){
            $gendar = 'S/O';
            $spouse_name = '';
         }
         if($application->gender == 'f'){
            $gendar .= 'D/O';
            //$wo .= 'W/O';
            $spouse_name .= 'W/O '. $application->spouse_name .',';
         }

         //echo $image = URL::to('/').'/images/'.$application->application_id.'/'.$application->profile_img;

        //echo URL::to('/').'/'.$application->profile_img;
       //$image = URL::to('/').'/'.$application->application_id.'/'.$application->profile_img;
       //echo $imgsrc = '<img src="'.$image.'" alt="Smiley face" height="42" width="42">';

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);         


   

    //$pdf->write2DBarcode('[url]www.pcc.bidhannagarcitypolice.gov.in[/url]',$qrcode,$applicant,$certificate,$verified,$validTime, 'PCC,Q', 20, 150, 50, 50, $style, 'N');
    //$pdf->Text(20, 145, 'PCC Q');

    //$code = '111011101110111,010010001000010,010011001110010,010010000010010,010011101110010';
    //$pdf->write2DBarcode($code, 'RAW', 80, 30, 30, 20, $style, 'N');

    
    //$barcodeobj = new TCPDF2DBarcode($qrcode, 'QRCODE,H');                           
    //echo "<img src='". $barcodeobj->getBarcodePNG(10, 10, array(0,0,0)).".png'>";

        $img_back = URL::to('/').'/frontend/img/team-4.jpg';
        $pdf->Image($img_back, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        $img_file = URL::to('/').'/frontend/img/Bidhannagar_Police_logo.png';
        $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();
        //$html="";
                                      
    /*$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
    );
    $qrcode = "Kolkata Police Clearance Certificate";
    $applicant = "Applicant Name :".$application->first_name. ' ' .$application->middle_name.' '.$application->last_name ;
    $certificate = "Certificate No:".$application->application_id;
    $verified = $application->present_stay_frm_date. '\/'. $application->present_stay_to_date;
    $validTime = "Pcc valid till 6 months from date of issue";

       $pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,H', 140, 210, 50, 50, $style, 'N');
       $pdf->Text(140, 205, 'QRCODE H - NO PADDING');
    */
        //Log::info($html);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Image($image);
        $pdf->SetTitle('Certificate');
        $pdf->SetFont('times', 'N', 12);
        $pdf->AddPage('P', 'A4');
        //$pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Certificate.pdf','I');
    }
}
