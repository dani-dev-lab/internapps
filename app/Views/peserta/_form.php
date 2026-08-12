<?php
/**
 * Isian form peserta magang, dipakai bersama oleh halaman tambah dan ubah.
 *
 * Variabel dikirim dari controller (bukan lewat parameter $this->include(),
 * karena parameter kedua include() adalah opsi renderer, bukan data view).
 *
 * @var array|null $p      Data peserta saat mengubah, null saat menambah
 * @var string     $aksi   URL tujuan form
 * @var array      $akun   Akun peserta yang belum tertaut, untuk dropdown
 * @var string     $tombol Teks tombol simpan
 */
$galat = session('errors') ?? [];
$ubah  = $p !== null;

$nilai = static function (string $kolom) use ($p, $ubah) {
    return old($kolom, $ubah ? $p[$kolom] : '');
};

$kelas = static fn (string $kolom): string => isset($galat[$kolom]) ? ' is-invalid' : '';
?>
<?= form_open_multipart($aksi) ?>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="nik">NIK <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= $kelas('nik') ?>"
					id="nik" name="nik"
					value="<?= esc($nilai('nik')) ?>"
					maxlength="16" inputmode="numeric"
					placeholder="16 digit angka">
				<small class="form-text text-muted">Tepat 16 digit angka, dan tidak boleh sama dengan peserta lain.</small>
				<?php if (isset($galat['nik'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nik']) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="nama_peserta">Nama Peserta <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= $kelas('nama_peserta') ?>"
					id="nama_peserta" name="nama_peserta"
					value="<?= esc($nilai('nama_peserta')) ?>"
					placeholder="Nama lengkap peserta">
				<?php if (isset($galat['nama_peserta'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nama_peserta']) ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="nama_universitas">Perguruan Tinggi <span class="text-danger">*</span></label>
		<input type="text"
			class="form-control<?= $kelas('nama_universitas') ?>"
			id="nama_universitas" name="nama_universitas"
			value="<?= esc($nilai('nama_universitas')) ?>"
			placeholder="Contoh: Politeknik Negeri Banjarmasin">
		<?php if (isset($galat['nama_universitas'])): ?>
			<div class="invalid-feedback"><?= esc($galat['nama_universitas']) ?></div>
		<?php endif; ?>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="nama_fakultas">Fakultas / Jurusan <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= $kelas('nama_fakultas') ?>"
					id="nama_fakultas" name="nama_fakultas"
					value="<?= esc($nilai('nama_fakultas')) ?>"
					placeholder="Contoh: Teknologi Informasi">
				<?php if (isset($galat['nama_fakultas'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nama_fakultas']) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="nama_jurusan">Program Studi <span class="text-danger">*</span></label>
				<input type="text"
					class="form-control<?= $kelas('nama_jurusan') ?>"
					id="nama_jurusan" name="nama_jurusan"
					value="<?= esc($nilai('nama_jurusan')) ?>"
					placeholder="Contoh: D3 Teknik Informatika">
				<?php if (isset($galat['nama_jurusan'])): ?>
					<div class="invalid-feedback"><?= esc($galat['nama_jurusan']) ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="tanggal_mulai_magang">Tanggal Mulai Magang <span class="text-danger">*</span></label>
				<input type="date"
					class="form-control<?= $kelas('tanggal_mulai_magang') ?>"
					id="tanggal_mulai_magang" name="tanggal_mulai_magang"
					value="<?= esc($nilai('tanggal_mulai_magang')) ?>">
				<?php if (isset($galat['tanggal_mulai_magang'])): ?>
					<div class="invalid-feedback"><?= esc($galat['tanggal_mulai_magang']) ?></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="tanggal_berakhir_magang">Tanggal Berakhir Magang <span class="text-danger">*</span></label>
				<input type="date"
					class="form-control<?= $kelas('tanggal_berakhir_magang') ?>"
					id="tanggal_berakhir_magang" name="tanggal_berakhir_magang"
					value="<?= esc($nilai('tanggal_berakhir_magang')) ?>">
				<small class="form-text text-muted">Tidak boleh lebih awal daripada tanggal mulai.</small>
				<?php if (isset($galat['tanggal_berakhir_magang'])): ?>
					<div class="invalid-feedback"><?= esc($galat['tanggal_berakhir_magang']) ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="user_id">Akun Login Peserta</label>
		<?php $akunTerpilih = old('user_id', $ubah ? $p['user_id'] : ''); ?>
		<select class="form-control" id="user_id" name="user_id">
			<option value="">-- Tanpa akun login --</option>
			<?php foreach ($akun as $a): ?>
				<option value="<?= (int) $a['id'] ?>" <?= (string) $akunTerpilih === (string) $a['id'] ? 'selected' : '' ?>>
					<?= esc($a['nama_pengguna']) ?> (<?= esc($a['username']) ?>)
				</option>
			<?php endforeach; ?>
		</select>
		<small class="form-text text-muted">
			Hanya akun ber-role peserta yang belum tertaut ke data lain yang muncul di sini.
			Boleh dikosongkan.
		</small>
	</div>

	<div class="form-group">
		<label for="<?= esc('link_foto_peserta') ?>">Foto Peserta</label>

		<?php if ($ubah && ! empty($p['link_foto_peserta'])): ?>
			<div class="mb-2">
				<img src="<?= esc(foto_peserta($p['link_foto_peserta'])) ?>"
					alt="Foto saat ini"
					class="rounded"
					style="width: 90px; height: 90px; object-fit: cover;">
				<span class="text-muted small ml-2">Foto saat ini</span>
			</div>
		<?php endif; ?>

		<input type="file"
			class="form-control-file<?= $kelas('link_foto_peserta') ?>"
			id="link_foto_peserta" name="link_foto_peserta"
			accept="image/jpeg,image/png,image/webp">
		<small class="form-text text-muted">
			Format JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.
			<?= $ubah ? 'Biarkan kosong jika tidak ingin mengganti foto.' : 'Boleh dikosongkan.' ?>
		</small>
		<?php if (isset($galat['link_foto_peserta'])): ?>
			<div class="invalid-feedback d-block"><?= esc($galat['link_foto_peserta']) ?></div>
		<?php endif; ?>
	</div>

	<div class="text-right mt-4">
		<a href="<?= base_url('peserta') ?>" class="btn btn-danger btn-border btn-round">Batal</a>
		<button type="submit" class="btn btn-primary btn-round">
			<i class="fas fa-save mr-1"></i> <?= esc($tombol) ?>
		</button>
	</div>

<?= form_close() ?>
