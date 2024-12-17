<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sms;
use App\User;


class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
        $this->middleware('Admin');
    }

    public function index()
    {
      $users = User::where('is_active',1)->get();
      $smss = Sms::paginate(5);


      

       return view('sms-mgmt/index', ['users' => $users])->with('smss',$smss);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('is_active',1)->get();

        return view('sms-mgmt/create')->with('users',$users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   $users = User::where('is_active',1)->get();
        $this->validateInput($request);
        Sms::create([
            'message' => $request['sms_message'],
            'reason' => $request['sms_reason']
        ]);


          return redirect()->intended('sms-mgmt/smsTemplate')->with('users',$users);
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
        $users = User::where('is_active',1)->get();
        $smss = Sms::find($id);



        if ($smss != null) {
            return view('/sms-mgmt/edit')->with('users',$users)->with('smss',$smss);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $users = User::where('is_active',1)->get();
        $sms = Sms::findOrFail($id);
         $this->validate($request, [
        'sms_message' => 'required|max:60',
        'sms_reason' => 'required|max:60'
        ]);
        $input = [
            'message' => $request['sms_message'],
            'reason' => $request['sms_reason']
        ];
        Sms::where('id', $id)
            ->update($input);
        
        return redirect()->intended('sms-mgmt/smsTemplate')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search(Request $request){
        $users = User::where('is_active',1)->get();
         $constraints = [
            'reason' => $request['reason']
            ];
      
        
        $smss = $this->doSearchingQuery($constraints);
        //return view('sms-mgmt/index', ['searchsms' => $searchsms, 'searchingVals' => $constraints])->with('users',$users);
        /*echo "<pre>";
        print_r($constraints);
        echo "</pre>";
        die();*/
        return view('sms-mgmt/index', ['smss' => $smss, 'searchingVals' => $constraints])->with('users',$users);
        //return redirect()->intended('sms-mgmt/smsTemplate')->with('searchingVals',$constraints)->with('smss',$smss)->with('users',$users);

       
    }

    private function doSearchingQuery($constraints){
        $query = Sms::query();
        $fields = array_keys($constraints);
         $index = 0;
          foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }
            $index++;
          }
          return $query->paginate(10);
    }

     private function validateInput($request) {
        $this->validate($request, [
        'sms_message' => 'required|max:60',
        'sms_reason' => 'required'
    ]);
    }
}
