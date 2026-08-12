<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Menolak akses halaman aplikasi bagi yang belum masuk.
 *
 * Inilah pengaman yang sesungguhnya. Menyembunyikan menu di sidebar hanya
 * merapikan tampilan — tanpa filter ini, halaman tetap bisa dibuka dengan
 * mengetik alamatnya langsung di browser.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman itu.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
