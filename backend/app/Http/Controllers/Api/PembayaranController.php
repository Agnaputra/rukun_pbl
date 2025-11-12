<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * Warga melakukan pembayaran (upload bukti transfer)
     * [POST] /api/pembayaran
     */
    public function store(Request $request)
    {
        $warga = Auth::user()->warga;
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tagihan_id' => 'required|integer|exists:tagihan,tagihan_id',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // File upload
            'metode_bayar' => 'required|in:Transfer,QRIS,Gopay,Tunai',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tagihan = Tagihan::find($request->tagihan_id);

        // Keamanan: Cek apakah tagihan ini milik keluarga si user
        if ($tagihan->keluarga_id != $warga->keluarga_id) {
            return response()->json(['message' => 'Akses ditolak. Ini bukan tagihan Anda.'], 403);
        }

        // Keamanan: Cek apakah sudah lunas
        if ($tagihan->status_pembayaran == 'Lunas') {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Simpan file bukti bayar
            $buktiPath = $request->file('bukti_bayar')->store("private/bukti_bayar/keluarga_{$warga->keluarga_id}");

            // 2. Panggil FastAPI untuk OCR (Simulasi)
            $statusVerifikasi = 'Pending'; // Default
            $ocrText = null;

            /*
            // --- KODE ASLI FASTAPI (OCR Bukti Transfer) ---
            try {
                $fastApiResponse = Http::withHeaders([...])
                    ->attach('image', file_get_contents(Storage::path($buktiPath)), 'bukti.jpg')
                    ->post(env('FASTAPI_OCR_URL', 'http://url-fastapi-anda/ocr-transfer'));

                if ($fastApiResponse->successful()) {
                    $ocrResult = $fastApiResponse->json();
                    $ocrText = json_encode($ocrResult);

                    // Cek otomatis
                    if (isset($ocrResult['nominal']) && $ocrResult['nominal'] == $tagihan->nominal) {
                        $statusVerifikasi = 'Disetujui'; // AUTO-APPROVE
                    }
                }
            } catch (\Exception $e) {
                // Biarkan status 'Pending' jika FastAPI gagal
            }
            */
            // --- AKHIR KODE FASTAPI ---

            // 3. Buat data pembayaran
            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->tagihan_id,
                'warga_id_pembayar' => $warga->warga_id,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $tagihan->nominal, // Asumsi bayar lunas
                'metode_bayar' => $request->metode_bayar,
                'bukti_bayar_path' => $buktiPath,
                'ocr_result_text' => $ocrText,
                'status_verifikasi' => $statusVerifikasi
            ]);

            // 4. Update status tagihan
            $tagihan->status_pembayaran = $statusVerifikasi == 'Disetujui' ? 'Lunas' : 'Menunggu Verifikasi';
            $tagihan->save();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil diupload. Menunggu verifikasi.',
                'data' => $pembayaran
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan pembayaran', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin/Bendahara memverifikasi pembayaran yang 'Pending'
     * [POST] /api/pembayaran/{id}/verifikasi
     */
    public function verifikasiPembayaran(Request $request, $id)
    {
        // Dilindungi oleh role:Admin,Bendahara
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $pembayaran = Pembayaran::with('tagihan')->find($id);
        if (!$pembayaran) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        if ($pembayaran->status_verifikasi != 'Pending') {
            return response()->json(['message' => 'Pembayaran ini sudah diproses.'], 400);
        }

        DB::beginTransaction();
        try {
            $pembayaran->status_verifikasi = $request->status;
            $pembayaran->catatan_verifikasi = $request->catatan;
            $pembayaran->verifikasi_oleh_user_id = Auth::id();
            $pembayaran->verifikasi_timestamp = now();
            $pembayaran->save();

            // Update tagihan terkait
            if ($request->status == 'Disetujui') {
                $pembayaran->tagihan->update(['status_pembayaran' => 'Lunas']);
            } else {
                $pembayaran->tagihan->update(['status_pembayaran' => 'Ditolak']);
            }

            DB::commit();

            // TODO: Kirim notifikasi ke warga

            return response()->json([
                'message' => 'Verifikasi pembayaran berhasil.',
                'data' => $pembayaran
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal verifikasi', 'error' => $e->getMessage()], 500);
        }
    }
}
