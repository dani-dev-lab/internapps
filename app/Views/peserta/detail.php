<?php
/**
 * Detail peserta magang. Dipakai halaman detail (admin/staff) dan
 * halaman "Data Magang Saya" (peserta).
 *
 * @var array|null $p        Data peserta, null kalau belum ada
 * @var bool       $bisaUbah Tampilkan tombol ubah atau tidak
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if ($p === null): ?>

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

	<?php $status = status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang']); ?>

	<div class="row">
		<div class="col-md-4">
			<div class="card card-round">
				<div class="card-body text-center py-4">
					<div class="avatar avatar-xl mx-auto mb-3">
						<img src="<?= esc(foto_peserta($p['link_foto_peserta'])) ?>"
							alt="Foto <?= esc($p['nama_peserta']) ?>"
							class="avatar-img rounded-circle">
					</div>
					<h4 class="fw-bold mb-1"><?= esc($p['nama_peserta']) ?></h4>
					<p class="text-muted mb-3"><?= esc($p['nama_jurusan']) ?></p>
					<?= badge_status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang']) ?>

					<?php if ($status === 'Sedang Magang'): ?>
						<p class="text-muted small mt-3 mb-0">
							Sisa waktu magang: <strong><?= (int) sisa_hari_magang($p['tanggal_berakhir_magang']) ?> hari</strong>
						</p>
					<?php elseif ($status === 'Belum Mulai'): ?>
						<p class="text-muted small mt-3 mb-0">
							Magang dimulai dalam <strong><?= (int) sisa_hari_magang($p['tanggal_mulai_magang']) ?> hari</strong>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ($bisaUbah): ?>
				<a href="<?= base_url('peserta/edit/' . $p['id']) ?>" class="btn btn-primary btn-round btn-block">
					<i class="fas fa-edit mr-1"></i> Ubah Data
				</a>
				<a href="<?= base_url('peserta') ?>" class="btn btn-danger btn-border btn-round btn-block">
					<i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
				</a>
			<?php endif; ?>
		</div>

		<div class="col-md-8">
			<div class="card card-round">
				<div class="card-header">
					<h4 class="card-title">Informasi Lengkap</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped mb-0">
							<tbody>
								<tr>
									<th style="width: 38%;">NIK</th>
									<td><?= esc($p['nik']) ?></td>
								</tr>
								<tr>
									<th>Nama Peserta</th>
									<td><?= esc($p['nama_peserta']) ?></td>
								</tr>
								<tr>
									<th>Perguruan Tinggi</th>
									<td><?= esc($p['nama_universitas']) ?></td>
								</tr>
								<tr>
									<th>Fakultas / Jurusan</th>
									<td><?= esc($p['nama_fakultas']) ?></td>
								</tr>
								<tr>
									<th>Program Studi</th>
									<td><?= esc($p['nama_jurusan']) ?></td>
								</tr>
								<tr>
									<th>Tanggal Mulai Magang</th>
									<td><?= esc(tanggal_indonesia($p['tanggal_mulai_magang'])) ?></td>
								</tr>
								<tr>
									<th>Tanggal Berakhir Magang</th>
									<td><?= esc(tanggal_indonesia($p['tanggal_berakhir_magang'])) ?></td>
								</tr>
								<tr>
									<th>Status Magang</th>
									<td><?= badge_status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang']) ?></td>
								</tr>
								<tr>
									<th>Akun Login</th>
									<td>
										<?php if (! empty($p['username'])): ?>
											<?= esc($p['nama_pengguna']) ?>
											<span class="text-muted">(<?= esc($p['username']) ?>)</span>
										<?php else: ?>
											<span class="text-muted">Belum ada akun login</span>
										<?php endif; ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<?php if (! $bisaUbah): ?>
						<p class="text-muted small mt-3 mb-0">
							<i class="fas fa-info-circle mr-1"></i>
							Data magang di atas dikelola oleh admin dan staff. Hubungi mereka
							jika ada yang perlu diperbaiki.
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>

<?= $this->endSection() ?>
