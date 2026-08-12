<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Kebalikan dari AuthFilter: menahan pengguna yang sudah masuk agar tidak
 * membuka halaman login lagi, dan langsung mengarahkannya ke dashboard.
 */
class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
