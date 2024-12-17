<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class SI
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
        if (Auth::check() && Auth::user()->designation_id_old == 'SI') {
            return $next($request);
        }
        elseif (Auth::check() && Auth::user()->designation_id_old == 'DCP') {
            return redirect('/DCP');
        }
        elseif (Auth::check() && Auth::user()->designation_id_old == 'ACP') {
            return redirect('/ACP');
        }
        else {
            return redirect('/Admin');
        }
    }
}
