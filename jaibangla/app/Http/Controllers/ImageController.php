<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    //
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('Admin');
    }

    public function show($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep\\' . $slug);
        $storagePath = storage_path('app/keep/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }

    public function show_wcd($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_wcd\\' . $slug);
        $storagePath = storage_path('app/keep_wcd/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_manabik($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_manabik\\' . $slug);
        $storagePath = storage_path('app/keep_manabik/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_wp($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_wp\\' . $slug);
        $storagePath = storage_path('app/keep_wp/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_oap($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_oap\\' . $slug);
        $storagePath = storage_path('app/keep_oap/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_sc($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_sc\\' . $slug);
        $storagePath = storage_path('app/keep_sc/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_st($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_st\\' . $slug);
        $storagePath = storage_path('app/keep_st/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function show_legacy($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_legacy\\' . $slug);
        $storagePath = storage_path('app/keep_legacy/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }

    public function show_icad($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_ICAD\\' . $slug);
        $storagePath = storage_path('app/keep_ICAD/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }
    public function frontpageImage( $slug)
    {
        $storagePath = storage_path('app/keep/' . $slug);

        //Log::info($storagePath);
        return Image::make($storagePath)->response();
        //return response()->download(storage_path($storagePath), null, [], null);
    }



    public function spshow($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\sp_lot_mandate\\' . $slug);
        $storagePath = storage_path('app/sp_lot_mandate/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }

     public function show_farmer($slug)
    {
        //$storagePath = storage_path('app/images/' . $user_id . '/' . $slug);
        //$storagePath = Storage::url('app/public/' . $user_id . '/' . $slug);
        //return Image::make($storagePath)->response();
        $storagePath = storage_path('app\\keep_farmer\\' . $slug);
        $storagePath = storage_path('app/keep_farmer/' . $slug);
        Log::info($storagePath);
        //return $storagePath;
        return response()->download($storagePath,null,[],null) ;
    }



}
