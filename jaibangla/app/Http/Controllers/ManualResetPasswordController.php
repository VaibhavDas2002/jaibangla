<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Log;
use Auth;

class ManualResetPasswordController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('resetpassword');
    }

    
    public function update(Request $request)
    {   
    	Log::info($request);     
        $this->validateInput($request);
        Log::info('this is second line'); 
        $input = [
            'password' => bcrypt($request['password'])
        ];
        User::where('id', Auth::id())
            ->update($input);
        
        return redirect()->intended('/');
    }

    private function validateInput($request) {
        $this->validate($request, [
        'password' => 'required|min:6|confirmed'
    ]);
    }
}
