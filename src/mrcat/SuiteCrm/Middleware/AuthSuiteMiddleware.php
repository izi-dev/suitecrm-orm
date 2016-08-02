<?php

namespace MrCat\SuiteCrm\Middleware;

use MrCat\SuiteCrm\Http\Api;
use MrCat\SuiteCrm\Token\GenerateToken;
use Closure;

class AuthSuiteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = GenerateToken::getToken();
        
        if (!is_null($token)) {
            
            Api::get()->setSession($token['id']);

            return $next($request);
        }

        abort(401, "permission denied");
    }
}
