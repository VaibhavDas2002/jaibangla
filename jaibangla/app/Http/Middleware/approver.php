<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class approver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        if (Auth::check() && Auth::user()->designation_id == 'Approver') {
            return $next($request);
        }
       
        else {
            return redirect('/')->with('success', 'User not Authorized');
        }
        //return $next($request);
    }
}
