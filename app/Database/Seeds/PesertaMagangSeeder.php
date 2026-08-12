<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * ==========================================================================
 *  DATA CONTOH — BUKAN DATA PESERTA MAGANG YANG SEBENARNYA
 * ==========================================================================
 *
 *  Isi array $peserta di bawah adalah data contoh untuk keperluan pengujian
 *  dan demonstrasi di komputer yang databasenya masih kosong.
 *
 *  Nama, NIK, universitas, fakultas, dan program studinya sengaja ditulis
 *  terang-terangan sebagai "Contoh" supaya tidak mungkin dikira data peserta
 *  magang sungguhan.
 *
 *  Data peserta yang asli TIDAK dimasukkan lewat berkas ini, melainkan lewat
 *  aplikasi: masuk sebagai admin, lalu buka menu Data Peserta Magang dan
 *  tekan Tambah Peserta. Alasannya, NIK adalah data pribadi dan tidak
 *  seharusnya ikut tersimpan di dalam berkas kode program yang bisa tersalin
 *  ke mana-mana.
 * ==========================================================================
 *
 * Tanggal sengaja disebar supaya ketiga status magang muncul dan bisa diuji:
 * ada yang belum mulai, sedang berjalan, dan sudah selesai.
 */
class PesertaMagangSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('peserta_magang')->countAllResults() > 0) {
            CLI::write('PesertaMagangSeeder dilewati: tabel peserta_magang sudah berisi data.', 'yellow');

            return;
        }

        // Petakan username akun peserta ke id-nya, supaya data magang bisa
        // ditautkan ke akun login yang benar tanpa menebak id.
        $userId = [];

        foreach ($this->db->table('users')->select('id, username')->get()->getResultArray() as $u) {
            $userId[$u['username']] = (int) $u['id'];
        }

        $now = date('Y-m-d H:i:s');

        $peserta = [
            [
                'username'                => 'peserta1',
                'nik'                     => '0000000000000001',
                'nama_peserta'            => 'Peserta Contoh Satu',
                'nama_universitas'        => 'Universitas Contoh',
                'nama_fakultas'           => 'Fakultas Contoh',
                'nama_jurusan'            => 'Program Studi Contoh',
                'tanggal_mulai_magang'    => '2026-07-01',
                'tanggal_berakhir_magang' => '2026-09-30',
            ],
            [
                'username'                => 'peserta2',
                'nik'                     => '0000000000000002',
                'nama_peserta'            => 'Peserta Contoh Dua',
                'nama_universitas'        => 'Universitas Contoh',
                'nama_fakultas'           => 'Fakultas Contoh',
                'nama_jurusan'            => 'Program Studi Contoh',
                'tanggal_mulai_magang'    => '2026-08-01',
                'tanggal_berakhir_magang' => '2026-10-31',
            ],
            [
                'username'                => 'peserta3',
                'nik'                     => '0000000000000003',
                'nama_peserta'            => 'Peserta Contoh Tiga',
                'nama_universitas'        => 'Universitas Contoh',
                'nama_fakultas'           => 'Fakultas Contoh',
                'nama_jurusan'            => 'Program Studi Contoh',
                'tanggal_mulai_magang'    => '2026-02-01',
                'tanggal_berakhir_magang' => '2026-04-30',
            ],
            [
                'username'                => 'peserta4',
                'nik'                     => '0000000000000004',
                'nama_peserta'            => 'Peserta Contoh Empat',
                'nama_universitas'        => 'Universitas Contoh',
                'nama_fakultas'           => 'Fakultas Contoh',
                'nama_jurusan'            => 'Program Studi Contoh',
                'tanggal_mulai_magang'    => '2026-09-01',
                'tanggal_berakhir_magang' => '2026-11-30',
            ],
            [
                'username'                => 'peserta5',
                'nik'                     => '0000000000000005',
                'nama_peserta'            => 'Peserta Contoh Lima',
                'nama_universitas'        => 'Universitas Contoh',
                'nama_fakultas'           => 'Fakultas Contoh',
                'nama_jurusan'            => 'Program Studi Contoh',
                'tanggal_mulai_magang'    => '2026-01-05',
                'tanggal_berakhir_magang' => '2026-03-31',
            ],
        ];

        $data = [];

        foreach ($peserta as $p) {
            $username = $p['username'];
            unset($p['username']);

            $data[] = $p + [
                'user_id'           => $userId[$username] ?? null,
                'link_foto_peserta' => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        $this->db->table('peserta_magang')->insertBatch($data);

        CLI::write('PesertaMagangSeeder: ' . count($data) . ' peserta contoh ditambahkan.', 'green');
        CLI::write('CATATAN: itu data contoh, bukan peserta sungguhan. Data asli dimasukkan lewat menu Data Peserta Magang di aplikasi.', 'yellow');
    }
}
