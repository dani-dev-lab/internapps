<?php

namespace App\Validation;
class AturanMagang
{
    public function tanggal_tidak_sebelum(?string $str, string $kolomPembanding, array $data): bool
    {
        $pembanding = $data[$kolomPembanding] ?? null;

        if ($str === null || $str === '' || $pembanding === null || $pembanding === '') {
            return true;
        }

        $tanggalIni  = strtotime($str);
        $tanggalItu  = strtotime($pembanding);

        if ($tanggalIni === false || $tanggalItu === false) {
            return true;
        }

        return $tanggalIni >= $tanggalItu;
    }
}
