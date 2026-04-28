<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUtf8Encoding
{
    public function handle(Request $request, Closure $next): Response
    {
        // Process the request
        $response = $next($request);

        // Force Content-Type header with UTF-8 charset
        $contentType = $response->headers->get('Content-Type', 'text/html');
        if (strpos($contentType, 'charset') === false) {
            $response->headers->set('Content-Type', $contentType.'; charset=UTF-8');
        }

        return $response;
    }
}
