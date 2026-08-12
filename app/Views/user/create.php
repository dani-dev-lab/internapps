<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
	<div class="col-md-8">
		<div class="card card-round">
			<div class="card-header">
				<h4 class="card-title">Tambah Pengguna Baru</h4>
			</div>
			<div class="card-body">
				<?= $this->include('user/_form') ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
