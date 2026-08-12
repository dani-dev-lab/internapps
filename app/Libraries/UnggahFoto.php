<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Penanganan unggahan foto.
 *
 * Dipakai dua tempat dengan folder berbeda: foto resmi peserta magang
 * (uploads/peserta) dan foto profil akun (uploads/avatar). Dikumpulkan di satu
 * kelas supaya aturan keamanannya tidak ditulis ulang dan tidak berbeda-beda.
 */
class UnggahFoto
{
    /** Batas ukuran berkas dalam kilobyte. */
    public const MAKS_KB = 2048;

    /**
     * Tipe berkas diperiksa dari isinya (MIME), bukan dari ekstensi nama
     * berkas. Berkas skrip yang namanya diubah jadi .jpg tetap tertolak.
     */
    public const MIME_DIIZINKAN = 'image/jpg,image/jpeg,image/png,image/webp';

    private string $folder;

    public function __construct(string $folder)
    {
        $this->folder = trim($folder, '/\\');
    }

    /**
     * Aturan validasi CI4 untuk kolom unggahan.
     *
     * Tanpa 'uploaded[...]', aturan file CI4 meloloskan keadaan "tidak ada
     * berkas dikirim" — itu memang yang diinginkan saat mengubah data tanpa
     * mengganti fotonya.
     */
    public function aturanValidasi(string $kolom, bool $wajib = false): string
    {
        $aturan = [];

        if ($wajib) {
            $aturan[] = 'uploaded[' . $kolom . ']';
        }

        $aturan[] = 'is_image[' . $kolom . ']';
        $aturan[] = 'mime_in[' . $kolom . ',' . self::MIME_DIIZINKAN . ']';
        $aturan[] = 'max_size[' . $kolom . ',' . self::MAKS_KB . ']';
        $aturan[] = 'max_dims[' . $kolom . ',5000,5000]';

        return implode('|', $aturan);
    }

    public function pesanValidasi(string $kolom): array
    {
        return [
            $kolom => [
                'uploaded' => 'Foto wajib diunggah.',
                'is_image' => 'Berkas yang diunggah bukan gambar.',
                'mime_in'  => 'Foto harus berformat JPG, PNG, atau WEBP.',
                'max_size' => 'Ukuran foto maksimal ' . (self::MAKS_KB / 1024) . ' MB.',
                'max_dims' => 'Ukuran gambar terlalu besar, maksimal 5000 x 5000 piksel.',
            ],
        ];
    }

    /**
     * Simpan berkas dan kembalikan nama berkas barunya, atau null kalau
     * memang tidak ada berkas yang dikirim.
     */
    public function simpan(?UploadedFile $berkas): ?string
    {
        if ($berkas === null || ! $berkas->isValid() || $berkas->hasMoved()) {
            return null;
        }

        $tujuan = $this->jalurFolder();

        if (! is_dir($tujuan)) {
            mkdir($tujuan, 0755, true);
        }

        // getRandomName() membuang nama asli dari pengguna. Nama asli bisa
        // berisi karakter yang menyulitkan, atau sengaja dibuat untuk menimpa
        // berkas lain yang sudah ada.
        $nama = $berkas->getRandomName();

        $berkas->move($tujuan, $nama);

        return $nama;
    }

    public function hapus(?string $namaBerkas): void
    {
        if (empty($namaBerkas)) {
            return;
        }

        // basename() menutup upaya keluar dari folder lewat nama seperti
        // "../../app/Config/App.php".
        $berkas = $this->jalurFolder() . basename($namaBerkas);

        if (is_file($berkas)) {
            @unlink($berkas);
        }
    }

    private function jalurFolder(): string
    {
        return FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $this->folder . DIRECTORY_SEPARATOR;
    }
}
