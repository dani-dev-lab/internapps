<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaMagangModel extends Model
{
    protected $table         = 'peserta_magang';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'nik',
        'nama_peserta',
        'nama_universitas',
        'nama_fakultas',
        'nama_jurusan',
        'tanggal_mulai_magang',
        'tanggal_berakhir_magang',
        'link_foto_peserta',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'nik'                     => 'required|numeric|exact_length[16]|is_unique[peserta_magang.nik]',
        'nama_peserta'            => 'required|min_length[3]|max_length[100]',
        'nama_universitas'        => 'required|min_length[3]|max_length[150]',
        'nama_fakultas'           => 'required|min_length[2]|max_length[150]',
        'nama_jurusan'            => 'required|min_length[2]|max_length[150]',
        'tanggal_mulai_magang'    => 'required|valid_date[Y-m-d]',
        'tanggal_berakhir_magang' => 'required|valid_date[Y-m-d]|tanggal_tidak_sebelum[tanggal_mulai_magang]',
    ];

    protected $validationMessages = [
        'nik' => [
            'required'     => 'NIK wajib diisi.',
            'numeric'      => 'NIK hanya boleh berisi angka.',
            'exact_length' => 'NIK harus tepat 16 digit.',
            'is_unique'    => 'NIK ini sudah terdaftar pada peserta lain.',
        ],
        'nama_peserta'     => ['required' => 'Nama peserta wajib diisi.'],
        'nama_universitas' => ['required' => 'Nama universitas wajib diisi.'],
        'nama_fakultas'    => ['required' => 'Nama fakultas wajib diisi.'],
        'nama_jurusan'     => ['required' => 'Program studi wajib diisi.'],
        'tanggal_mulai_magang' => [
            'required'   => 'Tanggal mulai magang wajib diisi.',
            'valid_date' => 'Format tanggal mulai magang tidak valid.',
        ],
        'tanggal_berakhir_magang' => [
            'required'               => 'Tanggal berakhir magang wajib diisi.',
            'valid_date'             => 'Format tanggal berakhir magang tidak valid.',
            'tanggal_tidak_sebelum'  => 'Tanggal berakhir magang tidak boleh lebih awal daripada tanggal mulai.',
        ],
    ];

    public function aturanUbah(int $id): array
    {
        $aturan = $this->validationRules;

        $aturan['nik'] = 'required|numeric|exact_length[16]|is_unique[peserta_magang.nik,id,' . $id . ']';

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

    public function denganAkun()
    {
        return $this->select('peserta_magang.*, users.username, users.nama_pengguna')
            ->join('users', 'users.id = peserta_magang.user_id', 'left');
    }

    public function cariDenganAkun(int $id): ?array
    {
        return $this->denganAkun()
            ->where('peserta_magang.id', $id)
            ->first();
    }

    public function cariMilikUser(int $userId): ?array
    {
        return $this->denganAkun()
            ->where('peserta_magang.user_id', $userId)
            ->first();
    }

    public function cariNik(string $nik): ?array
    {
        return $this->where('nik', $nik)->first();
    }

    public function hitungPerStatus(): array
    {
        $hariIni = date('Y-m-d');

        return [
            'total'         => $this->countAllResults(),
            'belum_mulai'   => $this->where('tanggal_mulai_magang >', $hariIni)->countAllResults(),
            'sedang_magang' => $this->where('tanggal_mulai_magang <=', $hariIni)
                ->where('tanggal_berakhir_magang >=', $hariIni)
                ->countAllResults(),
            'selesai' => $this->where('tanggal_berakhir_magang <', $hariIni)->countAllResults(),
        ];
    }

    public function sedangMagang(int $batas = 5): array
    {
        $hariIni = date('Y-m-d');

        return $this->where('tanggal_mulai_magang <=', $hariIni)
            ->where('tanggal_berakhir_magang >=', $hariIni)
            ->orderBy('tanggal_berakhir_magang', 'ASC')
            ->findAll($batas);
    }
}
