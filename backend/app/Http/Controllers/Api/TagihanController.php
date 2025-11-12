<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagihanController extends Controller
{
    /**
     * Menampilkan semua tagihan (Hanya Admin/Bendahara)
     * [GET] /api/tagihan
     */
    public function index()
    {
        // Dilindungi oleh role:Admin,Bendahara
        $tagihan = Tagihan::with('keluarga:keluarga_id,nomor_kk,alamat', 'jenisIuran')
            ->paginate(20);
        return response()->json($tagihan);
    }

    /**
     * Membuat tagihan baru (Hanya Admin/Bendahara)
     * [POST] /api/tagihan
     */
    public function store(Request $request)
    {
        // Dilindungi oleh role:Admin,Bendahara
        $validator = Validator::make($request->all(), [
            'keluarga_id' => 'required|integer|exists:keluarga,keluarga_id',
            'jenis_iuran_id' => 'required|integer|exists:jenis_iuran,jenis_iuran_id',
            'nominal' => 'required|numeric|min:0',
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2020',
            'jatuh_tempo' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tagihan = Tagihan::create($request->all());

        // TODO: Kirim notifikasi ke 'keluarga_id'

        return response()->json($tagihan, 201);
    }

    /**
     * Menampilkan tagihan milik Warga yang sedang login
     * [GET] /api/tagihan/saya
     */
    public function getMyTagihan()
    {
        $warga = Auth::user()->warga;
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan'], 404);
        }

        $tagihan = Tagihan::where('keluarga_id', $warga->keluarga_id)
            ->with('jenisIuran', 'pembayaran')
            ->paginate(10);

        return response()->json($tagihan);
    }
}
