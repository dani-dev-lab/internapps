<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel users — akun pengguna aplikasi (admin, staff, maupun peserta).
 *
 * Peserta memakai tabel yang sama dengan role 'peserta', bukan sistem
 * autentikasi terpisah, supaya hanya ada satu alur login dan satu session.
 *
 * Kolom 'password' sengaja VARCHAR(255): hash bcrypt hanya 60 karakter, tapi
 * ukuran ini menyisakan ruang kalau nanti pindah ke algoritma yang lebih
 * panjang seperti Argon2id tanpa perlu mengubah struktur tabel.
 */
class CreateUsersTable extends Migration
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
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama file foto profil di public/uploads/avatar/',
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
        $this->forge->addUniqueKey('username');

        // RESTRICT: sebuah role tidak boleh dihapus selama masih ada user yang memakainya.
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('users');
    }

    public function down(): void
    {
        $this->forge->dropTable('users');
    }
}
