<?php

namespace App\Controllers;

use App\Models\PesertaMagangModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    /**
     * Satu alamat /dashboard, dua tampilan berbeda.
     *
     * Pengelola (admin & staff) melihat ringkasan seluruh peserta, sedangkan
     * peserta melihat data magangnya sendiri. Dipisah di sini supaya view-nya
     * tidak penuh percabangan.
     */
    public function index(): string
    {
        return session('nama_role') === 'peserta'
            ? $this->dashboardPeserta()
            : $this->dashboardPengelola();
    }

    private function dashboardPengelola(): string
    {
        $pesertaModel = new PesertaMagangModel();

        $data = [
            'page_title' => 'Dashboard',
            'statistik'  => $pesertaModel->hitungPerStatus(),
            'sedang'     => $pesertaModel->sedangMagang(5),
            'pengguna'   => null,
        ];

        // Ringkasan akun hanya ditampilkan untuk admin, karena staff memang
        // tidak berhak melihat data pengguna aplikasi.
        if (session('nama_role') === 'admin') {
            $userModel = new UserModel();

            $data['pengguna'] = [
                'total'   => $userModel->countAllResults(),
                'admin'   => $userModel->hitungPerRole('admin'),
                'staff'   => $userModel->hitungPerRole('staff'),
                'peserta' => $userModel->hitungPerRole('peserta'),
            ];
        }

        return view('dashboard/index', $data);
    }

    private function dashboardPeserta(): string
    {
        // Data diambil berdasarkan user_id dari session, bukan dari alamat URL,
        // supaya peserta tidak bisa melihat data peserta lain.
        $peserta = (new PesertaMagangModel())->cariMilikUser((int) session('user_id'));

        return view('dashboard/peserta', [
            'page_title' => 'Dashboard',
            'peserta'    => $peserta,
        ]);
    }
}
