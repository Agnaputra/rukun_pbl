<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSayur;
use Illuminate\Support\Facades\DB;

class JenisSayurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jenis_sayur')->delete();

        JenisSayur::create([
            'nama_jenis' => 'Sayuran Daun',
            'deskripsi' => 'Sayuran yang dimanfaatkan daunnya. Contoh: Bayam, Kangkung, Sawi.',
        ]);
        JenisSayur::create([
            'nama_jenis' => 'Sayuran Buah',
            'deskripsi' => 'Sayuran yang dimanfaatkan buahnya. Contoh: Tomat, Terong, Cabai.',
        ]);
        JenisSayur::create([
            'nama_jenis' => 'Sayuran Umbi',
            'deskripsi' => 'Sayuran yang dimanfaatkan umbinya. Contoh: Kentang, Wortel, Bawang.',
        ]);
        JenisSayur::create([
            'nama_jenis' => 'Sayuran Kacang-kacangan',
            'deskripsi' => 'Sayuran yang dimanfaatkan biji/polongnya. Contoh: Buncis, Kacang Panjang.',
        ]);
        JenisSayur::create([
            'nama_jenis' => 'Bumbu Dapur',
            'deskripsi' => 'Rempah dan bumbu pelengkap masakan. Contoh: Jahe, Kunyit, Lengkuas.',
        ]);
    }
}
