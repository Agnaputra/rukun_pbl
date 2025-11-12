<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Support\Facades\DB;

class RtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rt')->delete();

        $rw1 = Rw::where('nomor_rw', '01')->first();
        $rw2 = Rw::where('nomor_rw', '02')->first();

        Rt::create([
            'rw_id' => $rw1->rw_id,
            'nomor_rt' => '001',
            'nama_ketua_rt' => 'Ibu Siti Aminah',
        ]);
        Rt::create([
            'rw_id' => $rw1->rw_id,
            'nomor_rt' => '002',
            'nama_ketua_rt' => 'Bapak Heru Purnomo',
        ]);
        Rt::create([
            'rw_id' => $rw1->rw_id,
            'nomor_rt' => '003',
            'nama_ketua_rt' => 'Bapak Eko Prasetyo',
        ]);

        Rt::create([
            'rw_id' => $rw2->rw_id,
            'nomor_rt' => '001',
            'nama_ketua_rt' => 'Ibu Wati Susanti',
        ]);
        Rt::create([
            'rw_id' => $rw2->rw_id,
            'nomor_rt' => '002',
            'nama_ketua_rt' => 'Bapak Joko Mulyono',
        ]);
    }
}
