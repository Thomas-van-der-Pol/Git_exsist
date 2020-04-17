<?php

namespace App\Http\Middleware;

use Closure;
use hisorange\BrowserDetect\Facade as Browser;

class BrowserDetect
{
    public function handle($request, Closure $next)
    {
        // IE11 or lower, not supported
        if (Browser::isIEVersion(11) || Browser::isIEVersion(10) || Browser::isIEVersion(9) || Browser::isIEVersion(8) || Browser::isIEVersion(7) || Browser::isIEVersion(6) || Browser::isIEVersion(5.5)) {
            return response(view('errors.browser'));
        }

        return $next($request);
    }
}