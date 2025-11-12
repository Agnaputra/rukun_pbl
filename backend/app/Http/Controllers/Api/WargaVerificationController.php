<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http; // Untuk memanggil FastAPI
use Illuminate\Support\Facades\Storage; // Untuk menyimpan file

class WargaVerificationController extends Controller
{
    /**
     * Menerima file KTP dan Wajah, memverifikasi, dan mengaktifkan akun/toko.
     * Endpoint: [POST] /api/warga/verifikasi-identitas
     */
    public function verifyIdentity(Request $request)
    {
        // 1. Dapatkan data warga dari user yang sedang login
        $user = Auth::user();
        if (!$user->warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan.'], 404);
        }
        $warga = $user->warga;

        // 2. Cek apakah sudah terverifikasi
        if ($warga->is_verified_face) {
            return response()->json(['message' => 'Akun Anda sudah terverifikasi.'], 400);
        }

        // 3. Validasi file yang di-upload
        $validator = Validator::make($request->all(), [
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
            'foto_wajah' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // 4. Simpan file-file ke storage
            // (Contoh: private/verifikasi/warga_1/ktp.jpg)
            // Pastikan folder 'storage/app/private' bisa ditulis oleh server
            $pathKtp = $request->file('foto_ktp')->store("private/verifikasi/warga_{$warga->warga_id}");
            $pathWajah = $request->file('foto_wajah')->store("private/verifikasi/warga_{$warga->warga_id}");

            // 5. Panggil API FastAPI Anda
            // INI ADALAH BAGIAN UTAMA CV/ML ANDA
            // ---------------------------------------------

            /*
            // --- CONTOH KODE ASLI UNTUK MEMANGGIL FASTAPI ---
            // (Anda bisa aktifkan ini saat FastAPI Anda siap)

            $fastApiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FASTAPI_SECRET_KEY') // Kunci rahasia antar server
            ])
            ->attach(
                'ktp_image', file_get_contents(Storage::path($pathKtp)), 'ktp.jpg'
            )
            ->attach(
                'face_image', file_get_contents(Storage::path($pathWajah)), 'wajah.jpg'
            )
            ->post(env('FASTAPI_VERIFY_URL', 'http://url-fastapi-anda/verify'));

            if (!$fastApiResponse->successful() || !$fastApiResponse->json('match')) {
                 // Hapus file jika gagal verifikasi
                 Storage::delete([$pathKtp, $pathWajah]);
                 return response()->json([
                     'message' => 'Verifikasi gagal.',
                     'error' => $fastApiResponse->json('error', 'Wajah tidak cocok atau KTP tidak terbaca.')
                 ], 400);
            }
            // ---------------------------------------------
            */

            // --- UNTUK SAAT INI, KITA SIMULASIKAN SUKSES ---
            $simulatedMatch = true; // Ganti jadi 'false' untuk tes gagal

            if (!$simulatedMatch) {
                Storage::delete([$pathKtp, $pathWajah]); // Hapus file jika gagal
                return response()->json(['message' => 'Verifikasi (simulasi) gagal. Wajah tidak cocok.'], 400);
            }

            // 6. Jika Sukses Verifikasi (dari FastAPI)
            $warga->foto_ktp = $pathKtp;
            $warga->foto_wajah = $pathWajah;
            $warga->is_verified_face = true; // <-- INI DIA!
            $warga->save();

            // 7. Cek dan setujui toko yang 'Pending'
            $pesanToko = "";
            if ($warga->toko && $warga->toko->status_verifikasi == 'Pending') {
                $warga->toko->update(['status_verifikasi' => 'Disetujui']);
                $pesanToko = " Toko Anda '${warga->toko->nama_toko}' kini telah disetujui!";
            }

            return response()->json([
                'message' => 'Verifikasi identitas berhasil.' . $pesanToko,
                'data' => $warga
            ], 200);
        } catch (\Exception $e) {
            // Tangani jika ada error saat penyimpanan file atau lainnya
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat verifikasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
