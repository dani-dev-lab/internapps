<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('roles')->countAllResults() > 0) {
            CLI::write('RoleSeeder dilewati: tabel roles sudah berisi data.', 'yellow');

            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('roles')->insertBatch([
            [
                'nama_role'  => 'admin',
                'deskripsi'  => 'Akses penuh: mengelola pengguna aplikasi dan seluruh data peserta magang.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_role'  => 'staff',
                'deskripsi'  => 'Mengelola data peserta magang, tanpa akses ke data pengguna dan tanpa hak hapus.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_role'  => 'peserta',
                'deskripsi'  => 'Peserta magang. Hanya dapat melihat data magangnya sendiri dan mengubah profilnya.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        CLI::write('RoleSeeder: 3 role ditambahkan.', 'green');
    }
}
