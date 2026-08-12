<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<?php $galat = session('errors') ?? []; ?>

<div class="text-center mb-4">
	<img src="<?= base_url('assets/img/internapps/logo-mark-white.svg') ?>" alt="" width="64" class="mb-3">
	<div>
		<img src="<?= base_url('assets/img/internapps/logo-wordmark-white.svg') ?>" alt="Internapps" height="26">
	</div>
	<p class="text-white op-7 mt-2 mb-0">Magang jadi lebih terarah</p>
</div>

<div class="card card-round">
	<div class="card-body p-4">
		<h4 class="text-center mb-1 fw-bold">Masuk</h4>
		<p class="text-muted text-center mb-4">Sistem Informasi Peserta Magang</p>

		<?php if (session('error')): ?>
			<div class="alert alert-danger" role="alert">
				<i class="fas fa-exclamation-circle mr-1"></i>
				<?= esc(session('error')) ?>
			</div>
		<?php endif; ?>

		<?php if (session('sukses')): ?>
			<div class="alert alert-success" role="alert">
				<?= esc(session('sukses')) ?>
			</div>
		<?php endif; ?>

		<?php
		// Username dan password sengaja TIDAK diisikan otomatis oleh browser.
		// autocomplete="off" pada form dan kolom username mencegah saran
		// isian, sedangkan "new-password" pada kolom password adalah cara
		// paling ampuh menghentikan pengisian otomatis — browser modern
		// mengabaikan "off" pada kolom sandi, tapi menghormati "new-password"
		// karena menganggapnya sandi yang sedang dibuat, bukan yang tersimpan.
		?>
		<?= form_open(base_url('login'), ['autocomplete' => 'off']) ?>
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text"
					class="form-control<?= isset($galat['username']) ? ' is-invalid' : '' ?>"
					id="username"
					name="username"
					value="<?= esc(old('username')) ?>"
					placeholder="Masukkan username"
					autocomplete="off"
					autofocus>
				<?php if (isset($galat['username'])): ?>
					<div class="invalid-feedback"><?= esc($galat['username']) ?></div>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password"
					class="form-control<?= isset($galat['password']) ? ' is-invalid' : '' ?>"
					id="password"
					name="password"
					placeholder="Masukkan password"
					autocomplete="new-password">
				<?php if (isset($galat['password'])): ?>
					<div class="invalid-feedback"><?= esc($galat['password']) ?></div>
				<?php endif; ?>
			</div>

			<button type="submit" class="btn btn-primary btn-block btn-round mt-4">
				<i class="fas fa-sign-in-alt mr-1"></i> Masuk
			</button>
		<?= form_close() ?>

		<p class="text-center text-muted mt-4 mb-0">
			Peserta magang belum punya akun?
			<a href="<?= base_url('register') ?>">Daftar di sini</a>
		</p>
	</div>
</div>

<p class="text-center text-white op-6 mt-3 mb-0 small">
	Internapps &middot; <?= date('Y') ?>
</p>
<?= $this->endSection() ?>
