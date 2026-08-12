<?php
/**
 * Halaman ubah pengguna aplikasi.
 *
 * @var array  $user   Data pengguna yang sedang diubah
 * @var array  $roles  Daftar role untuk dropdown (dipakai user/_form)
 * @var string $aksi   URL tujuan form (dipakai user/_form)
 * @var string $tombol Teks tombol simpan (dipakai user/_form)
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
	<div class="col-md-8">
		<div class="card card-round">
			<div class="card-header">
				<div class="card-head-row">
					<h4 class="card-title">Ubah Pengguna</h4>
					<div class="card-tools">
						<span class="badge badge-light">ID #<?= (int) $user['id'] ?></span>
					</div>
				</div>
			</div>
			<div class="card-body">
				<?php if ((int) $user['id'] === (int) session('user_id')): ?>
					<div class="alert alert-info" role="alert">
						<i class="fas fa-info-circle mr-1"></i>
						Ini akun yang sedang Anda pakai. Role-nya tidak dapat diubah dari sini
						agar Anda tidak kehilangan akses.
					</div>
				<?php endif; ?>

				<?= $this->include('user/_form') ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
