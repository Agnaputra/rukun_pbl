<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TokoController extends Controller
{
    /**
     * Mendaftarkan toko baru untuk user yang sedang login.
     * [POST] /api/toko
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan.'], 404);
        }
        $warga = $user->warga;

        // Cek apakah sudah punya toko
        if ($warga->toko) {
            return response()->json(['message' => 'Anda sudah memiliki toko.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:100|unique:toko,nama_toko',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Maks 1MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek flag verifikasi dari tabel warga (sesuai alur kita)
        $statusVerifikasi = $warga->is_verified_face
                            ? 'Disetujui' // Langsung setujui
                            : 'Pending';  // Set pending jika belum verifikasi

        $logoPath = null;
        if ($request->hasFile('logo')) {
            // Path disimpan di 'storage/app/public/toko_logo/...'
            // Pastikan Anda menjalankan `php artisan storage:link`
            $logoPath = $request->file('logo')->store("public/toko_logo/warga_{$warga->warga_id}");
        }

        $toko = Toko::create([
            'warga_pemilik_id' => $warga->warga_id,
            'nama_toko' => $request->nama_toko,
            'deskripsi' => $request->deskripsi,
            'logo_path' => $logoPath,
            'status_verifikasi' => $statusVerifikasi
        ]);

        $message = $statusVerifikasi == 'Disetujui'
            ? 'Selamat! Toko Anda telah disetujui dan siap digunakan.'
            : 'Toko Anda telah dibuat, tapi masih pending. Silakan lakukan Verifikasi Identitas di menu profil.';

        return response()->json([
            'message' => $message,
            'data' => $toko
        ], 201);
    }

    /**
     * Mendapatkan detail toko milik user yang sedang login.
     * [GET] /api/toko/saya
     */
    public function getMyToko()
    {
        $warga = Auth::user()->warga;
        if (!$warga || !$warga->toko) {
            return response()->json(['message' => 'Anda belum memiliki toko.'], 404);
        }

        $warga->toko->load('produk'); // Muat juga produk-produknya
        return response()->json($warga->toko);
    }

    /**
     * Update toko milik user yang sedang login.
     * [PUT/PATCH] /api/toko/saya
     */
    public function updateMyToko(Request $request)
    {
        $warga = Auth::user()->warga;
        if (!$warga || !$warga->toko) {
            return response()->json(['message' => 'Anda belum memiliki toko.'], 404);
        }

        $toko = $warga->toko;

        // Gunakan 'sometimes' untuk PATCH
        $validator = Validator::make($request->all(), [
            'nama_toko' => 'sometimes|required|string|max:100|unique:toko,nama_toko,' . $toko->toko_id . ',toko_id',
            'deskripsi' => 'sometimes|nullable|string',
            // TODO: Tambahkan logika update logo jika perlu
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $toko->fill($request->only('nama_toko', 'deskripsi'));
        $toko->save();

        return response()->json([
            'message' => 'Toko berhasil diperbarui.',
            'data' => $toko
        ]);
    }
}
