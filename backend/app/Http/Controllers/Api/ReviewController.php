<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewProduk;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Warga memberikan review untuk produk yang sudah dibeli
     * [POST] /api/produk/{id}/review
     */
    public function store(Request $request, $produk_id)
    {
        $warga = Auth::user()->warga;
        $produk = Produk::find($produk_id);

        if (!$warga || !$produk) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5',
            'komentar' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        // Keamanan: Cek apakah warga ini pernah membeli produk ini?
        $hasPurchased = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.pesanan_id', '=', 'detail_pesanan.pesanan_id')
            ->where('pesanan.warga_pembeli_id', $warga->warga_id)
            ->where('detail_pesanan.produk_id', $produk_id)
            ->where('pesanan.status_pesanan', 'Selesai') // Hanya yg selesai
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['message' => 'Anda hanya bisa memberi review untuk produk yang sudah Anda beli.'], 403);
        }

        // Cek apakah sudah pernah review
        $existingReview = ReviewProduk::where('produk_id', $produk_id)
            ->where('warga_id', $warga->warga_id)
            ->first();
        if ($existingReview) {
            return response()->json(['message' => 'Anda sudah pernah memberikan review untuk produk ini.'], 400);
        }

        $review = ReviewProduk::create([
            'produk_id' => $produk_id,
            'warga_id' => $warga->warga_id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json($review, 201);
    }
}
