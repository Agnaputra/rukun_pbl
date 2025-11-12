<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pembayaran')->delete();

        $tagihanLunas = Tagihan::whereIn('status_pembayaran', ['Lunas', 'Menunggu Verifikasi'])->get();
        $adminUsers = User::where('role_id', Role::whereIn('nama_role', ['Admin', 'Bendahara'])->first()->role_id)->pluck('user_id');

        if ($tagihanLunas->isEmpty()) {
            $this->command->info('Tidak ada tagihan lunas/menunggu verifikasi untuk dibuatkan pembayaran.');
            return;
        }

        foreach ($tagihanLunas as $tagihan) {
            // Ambil warga acak dari keluarga tagihan tsb
            $wargaPembayar = Warga::where('keluarga_id', $tagihan->keluarga_id)->inRandomOrder()->first();
            if (!$wargaPembayar) continue; // Lewati jika keluarga tsb belum punya warga

            $tanggalBayar = Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 15)->addDays(rand(-5, 5));
            $verifikatorId = null;
            $verifikasiTimestamp = null;
            $catatan = null;

            if ($tagihan->status_pembayaran == 'Lunas') {
                $verifikatorId = $adminUsers->random();
                $verifikasiTimestamp = $tanggalBayar->copy()->addHours(rand(1, 24));
                $catatan = 'Pembayaran Lunas Diverifikasi Otomatis oleh Seeder.';
            }

            Pembayaran::create([
                'tagihan_id' => $tagihan->tagihan_id,
                'warga_id_pembayar' => $wargaPembayar->warga_id,
                'tanggal_bayar' => $tanggalBayar,
                'jumlah_bayar' => $tagihan->nominal,
                'metode_bayar' => rand(0, 1) ? 'Transfer' : 'Tunai',
                'bukti_bayar_path' => 'path/to/bukti/bayar-' . $tagihan->tagihan_id . '.jpg',
                'ocr_result_text' => null,
                'status_verifikasi' => $tagihan->status_pembayaran, // Samakan dengan status tagihan
                'verifikasi_oleh_user_id' => $verifikatorId,
                'verifikasi_timestamp' => $verifikasiTimestamp,
                'catatan_verifikasi' => $catatan,
            ]);
        }
    }
}
