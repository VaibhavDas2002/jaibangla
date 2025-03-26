<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\TraitCMOValidate;
class TestCMOController extends Controller
{
    use TraitCMOValidate;

    public function test(){
        // dd('ok');
        $data = $this->pullNewCmo();
    }
}
