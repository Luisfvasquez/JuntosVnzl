<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles Allowed roles: superadmin, coordinator
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado. Inicie sesión primero.',
            ], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para realizar esta acción. Rol requerido: ' . implode(', ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
