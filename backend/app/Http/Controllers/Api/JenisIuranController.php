<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisIuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JenisIuranController extends Controller
{
    // Rute ini akan dilindungi oleh role Admin

    public function index()
    {
        return JenisIuran::paginate(15);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_iuran' => 'required|string|max:100|unique:jenis_iuran',
            'nominal_default' => 'required|numeric|min:0',
            'periode' => ['required', Rule::in(['Bulanan', 'Tahunan', 'Sekali Bayar'])],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jenisIuran = JenisIuran::create($request->all());
        return response()->json($jenisIuran, 201);
    }

    public function show($id)
    {
        $jenisIuran = JenisIuran::find($id);
        if (!$jenisIuran) {
            return response()->json(['message' => 'Jenis iuran tidak ditemukan'], 404);
        }
        return response()->json($jenisIuran);
    }

    public function update(Request $request, $id)
    {
        $jenisIuran = JenisIuran::find($id);
        if (!$jenisIuran) {
            return response()->json(['message' => 'Jenis iuran tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_iuran' => 'required|string|max:100|' . Rule::unique('jenis_iuran')->ignore($id, 'jenis_iuran_id'),
            'nominal_default' => 'required|numeric|min:0',
            'periode' => ['required', Rule::in(['Bulanan', 'Tahunan', 'Sekali Bayar'])],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jenisIuran->update($request->all());
        return response()->json($jenisIuran);
    }

    public function destroy($id)
    {
        $jenisIuran = JenisIuran::find($id);
        if (!$jenisIuran) {
            return response()->json(['message' => 'Jenis iuran tidak ditemukan'], 404);
        }

        // TODO: Cek apakah iuran ini sudah dipakai di 'tagihan' sebelum dihapus

        $jenisIuran->delete();
        return response()->json(null, 204);
    }
}
