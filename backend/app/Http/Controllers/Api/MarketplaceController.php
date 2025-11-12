<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Menampilkan semua produk dari toko yang disetujui.
     * [GET] /api/marketplace/produk
     */
    public function listProduk(Request $request)
    {
        $produk = Produk::whereHas('toko', function ($query) {
            $query->where('status_verifikasi', 'Disetujui'); // Hanya dari toko aktif
        })
        ->with('toko:toko_id,nama_toko', 'jenisSayur:jenis_id,nama_jenis') // Hanya data yg relevan
        ->paginate(20);

        return response()->json($produk);
    }

    /**
     * Menampilkan detail satu produk.
     * [GET] /api/marketplace/produk/{id}
     */
    public function showProduk($id)
    {
        $produk = Produk::with('toko', 'jenisSayur')->find($id);

        if (!$produk || $produk->toko->status_verifikasi != 'Disetujui') {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // TODO: Muat review produk di sini
        // $produk->load('review.warga:warga_id,nama_lengkap');

        return response()->json($produk);
    }

    /**
     * Menampilkan semua toko yang disetujui.
     * [GET] /api/marketplace/toko
     */
    public function listToko(Request $request)
    {
        $toko = Toko::where('status_verifikasi', 'Disetujui')->paginate(20);
        return response()->json($toko);
    }

    /**
     * Menampilkan detail satu toko.
     * [GET] /api/marketplace/toko/{id}
     */
    public function showToko($id)
    {
        $toko = Toko::with('produk')->find($id);

        if (!$toko || $toko->status_verifikasi != 'Disetujui') {
            return response()->json(['message' => 'Toko tidak ditemukan'], 404);
        }

        return response()->json($toko);
    }
}
