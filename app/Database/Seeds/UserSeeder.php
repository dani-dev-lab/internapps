<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('users')->countAllResults() > 0) {
            CLI::write('UserSeeder dilewati: tabel users sudah berisi data.', 'yellow');

            return;
        }

        $role = [];

        foreach ($this->db->table('roles')->get()->getResultArray() as $r) {
            $role[$r['nama_role']] = (int) $r['id'];
        }

        $now = date('Y-m-d H:i:s');

        $akun = [
            ['superadmin', 'super123', 'Super Admin Internapps',   'superadmin'],
            ['admin',    'admin123',   'Administrator Internapps', 'admin'],
            ['staff',    'staff123',   'Staff Kepegawaian',        'staff'],
            ['peserta1', 'peserta123', 'Peserta Contoh Satu',      'peserta'],
            ['peserta2', 'peserta123', 'Peserta Contoh Dua',       'peserta'],
            ['peserta3', 'peserta123', 'Peserta Contoh Tiga',      'peserta'],
            ['peserta4', 'peserta123', 'Peserta Contoh Empat',     'peserta'],
            ['peserta5', 'peserta123', 'Peserta Contoh Lima',      'peserta'],
        ];

        $data = [];

        foreach ($akun as [$username, $passwordPolos, $namaPengguna, $namaRole]) {
            $data[] = [
                'role_id'       => $role[$namaRole],
                'username'      => $username,
                'password'      => password_hash($passwordPolos, PASSWORD_DEFAULT),
                'nama_pengguna' => $namaPengguna,
                'foto'          => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        $this->db->table('users')->insertBatch($data);

        CLI::write('UserSeeder: ' . count($data) . ' akun ditambahkan (password sudah di-hash).', 'green');
    }
}
