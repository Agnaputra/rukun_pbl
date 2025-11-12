<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class DetailPesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('detail_pesanan')->delete();

        $pesananIds = Pesanan::pluck('pesanan_id');
        $produkIds = Produk::where('stok', '>', 0)->pluck('produk_id');

        if ($pesananIds->isEmpty() || $produkIds->isEmpty()) {
            $this->command->error('Pesanan atau Produk kosong.');
            return;
        }

        foreach ($pesananIds as $pesananId) {
            $pesanan = Pesanan::find($pesananId);
            $totalHargaPesanan = 0;
            $jumlahItem = rand(1, 3); // 1-3 item per pesanan

            for ($i = 0; $i < $jumlahItem; $i++) {
                $produk = Produk::find($produkIds->random());
                $jumlahBeli = rand(1, 3);
                $hargaSaatBeli = $produk->harga;
                $subtotal = $jumlahBeli * $hargaSaatBeli;

                DetailPesanan::create([
                    'pesanan_id' => $pesananId,
                    'produk_id' => $produk->produk_id,
                    'jumlah' => $jumlahBeli,
                    'harga_saat_beli' => $hargaSaatBeli,
                ]);

                $totalHargaPesanan += $subtotal;
            }

            // *** PENTING: Update total_harga di tabel pesanan ***
            $pesanan->total_harga = $totalHargaPesanan;
            $pesanan->save();
        }
    }
}
