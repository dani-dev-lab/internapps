<?php

if (! function_exists('status_magang')) {
    function status_magang(?string $mulai, ?string $berakhir): string
    {
        if (empty($mulai) || empty($berakhir)) {
            return 'Tidak Diketahui';
        }

        $hariIni = date('Y-m-d');

        if ($hariIni < $mulai) {
            return 'Belum Mulai';
        }

        if ($hariIni > $berakhir) {
            return 'Selesai';
        }

        return 'Sedang Magang';
    }
}

if (! function_exists('kelas_badge_status')) {
    function kelas_badge_status(string $status): string
    {
        return match ($status) {
            'Belum Mulai'   => 'badge-secondary',
            'Sedang Magang' => 'badge-success',
            'Selesai'       => 'badge-info',
            default         => 'badge-light',
        };
    }
}

if (! function_exists('badge_status_magang')) {
    function badge_status_magang(?string $mulai, ?string $berakhir): string
    {
        $status = status_magang($mulai, $berakhir);

        return '<span class="badge ' . kelas_badge_status($status) . '">' . esc($status) . '</span>';
    }
}

if (! function_exists('sisa_hari_magang')) {
    function sisa_hari_magang(?string $berakhir): ?int
    {
        if (empty($berakhir)) {
            return null;
        }

        $akhir = strtotime($berakhir);

        if ($akhir === false) {
            return null;
        }

        $selisih = $akhir - strtotime(date('Y-m-d'));

        return (int) floor($selisih / 86400);
    }
}

if (! function_exists('foto_peserta')) {
    function foto_peserta(?string $namaFile): string
    {
        if (empty($namaFile)) {
            return base_url('assets/img/internapps/default-avatar.svg');
        }

        return base_url('uploads/peserta/' . $namaFile);
    }
}

if (! function_exists('foto_user')) {
    function foto_user(?string $namaFile): string
    {
        if (empty($namaFile)) {
            return base_url('assets/img/internapps/default-avatar.svg');
        }

        return base_url('uploads/avatar/' . $namaFile);
    }
}

if (! function_exists('tanggal_singkat')) {
    function tanggal_singkat(?string $tanggal): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        $waktu = strtotime($tanggal);

        if ($waktu === false) {
            return '-';
        }

        $bulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return date('j', $waktu) . ' ' . $bulan[(int) date('n', $waktu)] . ' ' . date('Y', $waktu);
    }
}

if (! function_exists('tanggal_indonesia')) {
    function tanggal_indonesia(?string $tanggal): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        $waktu = strtotime($tanggal);

        if ($waktu === false) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return date('j', $waktu) . ' ' . $bulan[(int) date('n', $waktu)] . ' ' . date('Y', $waktu);
    }
}
