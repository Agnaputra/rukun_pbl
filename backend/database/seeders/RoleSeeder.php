<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->delete(); // Hapus data lama

        Role::create(['nama_role' => 'Admin']);
        Role::create(['nama_role' => 'Ketua RW']);
        Role::create(['nama_role' => 'Ketua RT']);
        Role::create(['nama_role' => 'Warga']);
        Role::create(['nama_role' => 'Bendahara']);
    }
}
