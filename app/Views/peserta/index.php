<?php
/**
 * @var array $peserta    Seluruh data peserta beserta akun yang tertaut
 * @var bool  $bolehHapus Dikirim controller, sejalan dengan filter route
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card card-round">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Daftar Peserta Magang</h4>
			<div class="card-tools">
				<a href="<?= base_url('peserta/create') ?>" class="btn btn-primary btn-round btn-sm">
					<i class="fas fa-plus mr-1"></i> Tambah Peserta
				</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="tabel-peserta"
				class="display table table-striped table-hover"
				style="table-layout: fixed; width: 100%;">
				<thead>
					<tr>
						<th style="width: 4%;" class="text-nowrap">No</th>
						<th style="width: 5%;" class="text-nowrap">Foto</th>
						<th style="width: 15%;" class="text-nowrap">NIK</th>
						<th style="width: 25%;">Peserta</th>
						<th style="width: 16%;">Program Studi</th>
						<th style="width: 12%;" class="text-nowrap">Periode Magang</th>
						<th style="width: 14%;" class="text-nowrap">Status</th>
						<th style="width: 9%;" class="text-nowrap text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($peserta as $i => $p): ?>
						<tr>
							<td><?= $i + 1 ?></td>
							<td>
								<div class="avatar-sm">
									<img src="<?= esc(foto_peserta($p['link_foto_peserta'])) ?>"
										alt="Foto <?= esc($p['nama_peserta']) ?>"
										class="avatar-img rounded-circle">
								</div>
							</td>
							<td class="text-nowrap align-middle"><?= esc($p['nik']) ?></td>
							<td class="align-middle">
								<strong class="d-block text-truncate"
									title="<?= esc($p['nama_peserta'], 'attr') ?>"><?= esc($p['nama_peserta']) ?></strong>
								<span class="text-muted small d-block text-truncate"
									title="<?= esc($p['nama_universitas'], 'attr') ?>"><?= esc($p['nama_universitas']) ?></span>
								<span class="text-muted small d-block text-truncate">
									<?php if (! empty($p['username'])): ?>
										<i class="fas fa-user mr-1"></i><?= esc($p['username']) ?>
									<?php else: ?>
										<i class="fas fa-user-slash mr-1"></i>tanpa akun
									<?php endif; ?>
								</span>
							</td>
							<td class="align-middle">
								<span class="d-block text-truncate"
									title="<?= esc($p['nama_jurusan'], 'attr') ?>"><?= esc($p['nama_jurusan']) ?></span>
								<span class="text-muted small d-block text-truncate"
									title="<?= esc($p['nama_fakultas'], 'attr') ?>"><?= esc($p['nama_fakultas']) ?></span>
							</td>
							<td class="text-nowrap align-middle" data-order="<?= esc($p['tanggal_mulai_magang'], 'attr') ?>">
								<?= esc(tanggal_singkat($p['tanggal_mulai_magang'])) ?>
								<br><span class="text-muted small">s/d <?= esc(tanggal_singkat($p['tanggal_berakhir_magang'])) ?></span>
							</td>
							<td class="text-nowrap align-middle"><?= badge_status_magang($p['tanggal_mulai_magang'], $p['tanggal_berakhir_magang']) ?></td>
							<td class="text-nowrap text-center align-middle">
								<a href="<?= base_url('peserta/detail/' . $p['id']) ?>"
									class="btn btn-link btn-info p-1" title="Detail">
									<i class="fa fa-eye"></i>
								</a>
								<a href="<?= base_url('peserta/edit/' . $p['id']) ?>"
									class="btn btn-link btn-primary p-1" title="Ubah">
									<i class="fa fa-edit"></i>
								</a>

								<?php if ($bolehHapus): ?>
									<form action="<?= base_url('peserta/delete/' . $p['id']) ?>"
										method="post"
										class="d-inline form-hapus"
										data-nama="<?= esc($p['nama_peserta'], 'attr') ?>">
										<?= csrf_field() ?>
										<button type="submit" class="btn btn-link btn-danger p-1" title="Hapus">
											<i class="fa fa-times"></i>
										</button>
									</form>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<?php if (! $bolehHapus): ?>
			<p class="text-muted small mt-3 mb-0">
				<i class="fas fa-info-circle mr-1"></i>
				Penghapusan data peserta hanya dapat dilakukan oleh admin.
			</p>
		<?php endif; ?>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function () {
		$('#tabel-peserta').DataTable({
			pageLength: 10,
			order: [[3, 'asc']],
			autoWidth: false,
			columnDefs: [
				{ orderable: false, targets: [0, 1, 7] }
			],
			language: {
				search: 'Cari:',
				lengthMenu: 'Tampilkan _MENU_ data',
				info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ peserta',
				infoEmpty: 'Tidak ada data',
				infoFiltered: '(disaring dari _MAX_ total data)',
				zeroRecords: 'Peserta tidak ditemukan',
				emptyTable: 'Belum ada data peserta magang',
				paginate: {
					first: 'Pertama',
					last: 'Terakhir',
					next: 'Berikutnya',
					previous: 'Sebelumnya'
				}
			}
		});

		$('.form-hapus').on('submit', function (e) {
			e.preventDefault();

			var form = this;

			swal({
				title: 'Hapus data peserta ini?',
				text: 'Data "' + $(form).data('nama') + '" beserta fotonya akan dihapus permanen.',
				icon: 'warning',
				buttons: ['Batal', 'Ya, hapus'],
				dangerMode: true
			}).then(function (setuju) {
				if (setuju) {
					form.submit();
				}
			});
		});
	});
</script>
<?= $this->endSection() ?>
