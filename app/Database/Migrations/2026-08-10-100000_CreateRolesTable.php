<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel roles — daftar peran pengguna aplikasi.
 *
 * Dibuat sebagai tabel terpisah (bukan sekadar kolom 'role' di users) supaya
 * nama peran bisa diambil lewat JOIN tanpa memetakan angka ke teks di PHP,
 * dan penambahan peran baru cukup lewat INSERT tanpa mengubah struktur tabel.
 */
class CreateRolesTable extends Migration
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
            'nama_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addUniqueKey('nama_role');
        $this->forge->createTable('roles');
    }

    public function down(): void
    {
        $this->forge->dropTable('roles');
    }
}
