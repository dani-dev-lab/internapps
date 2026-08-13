<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Pengelolaan pengguna aplikasi. Untuk role superadmin dan admin —
 * pembatasannya dipasang di app/Config/Routes.php lewat filter role.
 *
 * Aturan tambahan soal superadmin, semuanya ditegakkan di controller ini:
 *
 *  1. Akun superadmin tidak dapat diubah maupun dihapus oleh admin biasa.
 *     Termasuk mengubah passwordnya — kalau ini dibiarkan, admin bisa
 *     memasang password baru lalu masuk sebagai superadmin.
 *  2. Hanya superadmin yang boleh memberikan role superadmin kepada akun
 *     lain, supaya admin tidak bisa mencetak superadmin baru.
 *  3. Superadmin terakhir tidak dapat dihapus atau diturunkan rolenya,
 *     bahkan oleh sesama superadmin, supaya aplikasi tidak pernah
 *     kehilangan pemiliknya.
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

    /**
     * Apakah yang sedang masuk adalah superadmin.
     */
    private function sayaSuperadmin(): bool
    {
        return session('nama_role') === 'superadmin';
    }

    /**
     * Nama role dari sebuah id role. Null kalau rolenya tidak ada.
     */
    private function namaRole(int $roleId): ?string
    {
        $role = $this->roleModel->find($roleId);

        return $role === null ? null : $role['nama_role'];
    }

    /**
     * Daftar role yang boleh dipilih di formulir.
     *
     * Admin biasa tidak ditawari pilihan superadmin. Ini hanya membereskan
     * tampilan — penolakan yang sebenarnya tetap ada di store() dan update(),
     * karena isian formulir bisa saja dikirim langsung tanpa lewat halaman.
     */
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
        // Variabel untuk partial user/_form dikirim dari sini, bukan lewat
        // $this->include(). Parameter kedua include() adalah opsi renderer,
        // bukan data view — data diwarisi dari view induknya.
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
        // Hanya superadmin yang boleh mencetak superadmin baru.
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

        // Admin biasa tidak boleh menyentuh akun superadmin sama sekali —
        // bukan hanya rolenya. Kalau ia bisa mengganti password superadmin,
        // ia tinggal masuk memakai password itu.
        if ($user['nama_role'] === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Akun superadmin hanya dapat diubah oleh superadmin sendiri.');
        }

        // Hanya superadmin yang boleh menaikkan akun lain menjadi superadmin.
        if ($this->namaRole($roleBaru) === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hanya superadmin yang dapat memberikan role superadmin.');
        }

        // Superadmin terakhir tidak boleh diturunkan rolenya, karena sesudah
        // itu tidak ada lagi akun yang dapat mengelola superadmin.
        if ($user['nama_role'] === 'superadmin'
            && $roleBaru !== (int) $user['role_id']
            && $this->userModel->hitungPerRole('superadmin') <= 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ini superadmin terakhir. Angkat superadmin lain dulu sebelum menurunkan rolenya.');
        }

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

        // Pengaman 2: admin biasa tidak boleh menghapus superadmin. Inilah
        // yang mencegah admin baru "mengkudeta" pemilik aplikasi.
        if ($user['nama_role'] === 'superadmin' && ! $this->sayaSuperadmin()) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Akun superadmin tidak dapat dihapus oleh admin.');
        }

        // Pengaman 3: superadmin terakhir tidak boleh dihapus, bahkan oleh
        // sesama superadmin.
        if ($user['nama_role'] === 'superadmin' && $this->userModel->hitungPerRole('superadmin') <= 1) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Ini superadmin terakhir. Angkat superadmin lain dulu sebelum menghapusnya.');
        }

        // Pengaman 4: jangan sampai tidak tersisa satu pun akun yang dapat
        // mengelola pengguna. Yang dihitung adalah superadmin DAN admin,
        // karena keduanya sama-sama berhak membuka halaman ini. Menghapus
        // admin terakhir tidak masalah selama masih ada superadmin.
        if ($user['nama_role'] === 'admin'
            && $this->userModel->hitungPerRole('admin') <= 1
            && $this->userModel->hitungPerRole('superadmin') === 0) {
            return redirect()->to(base_url('users'))
                ->with('error', 'Ini admin terakhir dan tidak ada superadmin. Buat admin lain dulu sebelum menghapusnya.');
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
