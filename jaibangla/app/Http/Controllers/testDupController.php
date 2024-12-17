<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DupCheck;
class testDupController extends Controller
{
    public function index(){
        $bank_code='33591317769';
        $aadharDupCheckBandhu = DupCheck::getDupCheckBank(20,$bank_code);
        // dd($aadharDupCheckBandhu);
        if (!empty($aadharDupCheckBandhu)){
            dd($aadharDupCheckBandhu);
        }else{
            dd('error');
        }
    }
}
