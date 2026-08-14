<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $daftar = [
            [
                'nama_role'  => 'superadmin',
                'deskripsi'  => 'Pemilik aplikasi. Sama seperti admin, tetapi tidak dapat dihapus atau diturunkan rolenya oleh admin biasa.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
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
        ];

        $baru = 0;

        foreach ($daftar as $role) {
            $sudahAda = $this->db->table('roles')
                ->where('nama_role', $role['nama_role'])
                ->countAllResults() > 0;

            if ($sudahAda) {
                continue;
            }

            $this->db->table('roles')->insert($role);
            $baru++;
        }

        if ($baru === 0) {
            CLI::write('RoleSeeder: semua role sudah tersedia, tidak ada yang ditambahkan.', 'yellow');

            return;
        }

        CLI::write('RoleSeeder: ' . $baru . ' role ditambahkan.', 'green');
    }
}
