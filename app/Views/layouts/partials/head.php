<?php
/**
 * Bagian <head>: judul halaman, favicon, font, dan CSS Atlantis.
 *
 * @var string $page_title Judul halaman, opsional (ada nilai cadangan)
 */
?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?= isset($page_title) ? esc($page_title) . ' - Internapps' : 'Internapps' ?></title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="<?= base_url('assets/img/internapps/logo-mark.svg') ?>" type="image/svg+xml"/>

	<!-- Fonts and icons -->
	<script src="<?= base_url('assets/js/plugin/webfont/webfont.min.js') ?>"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['<?= base_url('assets/css/fonts.min.css') ?>']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/atlantis.min.css') ?>">

	<!-- Penyesuaian warna komponen yang tidak punya varian indigo di Atlantis -->
	<link rel="stylesheet" href="<?= base_url('assets/css/internapps.css') ?>">
</head>
