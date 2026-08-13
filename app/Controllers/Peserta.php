<?php

namespace App\Controllers;

use App\Libraries\UnggahFoto;
use App\Models\PesertaMagangModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Pengelolaan data peserta magang.
 *
 * Melihat, menambah, dan mengubah: superadmin, admin, dan staff.
 * Menghapus: superadmin dan admin saja — penghapusan bersifat permanen dan
 * sekaligus membuang berkas fotonya, jadi haknya dipersempit.
 * dataSaya(): khusus role peserta, hanya menampilkan datanya sendiri.
 *
 * Semua pembatasan itu dipasang di app/Config/Routes.php lewat filter role.
 */
class Peserta extends BaseController
{
    private const KOLOM_FOTO = 'link_foto_peserta';

    protected PesertaMagangModel $pesertaModel;
    protected UserModel $userModel;
    protected UnggahFoto $unggah;

    public function __construct()
    {
        $this->pesertaModel = new PesertaMagangModel();
        $this->userModel    = new UserModel();
        $this->unggah       = new UnggahFoto('peserta');
    }

    public function index(): string
    {
        return view('peserta/index', [
            'page_title' => 'Data Peserta Magang',
            'peserta'    => $this->pesertaModel->denganAkun()
                ->orderBy('peserta_magang.nama_peserta', 'ASC')
                ->findAll(),
            // Ditentukan di sini, bukan di dalam view, supaya daftar role yang
            // boleh menghapus hanya ditulis di satu tempat dan tidak mudah
            // tertinggal saat daftar rolenya berubah. Harus sejalan dengan
            // filter route 'role:superadmin,admin' pada peserta/delete.
            'bolehHapus' => in_array(session('nama_role'), ['superadmin', 'admin'], true),
        ]);
    }

    public function detail(int $id): string|RedirectResponse
    {
        $data = $this->pesertaModel->cariDenganAkun($id);

        if ($data === null) {
            return redirect()->to(base_url('peserta'))
                ->with('error', 'Data peserta tidak ditemukan.');
        }

        return view('peserta/detail', [
            'page_title' => 'Detail Peserta',
            'p'          => $data,
            'bisaUbah'   => true,
        ]);
    }

    /**
     * Halaman "Data Magang Saya" untuk role peserta.
     *
     * Data diambil lewat user_id dari session, bukan dari angka di URL, jadi
     * peserta tidak punya cara untuk melihat data peserta lain.
     */
    public function dataSaya(): string
    {
        $data = $this->pesertaModel->cariMilikUser((int) session('user_id'));

        return view('peserta/detail', [
            'page_title' => 'Data Magang Saya',
            'p'          => $data,
            'bisaUbah'   => false,
        ]);
    }

    public function create(): string
    {
        return view('peserta/create', [
            'page_title' => 'Tambah Peserta Magang',
            'p'          => null,
            'akun'       => $this->userModel->akunPesertaBelumTertaut(),
            'aksi'       => base_url('peserta'),
            'tombol'     => 'Simpan Peserta',
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->fotoLolosValidasi()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data      = $this->ambilInput();
        $namaFoto  = $this->unggah->simpan($this->request->getFile(self::KOLOM_FOTO));

        if ($namaFoto !== null) {
            $data[self::KOLOM_FOTO] = $namaFoto;
        }

        if (! $this->pesertaModel->insert($data)) {
            // Berkas sudah terlanjur dipindahkan sebelum data ditolak model.
            // Dibuang lagi supaya tidak ada foto yang menganggur tanpa pemilik.
            $this->unggah->hapus($namaFoto);

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->pesertaModel->errors());
        }

        return redirect()->to(base_url('peserta'))
            ->with('sukses', 'Peserta "' . $data['nama_peserta'] . '" berhasil ditambahkan.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $p = $this->pesertaModel->find($id);

        if ($p === null) {
            return redirect()->to(base_url('peserta'))
                ->with('error', 'Data peserta yang ingin diubah tidak ditemukan.');
        }

        return view('peserta/edit', [
            'page_title' => 'Ubah Peserta Magang',
            'p'          => $p,
            'akun'       => $this->userModel->akunPesertaBelumTertaut(
                $p['user_id'] === null ? null : (int) $p['user_id']
            ),
            'aksi'       => base_url('peserta/update/' . $p['id']),
            'tombol'     => 'Simpan Perubahan',
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $p = $this->pesertaModel->find($id);

        if ($p === null) {
            return redirect()->to(base_url('peserta'))
                ->with('error', 'Data peserta yang ingin diubah tidak ditemukan.');
        }

        if (! $this->fotoLolosValidasi()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data     = $this->ambilInput();
        $namaFoto = $this->unggah->simpan($this->request->getFile(self::KOLOM_FOTO));

        if ($namaFoto !== null) {
            $data[self::KOLOM_FOTO] = $namaFoto;
        }

        if (! $this->pesertaModel->update($id, $data)) {
            $this->unggah->hapus($namaFoto);

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->pesertaModel->errors());
        }

        // Foto lama baru dibuang setelah data barunya benar-benar tersimpan.
        if ($namaFoto !== null) {
            $this->unggah->hapus($p[self::KOLOM_FOTO]);
        }

        return redirect()->to(base_url('peserta'))
            ->with('sukses', 'Data peserta "' . $data['nama_peserta'] . '" berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $p = $this->pesertaModel->find($id);

        if ($p === null) {
            return redirect()->to(base_url('peserta'))
                ->with('error', 'Data peserta yang ingin dihapus tidak ditemukan.');
        }

        $this->pesertaModel->delete($id);
        $this->unggah->hapus($p[self::KOLOM_FOTO]);

        return redirect()->to(base_url('peserta'))
            ->with('sukses', 'Data peserta "' . $p['nama_peserta'] . '" berhasil dihapus.');
    }

    /**
     * Validasi berkas foto, hanya kalau memang ada berkas yang dikirim.
     *
     * Aturan file CI4 menganggap kolom berkas yang sama sekali tidak ada
     * sebagai pelanggaran, lalu pesan "Berkas yang diunggah bukan gambar"
     * menutupi seluruh pesan validasi kolom lain. Browser memang selalu
     * mengirim kolom file walau kosong, tapi pengecekan ini membuat form
     * tetap benar walau kolomnya tidak ikut terkirim.
     */
    private function fotoLolosValidasi(): bool
    {
        $berkas = $this->request->getFile(self::KOLOM_FOTO);

        if ($berkas === null || $berkas->getError() === UPLOAD_ERR_NO_FILE) {
            return true;
        }

        return $this->validate(
            [self::KOLOM_FOTO => $this->unggah->aturanValidasi(self::KOLOM_FOTO)],
            $this->unggah->pesanValidasi(self::KOLOM_FOTO)
        );
    }

    /**
     * Kolom yang boleh diisi dari form. Nama berkas foto tidak diambil dari
     * sini, melainkan dari hasil unggahan, supaya tidak bisa diisi sembarangan.
     */
    private function ambilInput(): array
    {
        $akun = $this->request->getPost('user_id');

        return [
            'user_id'                 => $akun === '' || $akun === null ? null : (int) $akun,
            'nik'                     => trim((string) $this->request->getPost('nik')),
            'nama_peserta'            => trim((string) $this->request->getPost('nama_peserta')),
            'nama_universitas'        => trim((string) $this->request->getPost('nama_universitas')),
            'nama_fakultas'           => trim((string) $this->request->getPost('nama_fakultas')),
            'nama_jurusan'            => trim((string) $this->request->getPost('nama_jurusan')),
            'tanggal_mulai_magang'    => (string) $this->request->getPost('tanggal_mulai_magang'),
            'tanggal_berakhir_magang' => (string) $this->request->getPost('tanggal_berakhir_magang'),
        ];
    }
}
