<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHosts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedHosts = ['localhost', '127.0.0.1']; // Not a domain yet

        if (!in_array($request->getHost(), $allowedHosts)) {
            abort(403);
        }

        return $next($request);
    }
}
