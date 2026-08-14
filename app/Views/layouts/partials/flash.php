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
