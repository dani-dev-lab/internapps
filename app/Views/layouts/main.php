<?php
/**
 * Layout utama Atlantis Lite.
 * File ini sengaja dibuat pendek — isi aslinya dipecah ke dalam
 * app/Views/layouts/partials/, supaya tiap bagian (head, header,
 * sidebar, content, footer, scripts) gampang dicari dan diedit
 * tanpa harus scroll satu file panjang.
 */
?>
<!DOCTYPE html>
<html lang="en">
<?= $this->include('layouts/partials/head') ?>
<body>
	<div class="wrapper">
		<?= $this->include('layouts/partials/header') ?>

		<?= $this->include('layouts/partials/sidebar') ?>

		<?= $this->include('layouts/partials/content') ?>

		<?= $this->include('layouts/partials/footer') ?>
		</div>
	</div>

	<?= $this->include('layouts/partials/scripts') ?>
	<?= $this->renderSection('scripts') ?>
</body>
</html>