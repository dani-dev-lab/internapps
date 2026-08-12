<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    /**
     * Halaman depan tidak menampilkan apa pun, hanya mengarahkan sesuai
     * keadaan pengguna. Dengan begitu alamat root selalu berujung ke
     * halaman yang benar.
     */
    public function index(): RedirectResponse
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return redirect()->to(base_url('login'));
    }
}
