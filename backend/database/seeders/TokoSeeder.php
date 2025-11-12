<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Toko;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('toko')->delete();
        $faker = Faker::create('id_ID');

        // Ambil 5 warga acak yang belum punya toko
        $wargaPemilikIds = Warga::doesntHave('toko')->inRandomOrder()->limit(5)->pluck('warga_id');

        if ($wargaPemilikIds->isEmpty()) {
            $this->command->error('Tidak ada Warga yang tersedia untuk jadi pemilik toko.');
            return;
        }

        $namaToko = ['Warung Bu Siti', 'Toko Kelontong Pak Budi', 'Sayur Segar Makmur', 'Sembako Jaya', 'Warung Kita Bersama'];
        $status = ['Pending', 'Disetujui', 'Ditolak'];

        foreach ($wargaPemilikIds as $index => $wargaId) {
            $nama = $namaToko[$index % count($namaToko)] . " " . $faker->lastName;
            Toko::create([
                'warga_pemilik_id' => $wargaId,
                'nama_toko' => $nama,
                'deskripsi' => $faker->paragraph(2),
                'logo_path' => 'path/to/logo/' . str_replace(' ', '-', $nama) . '.png',
                'status_verifikasi' => $faker->randomElement($status),
            ]);
        }
    }
}
