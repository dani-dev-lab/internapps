<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Pengelolaan pengguna aplikasi. Hanya untuk role admin — pembatasannya
 * dipasang di app/Config/Routes.php lewat filter 'role:admin'.
 */
class User extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index(): string
    {
        return view('user/index', [
            'page_title' => 'Data Pengguna',
            'users'      => $this->userModel->denganRole()->orderBy('users.id', 'ASC')->findAll(),
        ]);
    }

    public function create(): string
    {
        // Variabel untuk partial user/_form dikirim dari sini, bukan lewat
        // $this->include(). Parameter kedua include() adalah opsi renderer,
        // bukan data view — data diwarisi dari view induknya.
        return view('user/create', [
            'page_title' => 'Tambah Pengguna',
            'roles'      => $this->roleModel->orderBy('nama_role', 'ASC')->findAll(),
            'aksi'       => base_url('users'),
            'user'       => null,
            'tombol'     => 'Simpan Pengguna',
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = [
            'role_id'       => $this->request->getPost('role_id'),
            'username'      => trim((string) $this->request->getPost('username')),
            'password'      => (string) $this->request->getPost('password'),
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
        ];

        // Password di-hash oleh callback UserModel, bukan di sini.
        if (! $this->userModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        return redirect()->to(base_url('users'))
            ->with('sukses', 'Pengguna "' . $data['nama_pengguna'] . '" berhasil ditambahkan.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $user = $this->userModel->find($id);

        if ($user === null) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Pengguna yang ingin diubah tidak ditemukan.');
        }

        return view('user/edit', [
            'page_title' => 'Ubah Pengguna',
            'user'       => $user,
            'roles'      => $this->roleModel->orderBy('nama_role', 'ASC')->findAll(),
            'aksi'       => base_url('users/update/' . $user['id']),
            'tombol'     => 'Simpan Perubahan',
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $user = $this->userModel->find($id);

        if ($user === null) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Pengguna yang ingin diubah tidak ditemukan.');
        }

        $roleBaru = (int) $this->request->getPost('role_id');

        // Admin yang menurunkan role-nya sendiri akan langsung kehilangan
        // akses ke halaman ini dan tidak bisa mengembalikannya.
        if ($id === (int) session('user_id') && $roleBaru !== (int) $user['role_id']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $data = [
            'role_id'       => $roleBaru,
            'username'      => trim((string) $this->request->getPost('username')),
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
            // Kosong berarti password lama dipertahankan — ditangani UserModel.
            'password'      => (string) $this->request->getPost('password'),
        ];

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        // Kalau admin mengubah datanya sendiri, segarkan session supaya nama
        // di navbar dan sidebar tidak menampilkan data lama.
        if ($id === (int) session('user_id')) {
            session()->set([
                'username'      => $data['username'],
                'nama_pengguna' => $data['nama_pengguna'],
            ]);
        }

        return redirect()->to(base_url('users'))
            ->with('sukses', 'Data pengguna "' . $data['nama_pengguna'] . '" berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $user = $this->userModel->cariDenganRole($id);

        if ($user === null) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Pengguna yang ingin dihapus tidak ditemukan.');
        }

        // Pengaman 1: jangan sampai admin menghapus akun yang sedang dipakainya
        // sendiri, karena sesudah itu ia langsung terlempar keluar.
        if ($id === (int) session('user_id')) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda pakai.');
        }

        // Pengaman 2: kalau admin terakhir dihapus, tidak ada lagi yang bisa
        // mengelola pengguna dan aplikasi terkunci selamanya.
        if ($user['nama_role'] === 'admin' && $this->userModel->hitungPerRole('admin') <= 1) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Ini admin terakhir. Buat admin lain dulu sebelum menghapusnya.');
        }

        // Buang juga berkas foto profilnya supaya tidak menumpuk tanpa pemilik.
        if (! empty($user['foto'])) {
            $berkas = FCPATH . 'uploads/avatar/' . $user['foto'];

            if (is_file($berkas)) {
                @unlink($berkas);
            }
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('users'))
            ->with('sukses', 'Pengguna "' . $user['nama_pengguna'] . '" berhasil dihapus.');
    }
}
