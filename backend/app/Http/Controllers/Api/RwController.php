<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RwController extends Controller
{
    /**
     * Menampilkan daftar semua RW.
     * [GET] /api/rw
     */
    public function index()
    {
        // Muat juga data RT yang ada di bawahnya
        $rws = Rw::with('rt')->paginate(10);
        return response()->json($rws);
    }

    /**
     * Menyimpan data RW baru.
     * [POST] /api/rw
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_rw' => 'required|string|max:5|unique:rw,nomor_rw',
            'nama_ketua_rw' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rw = Rw::create($request->all());

        return response()->json([
            'message' => 'Data RW berhasil ditambahkan',
            'data' => $rw
        ], 201);
    }

    /**
     * Menampilkan detail satu RW.
     * [GET] /api/rw/{id}
     */
    public function show($id)
    {
        // Muat semua RT di RW ini, dan semua Keluarga di setiap RT tsb.
        $rw = Rw::with('rt.keluarga')->find($id);

        if (!$rw) {
            return response()->json(['message' => 'Data RW tidak ditemukan'], 404);
        }

        return response()->json($rw);
    }

    /**
     * Mengupdate data RW.
     * [PUT/PATCH] /api/rw/{id}
     */
    public function update(Request $request, $id)
    {
        $rw = Rw::find($id);
        if (!$rw) {
            return response()->json(['message' => 'Data RW tidak ditemukan'], 404);
        }

        // --- INI PERBAIKANNYA ---

        // Tentukan apakah ini PUT (wajib semua) atau PATCH (opsional)
        $isPatch = $request->isMethod('PATCH');

        $validator = Validator::make($request->all(), [
            'nomor_rw' => [
                // 'sometimes' berarti: jalankan validasi ini HANYA JIKA
                // field 'nomor_rw' ada di dalam request.
                // 'required' berarti: field WAJIB ada.
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:5',
                // Pastikan 'nomor_rw' unik, KECUALI untuk ID $id ini
                Rule::unique('rw', 'nomor_rw')->ignore($id, 'rw_id')
            ],
            'nama_ketua_rw' => 'nullable|string|max:100',
            // Tambahkan 'sometimes' untuk field lain jika ada
        ]);

        // --- AKHIR PERBAIKAN ---

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gunakan fill() dan save() agar lebih aman untuk PATCH
        // Ini HANYA akan mengupdate field yang Anda kirim
        $rw->fill($request->all());
        $rw->save();

        return response()->json([
            'message' => 'Data RW berhasil diperbarui',
            'data' => $rw
        ]);
    }

    /**
     * Menghapus data RW.
     * [DELETE] /api/rw/{id}
     */
    public function destroy($id)
    {
        $rw = Rw::find($id);
        if (!$rw) {
            return response()->json(['message' => 'Data RW tidak ditemukan'], 404);
        }
        $rw->delete();

        return response()->json(['message' => 'Data RW berhasil dihapus']);
    }
}
