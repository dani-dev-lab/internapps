<?php
/**
 * Pesan hasil aksi (berhasil / gagal), memakai komponen alert Atlantis Lite.
 * Ditampilkan di semua halaman lewat layouts/partials/content.php, sehingga
 * berlaku untuk seluruh role tanpa perlu diatur per halaman.
 *
 * Pesan menghilang sendiri setelah beberapa detik — lihat pengaturannya di
 * layouts/partials/scripts.php. Karena itu tombol tutup (x) tidak dipasang,
 * dan kelas "alert-dismissible" tidak dipakai lagi sebab tugasnya hanya
 * menyediakan ruang kosong di kanan untuk tombol tersebut.
 */
?>
<?php if (session('sukses')): ?>
	<div class="alert alert-success alert-sementara fade show" role="alert">
		<i class="fas fa-check-circle mr-1"></i>
		<?= esc(session('sukses')) ?>
	</div>
<?php endif; ?>

<?php if (session('error')): ?>
	<div class="alert alert-danger alert-sementara fade show" role="alert">
		<i class="fas fa-exclamation-circle mr-1"></i>
		<?= esc(session('error')) ?>
	</div>
<?php endif; ?>
