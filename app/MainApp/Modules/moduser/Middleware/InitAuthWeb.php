<?php

namespace App\MainApp\Modules\moduser\Middleware;

use Closure;
// use Illuminate\Support\Facades\Auth;
use Facades\App\MainApp\Modules\moduser\Services\UserAuth;

/**
 * 
 */
class InitAuthWeb
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
        UserAuth::setInit();
        return $next($request);
    }
    
}
