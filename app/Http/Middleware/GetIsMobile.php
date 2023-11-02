<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;
use App\Helper;

class GetIsMobile
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
        $isMobile = Helper::IsMobile();
        View::share('isMobile', $isMobile);
        View::share('MobileDisabled', $isMobile ? 'disabled' : '');
        return $next($request);
    }
}
