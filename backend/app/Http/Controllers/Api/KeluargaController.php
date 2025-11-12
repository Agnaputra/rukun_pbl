<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeluargaController extends Controller
{
    /**
     * Menampilkan daftar semua keluarga.
     * [GET] /api/keluarga
     */
    public function index()
    {
        // Muat juga relasi RT dan jumlah anggota warga
        $keluarga = Keluarga::with('rt')->withCount('warga')->paginate(15);
        return response()->json($keluarga);
    }

    /**
     * Menyimpan data keluarga baru.
     * [POST] /api/keluarga
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rt_id' => 'required|integer|exists:rt,rt_id',
            'nomor_kk' => 'required|string|max:20|unique:keluarga,nomor_kk',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $keluarga = Keluarga::create($request->all());

        return response()->json([
            'message' => 'Data keluarga berhasil ditambahkan',
            'data' => $keluarga
        ], 201);
    }

    /**
     * Menampilkan detail satu keluarga.
     * [GET] /api/keluarga/{id}
     */
    public function show($id)
    {
        // Muat relasi lengkap: RT, semua Warga, dan Kepala Keluarga
        $keluarga = Keluarga::with(['rt', 'warga', 'kepalaKeluarga'])->find($id);

        if (!$keluarga) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        return response()->json($keluarga);
    }

    /**
     * Mengupdate data keluarga.
     * [PUT/PATCH] /api/keluarga/{id}
     */
    public function update(Request $request, $id)
    {
        $keluarga = Keluarga::find($id);
        if (!$keluarga) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rt_id' => 'required|integer|exists:rt,rt_id',
            'nomor_kk' => 'required|string|max:20|unique:keluarga,nomor_kk,' . $id . ',keluarga_id',
            'alamat' => 'required|string',
            'kepala_keluarga_id' => 'nullable|integer|exists:warga,warga_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $keluarga->update($request->all());

        return response()->json([
            'message' => 'Data keluarga berhasil diperbarui',
            'data' => $keluarga
        ]);
    }

    /**
     * Menghapus data keluarga.
     * [DELETE] /api/keluarga/{id}
     */
    public function destroy($id)
    {
        $keluarga = Keluarga::find($id);
        if (!$keluarga) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        // Hati-hati: Menghapus keluarga akan menghapus semua warga di dalamnya
        // (jika foreign key di-set 'on cascade'). Pastikan ini yang Anda mau.
        $keluarga->delete();

        return response()->json(['message' => 'Data keluarga berhasil dihapus']);
    }
}
