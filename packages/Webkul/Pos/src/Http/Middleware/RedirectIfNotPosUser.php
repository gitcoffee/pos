<?php

namespace Webkul\Pos\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * RedirectIfNotPosUser middleware
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class RedirectIfNotPosUser
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string|null  $guard
    * @return mixed
    */
    public function handle($request, Closure $next, $guard = 'posuser')
    {
        if (! Auth::guard($guard)->check()) {
            return response()->json(['error'=>'Warning: Unauthorized Pos User!']);
        }

        return $next($request);
    }
}
