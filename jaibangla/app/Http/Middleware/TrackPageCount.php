<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

use App\PageHits;
use Carbon\Carbon;
use Closure;
use App\Helpers\AuthChecker;

class TrackPageCount
{
    public function handle($request, Closure $next)
    {
        /* You can store your user data with model, db or whatever...
           Here I use a repository that contains all my model queries. */
        $PageHits = new PageHits();
        if (empty(Auth::user()->id)) {
            $user_id = NULL;
        } else
            $user_id = AuthChecker::getUserId();
        $PageHits->insert([
            'user_id'   => $user_id,
            'ip'   => request()->ip(),
            'page_url'   => request()->path(),
            'visit_datetime' => Carbon::now()
        ]);

        return $next($request);
    }
}
