<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecureToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-Secure-Token')) {
            return response()->json([
                'success' => false,
                'message' => 'Token de seguridad requerido en el header X-Secure-Token',
            ], 403);
        }

        return $next($request);
    }
}
