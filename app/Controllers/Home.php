<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): RedirectResponse
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return redirect()->to(base_url('login'));
    }
}
