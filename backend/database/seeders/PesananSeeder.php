<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('pesanan')->delete(); <-- HAPUS BARIS INI
        $faker = Faker::create('id_ID');

        $wargaIds = Warga::pluck('warga_id');

        if ($wargaIds->isEmpty()) {
            $this->command->error('Tabel Warga kosong.');
            return;
        }

        $statuses = ['Menunggu Pembayaran', 'Keranjang', 'Diproses', 'Selesai', 'Batal'];

        // Buat 15 pesanan
        for ($i = 0; $i < 15; $i++) {
            $warga = Warga::find($wargaIds->random());
            Pesanan::create([
                'warga_pembeli_id' => $warga->warga_id,
                'tanggal_pesan' => $faker->dateTimeBetween('-3 months', 'now'),
                'total_harga' => 0, // Akan di-update oleh DetailPesananSeeder
                'status_pesanan' => $faker->randomElement($statuses),
                'alamat_pengiriman' => $warga->keluarga->alamat, // Ambil alamat dari keluarga
            ]);
        }
    }
}
