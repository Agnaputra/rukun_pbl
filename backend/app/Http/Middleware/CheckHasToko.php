<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckHasToko
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user->warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan.'], 403);
        }

        if (!$user->warga->toko) {
            return response()->json(['message' => 'Akses ditolak. Anda belum mendaftar toko.'], 403);
        }

        if ($user->warga->toko->status_verifikasi !== 'Disetujui') {
            return response()->json([
                'message' => 'Akses ditolak. Toko Anda masih ' . $user->warga->toko->status_verifikasi
            ], 403);
        }

        return $next($request);
    }
}
