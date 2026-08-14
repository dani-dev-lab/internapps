<?php

namespace App\Controllers;

use App\Libraries\UnggahFoto;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Profil extends BaseController
{
    protected UserModel $userModel;
    protected UnggahFoto $unggah;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->unggah    = new UnggahFoto('avatar');
    }

    public function index(): string|RedirectResponse
    {
        $user = $this->userModel->cariDenganRole($this->idSaya());

        if ($user === null) {
            return redirect()->to(base_url('logout'));
        }

        return view('profil/index', [
            'page_title' => 'Profil Saya',
            'user'       => $user,
        ]);
    }

    public function updateData(): RedirectResponse
    {
        $id = $this->idSaya();

        $data = [
            'username'      => trim((string) $this->request->getPost('username')),
            'nama_pengguna' => trim((string) $this->request->getPost('nama_pengguna')),
        ];

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->userModel->errors());
        }

        session()->set([
            'username'      => $data['username'],
            'nama_pengguna' => $data['nama_pengguna'],
        ]);

        return redirect()->to(base_url('profil'))
            ->with('sukses', 'Data profil berhasil diperbarui.');
    }

    public function updateFoto(): RedirectResponse
    {
        $id   = $this->idSaya();
        $user = $this->userModel->find($id);

        $berkas = $this->request->getFile('foto');

        if ($berkas === null || $berkas->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->to(base_url('profil'))
                ->with('error', 'Belum ada berkas foto yang dipilih.');
        }

        if (! $this->validate(
            ['foto' => $this->unggah->aturanValidasi('foto', true)],
            $this->unggah->pesanValidasi('foto')
        )) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $namaBaru = $this->unggah->simpan($berkas);

        if ($namaBaru === null) {
            return redirect()->to(base_url('profil'))
                ->with('error', 'Foto gagal diunggah, silakan coba lagi.');
        }

        if (! $this->userModel->update($id, ['foto' => $namaBaru])) {
            $this->unggah->hapus($namaBaru);

            return redirect()->to(base_url('profil'))
                ->with('error', 'Foto gagal disimpan.');
        }

        $this->unggah->hapus($user['foto']);

        session()->set('foto', $namaBaru);

        return redirect()->to(base_url('profil'))
            ->with('sukses', 'Foto profil berhasil diperbarui.');
    }

    public function hapusFoto(): RedirectResponse
    {
        $id   = $this->idSaya();
        $user = $this->userModel->find($id);

        if (empty($user['foto'])) {
            return redirect()->to(base_url('profil'))
                ->with('error', 'Anda belum punya foto profil.');
        }

        $this->userModel->update($id, ['foto' => null]);
        $this->unggah->hapus($user['foto']);

        session()->set('foto', null);

        return redirect()->to(base_url('profil'))
            ->with('sukses', 'Foto profil berhasil dihapus.');
    }

    public function updatePassword(): RedirectResponse
    {
        $id   = $this->idSaya();
        $user = $this->userModel->find($id);

        $aturan = [
            'password_lama'       => 'required',
            'password_baru'       => 'required|min_length[6]|max_length[100]',
            'password_konfirmasi' => 'required|matches[password_baru]',
        ];

        $pesan = [
            'password_lama' => ['required' => 'Password lama wajib diisi.'],
            'password_baru' => [
                'required'   => 'Password baru wajib diisi.',
                'min_length' => 'Password baru minimal 6 karakter.',
            ],
            'password_konfirmasi' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak sama dengan password baru.',
            ],
        ];

        if (! $this->validate($aturan, $pesan)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        if (! password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->to(base_url('profil'))
                ->with('error', 'Password lama tidak sesuai.');
        }

        $this->userModel->update($id, [
            'password' => (string) $this->request->getPost('password_baru'),
        ]);

        return redirect()->to(base_url('profil'))
            ->with('sukses', 'Password berhasil diubah.');
    }

    private function idSaya(): int
    {
        return (int) session('user_id');
    }
}
