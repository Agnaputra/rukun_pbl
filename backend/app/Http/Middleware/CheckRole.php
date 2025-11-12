<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user || !$user->loadMissing('role')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!in_array($user->role->nama_role, $roles)) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak memiliki hak akses (role) untuk aksi ini.'
            ], 403);
        }

        return $next($request);
    }
}
