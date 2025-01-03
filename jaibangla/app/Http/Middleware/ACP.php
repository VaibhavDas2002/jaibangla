<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ACP
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
        if (Auth::check() && Auth::user()->designation_id == 'ACP') {
            return $next($request);
        }
        /*elseif (Auth::check() && Auth::user()->designation_id == 'Admin') {
            return redirect('/Admin');
        }
        elseif (Auth::check() && Auth::user()->designation_id == 'DCP') {
            return redirect('/DCP');
        }*/
        else {
            return redirect('/login');
        }
    }
}
