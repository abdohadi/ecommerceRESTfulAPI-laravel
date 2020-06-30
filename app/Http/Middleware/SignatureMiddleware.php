<?php

namespace App\Http\Middleware;

use Closure;

class SignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $headerName = 'X-Name')     // it's a good practice to add 'X-' to custom headers in our response
    {
        $response = $next($request);

        $response->headers->set($headerName, config('app.name'));

        return $response;
    }
}
