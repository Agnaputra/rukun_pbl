<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keluarga;
use App\Models\Rt;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('keluarga')->delete();
        $faker = Faker::create('id_ID');

        $rtIds = Rt::pluck('rt_id');

        if ($rtIds->isEmpty()) {
            $this->command->error('Tabel RT kosong. Jalankan RtSeeder terlebih dahulu.');
            return;
        }

        // Buat 10 Keluarga
        for ($i = 0; $i < 10; $i++) {
            Keluarga::create([
                'rt_id' => $rtIds->random(),
                'nomor_kk' => $faker->unique()->numerify('3201################'),
                'alamat' => $faker->address,
                'kepala_keluarga_id' => null, // Akan diisi oleh WargaSeeder
                'file_kk_scan' => 'path/to/scan/kk-' . $i . '.jpg',
            ]);
        }
    }
}
