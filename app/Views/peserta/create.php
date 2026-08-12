<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
	<div class="col-md-10">
		<div class="card card-round">
			<div class="card-header">
				<h4 class="card-title">Tambah Peserta Magang</h4>
			</div>
			<div class="card-body">
				<?= $this->include('peserta/_form') ?>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
