<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisIuran;
use Illuminate\Support\Facades\DB;

class JenisIuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_iuran')->delete();

        JenisIuran::create([
            'nama_iuran' => 'Iuran Keamanan Bulanan',
            'deskripsi' => 'Iuran wajib untuk jasa keamanan lingkungan.',
            'nominal_default' => 50000.00,
            'periode' => 'Bulanan',
        ]);

        JenisIuran::create([
            'nama_iuran' => 'Iuran Kebersihan Bulanan',
            'deskripsi' => 'Iuran wajib untuk jasa pengangkutan sampah.',
            'nominal_default' => 35000.00,
            'periode' => 'Bulanan',
        ]);

        JenisIuran::create([
            'nama_iuran' => 'Dana Sosial (Kematian)',
            'deskripsi' => 'Dana sosial untuk membantu warga yang berduka.',
            'nominal_default' => 15000.00,
            'periode' => 'Bulanan',
        ]);

        JenisIuran::create([
            'nama_iuran' => 'Iuran 17 Agustus',
            'deskripsi' => 'Iuran insidental untuk perayaan HUT RI.',
            'nominal_default' => 100000.00,
            'periode' => 'Tahunan',
        ]);
    }
}
