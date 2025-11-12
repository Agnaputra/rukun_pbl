<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Warga membuat pesanan baru (Checkout)
     * [POST] /api/pesanan
     */
    public function store(Request $request)
    {
        $warga = Auth::user()->warga;
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'alamat_pengiriman' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer|exists:produk,produk_id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $itemsData = $request->items;

            // Kunci ID produk untuk 'pessimistic lock' (mencegah race condition stok)
            $produkIds = array_column($itemsData, 'produk_id');
            $produks = Produk::whereIn('produk_id', $produkIds)->lockForUpdate()->get();

            $detailPesananData = [];

            foreach ($itemsData as $item) {
                $produk = $produks->find($item['produk_id']);

                // Cek stok
                if (!$produk || $produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok produk '{$produk->nama_produk}' tidak mencukupi.");
                }

                $hargaItem = $produk->harga * $item['jumlah'];
                $totalHarga += $hargaItem;

                // Kurangi stok
                $produk->stok -= $item['jumlah'];
                $produk->save();

                // Siapkan data detail
                $detailPesananData[] = [
                    'produk_id' => $produk->produk_id,
                    'jumlah' => $item['jumlah'],
                    'harga_saat_beli' => $produk->harga,
                ];
            }

            // 1. Buat Pesanan (Header)
            $pesanan = Pesanan::create([
                'warga_pembeli_id' => $warga->warga_id,
                'tanggal_pesan' => now(),
                'total_harga' => $totalHarga,
                'status_pesanan' => 'Menunggu Pembayaran',
                'alamat_pengiriman' => $request->alamat_pengiriman,
            ]);

            // 2. Buat Detail Pesanan (Items)
            // 'createMany' akan otomatis mengisi 'pesanan_id'
            $pesanan->detail()->createMany($detailPesananData);

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.',
                'data' => $pesanan->load('detail.produk')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat pesanan', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Warga melihat riwayat pesanannya
     * [GET] /api/pesanan/saya
     */
    public function getMyPesanan()
    {
        $warga = Auth::user()->warga;
        if (!$warga) {
            return response()->json(['message' => 'Profil warga tidak ditemukan'], 404);
        }

        $pesanan = Pesanan::where('warga_pembeli_id', $warga->warga_id)
            ->with('detail.produk:produk_id,nama_produk,foto_produk_path')
            ->paginate(10);

        return response()->json($pesanan);
    }
}
