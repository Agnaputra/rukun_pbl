<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewProduk;
use App\Models\Produk;
use App\Models\Warga;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReviewProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('review_produk')->delete();
        $faker = Faker::create('id_ID');

        // Ambil pesanan yang sudah 'Selesai'
        $pesananSelesai = Pesanan::where('status_pesanan', 'Selesai')->get();

        if ($pesananSelesai->isEmpty()) {
            $this->command->info('Tidak ada pesanan "Selesai" untuk direview.');
            return;
        }

        foreach ($pesananSelesai as $pesanan) {
            // Ambil detail dari pesanan ini
            foreach ($pesanan->detail as $detail) {
                // Beri review dengan kans 50%
                if (rand(0, 1) == 1) {
                    ReviewProduk::create([
                        'produk_id' => $detail->produk_id,
                        'warga_id' => $pesanan->warga_pembeli_id,
                        'rating' => rand(3, 5),
                        'komentar' => $faker->sentence(rand(5, 15)),
                    ]);
                }
            }
        }
    }
}
