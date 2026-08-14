<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class User extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    private function sayaSuperadmin(): bool
    {
        return session('nama_role') === 'superadmin';
    }

    private function namaRole(int $roleId): ?string
    {
        $role = $this->roleModel->find($roleId);

        return $role === null ? null : $role['nama_role'];
    }

    private function pilihanRole(): array
    {
        $roles = $this->roleModel->orderBy('nama_role', 'ASC')->findAll();

        if ($this->sayaSuperadmin()) {
            return $roles;
        }

        return array_values(array_filter(
            $roles,
            static fn (array $r): bool => $r['nama_role'] !== 'superadmin'
        ));
    }

    public function index(): string
    {
        return view('user/index', [
            'page_title'      => 'Data Pengguna',
            'users'           => $this->userModel->denganRole()->orderBy('users.id', 'ASC')->findAll(),
            'sayaSuperadmin'  => $this->sayaSuperadmin(),
        ]);
    }

    public function create(): string
    {
        return view('user/create', [
            'page_title' => 'Tambah Pengguna',
            'roles'      => $this->pilihanRole(),
            'aksi'       => base_url('users'),
            'user'       => null,
            'tombol'     => 'Simpan Pengguna',
        ]);
    }

    public function store(): RedirectResponse
    {
        if ($this->namaRole((int) $this->request->getPost('role_id')) === 'superadmin'
            && ! $this->sayaSuperadmin()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hanya superadmin yang dapat memberikan role superadmin.');
        }

        $data = [
            'role_id'       => $this->request->getPost('role_id'),
            'username'      => trim((string) $this->request->getPost('username')),
            'password'      => (string) $this->request->getPost('password'),
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
        ];

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
        $user = $this->userModel->cariDenganRole($id);

        if ($user === null) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Pengguna yang ingin diubah tidak ditemukan.');
        }

        if ($user['nama_role'] === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Akun superadmin hanya dapat diubah oleh superadmin sendiri.');
        }

        return view('user/edit', [
            'page_title' => 'Ubah Pengguna',
            'user'       => $user,
            'roles'      => $this->pilihanRole(),
            'aksi'       => base_url('users/update/' . $user['id']),
            'tombol'     => 'Simpan Perubahan',
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $user = $this->userModel->cariDenganRole($id);

        if ($user === null) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Pengguna yang ingin diubah tidak ditemukan.');
        }

        $roleBaru = (int) $this->request->getPost('role_id');

        if ($user['nama_role'] === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Akun superadmin hanya dapat diubah oleh superadmin sendiri.');
        }

        if ($this->namaRole($roleBaru) === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hanya superadmin yang dapat memberikan role superadmin.');
        }

        if ($user['nama_role'] === 'superadmin'
            && $roleBaru !== (int) $user['role_id']
            && $this->userModel->hitungPerRole('superadmin') <= 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ini superadmin terakhir. Angkat superadmin lain dulu sebelum menurunkan rolenya.');
        }

        if ($id === (int) session('user_id') && $roleBaru !== (int) $user['role_id']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $data = [
            'role_id'       => $roleBaru,
            'username'      => trim((string) $this->request->getPost('username')),
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
            'password'      => (string) $this->request->getPost('password'),
        ];

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

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

        if ($id === (int) session('user_id')) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda pakai.');
        }

        if ($user['nama_role'] === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Akun superadmin tidak dapat dihapus oleh admin.');
        }

        if ($user['nama_role'] === 'superadmin' && $this->userModel->hitungPerRole('superadmin') <= 1) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Ini superadmin terakhir. Angkat superadmin lain dulu sebelum menghapusnya.');
        }

        if ($user['nama_role'] === 'admin'
            && $this->userModel->hitungPerRole('admin') <= 1
            && $this->userModel->hitungPerRole('superadmin') === 0) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Ini admin terakhir dan tidak ada superadmin. Buat admin lain dulu sebelum menghapusnya.');
        }

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
