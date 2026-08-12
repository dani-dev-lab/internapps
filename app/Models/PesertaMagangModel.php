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

    /**
     * Aturan untuk mengubah peserta: NIK miliknya sendiri tidak dianggap duplikat.
     */
    public function aturanUbah(int $id): array
    {
        $aturan = $this->validationRules;

        $aturan['nik'] = 'required|numeric|exact_length[16]|is_unique[peserta_magang.nik,id,' . $id . ']';

        return $aturan;
    }

    /**
     * Setiap pengubahan data otomatis memakai aturanUbah(), supaya NIK milik
     * peserta itu sendiri tidak dianggap duplikat oleh aturan is_unique.
     */
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

    /**
     * Data magang milik satu akun. Dipakai role peserta.
     *
     * Penting: id peserta diambil dari user_id yang tersimpan di session,
     * bukan dari parameter URL, supaya peserta tidak bisa membuka data
     * peserta lain hanya dengan mengganti angka di alamat browser.
     */
    public function cariMilikUser(int $userId): ?array
    {
        return $this->denganAkun()
            ->where('peserta_magang.user_id', $userId)
            ->first();
    }

    /**
     * Cari data peserta berdasarkan NIK, untuk keperluan pendaftaran akun.
     *
     * Dipakai saat peserta mendaftarkan akunnya sendiri: NIK-nya harus sudah
     * terdaftar sebagai peserta magang, dan belum dipakai akun lain.
     */
    public function cariNik(string $nik): ?array
    {
        return $this->where('nik', $nik)->first();
    }

    /**
     * Jumlah peserta per status untuk kartu dashboard.
     *
     * Status dihitung lewat perbandingan tanggal di SQL, bukan dengan
     * mengambil seluruh baris lalu menghitungnya di PHP.
     */
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

    /**
     * Peserta yang magangnya sedang berjalan, untuk tabel ringkas di dashboard.
     */
    public function sedangMagang(int $batas = 5): array
    {
        $hariIni = date('Y-m-d');

        return $this->where('tanggal_mulai_magang <=', $hariIni)
            ->where('tanggal_berakhir_magang >=', $hariIni)
            ->orderBy('tanggal_berakhir_magang', 'ASC')
            ->findAll($batas);
    }
}
