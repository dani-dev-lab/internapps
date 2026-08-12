<?php
/**
 * Halaman profil akun sendiri.
 *
 * @var array $user Data akun yang sedang masuk, beserta nama role-nya
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $galat = session('errors') ?? []; ?>

<div class="row">
	<div class="col-md-4">
		<div class="card card-round">
			<div class="card-body text-center py-4">
				<div class="mb-3">
					<img src="<?= esc(foto_user($user['foto'])) ?>"
						alt="Foto profil"
						class="img-fluid foto-penuh">
				</div>

				<h4 class="fw-bold mb-1"><?= esc($user['nama_pengguna']) ?></h4>
				<p class="text-muted mb-2">@<?= esc($user['username']) ?></p>
				<span class="badge badge-secondary"><?= esc(ucfirst($user['nama_role'])) ?></span>

				<hr>

				<?= form_open_multipart(base_url('profil/foto')) ?>
					<div class="form-group text-left">
						<label for="foto">Ganti Foto Profil</label>
						<input type="file"
							class="form-control-file"
							id="foto" name="foto"
							accept="image/jpeg,image/png,image/webp">
						<small class="form-text text-muted">
							JPG, PNG, atau WEBP. Maksimal 2 MB.
						</small>
						<?php if (isset($galat['foto'])): ?>
							<div class="invalid-feedback d-block"><?= esc($galat['foto']) ?></div>
						<?php endif; ?>
					</div>
					<button type="submit" class="btn btn-primary btn-round btn-block">
						<i class="fas fa-upload mr-1"></i> Unggah Foto
					</button>
				<?= form_close() ?>

				<?php if (! empty($user['foto'])): ?>
					<?= form_open(base_url('profil/foto/hapus'), ['class' => 'mt-2 form-hapus-foto']) ?>
						<button type="submit" class="btn btn-danger btn-border btn-round btn-block">
							<i class="fas fa-trash mr-1"></i> Hapus Foto
						</button>
					<?= form_close() ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="card card-round">
			<div class="card-header">
				<h4 class="card-title">Data Akun</h4>
			</div>
			<div class="card-body">
				<?= form_open(base_url('profil/data')) ?>
					<div class="form-group">
						<label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
						<input type="text"
							class="form-control<?= isset($galat['nama_pengguna']) ? ' is-invalid' : '' ?>"
							id="nama_pengguna" name="nama_pengguna"
							value="<?= esc(old('nama_pengguna', $user['nama_pengguna'])) ?>">
						<?php if (isset($galat['nama_pengguna'])): ?>
							<div class="invalid-feedback"><?= esc($galat['nama_pengguna']) ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label for="username">Username <span class="text-danger">*</span></label>
						<input type="text"
							class="form-control<?= isset($galat['username']) ? ' is-invalid' : '' ?>"
							id="username" name="username"
							value="<?= esc(old('username', $user['username'])) ?>"
							autocomplete="off">
						<?php if (isset($galat['username'])): ?>
							<div class="invalid-feedback"><?= esc($galat['username']) ?></div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label>Role</label>
						<input type="text" class="form-control" value="<?= esc(ucfirst($user['nama_role'])) ?>" disabled>
						<small class="form-text text-muted">
							Role hanya dapat diubah oleh admin melalui halaman Data Pengguna.
						</small>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary btn-round">
							<i class="fas fa-save mr-1"></i> Simpan Perubahan
						</button>
					</div>
				<?= form_close() ?>
			</div>
		</div>

		<div class="card card-round">
			<div class="card-header">
				<h4 class="card-title">Ganti Password</h4>
			</div>
			<div class="card-body">
				<?= form_open(base_url('profil/password')) ?>
					<div class="form-group">
						<label for="password_lama">Password Lama <span class="text-danger">*</span></label>
						<input type="password"
							class="form-control<?= isset($galat['password_lama']) ? ' is-invalid' : '' ?>"
							id="password_lama" name="password_lama"
							autocomplete="current-password">
						<?php if (isset($galat['password_lama'])): ?>
							<div class="invalid-feedback"><?= esc($galat['password_lama']) ?></div>
						<?php endif; ?>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_baru">Password Baru <span class="text-danger">*</span></label>
								<input type="password"
									class="form-control<?= isset($galat['password_baru']) ? ' is-invalid' : '' ?>"
									id="password_baru" name="password_baru"
									autocomplete="new-password">
								<small class="form-text text-muted">Minimal 6 karakter.</small>
								<?php if (isset($galat['password_baru'])): ?>
									<div class="invalid-feedback"><?= esc($galat['password_baru']) ?></div>
								<?php endif; ?>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="password_konfirmasi">Ulangi Password Baru <span class="text-danger">*</span></label>
								<input type="password"
									class="form-control<?= isset($galat['password_konfirmasi']) ? ' is-invalid' : '' ?>"
									id="password_konfirmasi" name="password_konfirmasi"
									autocomplete="new-password">
								<?php if (isset($galat['password_konfirmasi'])): ?>
									<div class="invalid-feedback"><?= esc($galat['password_konfirmasi']) ?></div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-primary btn-round">
							<i class="fas fa-key mr-1"></i> Ganti Password
						</button>
					</div>
				<?= form_close() ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function () {
		$('.form-hapus-foto').on('submit', function (e) {
			e.preventDefault();

			var form = this;

			swal({
				title: 'Hapus foto profil?',
				text: 'Foto akan diganti dengan gambar bawaan.',
				icon: 'warning',
				buttons: ['Batal', 'Ya, hapus'],
				dangerMode: true
			}).then(function (setuju) {
				if (setuju) {
					form.submit();
				}
			});
		});
	});
</script>
<?= $this->endSection() ?>
