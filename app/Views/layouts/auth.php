<!DOCTYPE html>
<html lang="id">
<?= $this->include('layouts/partials/head') ?>
<body>
	<style>
		.halaman-auth { min-height: 100vh; }
	</style>

	<div class="halaman-auth bg-primary-gradient d-flex align-items-center py-5">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-sm-10 col-md-8 col-lg-5">
					<?= $this->renderSection('content') ?>
				</div>
			</div>
		</div>
	</div>

	<script src="<?= base_url('assets/js/core/jquery.3.2.1.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>
	<?= $this->renderSection('scripts') ?>
</body>
</html>
