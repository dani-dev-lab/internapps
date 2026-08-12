<?php
/**
 * Halaman 404 Internapps.
 *
 * Menggantikan halaman bawaan CodeIgniter supaya tetap satu gaya dengan
 * seluruh aplikasi. Dirender di luar controller, jadi helper url dimuat
 * di sini secara eksplisit — tidak bisa mengandalkan BaseController.
 *
 * @var string $message Keterangan teknis dari CodeIgniter
 */
helper('url');

$pesanTeknis = ENVIRONMENT === 'development' && ! empty($message) ? $message : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Halaman Tidak Ditemukan - Internapps</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="<?= base_url('assets/img/internapps/logo-mark.svg') ?>" type="image/svg+xml"/>
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/internapps.css') ?>">
	<style>
		.halaman-galat { min-height: 100vh; }
		.angka-galat { font-size: 6rem; line-height: 1; font-weight: 700; }
	</style>
</head>
<body>
	<div class="halaman-galat bg-primary-gradient d-flex align-items-center py-5">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-sm-10 col-md-8 col-lg-6">

					<div class="text-center mb-4">
						<img src="<?= base_url('assets/img/internapps/logo-mark-white.svg') ?>" alt="" width="56">
					</div>

					<div class="card card-round">
						<div class="card-body text-center py-5">
							<p class="angka-galat text-primary mb-2">404</p>

							<h4 class="fw-bold mb-2">Halaman Tidak Ditemukan</h4>

							<p class="text-muted mb-4">
								Alamat yang Anda buka tidak ada di aplikasi ini.
								Mungkin salah ketik, atau halamannya sudah dipindahkan.
							</p>

							<a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-round">
								<i class="fas fa-home mr-1"></i> Kembali ke Dashboard
							</a>

							<?php if ($pesanTeknis !== null): ?>
								<p class="text-muted small mt-4 mb-0">
									<strong>Keterangan teknis:</strong><br>
									<?= esc($pesanTeknis) ?>
								</p>
								<p class="text-muted small mb-0">
									Keterangan ini hanya muncul selama <code>CI_ENVIRONMENT = development</code>.
								</p>
							<?php endif; ?>
						</div>
					</div>

					<p class="text-center text-white op-6 mt-3 mb-0 small">
						Internapps &middot; <?= date('Y') ?>
					</p>

				</div>
			</div>
		</div>
	</div>
</body>
</html>
