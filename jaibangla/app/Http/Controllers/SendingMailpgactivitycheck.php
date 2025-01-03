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

class SendingMailpgactivitycheck extends Controller
{

   
    // public function __construct()
    // {
    //     $this->middleware('auth');   
    // }
   
    public function sendingMail(Request $request){
        // dd($request->all());
        $return_arr=array();
       
        $data = array(
            'subject' =>'Check PG Activity',
            'body' => 'Database Concurrency Problem. Number of Users Currently Access in Database',
            //'cc' => 'basuri.pradyut@gmail.com',
        );
    

        $count_check = DB::select("select count(1) from pg_stat_activity")[0]->count;

       //dd($count_check);

        if($count_check>1000)
        {
            $val = $this->genericSendingMail($data,$count_check);
//dd($val);

            if ($val['is_send'] == 1) {

                $return_arr['is_send']=1;
                $return_arr['response']=$val['response'];
                // $return_arr['message']=$message;
               
                return $return_arr;
            }
            else {
                $return_arr['is_send']=0;
                $return_arr['response']=$val['response'];
                return $return_arr;
            }
        }

        else{
            // return redirect('sending-mail-check-pgactivity')->with('success', 'PG Activity No problem' );

            $return_arr['is_send']=0;
            $return_arr['response']='PG Activity No problem';
            return $return_arr;

        }

        
        
    }

    public function genericSendingMail($data,$count_check){
       
        $val = [];
        try {
            Mail::send('send-mail-to-users.sending_mail', array('bodyMessage' => $data['body']), function($message) use($data,$count_check) {
            $message->to(['debjit.talukdar359@gmail.com','subhankarbisoyee5@gmail.com','surajitpandit350@gmail.com','sau.gopinath@gmail.com','soumyajitde2903@gmail.com']);
            $message->subject($data['subject']);
            $message->setBody($data['body'].' count :'.$count_check);
                // $message->cc($data['cc']);
                
            });

            // Mail::to('subhankarbisoyee5@gmail.com')->send(new SendingToUser($data));
            $response = 'Database Concurrency Problem. Number of Users Currently Access in Database are increse, mail Send Successfully';
            return $val = ['is_send' => 1, 'response' => $response];
        }
        catch (\Exception $e) {

            //dd($e);
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $response = $e->getMessage();
            return $val = ['is_send' => 0, 'response' => $response];
        }
    }
}
