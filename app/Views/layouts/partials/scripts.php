<?php
/**
 * Script Internapps.
 *
 * atlantis.min.js memanggil tiga plugin dan HARUS ada semuanya:
 *   - jquery.scrollbar   -> .scrollbar()   (9 pemanggilan)
 *   - jQuery UI          -> .draggable()   (baris 98 atlantis.js)
 *   - Bootstrap          -> .tooltip(), .popover()
 *
 * jQuery UI sempat dibuang karena dikira tidak terpakai. Akibatnya
 * .draggable() melempar error dan MENGGAGALKAN seluruh blok
 * $(document).ready() di atlantis.js, sehingga tombol pengecil sidebar
 * dan tombol menu di layar kecil sama sekali tidak berfungsi.
 *
 * Yang tetap dibuang karena benar-benar tidak dipanggil: Chart.js,
 * Sparkline, Chart Circle, dan jQuery Vector Maps (~4 MB).
 */
?>
<!--   Core JS Files   -->
	<script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>

	<!-- jQuery UI: dibutuhkan atlantis.min.js untuk .draggable() -->
	<script src="<?= base_url('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') ?>"></script>

	<!-- jQuery Scrollbar (dibutuhkan atlantis.min.js) -->
	<script src="<?= base_url('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>

	<!-- Datatables -->
	<script src="<?= base_url('assets/js/plugin/datatables/datatables.min.js') ?>"></script>

	<!-- Bootstrap Notify -->
	<script src="<?= base_url('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') ?>"></script>

	<!-- Sweet Alert -->
	<script src="<?= base_url('assets/js/plugin/sweetalert/sweetalert.min.js') ?>"></script>

	<!-- Atlantis JS -->
	<script src="<?= base_url('assets/js/atlantis.min.js') ?>"></script>

	<script>
		// Pesan hasil aksi menutup dirinya sendiri, jadi tidak perlu tombol x.
		// Memakai metode alert() bawaan Bootstrap supaya animasi memudarnya
		// sama persis dengan komponen alert lain di template.
		$(function () {
			setTimeout(function () {
				$('.alert-sementara').alert('close');
			}, 5000);
		});
	</script>
