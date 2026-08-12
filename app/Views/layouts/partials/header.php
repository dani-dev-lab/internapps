<?php
/**
 * Navbar atas Internapps.
 *
 * Bawaan Atlantis yang dibuang: form pencarian, dropdown pesan, dropdown
 * notifikasi, dan panel quick actions — semuanya data palsu dan tidak ada
 * fiturnya di aplikasi ini.
 *
 * Dropdown avatar juga dibuang. Profil dan Logout sudah tersedia di blok
 * profil pada sidebar, jadi menaruhnya di sini hanya menduplikasi jalan
 * menuju halaman yang sama.
 *
 * Elemen <nav class="navbar-header"> ikut dibuang seluruhnya karena isinya
 * sudah kosong. Yang tersisa di header hanya blok logo. Konsekuensinya
 * ditangani di internapps.css: .main-header dibuat selebar logo dan tanpa
 * latar, sebab bawaannya membentang selebar layar dengan latar putih —
 * kalau dibiarkan, yang muncul justru strip putih, bukan hilang.
 */
?>
<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">

				<a href="<?= base_url('dashboard') ?>" class="logo">
					<img src="<?= base_url('assets/img/internapps/logo-mark-white.svg') ?>" alt="Internapps" height="30" class="mr-2">
					<img src="<?= base_url('assets/img/internapps/logo-wordmark-white.svg') ?>" alt="Internapps" height="17" class="logo-wordmark">
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Buka menu">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar" type="button" aria-label="Kecilkan sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->
		</div>
