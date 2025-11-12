<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RtController extends Controller
{
    /**
     * Menampilkan daftar semua RT.
     * [GET] /api/rt
     */
    public function index()
    {
        // Muat juga data RW induknya
        $rts = Rt::with('rw')->paginate(10);
        return response()->json($rts);
    }

    /**
     * Menyimpan data RT baru.
     * [POST] /api/rt
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rw_id' => 'required|integer|exists:rw,rw_id',
            'nomor_rt' => 'required|string|max:5',
            'nama_ketua_rt' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rt = Rt::create($request->all());

        return response()->json([
            'message' => 'Data RT berhasil ditambahkan',
            'data' => $rt
        ], 201);
    }

    /**
     * Menampilkan detail satu RT.
     * [GET] /api/rt/{id}
     */
    public function show($id)
    {
        // Muat data RW induk dan daftar keluarga di RT ini
        $rt = Rt::with(['rw', 'keluarga'])->find($id);

        if (!$rt) {
            return response()->json(['message' => 'Data RT tidak ditemukan'], 404);
        }

        return response()->json($rt);
    }

    /**
     * Mengupdate data RT.
     * [PUT/PATCH] /api/rt/{id}
     */
    public function update(Request $request, $id)
    {
        $rt = Rt::find($id);
        if (!$rt) {
            return response()->json(['message' => 'Data RT tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rw_id' => 'required|integer|exists:rw,rw_id',
            'nomor_rt' => 'required|string|max:5',
            'nama_ketua_rt' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rt->update($request->all());

        return response()->json([
            'message' => 'Data RT berhasil diperbarui',
            'data' => $rt
        ]);
    }

    /**
     * Menghapus data RT.
     * [DELETE] /api/rt/{id}
     */
    public function destroy($id)
    {
        $rt = Rt::find($id);
        if (!$rt) {
            return response()->json(['message' => 'Data RT tidak ditemukan'], 404);
        }

        $rt->delete();

        return response()->json(['message' => 'Data RT berhasil dihapus']);
    }
}
