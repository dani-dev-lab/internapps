<?php
/**
 * Halaman Akses Ditolak, dirender oleh App\Filters\RoleFilter.
 *
 * @var string $role      Role pengguna yang sedang masuk
 * @var array  $diizinkan Role yang boleh membuka halaman itu
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
	<div class="col-md-8 col-lg-6">
		<div class="card card-round">
			<div class="card-body text-center py-5">
				<div class="mb-4">
					<i class="fas fa-lock text-danger" style="font-size: 3.5rem;"></i>
				</div>

				<h3 class="fw-bold mb-2">Akses Ditolak</h3>

				<p class="text-muted mb-1">
					Halaman ini tidak dapat dibuka oleh role
					<span class="badge badge-secondary"><?= esc(ucfirst($role)) ?></span>.
				</p>

				<?php if (! empty($diizinkan)): ?>
					<p class="text-muted small mb-4">
						Halaman ini hanya untuk role:
						<?php foreach ($diizinkan as $r): ?>
							<span class="badge badge-success"><?= esc(ucfirst($r)) ?></span>
						<?php endforeach; ?>
					</p>
				<?php else: ?>
					<p class="text-muted small mb-4">
						Hubungi administrator jika Anda merasa seharusnya punya akses.
					</p>
				<?php endif; ?>

				<a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-round">
					<i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
				</a>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
