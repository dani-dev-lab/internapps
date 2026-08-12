<?php

namespace App\Controllers;

use App\Models\PesertaMagangModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function index(): string
    {
        return view('auth/login', ['page_title' => 'Masuk']);
    }

    public function prosesLogin(): RedirectResponse
    {
        $aturan = [
            'username' => 'required|max_length[50]',
            'password' => 'required|max_length[100]',
        ];

        $pesan = [
            'username' => ['required' => 'Username wajib diisi.'],
            'password' => ['required' => 'Password wajib diisi.'],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->cariUsername($username);

        // Pesan gagal sengaja dibuat sama persis untuk username yang tidak
        // terdaftar maupun password yang salah. Kalau pesannya dibedakan,
        // halaman login bisa dipakai untuk menebak username mana yang ada.
        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah.');
        }

        // Ganti id session dengan yang baru supaya id yang mungkin sudah
        // diketahui orang lain sebelum login tidak bisa dipakai lagi
        // (perlindungan terhadap session fixation).
        session()->regenerate(true);

        session()->set([
            'user_id'       => (int) $user['id'],
            'username'      => $user['username'],
            'nama_pengguna' => $user['nama_pengguna'],
            'nama_role'     => $user['nama_role'],
            'foto'          => $user['foto'],
            'logged_in'     => true,
        ]);

        return redirect()->to(base_url('dashboard'))
            ->with('sukses', 'Selamat datang, ' . $user['nama_pengguna'] . '.');
    }

    public function register(): string
    {
        return view('auth/register', ['page_title' => 'Daftar Akun']);
    }

    /**
     * Pendaftaran akun oleh peserta magang.
     *
     * Bukan pendaftaran terbuka: peserta hanya bisa membuat akun kalau datanya
     * SUDAH didaftarkan admin atau staff, dan belum diklaim akun lain. NIK dan
     * nama dipakai sebagai bukti bahwa yang mendaftar memang orang tersebut.
     *
     * Role tidak pernah diambil dari form — selalu dipaksa 'peserta' di sini,
     * supaya tidak ada yang bisa mendaftarkan dirinya sebagai admin.
     */
    public function prosesRegister(): RedirectResponse
    {
        $aturan = [
            'nik'                 => 'required|numeric|exact_length[16]',
            'nama_peserta'        => 'required|max_length[100]',
            'username'            => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username]',
            'password'            => 'required|min_length[6]|max_length[100]',
            'password_konfirmasi' => 'required|matches[password]',
        ];

        $pesan = [
            'nik' => [
                'required'     => 'NIK wajib diisi.',
                'numeric'      => 'NIK hanya boleh berisi angka.',
                'exact_length' => 'NIK harus tepat 16 digit.',
            ],
            'nama_peserta' => ['required' => 'Nama lengkap wajib diisi.'],
            'username'     => [
                'required'   => 'Username wajib diisi.',
                'alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan strip.',
                'min_length' => 'Username minimal 3 karakter.',
                'is_unique'  => 'Username ini sudah dipakai. Silakan pilih yang lain.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 6 karakter.',
            ],
            'password_konfirmasi' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak sama dengan password.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $nik  = trim((string) $this->request->getPost('nik'));
        $nama = trim((string) $this->request->getPost('nama_peserta'));

        $pesertaModel = new PesertaMagangModel();
        $peserta      = $pesertaModel->cariNik($nik);

        // Pesan sengaja tidak membedakan "NIK tidak ada" dengan "nama tidak
        // cocok". Kalau dibedakan, form ini bisa dipakai menebak NIK mana saja
        // yang terdaftar sebagai peserta magang.
        if ($peserta === null || mb_strtolower($peserta['nama_peserta']) !== mb_strtolower($nama)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'NIK dan nama tidak cocok dengan data peserta magang mana pun. Pastikan penulisannya sama persis dengan yang didaftarkan admin.');
        }

        if ($peserta['user_id'] !== null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data peserta ini sudah memiliki akun. Silakan masuk, atau hubungi admin jika lupa password.');
        }

        $role = (new RoleModel())->where('nama_role', 'peserta')->first();

        if ($role === null) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Role peserta belum tersedia di sistem. Hubungi admin.');
        }

        $userModel = new UserModel();
        $db        = db_connect();

        // Pembuatan akun dan penautannya ke data peserta harus berhasil
        // dua-duanya. Tanpa transaksi, kegagalan di langkah kedua menyisakan
        // akun yang tidak tertaut ke data magang mana pun.
        $db->transBegin();

        $idUser = $userModel->insert([
            'role_id'       => $role['id'],
            'username'      => trim((string) $this->request->getPost('username')),
            'password'      => (string) $this->request->getPost('password'),
            // Nama diambil dari data resmi peserta, bukan dari isian pendaftar.
            'nama_pengguna' => $peserta['nama_peserta'],
        ]);

        if ($idUser === false) {
            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('errors', $userModel->errors());
        }

        $pesertaModel->update($peserta['id'], ['user_id' => $idUser]);

        if ($db->transStatus() === false) {
            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun gagal dibuat. Silakan coba lagi.');
        }

        $db->transCommit();

        // Sengaja tidak langsung masuk. Pendaftar baru saja menetapkan
        // passwordnya, jadi mengetiknya sekali lagi sekaligus memastikan
        // ia benar-benar mengingatnya.
        return redirect()->to(base_url('login'))
            ->with('sukses', 'Akun berhasil dibuat. Silakan masuk memakai username dan password yang baru saja Anda buat.');
    }

    public function logout(): RedirectResponse
    {
        $session = session();

        // Buang SELURUH isi session, bukan hanya kunci login. Ini sekaligus
        // membuang sisa isian form lama (_ci_old_input), supaya kolom username
        // di halaman login benar-benar kosong sesudah keluar.
        $isi = $session->get();

        if (is_array($isi) && $isi !== []) {
            $session->remove(array_keys($isi));
        }

        // Terbitkan id session baru dan hapus berkas session yang lama,
        // sehingga id yang sempat dipegang browser tidak bisa dipakai lagi.
        $session->regenerate(true);

        return redirect()->to(base_url('login'))
            ->with('sukses', 'Anda telah keluar dari aplikasi.');
    }
}
