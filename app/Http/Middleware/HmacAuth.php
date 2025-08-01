<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HmacAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        $body = $request->getContent();
        $key = 'verysecret';
        $header = $timestamp . $body;
        
        $hash = hash_hmac('sha256', $header, $key);

        if($hash !== $signature){
            abort(403, 'Invalid!');
        }

        return $next($request);
    }
}
