<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel peserta_magang — data peserta magang.
 *
 * Catatan penting soal 'nik': tipenya CHAR(16), bukan BIGINT. NIK bisa diawali
 * angka 0 dan angka itu akan hilang kalau disimpan sebagai bilangan.
 *
 * Tidak ada kolom 'status'. Status (Belum Mulai / Sedang Magang / Selesai)
 * dihitung dari perbandingan tanggal hari ini dengan tanggal mulai dan
 * berakhir, sehingga mustahil terjadi status yang tidak cocok dengan tanggal.
 *
 * 'user_id' boleh NULL: data peserta bisa didaftarkan lebih dulu oleh admin
 * sebelum (atau tanpa) akun login dibuatkan untuknya.
 */
class CreatePesertaMagangTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Akun login peserta, boleh kosong',
            ],
            'nik' => [
                'type'       => 'CHAR',
                'constraint' => 16,
            ],
            'nama_peserta' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_universitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nama_fakultas' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nama_jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'tanggal_mulai_magang' => [
                'type' => 'DATE',
            ],
            'tanggal_berakhir_magang' => [
                'type' => 'DATE',
            ],
            'link_foto_peserta' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama file foto di public/uploads/peserta/',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nik');
        $this->forge->addUniqueKey('user_id');

        // SET NULL: kalau akun login dihapus, data magangnya tetap tersimpan.
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('peserta_magang');
    }

    public function down(): void
    {
        $this->forge->dropTable('peserta_magang');
    }
}
