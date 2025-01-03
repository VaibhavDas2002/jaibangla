<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\Configduty;
use App\District;
use App\User;
use App\UrbanBody;
use App\Taluka;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\SendingToUser;

class SendingMailToUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');   
    }
    public function index(Request $request){
        $designation = User::where('is_active',1)->distinct()->select('designation_id')->get();
        $district = District::all();
        $scheme = Scheme::all();
        return view('send-mail-to-users/index',['districts' => $district, 'schemes' => $scheme, 'designations' => $designation]);
    }
    public function getEmailAddress(Request $request){
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $designation = $request->designation;
            $scheme_id = $request->scheme_id;
            $district_code = $request->district_code;
            $is_rural = $request->is_rural;
            $block_ulb = $request->block_ulb;

            $query = "select u.email,u.username,u.mobile_no from public.users u join public.duty_assignement d on d.user_id=u.id where u.designation_id='".$designation."' and d.scheme_id=".$scheme_id." and d.is_active=1  and u.is_active=1";
            if ($designation == 'Approver') {
                if (!is_null($district_code)) {
                    $query .= " and d.district_code=".$district_code;
                }
            }
            else if ($designation == 'Verifier' || $designation == 'Operator') {
                if (!is_null($district_code)) {
                    $query .= " and d.district_code=".$district_code;
                }
                if (!is_null($is_rural)) {
                    if ($is_rural == 1) { //Urban (urban_body_code)
                        $query .= " and d.is_urban=".$is_rural;
                        if (!is_null($block_ulb)) {
                            $query .= " and d.urban_body_code=".$block_ulb;
                        }
                    }
                    else if ($is_rural == 2) { //Block (taluka_code)
                        $query .= " and d.is_urban=".$is_rural;
                        if (!is_null($block_ulb)) {
                            $query .= " and d.taluka_code=".$block_ulb;
                        }
                    }
                }
            }
            else {

            }

            $results = DB::connection('pgsql_mis')->select($query);
            $response = array(
                'query'=>$query, 'result'=>$results, 'designation'=>$designation, 'scheme_id'=>$scheme_id, 'district_code'=>$district_code, 'is_rural'=>$is_rural, 'block_ulb'=>$block_ulb
            );
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Oops. Connection time out. Please try agian later.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function getEmailUsingMobileNo(Request $request){
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $mobile_no = $request->mobile_no;
            $emailObj = User::where('mobile_no',$mobile_no)->where('is_active',1)->first();
            if (isset($emailObj)) {
                $response = array('email'=>$emailObj->email, 'is_exists'=>1);
            }
            else {
                $response = array('email'=>'Mobile no. not exists in the system','is_exists'=>0);
            }
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Oops. Connection time out. Please try agian later.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function sendingMail(Request $request){
        // dd($request->all());
        $data = array(
            'email_addr' => $request->email_addr,
            'subject' => $request->subject,
            'body' => $request->mail_body,
            //'cc' => 'basuri.pradyut@gmail.com',
        );
        $files = $request->file('mail_file');

        $val = $this->genericSendingMail($data, $files);
        if ($val['is_send'] == 1) {
            return redirect('sending-mail-to-users')->with('success', $val['response']);
        }
        else {
            return redirect('sending-mail-to-users')->with('error', $val['response']);
        }
    }

    public function genericSendingMail($data, $files){
        $val = [];
        try {
            Mail::send('send-mail-to-users.sending_mail', array('bodyMessage' => $data['body']), function($message) use($data, $files) {
                $message->to(['basuri.pradyut@gmail.com','subhankarbisoyee5@gmail.com']);
                $message->subject($data['subject']);
                // $message->setBody($data['body']);
                // $message->cc($data['cc']);
                if (count((array)$files) > 0) {
                    foreach ($files as $file) {
                        $message->attach($file->getRealPath(), array(
                            'as' => $file->getClientOriginalName(),      
                            'mime' => $file->getMimeType())
                        );
                    }
                }
            });

            // Mail::to('subhankarbisoyee5@gmail.com')->send(new SendingToUser($data));
            $response = 'Send Successfully';
            return $val = ['is_send' => 1, 'response' => $response];
        }
        catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $response = $e->getMessage();
            return $val = ['is_send' => 0, 'response' => $response];
        }
    }
}
