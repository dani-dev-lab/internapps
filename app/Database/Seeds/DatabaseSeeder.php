<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder utama. Cukup jalankan:
 *
 *     php spark db:seed DatabaseSeeder
 *
 * Urutannya tidak boleh ditukar: users butuh roles sudah ada (foreign key
 * role_id), dan peserta_magang butuh users sudah ada (foreign key user_id).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PesertaMagangSeeder::class);
    }
}
