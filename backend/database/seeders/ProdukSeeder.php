<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\JenisSayur;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produk')->delete();
        $faker = Faker::create('id_ID');

        $tokoIds = Toko::where('status_verifikasi', 'Disetujui')->pluck('toko_id');
        $jenisIds = JenisSayur::pluck('jenis_id');

        if ($tokoIds->isEmpty() || $jenisIds->isEmpty()) {
            $this->command->error('Toko terverifikasi atau JenisSayur kosong.');
            return;
        }

        $daftarProduk = [
            'Bayam Hijau Segar' => 5000,
            'Kangkung Ikat' => 3000,
            'Wortel Berastagi' => 8000,
            'Tomat Cherry' => 12000,
            'Cabai Rawit Merah' => 25000,
            'Bawang Merah Brebes' => 30000,
            'Kentang Dieng' => 15000,
            'Buncis Super' => 7000,
            'Terong Ungu' => 6000,
            'Jahe Emprit' => 20000,
        ];

        // Buat 2-3 produk per toko
        foreach ($tokoIds as $tokoId) {
            $produkSample = array_rand($daftarProduk, rand(2, 4));
            if (!is_array($produkSample)) $produkSample = [$produkSample]; // Handle jika cuma 1

            foreach ($produkSample as $namaProduk) {
                $harga = $daftarProduk[$namaProduk];
                Produk::create([
                    'toko_id' => $tokoId,
                    'jenis_id' => $jenisIds->random(),
                    'nama_produk' => $namaProduk,
                    'deskripsi' => $faker->paragraph(1),
                    'harga' => $harga,
                    'stok' => rand(10, 100),
                    'foto_produk_path' => 'path/to/produk/' . str_replace(' ', '-', $namaProduk) . '.jpg',
                ]);
            }
        }
    }
}
