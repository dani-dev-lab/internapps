<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('nama_role');

        if (! session()->get('logged_in') || $role === null) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman itu.');
        }

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
