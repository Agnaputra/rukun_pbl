<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DokumenWarga;
use App\Models\Warga;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DokumenWargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dokumen_warga')->delete();
        $faker = Faker::create('id_ID');

        $wargaIds = Warga::pluck('warga_id');

        if ($wargaIds->isEmpty()) {
            $this->command->error('Tabel Warga kosong. Jalankan WargaSeeder.');
            return;
        }

        $namaDokumen = ['Surat Pengantar RT', 'Formulir KTP', 'Surat Keterangan Domisili', 'Laporan Kehilangan'];

        // Buat 1-2 dokumen untuk 5 warga acak
        $wargaSample = $wargaIds->random(5);

        foreach ($wargaSample as $wargaId) {
            for ($i = 0; $i < rand(1, 2); $i++) {
                $docName = $faker->randomElement($namaDokumen);
                DokumenWarga::create([
                    'warga_id' => $wargaId,
                    'nama_dokumen' => $docName,
                    'file_path' => 'dokumen/warga/' . $wargaId . '/' . str_replace(' ', '_', $docName) . '.pdf',
                ]);
            }
        }
    }
}
