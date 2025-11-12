<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rw;
use Illuminate\Support\Facades\DB;

class RwSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rw')->delete();

        Rw::create([
            'nomor_rw' => '01',
            'nama_ketua_rw' => 'Bapak Budi Santoso',
            'alamat_sekretariat' => 'Jl. Merpati Putih No. 1',
        ]);

        Rw::create([
            'nomor_rw' => '02',
            'nama_ketua_rw' => 'Bapak Agus Wijaya',
            'alamat_sekretariat' => 'Jl. Elang Emas No. 10',
        ]);
    }
}
