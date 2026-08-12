<?php
/**
 * Isian form pengguna, dipakai bersama oleh halaman tambah dan ubah.
 *
 * Variabel dikirim dari controller, bukan lewat parameter $this->include().
 *
 * @var string     $aksi   URL tujuan form
 * @var array|null $user   Data pengguna saat mengubah, null saat menambah
 * @var array      $roles  Daftar role untuk dropdown
 * @var string     $tombol Teks tombol simpan
 */
$galat = session('errors') ?? [];
$ubah  = $user !== null;
?>
<?= form_open($aksi) ?>

	<div class="form-group">
		<label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
		<input type="text"
			class="form-control<?= isset($galat['nama_pengguna']) ? ' is-invalid' : '' ?>"
			id="nama_pengguna"
			name="nama_pengguna"
			value="<?= esc(old('nama_pengguna', $ubah ? $user['nama_pengguna'] : '')) ?>"
			placeholder="Contoh: Budi Santoso">
		<?php if (isset($galat['nama_pengguna'])): ?>
			<div class="invalid-feedback"><?= esc($galat['nama_pengguna']) ?></div>
		<?php endif; ?>
	</div>

	<div class="form-group">
		<label for="username">Username <span class="text-danger">*</span></label>
		<input type="text"
			class="form-control<?= isset($galat['username']) ? ' is-invalid' : '' ?>"
			id="username"
			name="username"
			value="<?= esc(old('username', $ubah ? $user['username'] : '')) ?>"
			placeholder="Contoh: budi"
			autocomplete="off">
		<small class="form-text text-muted">
			Hanya huruf, angka, garis bawah, dan strip. Minimal 3 karakter.
		</small>
		<?php if (isset($galat['username'])): ?>
			<div class="invalid-feedback"><?= esc($galat['username']) ?></div>
		<?php endif; ?>
	</div>

	<div class="form-group">
		<label for="password">
			Password
			<?php if ($ubah): ?>
				<small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
			<?php else: ?>
				<span class="text-danger">*</span>
			<?php endif; ?>
		</label>
		<input type="password"
			class="form-control<?= isset($galat['password']) ? ' is-invalid' : '' ?>"
			id="password"
			name="password"
			placeholder="<?= $ubah ? 'Biarkan kosong untuk mempertahankan password lama' : 'Minimal 6 karakter' ?>"
			autocomplete="new-password">
		<?php if (isset($galat['password'])): ?>
			<div class="invalid-feedback"><?= esc($galat['password']) ?></div>
		<?php endif; ?>
	</div>

	<div class="form-group">
		<label for="role_id">Role <span class="text-danger">*</span></label>
		<?php $roleTerpilih = old('role_id', $ubah ? $user['role_id'] : ''); ?>
		<select class="form-control<?= isset($galat['role_id']) ? ' is-invalid' : '' ?>"
			id="role_id"
			name="role_id">
			<option value="">-- Pilih Role --</option>
			<?php foreach ($roles as $r): ?>
				<option value="<?= (int) $r['id'] ?>" <?= (string) $roleTerpilih === (string) $r['id'] ? 'selected' : '' ?>>
					<?= esc(ucfirst($r['nama_role'])) ?><?= $r['deskripsi'] ? ' — ' . esc($r['deskripsi']) : '' ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if (isset($galat['role_id'])): ?>
			<div class="invalid-feedback"><?= esc($galat['role_id']) ?></div>
		<?php endif; ?>
	</div>

	<div class="text-right mt-4">
		<a href="<?= base_url('users') ?>" class="btn btn-danger btn-border btn-round">
			Batal
		</a>
		<button type="submit" class="btn btn-primary btn-round">
			<i class="fas fa-save mr-1"></i> <?= esc($tombol) ?>
		</button>
	</div>

<?= form_close() ?>
