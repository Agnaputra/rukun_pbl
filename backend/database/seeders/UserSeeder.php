<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();
        $faker = Faker::create('id_ID');

        $adminRole = Role::where('nama_role', 'Admin')->first();
        $wargaRole = Role::where('nama_role', 'Warga')->first();
        $rtRole = Role::where('nama_role', 'Ketua RT')->first();
        $rwRole = Role::where('nama_role', 'Ketua RW')->first();

        // 1. Admin
        User::create([
            'role_id' => $adminRole->role_id,
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password123'),
        ]);

        // 2. Ketua RW
        User::create([
            'role_id' => $rwRole->role_id,
            'username' => 'ketuarw01',
            'email' => 'ketuarw01@example.com',
            'password_hash' => Hash::make('password123'),
        ]);

        // 3. Ketua RT
        User::create([
            'role_id' => $rtRole->role_id,
            'username' => 'ketuart01',
            'email' => 'ketuart01@example.com',
            'password_hash' => Hash::make('password123'),
        ]);

        // 4. Buat 20 User Warga
        for ($i = 0; $i < 20; $i++) {
            $username = $faker->unique()->userName;
            User::create([
                'role_id' => $wargaRole->role_id,
                'username' => $username,
                'email' => $faker->unique()->safeEmail,
                'password_hash' => Hash::make('password123'),
            ]);
        }
    }
}
