<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
class UnggahFoto
{
    public const MAKS_KB = 2048;

    public const MIME_DIIZINKAN = 'image/jpg,image/jpeg,image/png,image/webp';

    private string $folder;

    public function __construct(string $folder)
    {
        $this->folder = trim($folder, '/\\');
    }

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

    public function simpan(?UploadedFile $berkas): ?string
    {
        if ($berkas === null || ! $berkas->isValid() || $berkas->hasMoved()) {
            return null;
        }

        $tujuan = $this->jalurFolder();

        if (! is_dir($tujuan)) {
            mkdir($tujuan, 0755, true);
        }

        $nama = $berkas->getRandomName();

        $berkas->move($tujuan, $nama);

        return $nama;
    }

    public function hapus(?string $namaBerkas): void
    {
        if (empty($namaBerkas)) {
            return;
        }

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
