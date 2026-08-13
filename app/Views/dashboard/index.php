<?php
/**
 * Dashboard superadmin, admin, dan staff.
 *
 * @var array      $statistik Jumlah peserta per status
 * @var array      $sedang    Peserta yang magangnya sedang berjalan
 * @var array|null $pengguna  Ringkasan akun, hanya diisi untuk superadmin dan admin
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('banner') ?>
<div class="panel-header bg-primary-gradient">
	<div class="page-inner py-5">
		<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
			<div>
				<h2 class="text-white pb-2 fw-bold">Halo, <?= esc(session('nama_pengguna')) ?></h2>
				<h5 class="text-white op-7 mb-2">Ringkasan data peserta magang Internapps</h5>
			</div>
			<div class="ml-md-auto py-2 py-md-0">
				<!-- <a href="<?= base_url('peserta') ?>" class="btn btn-white btn-border btn-round mr-2">
					Lihat Semua Peserta
				</a> -->
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
	<div class="col-sm-6 col-md-3">
		<div class="card card-stats card-round">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-icon">
						<div class="icon-big text-center icon-secondary">
							<i class="fas fa-users"></i>
						</div>
					</div>
					<div class="col col-stats">
						<div class="numbers">
							<p class="card-category">Total Peserta</p>
							<h4 class="card-title"><?= (int) $statistik['total'] ?></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6 col-md-3">
		<div class="card card-stats card-round">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-icon">
						<div class="icon-big text-center icon-success">
							<i class="fas fa-user-check"></i>
						</div>
					</div>
					<div class="col col-stats">
						<div class="numbers">
							<p class="card-category">Sedang Magang</p>
							<h4 class="card-title"><?= (int) $statistik['sedang_magang'] ?></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6 col-md-3">
		<div class="card card-stats card-round">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-icon">
						<div class="icon-big text-center icon-info">
							<i class="fas fa-user-graduate"></i>
						</div>
					</div>
					<div class="col col-stats">
						<div class="numbers">
							<p class="card-category">Selesai</p>
							<h4 class="card-title"><?= (int) $statistik['selesai'] ?></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6 col-md-3">
		<div class="card card-stats card-round">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-icon">
						<div class="icon-big text-center icon-warning">
							<i class="fas fa-user-clock"></i>
						</div>
					</div>
					<div class="col col-stats">
						<div class="numbers">
							<p class="card-category">Belum Mulai</p>
							<h4 class="card-title"><?= (int) $statistik['belum_mulai'] ?></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="<?= $pengguna === null ? 'col-md-12' : 'col-md-8' ?>">
		<div class="card card-round">
			<div class="card-header">
				<div class="card-head-row">
					<h4 class="card-title">Peserta Yang Sedang Magang</h4>
					<div class="card-tools">
						<a href="<?= base_url('peserta') ?>" class="btn btn-primary btn-border btn-round btn-sm">
							Selengkapnya
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<?php if (empty($sedang)): ?>
					<p class="text-muted text-center my-4 mb-0">
						Tidak ada peserta yang sedang magang saat ini.
					</p>
				<?php else: ?>
					<div class="table-responsive">
						<?php // Lebar kolom ditulis persen dan dipakai apa adanya berkat
								// table-layout: fixed. Tanpa itu browser menghitung sendiri,
								// memberi ruang berlebih ke kolom Foto dan Berakhir sambil
								// menyempitkan kolom Nama sampai universitasnya pecah 3 baris. ?>
						<table class="table table-striped mb-0" style="table-layout: fixed; width: 100%;">
							<thead>
								<tr>
									<th style="width: 7%;" class="text-nowrap">Foto</th>
									<th style="width: 36%;">Nama Peserta</th>
									<th style="width: 28%;">Program Studi</th>
									<th style="width: 15%;" class="text-nowrap">Berakhir</th>
									<th style="width: 14%;" class="text-nowrap text-center">Sisa</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($sedang as $p): ?>
									<?php $sisa = sisa_hari_magang($p['tanggal_berakhir_magang']); ?>
									<tr>
										<td>
											<div class="avatar-sm">
												<img src="<?= esc(foto_peserta($p['link_foto_peserta'])) ?>"
													alt="Foto <?= esc($p['nama_peserta']) ?>"
													class="avatar-img rounded-circle">
											</div>
										</td>
										<td class="align-middle">
											<strong><?= esc($p['nama_peserta']) ?></strong>
											<br>
											<?php // Nama universitas dipotong dengan elipsis supaya tiap baris
													// tabel ringkas ini tinggi seragam. Nama utuhnya muncul saat
													// disorot, dan tetap lengkap di halaman Data Peserta Magang. ?>
											<span class="text-muted small d-block text-truncate"
												title="<?= esc($p['nama_universitas'], 'attr') ?>"><?= esc($p['nama_universitas']) ?></span>
										</td>
										<td class="align-middle text-truncate"
											title="<?= esc($p['nama_jurusan'], 'attr') ?>"><?= esc($p['nama_jurusan']) ?></td>
										<td class="text-nowrap align-middle"><?= esc(tanggal_singkat($p['tanggal_berakhir_magang'])) ?></td>
										<td class="text-nowrap text-center align-middle">
											<span class="badge badge-<?= $sisa !== null && $sisa <= 7 ? 'warning' : 'success' ?>">
												<?= (int) $sisa ?> hari
											</span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ($pengguna !== null): ?>
		<div class="col-md-4">
			<div class="card card-round">
				<div class="card-header">
					<h4 class="card-title">Pengguna Aplikasi</h4>
				</div>
				<div class="card-body">
					<div class="text-center mb-4">
						<h1 class="fw-bold mb-0"><?= (int) $pengguna['total'] ?></h1>
						<p class="text-muted mb-0">total akun terdaftar</p>
					</div>

					<div class="d-flex justify-content-between align-items-center py-2 border-top">
						<span><i class="fas fa-shield-alt text-primary mr-2"></i> Superadmin</span>
						<span class="badge badge-primary"><?= (int) $pengguna['superadmin'] ?></span>
					</div>
					<div class="d-flex justify-content-between align-items-center py-2 border-top">
						<span><i class="fas fa-user-shield text-secondary mr-2"></i> Admin</span>
						<span class="badge badge-secondary"><?= (int) $pengguna['admin'] ?></span>
					</div>
					<div class="d-flex justify-content-between align-items-center py-2 border-top">
						<span><i class="fas fa-user-tie text-info mr-2"></i> Staff</span>
						<span class="badge badge-info"><?= (int) $pengguna['staff'] ?></span>
					</div>
					<div class="d-flex justify-content-between align-items-center py-2 border-top">
						<span><i class="fas fa-user-graduate text-success mr-2"></i> Peserta</span>
						<span class="badge badge-success"><?= (int) $pengguna['peserta'] ?></span>
					</div>

					<a href="<?= base_url('users') ?>" class="btn btn-primary btn-round btn-block mt-4">
						Kelola Pengguna
					</a>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<?= $this->endSection() ?>
