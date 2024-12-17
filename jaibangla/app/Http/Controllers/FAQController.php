<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FAQController extends Controller
{
    public function __construct(){
		set_time_limit(120);
  	    $this->middleware('auth');
	    date_default_timezone_set('Asia/Kolkata');
    }
    public function index() { 
        $faq_data = DB::table('public.m_faq')->orderBy('id')->get();
        return view('faq/faq_index', ['faq' => $faq_data]);
    }
}
