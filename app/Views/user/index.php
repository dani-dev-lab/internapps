<?php
/**
 * Daftar pengguna aplikasi.
 *
 * @var array $users Seluruh pengguna beserta nama role-nya
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card card-round">
	<div class="card-header">
		<div class="card-head-row">
			<h4 class="card-title">Daftar Pengguna Aplikasi</h4>
			<div class="card-tools">
				<a href="<?= base_url('users/create') ?>" class="btn btn-primary btn-round btn-sm">
					<i class="fas fa-plus mr-1"></i> Tambah Pengguna
				</a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<?php // Lebar kolom ditulis sebagai persen di <th> dan dipakai apa adanya
					// berkat table-layout: fixed. Lebar TIDAK ditulis ulang di columnDefs,
					// karena DataTable akan menimpanya dan hasilnya saling bertabrakan. ?>
				<table id="tabel-pengguna"
					class="display table table-striped table-hover"
					style="table-layout: fixed; width: 100%;">
				<thead>
					<tr>
						<th style="width: 6%;" class="text-nowrap">No</th>
						<th style="width: 8%;" class="text-nowrap">Foto</th>
						<th style="width: 34%;">Nama Pengguna</th>
						<th style="width: 22%;">Username</th>
						<th style="width: 15%;" class="text-nowrap">Role</th>
						<th style="width: 15%;" class="text-nowrap text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($users as $i => $u): ?>
						<tr>
							<td><?= $i + 1 ?></td>
							<td>
								<div class="avatar-sm">
									<img src="<?= esc(foto_user($u['foto'])) ?>"
										alt="Foto <?= esc($u['nama_pengguna']) ?>"
										class="avatar-img rounded-circle">
								</div>
							</td>
							<td>
								<?= esc($u['nama_pengguna']) ?>
								<?php if ((int) $u['id'] === (int) session('user_id')): ?>
									<span class="badge badge-primary ml-1">Anda</span>
								<?php endif; ?>
							</td>
							<td class="align-middle"><?= esc($u['username']) ?></td>
							<td>
								<?php
								$warnaRole = match ($u['nama_role']) {
								    'superadmin' => 'badge-primary',
								    'admin'      => 'badge-secondary',
								    'staff'      => 'badge-info',
								    'peserta'    => 'badge-success',
								    default      => 'badge-light',
								};

								// Baris superadmin terkunci bagi admin biasa. Penolakan yang
								// sebenarnya ada di controller; ini supaya tombolnya tidak
								// ditampilkan padahal pasti ditolak.
								$terkunci = $u['nama_role'] === 'superadmin' && ! $sayaSuperadmin;
								?>
								<span class="badge <?= $warnaRole ?>"><?= esc(ucfirst($u['nama_role'])) ?></span>
							</td>
							<td class="text-nowrap text-center align-middle">
								<?php if ($terkunci): ?>
									<span class="text-muted p-1 d-inline-block"
										data-toggle="tooltip"
										title="Akun superadmin hanya dapat dikelola oleh superadmin sendiri">
										<i class="fas fa-shield-alt"></i>
									</span>
								<?php else: ?>
								<a href="<?= base_url('users/edit/' . $u['id']) ?>"
									class="btn btn-link btn-primary btn-lg p-1"
									title="Ubah">
									<i class="fa fa-edit"></i>
								</a>

								<?php if ((int) $u['id'] !== (int) session('user_id')): ?>
									<form action="<?= base_url('users/delete/' . $u['id']) ?>"
										method="post"
										class="d-inline form-hapus"
										data-nama="<?= esc($u['nama_pengguna'], 'attr') ?>">
										<?= csrf_field() ?>
										<button type="submit"
											class="btn btn-link btn-danger p-1"
											title="Hapus">
											<i class="fa fa-times"></i>
										</button>
									</form>
								<?php else: ?>
									<?php // Akun yang sedang dipakai tidak boleh dihapus. Ditampilkan
											// sebagai ikon gembok abu-abu, bukan tombol hapus yang
											// dinonaktifkan, supaya jelas ini keterangan — bukan tombol
											// yang kebetulan sedang rusak. ?>
									<span class="text-muted p-1 d-inline-block"
										data-toggle="tooltip"
										title="Akun yang sedang Anda pakai tidak dapat dihapus">
										<i class="fas fa-lock"></i>
									</span>
								<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function () {
		$('#tabel-pengguna').DataTable({
			pageLength: 10,
			// Kolom No, Foto, dan Aksi tidak masuk akal untuk diurutkan.
			columnDefs: [
				{ orderable: false, targets: [0, 1, 5] }
			],
			language: {
				search: 'Cari:',
				lengthMenu: 'Tampilkan _MENU_ data',
				info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
				infoEmpty: 'Tidak ada data',
				infoFiltered: '(disaring dari _MAX_ total data)',
				zeroRecords: 'Data tidak ditemukan',
				emptyTable: 'Belum ada pengguna',
				paginate: {
					first: 'Pertama',
					last: 'Terakhir',
					next: 'Berikutnya',
					previous: 'Sebelumnya'
				}
			}
		});

		// Konfirmasi hapus memakai SweetAlert bawaan Atlantis. Form baru
		// dikirim setelah pengguna benar-benar menyetujuinya.
		$('.form-hapus').on('submit', function (e) {
			e.preventDefault();

			var form = this;
			var nama = $(form).data('nama');

			swal({
				title: 'Hapus pengguna ini?',
				text: 'Akun "' + nama + '" akan dihapus permanen dan tidak bisa dikembalikan.',
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
