<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifikasi')->delete();
        $faker = Faker::create('id_ID');

        $userIds = User::pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->command->error('Tabel User kosong.');
            return;
        }

        $tipe = ['Umum', 'Iuran', 'Marketplace', 'Peringatan'];

        // Buat 30 notifikasi acak untuk user acak
        for ($i = 0; $i < 30; $i++) {
            Notifikasi::create([
                'user_id_penerima' => $userIds->random(),
                'judul' => $faker->sentence(3),
                'isi_pesan' => $faker->paragraph(1),
                'tipe_notifikasi' => $faker->randomElement($tipe),
                'is_read' => $faker->boolean(30), // 30% sudah dibaca
            ]);
        }
    }
}
