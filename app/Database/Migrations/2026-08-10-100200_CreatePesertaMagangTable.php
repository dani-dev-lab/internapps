<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

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

        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('peserta_magang');
    }

    public function down(): void
    {
        $this->forge->dropTable('peserta_magang');
    }
}
