<?php
/**
 * Dashboard khusus role peserta.
 *
 * @var array|null $peserta Data magang miliknya, null kalau akun belum ditautkan
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('banner') ?>
<div class="panel-header bg-primary-gradient">
	<div class="page-inner py-5">
		<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
			<div>
				<h2 class="text-white pb-2 fw-bold">Halo, <?= esc(session('nama_pengguna')) ?></h2>
				<h5 class="text-white op-7 mb-2">Berikut ringkasan data magang Anda</h5>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if ($peserta === null): ?>

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card card-round">
				<div class="card-body text-center py-5">
					<div class="mb-4">
						<i class="fas fa-folder-open text-secondary" style="font-size: 3.5rem;"></i>
					</div>
					<h4 class="fw-bold mb-2">Data Magang Belum Tersedia</h4>
					<p class="text-muted mb-0">
						Akun Anda belum ditautkan dengan data peserta magang.<br>
						Silakan hubungi admin atau staff untuk melengkapinya.
					</p>
				</div>
			</div>
		</div>
	</div>

<?php else: ?>

	<?php
	$status = status_magang($peserta['tanggal_mulai_magang'], $peserta['tanggal_berakhir_magang']);
	$sisa   = sisa_hari_magang($peserta['tanggal_berakhir_magang']);
	?>

	<div class="row">
		<div class="col-md-4">
			<div class="card card-round">
				<div class="card-body text-center py-4">
					<div class="avatar avatar-xl mx-auto mb-3">
						<img src="<?= esc(foto_peserta($peserta['link_foto_peserta'])) ?>"
							alt="Foto <?= esc($peserta['nama_peserta']) ?>"
							class="avatar-img rounded-circle">
					</div>
					<h4 class="fw-bold mb-1"><?= esc($peserta['nama_peserta']) ?></h4>
					<p class="text-muted mb-3"><?= esc($peserta['nama_jurusan']) ?></p>
					<?= badge_status_magang($peserta['tanggal_mulai_magang'], $peserta['tanggal_berakhir_magang']) ?>
				</div>
			</div>

			<div class="card card-stats card-round">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col-icon">
							<div class="icon-big text-center <?= $status === 'Sedang Magang' ? 'icon-success' : 'icon-secondary' ?>">
								<i class="fas fa-hourglass-half"></i>
							</div>
						</div>
						<div class="col col-stats">
							<div class="numbers">
								<?php if ($status === 'Sedang Magang'): ?>
									<p class="card-category">Sisa Waktu Magang</p>
									<h4 class="card-title"><?= (int) $sisa ?> hari</h4>
								<?php elseif ($status === 'Belum Mulai'): ?>
									<p class="card-category">Magang Dimulai Dalam</p>
									<h4 class="card-title"><?= (int) sisa_hari_magang($peserta['tanggal_mulai_magang']) ?> hari</h4>
								<?php else: ?>
									<p class="card-category">Status Magang</p>
									<h4 class="card-title">Selesai</h4>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="card card-round">
				<div class="card-header">
					<h4 class="card-title">Informasi Magang</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped mb-0">
							<tbody>
								<tr>
									<th style="width: 40%;">NIK</th>
									<td><?= esc($peserta['nik']) ?></td>
								</tr>
								<tr>
									<th>Nama Peserta</th>
									<td><?= esc($peserta['nama_peserta']) ?></td>
								</tr>
								<tr>
									<th>Perguruan Tinggi</th>
									<td><?= esc($peserta['nama_universitas']) ?></td>
								</tr>
								<tr>
									<th>Fakultas / Jurusan</th>
									<td><?= esc($peserta['nama_fakultas']) ?></td>
								</tr>
								<tr>
									<th>Program Studi</th>
									<td><?= esc($peserta['nama_jurusan']) ?></td>
								</tr>
								<tr>
									<th>Tanggal Mulai Magang</th>
									<td><?= esc(tanggal_indonesia($peserta['tanggal_mulai_magang'])) ?></td>
								</tr>
								<tr>
									<th>Tanggal Berakhir Magang</th>
									<td><?= esc(tanggal_indonesia($peserta['tanggal_berakhir_magang'])) ?></td>
								</tr>
								<tr>
									<th>Status</th>
									<td><?= badge_status_magang($peserta['tanggal_mulai_magang'], $peserta['tanggal_berakhir_magang']) ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<p class="text-muted small mt-3 mb-0">
						<i class="fas fa-info-circle mr-1"></i>
						Data magang di atas dikelola oleh admin dan staff. Hubungi mereka
						jika ada yang perlu diperbaiki.
					</p>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>

<?= $this->endSection() ?>
