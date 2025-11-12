<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $this->call([

            // RoleSeeder::class,
            // RwSeeder::class,
            // JenisIuranSeeder::class,
            // JenisSayurSeeder::class,
            // RtSeeder::class,
            // UserSeeder::class,
            // KeluargaSeeder::class,
            // WargaSeeder::class,
            // DokumenWargaSeeder::class,
            // NotifikasiSeeder::class,
            // TokoSeeder::class,
            // TagihanSeeder::class,
            // PembayaranSeeder::class,
            // ProdukSeeder::class,
            // PesananSeeder::class,
            DetailPesananSeeder::class,
            ReviewProdukSeeder::class,
        ]);
    }
}
