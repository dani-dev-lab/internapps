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