<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;
use App\Models\User;
use App\Models\Keluarga;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warga')->delete();
        $faker = Faker::create('id_ID');

        // Ambil user dengan role 'Warga' yang belum punya data warga
        $userIds = User::where('role_id', Role::where('nama_role', 'Warga')->first()->role_id)
            ->doesntHave('warga')
            ->pluck('user_id');

        $keluargaIds = Keluarga::pluck('keluarga_id');

        if ($userIds->isEmpty() || $keluargaIds->isEmpty()) {
            $this->command->error('User Warga atau Keluarga kosong. Jalankan UserSeeder/KeluargaSeeder.');
            return;
        }

        $genders = ['L', 'P'];
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        $statuses = ['Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $jobs = ['Pelajar/Mahasiswa', 'Karyawan Swasta', 'Wiraswasta', 'PNS', 'Ibu Rumah Tangga', 'Tidak Bekerja'];

        $i = 0;
        foreach ($keluargaIds as $keluargaId) {
            // Buat 2-3 warga per keluarga
            $jumlahWarga = rand(2, 3);
            for ($j = 0; $j < $jumlahWarga; $j++) {
                if (!isset($userIds[$i])) break; // Stop jika user habis

                $gender = $faker->randomElement($genders);
                $warga = Warga::create([
                    'user_id' => $userIds[$i],
                    'keluarga_id' => $keluargaId,
                    'nik' => $faker->unique()->numerify('3201################'),
                    'nama_lengkap' => $faker->name($gender == 'Laki-laki' ? 'male' : 'female'),
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->dateTimeBetween('-50 years', '-17 years')->format('Y-m-d'),
                    'jenis_kelamin' => $gender,
                    'agama' => $faker->randomElement($religions),
                    'status_perkawinan' => $faker->randomElement($statuses),
                    'pekerjaan' => $faker->randomElement($jobs),
                    'telepon' => $faker->unique()->phoneNumber,
                    'status_warga' => 'Aktif',
                    'foto_ktp' => 'path/to/ktp/foto-' . $i . '.jpg',
                    'foto_wajah' => 'path/to/wajah/foto-' . $i . '.jpg',
                    'is_verified_face' => $faker->boolean(80), // 80% terverifikasi
                ]);

                // *** PENTING: Update Kepala Keluarga ***
                // Jadikan warga pertama di keluarga itu sebagai Kepala Keluarga
                if ($j == 0) {
                    $keluarga = Keluarga::find($keluargaId);
                    if ($keluarga && is_null($keluarga->kepala_keluarga_id)) {
                        $keluarga->kepala_keluarga_id = $warga->warga_id;
                        $keluarga->save();
                    }
                }
                $i++; // Pindah ke user selanjutnya
            }
            if (!isset($userIds[$i])) break; // Stop jika user habis
        }
    }
}
