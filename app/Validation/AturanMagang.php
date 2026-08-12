<?php

namespace App\Validation;

/**
 * Aturan validasi tambahan milik Internapps.
 *
 * CodeIgniter 4 tidak menyediakan aturan bawaan untuk membandingkan dua kolom
 * tanggal, padahal aplikasi ini butuh memastikan tanggal berakhir magang tidak
 * mendahului tanggal mulai.
 *
 * Didaftarkan di app/Config/Validation.php pada properti $ruleSets.
 */
class AturanMagang
{
    /**
     * Memastikan nilai tanggal tidak lebih awal daripada tanggal di kolom lain.
     *
     * Pemakaian: tanggal_tidak_sebelum[nama_kolom_pembanding]
     */
    public function tanggal_tidak_sebelum(?string $str, string $kolomPembanding, array $data): bool
    {
        $pembanding = $data[$kolomPembanding] ?? null;

        // Kalau salah satu kosong, biarkan aturan 'required' yang memberi pesan.
        if ($str === null || $str === '' || $pembanding === null || $pembanding === '') {
            return true;
        }

        $tanggalIni  = strtotime($str);
        $tanggalItu  = strtotime($pembanding);

        // Kalau formatnya bukan tanggal, biarkan aturan 'valid_date' yang bicara.
        if ($tanggalIni === false || $tanggalItu === false) {
            return true;
        }

        return $tanggalIni >= $tanggalItu;
    }
}
