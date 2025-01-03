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
use URL;
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
        //$destinationPath = storage_path('app\\keep\\').$applications->application_id;
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

    public function downloadCertificate($id,$user_id, $slug){
     $users = User::All();
     $application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'READY')->first();

   
    return Image::make(storage_path() . '\\app\\keep/' . $user_id . '\\' . $slug)->response();
    $storagePath = storage_path('app\\keep\\'. $user_id . '\\' . $slug);
     //return response()->download($storagePath,null,[],null) ;

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

        /*echo "<pre>";
        print_r($application);
        echo "</pre>";
        die();*/

        $gendar ='';
        $spouse_name = '';
         if($application->gender == 'm'){
            $gendar = 'S/O';
            $spouse_name = '';
         }
         if($application->gender == 'f'){
            $gendar .= 'D/O';
            //$wo .= 'W/O';
            if($application->spouse_name == ''){
                $spouse_name .= '';
            }else{
                $spouse_name .=  ' and ' . 'W/O '. $application->spouse_name .',';
            }
            
         }
         // create new PDF document
         // create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information


// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 049', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(10, 20, 10);
$pdf->SetHeaderMargin(30);
$pdf->SetFooterMargin(0);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//$pdf->setCellPaddings(5, 5, 5, 5);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();
        
        //$img_background =public_path().'/frontend/img/background_police_new.jpg';
        //$pdf->Image($img_background, 30, 60, 150, 150, '', '', '', false, 300, '', false, false, 0);
        $img_file = public_path().'/frontend/img/Bidhannagar_Police_logo.png';
        //$pdf->Image($img_file, 0, 20, 100, 100, '', '', '', false, 300, '', false, false, 0);


        $html ='<style>
        .wrapper {
            border:1px dashed #2d2c80; width:1000px; margin:0px auto;background-image:url("'.$img_background.'"); background-repeat:no-repeat;
        }
        .logo_pos{
            text-align:center; padding-top: 20px; padding-bottom: 20px;
        }
        .pcc_certi_head {
            text-align: center;font-size: 20px;  padding-top: 10px;padding-bottom: 20px; font-weight: bold;
        }
        .profile_pic {
            width="30%" background: #eee; height: 180px;margin-top: 20px; 
        }
        .leftConnPosition{
            padding:20px 0 0 0px; 
        }
        </style>';

        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();
        $html .='<div class="wrapper" >
        <table width="100%" >';
        $html .='<tr>
                <td class="logo_pos" ><img src="'.$img_file.'" alt="logo" width="120"  ></td>
            </tr>
            <tr>
                <td style="text-align: center;font-size: 26px;">Bidhannagar Police </td>
            </tr>
            <tr>
                <td class="pcc_certi_head" >Police Clearance Certificate (PCC) </td>
            </tr>';

        $html .='<tr><td>&nbsp;</td></tr>';
        $html .='<tr>
            <td class="leftConnPosition">
            <table cellpadding="0" cellspacing="0">
            <tr>
            <td width="70%">
            <b style="font-size:12px;">Government of West Bengal</b>
            <br>            
            Office of the Deputy Commissioner of Police,<br>
            Special Branch, Bidhannagar Police Commissionerate <br>
            Araksha Bhawan, Ground Floor, Sector-II, Salt Lake <br>
            Kolkata-700091<br>
            <b>PHONE & FAX NO.033-2334-3080</b><br>
            Email-ID No.dcsbcontrol@gmail.com<br></td>
            <td class="profile_pic" >';
        
        $img_prifile = storage_path('app/keep/'. $application->application_id . '/' . $application->profile_img);
        $pdf->Image($img_prifile, 157, 75, 40, 40, '', '', '', false, 300, '', false, false, 0);  
        //Log::info('URL:'.$image);
        //$html .= '<img src="'.$img_prifile.'"  height="130" width="120" style="padding-top:20px; "  width="100%" height="180">'; 
            //$html .='<img src="'.$image.'" alt="logo">';                  
                          
        $html .='</td>
            </tr>
                    </table>
                </td>
            </tr>'; 
        $html .='<tr><td>&nbsp;</td></tr>';    

        $html .= '<tr><td style="padding:40px 20px;">';

         if($application->is_rejected=='R'){

        $html .='<table><tr><td style="text-indent: 30px;">Certified that  '.strtoupper($application->first_name).' '.strtoupper($application->middle_name).' '. strtoupper($application->last_name).', '.$gendar.' '.strtoupper($application->father_name).''.$spouse_name.' is presently residing at '.$application->present_address_line1.', '.$application->present_address_line2.', Pin- '.$application->present_pincode.'. since '.date("d-m-Y", strtotime($application->present_stay_frm_date)).' to '. date("d-m-Y", strtotime($application->present_stay_to_date)).'.
                    <p style="text-indent: 30px;">During local enquiry held by the Special Branch, Bidhannagar nothing adverse was found against '.strtoupper($application->first_name).' '.strtoupper($application->middle_name).' '. strtoupper($application->last_name).' , '.$gendar.' '.strtoupper($application->father_name).'.</p><p style="text-indent: 30px;"> This Certificate is issued for the purpose of '.$application->pcc_purpose.'</p> .</td></tr></table>';

        }
	if($application->is_rejected=='M'){

        $html .='<table><tr><td style="text-indent: 30px;">Certified that  '.strtoupper($application->first_name).' '.strtoupper($application->middle_name).' '. strtoupper($application->last_name).', '.$gendar.' '.strtoupper($application->father_name).''.$spouse_name.' is a Permanent resident of  '.$application->permanent_address_line1.','.$application->permanent_address_line2.', Pin-'.$application->permanent_pincode.' since '.date("d-m-Y", strtotime($application->permanent_stay_frm_date)).' to '. date("d-m-Y", strtotime($application->permanent_stay_to_date)).'.
                    <p style="text-indent: 30px;">During local enquiry held by the Special Branch, Bidhannagar nothing adverse was found against '.strtoupper($application->first_name).' '.strtoupper($application->middle_name).' '. strtoupper($application->last_name).' , '.$gendar.' '.strtoupper($application->father_name).'.</p><p style="text-indent: 30px;"> This Certificate is issued for the purpose of '.$application->pcc_purpose.'</p> .</td></tr></table>';

        }
        $html .=' </td></tr>'; 
        $html .='<tr><td>&nbsp;</td></tr>';   

        $html .='<tr><td ><table  cellpadding="0" cellspacing="0" width="100%"  >
                ';

        $html .='<tr><td style="width: 60%">Kolkata <br>'.date("d-m-Y", strtotime($application->updated_at)).'.</td>';

        $html .='<td style="width: 40%; text-align: center" > <br><br> Dy. Commissioner of Police,<br>Special Branch, Bidhannagar</td></tr>';
        $html .='<tr><td>Certificate No:'.$application->application_id.'</td><td></td></tr>';
       
        $html .='
                    </table>
                </td>
            </tr>';
        $html .='<tr><td>&nbsp;</td></tr>';  
        $html .='<tr><td >';
        $params = $pdf->serializeTCPDFtagParameters(array('Bidhannagar Police Clearance Certificate Applicant Name : '.$application->first_name.' '.$application->middle_name.' '. $application->last_name.' Certificate No: '. $application->application_id.' Valid Upto '. $application->valid_upto ,'QRCODE,H', 140, 200, 40, 40, array(
                'border' => 2,
                'padding' => 'auto',
                'fgcolor' => array(0,0,255),
                'bgcolor' => array(255,255,64)
            ), 'N'));
        $html .= '<tcpdf method="write2DBarcode" params="'.$params.'" />';

      $html .= '</td></tr>';         
        

        $html .='<tr><td >&nbsp;</td></tr>';       

        $html .='<tr>
                <td style="padding-top: 20px;">This certificate is digitally signed & valid for 6 months from the date of issue. The authenticity of the document can be verified by entering Certificate No. in pcc.bidhannagarpolice.org</td>
            </tr>
          </table>
         </div>'; 















// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Certificate.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+   

    





    }
}
