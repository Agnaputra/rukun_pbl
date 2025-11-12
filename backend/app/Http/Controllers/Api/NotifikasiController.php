<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Mendapatkan notifikasi untuk user yang login
     * [GET] /api/notifikasi
     */
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id_penerima', Auth::id())
            ->paginate(20);
        return response()->json($notifikasi);
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca
     * [POST] /api/notifikasi/{id}/baca
     */
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::find($id);

        // Keamanan: Cek kepemilikan
        if (!$notifikasi || $notifikasi->user_id_penerima != Auth::id()) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notifikasi->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca']);
    }
}
