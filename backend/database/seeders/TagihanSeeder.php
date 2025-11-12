<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tagihan;
use App\Models\Keluarga;
use App\Models\JenisIuran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tagihan')->delete();

        $keluargaIds = Keluarga::pluck('keluarga_id');
        $iuranBulanan = JenisIuran::where('periode', 'Bulanan')->get();

        if ($keluargaIds->isEmpty() || $iuranBulanan->isEmpty()) {
            $this->command->error('Keluarga atau JenisIuran Bulanan kosong.');
            return;
        }

        $now = Carbon::now();

        // Buat tagihan 3 bulan ke belakang untuk setiap keluarga
        foreach ($keluargaIds as $keluargaId) {
            foreach (range(0, 2) as $bulanMundur) {
                $tanggalTagihan = $now->copy()->subMonths($bulanMundur);

                foreach ($iuranBulanan as $iuran) {
                    $status = ['Belum Bayar', 'Menunggu Verifikasi', 'Lunas'];
                    Tagihan::create([
                        'keluarga_id' => $keluargaId,
                        'jenis_iuran_id' => $iuran->jenis_iuran_id,
                        'bulan' => $tanggalTagihan->month,
                        'tahun' => $tanggalTagihan->year,
                        'nominal' => $iuran->nominal_default,
                        'status_pembayaran' => $bulanMundur == 0 ? 'Belum Bayar' : $status[rand(0, 2)],
                        'jatuh_tempo' => $tanggalTagihan->copy()->endOfMonth()->format('Y-m-d'),
                    ]);
                }
            }
        }
    }
}
