<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PushToIfmsController;

class ImportResponseController extends Controller
{
    public function __construct() 
    {
        $this->middleware('Admin');
    }	
    public function import_rbi_response() 
    {
		echo 'Function import_rbi_response() is called by Admin manually.';
		$getPushToIfms = new PushToIfmsController();
		return $getPushToIfms->import_rbi_list();
    }
}
