<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['role_id', 'username', 'password', 'nama_pengguna', 'foto'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $validationRules = [
        'role_id'       => 'required|is_natural_no_zero',
        'username'      => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username]',
        'password'      => 'required|min_length[6]|max_length[100]',
        'nama_pengguna' => 'required|min_length[3]|max_length[100]',
    ];

    protected $validationMessages = [
        'role_id' => [
            'required'           => 'Role wajib dipilih.',
            'is_natural_no_zero' => 'Role yang dipilih tidak valid.',
        ],
        'username' => [
            'required'   => 'Username wajib diisi.',
            'alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan strip.',
            'min_length' => 'Username minimal 3 karakter.',
            'is_unique'  => 'Username ini sudah dipakai pengguna lain.',
        ],
        'password' => [
            'required'   => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.',
        ],
        'nama_pengguna' => [
            'required'   => 'Nama pengguna wajib diisi.',
            'min_length' => 'Nama pengguna minimal 3 karakter.',
        ],
    ];

    public function aturanUbah(int $id): array
    {
        $aturan = $this->validationRules;

        $aturan['username'] = 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username,id,' . $id . ']';
        $aturan['password'] = 'permit_empty|min_length[6]|max_length[100]';

        return $aturan;
    }

    public function update($id = null, $row = null): bool
    {
        $aturanAsli = $this->validationRules;

        if (is_numeric($id)) {
            $this->setValidationRules($this->aturanUbah((int) $id));
        }

        try {
            return parent::update($id, $row);
        } finally {
            $this->setValidationRules($aturanAsli);
        }
    }

    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        if ($data['data']['password'] === '') {
            unset($data['data']['password']);

            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

    public function denganRole()
    {
        return $this->select('users.*, roles.nama_role')
            ->join('roles', 'roles.id = users.role_id');
    }

    public function cariUsername(string $username): ?array
    {
        return $this->denganRole()
            ->where('users.username', $username)
            ->first();
    }

    public function cariDenganRole(int $id): ?array
    {
        return $this->denganRole()
            ->where('users.id', $id)
            ->first();
    }

    public function akunPesertaBelumTertaut(?int $userIdSekarang = null): array
    {
        $terpakai = $this->db->table('peserta_magang')
            ->select('user_id')
            ->where('user_id IS NOT NULL')
            ->get()
            ->getResultArray();

        $idTerpakai = array_map(static fn ($b): int => (int) $b['user_id'], $terpakai);

        if ($userIdSekarang !== null) {
            $idTerpakai = array_diff($idTerpakai, [$userIdSekarang]);
        }

        $builder = $this->select('users.id, users.username, users.nama_pengguna')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.nama_role', 'peserta');

        if ($idTerpakai !== []) {
            $builder->whereNotIn('users.id', $idTerpakai);
        }

        return $builder->orderBy('users.nama_pengguna', 'ASC')->findAll();
    }

    public function hitungPerRole(string $namaRole): int
    {
        return $this->join('roles', 'roles.id = users.role_id')
            ->where('roles.nama_role', $namaRole)
            ->countAllResults();
    }
}
