<?php
/**
 * Halaman ubah peserta magang.
 *
 * @var array  $p      Data peserta yang sedang diubah
 * @var array  $akun   Akun peserta yang bisa ditautkan (dipakai peserta/_form)
 * @var string $aksi   URL tujuan form (dipakai peserta/_form)
 * @var string $tombol Teks tombol simpan (dipakai peserta/_form)
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
	<div class="col-md-10">
		<div class="card card-round">
			<div class="card-header">
				<div class="card-head-row">
					<h4 class="card-title">Ubah Data Peserta Magang</h4>
					<div class="card-tools">
						<span class="badge <?= kelas_badge_status(status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang'])) ?>">
							<?= esc(status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang'])) ?>
						</span>
					</div>
				</div>
			</div>
			<div class="card-body">
				<?= $this->include('peserta/_form') ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
