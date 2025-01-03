<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   

    function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->designation_id == 'Admin') {
            return $next($request);
        }
        /*elseif (Auth::check() && Auth::user()->designation_id == 'DCP') {
            return redirect('/DCP');
        }
        else if (Auth::check() && Auth::user()->designation_id == 'ACP') { 
            return redirect('/ACP');
        }*/
        else {
            return redirect('/login');
        }
    }
}
