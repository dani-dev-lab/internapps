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
		<h4 class="text-center mb-1 fw-bold">Daftar Akun</h4>
		<p class="text-muted text-center mb-4">Khusus peserta magang</p>

		<div class="alert alert-info" role="alert">
			<i class="fas fa-info-circle mr-1"></i>
			Akun hanya dapat dibuat oleh peserta yang datanya <strong>sudah didaftarkan</strong>
			oleh admin atau staff. Isi NIK dan nama persis seperti yang terdaftar.
		</div>

		<?php if (session('error')): ?>
			<div class="alert alert-danger" role="alert">
				<i class="fas fa-exclamation-circle mr-1"></i>
				<?= esc(session('error')) ?>
			</div>
		<?php endif; ?>

		<?= form_open(base_url('register'), ['autocomplete' => 'off']) ?>

			<div class="form-group">
				<label for="nik">NIK <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= isset($galat['nik']) ? ' is-invalid' : '' ?>"
					id="nik" name="nik"
					value="<?= esc(old('nik')) ?>"
					maxlength="16" inputmode="numeric"
					placeholder="16 digit angka"
					autocomplete="off"
					autofocus>
				<?php if (isset($galat['nik'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nik']) ?></div>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<label for="nama_peserta">Nama Lengkap <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= isset($galat['nama_peserta']) ? ' is-invalid' : '' ?>"
					id="nama_peserta" name="nama_peserta"
					value="<?= esc(old('nama_peserta')) ?>"
					placeholder="Sesuai data yang didaftarkan"
					autocomplete="off">
				<?php if (isset($galat['nama_peserta'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nama_peserta']) ?></div>
				<?php endif; ?>
			</div>

			<hr>

			<div class="form-group">
				<label for="username">Username <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= isset($galat['username']) ? ' is-invalid' : '' ?>"
					id="username" name="username"
					value="<?= esc(old('username')) ?>"
					placeholder="Dipakai untuk masuk"
					autocomplete="off">
				<small class="form-text text-muted">
					Huruf, angka, garis bawah, dan strip. Minimal 3 karakter.
				</small>
				<?php if (isset($galat['username'])): ?>
					<div class="invalid-feedback"><?= esc($galat['username']) ?></div>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<label for="password">Password <span class="text-danger">*</span></label>
				<input type="password"
					class="form-control<?= isset($galat['password']) ? ' is-invalid' : '' ?>"
					id="password" name="password"
					placeholder="Minimal 6 karakter"
					autocomplete="new-password">
				<?php if (isset($galat['password'])): ?>
					<div class="invalid-feedback"><?= esc($galat['password']) ?></div>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<label for="password_konfirmasi">Ulangi Password <span class="text-danger">*</span></label>
				<input type="password"
					class="form-control<?= isset($galat['password_konfirmasi']) ? ' is-invalid' : '' ?>"
					id="password_konfirmasi" name="password_konfirmasi"
					placeholder="Ketik ulang password"
					autocomplete="new-password">
				<?php if (isset($galat['password_konfirmasi'])): ?>
					<div class="invalid-feedback"><?= esc($galat['password_konfirmasi']) ?></div>
				<?php endif; ?>
			</div>

			<button type="submit" class="btn btn-primary btn-block btn-round mt-4">
				<i class="fas fa-user-plus mr-1"></i> Daftar
			</button>
		<?= form_close() ?>

		<p class="text-center text-muted mt-4 mb-0">
			Sudah punya akun?
			<a href="<?= base_url('login') ?>">Masuk di sini</a>
		</p>
	</div>
</div>

<p class="text-center text-white op-6 mt-3 mb-0 small">
	Internapps &middot; <?= date('Y') ?>
</p>
<?= $this->endSection() ?>
