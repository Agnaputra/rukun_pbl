<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DokumenWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DokumenWargaController extends Controller
{
    /**
     * Mendapatkan daftar dokumen milik warga yang login
     * [GET] /api/dokumen
     */
    public function index()
    {
        $warga = Auth::user()->warga;
        $dokumen = DokumenWarga::where('warga_id', $warga->warga_id)->get();
        return response()->json($dokumen);
    }

    /**
     * Warga mengupload dokumen baru
     * [POST] /api/dokumen
     */
    public function store(Request $request)
    {
        $warga = Auth::user()->warga;

        $validator = Validator::make($request->all(), [
            'nama_dokumen' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,png|max:5120', // Maks 5MB
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $filePath = $request->file('file')->store("private/dokumen_warga/warga_{$warga->warga_id}");

        $dokumen = DokumenWarga::create([
            'warga_id' => $warga->warga_id,
            'nama_dokumen' => $request->nama_dokumen,
            'file_path' => $filePath,
        ]);

        return response()->json($dokumen, 201);
    }

    /**
     * Warga menghapus dokumen miliknya
     * [DELETE] /api/dokumen/{id}
     */
    public function destroy($id)
    {
        $warga = Auth::user()->warga;
        $dokumen = DokumenWarga::find($id);

        if (!$dokumen) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        // Keamanan: Cek kepemilikan
        if ($dokumen->warga_id != $warga->warga_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        Storage::delete($dokumen->file_path); // Hapus file dari storage
        $dokumen->delete(); // Hapus data dari DB

        return response()->json(null, 204);
    }
}
