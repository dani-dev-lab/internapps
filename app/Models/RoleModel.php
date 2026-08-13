<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_role', 'deskripsi'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'nama_role' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[roles.nama_role]',
        'deskripsi' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'nama_role' => [
            'required'   => 'Nama role wajib diisi.',
            'alpha_dash' => 'Nama role hanya boleh berisi huruf, angka, garis bawah, dan strip.',
            'is_unique'  => 'Nama role ini sudah digunakan.',
        ],
    ];
}
