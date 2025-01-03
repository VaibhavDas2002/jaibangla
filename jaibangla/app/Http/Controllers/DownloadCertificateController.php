<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use App\applicationModel;

class DownloadCertificateController extends Controller
{
    public function show(Request $request)
    {
        $mobile = $request->session()->get('session_mobile');
        $objAppliaction = applicationModel::where('mobile_no', $mobile)->where('current_status','READY')->orderBy('created_at', 'DESC')->first();
        $storagePath = storage_path('app/keep/'. $objAppliaction->application_id . '/' . $objAppliaction->signed_certificate);
        //$request->session()->forget('session_mobile');
        return response()->download($storagePath,null,[],null) ;
        
    }

    public function downloadImage($user_id, $slug, $request){
         $mobile = $request->session()->get('session_mobile');
         $objAppliaction = applicationModel::where('mobile_no', $mobile)->where('current_status','READY')->orderBy('created_at', 'DESC')->first();
         $storagePath = storage_path('app/keep/'. $objAppliaction->application_id . '/' . $objAppliaction->signed_certificate);
          return response()->download($storagePath,null,[],null) ;
          //$request->session()->forget('session_mobile');
    }
    /*public function viewImage(Request $request,$user_id, $slug)
    {
        $mobile = $request->session()->get('session_mobile');
        $objAppliaction = applicationModel::where('mobile_no', $mobile)->where('current_status','READY')->orderBy('created_at', 'DESC')->first();

        return Image::make(storage_path() . '/app/keep/' . $user_id . '/' . $slug)->response();
         Log::info($storagePath);

        //return Image::make(storage_path() . '/app/keep/' . $objAppliaction->application_id . '/' . $objAppliaction->signed_certificate)->response()

        //$storagePath = storage_path('app/keep/'. $objAppliaction->application_id . '/' . $objAppliaction->signed_certificate);

        //return Image::make(storage_path() . '/app/keep/' . $user_id . '/' . $slug)->response();
         Log::info($storagePath);

        return response()->download($storagePath,null,[],null) ;
    }*/
}
