<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Membatasi halaman berdasarkan role pengguna.
 *
 * Dipakai di route seperti ini:
 *     ['filter' => 'role:admin']
 *     ['filter' => 'role:admin,staff']
 *
 * Inilah pembatasan akses yang sesungguhnya. Menyembunyikan menu di sidebar
 * hanya merapikan tampilan — tanpa filter ini, staff cukup mengetik alamat
 * /users di browser untuk masuk ke halaman yang bukan haknya.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('nama_role');

        // Belum masuk sama sekali: arahkan ke halaman login, bukan halaman
        // ditolak, supaya pesannya sesuai dengan keadaan pengguna.
        if (! session()->get('logged_in') || $role === null) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman itu.');
        }

        // Route yang memasang filter ini tapi lupa menyebutkan role-nya
        // dianggap salah tulis, dan ditolak. Lebih baik menolak karena salah
        // konfigurasi daripada diam-diam membuka halaman untuk semua orang.
        if (empty($arguments)) {
            return $this->tolak($role);
        }

        if (! in_array($role, $arguments, true)) {
            return $this->tolak($role, $arguments);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    /**
     * Balas dengan status 403 dan halaman penjelasan bergaya Atlantis.
     *
     * Sengaja 403 (terlarang), bukan pengalihan diam-diam, supaya pengguna
     * tahu halamannya ada tetapi bukan haknya.
     */
    private function tolak(string $role, array $diizinkan = []): ResponseInterface
    {
        $isi = view('errors/403', [
            'page_title' => 'Akses Ditolak',
            'role'       => $role,
            'diizinkan'  => $diizinkan,
        ]);

        return response()->setStatusCode(403)->setBody($isi);
    }
}
