<?php
$namaPengguna = session('nama_pengguna') ?? 'Pengguna';
$namaRole     = session('nama_role') ?? '-';
$fotoPengguna = session('foto')
	? base_url('uploads/avatar/' . session('foto'))
	: base_url('assets/img/internapps/default-avatar.svg');

$roleAktif   = session('nama_role');
$segmenAktif = explode('/', trim(uri_string(), '/'))[0];

$menu = [
	['tipe' => 'item',  'label' => 'Dashboard',           'icon' => 'fas fa-home',          'url' => 'dashboard', 'segmen' => 'dashboard', 'roles' => ['superadmin', 'admin', 'staff', 'peserta']],

	['tipe' => 'seksi', 'label' => 'Master Data',                                                                                          'roles' => ['superadmin', 'admin', 'staff', 'peserta']],
	['tipe' => 'item',  'label' => 'Data Pengguna',       'icon' => 'fas fa-user-shield',   'url' => 'users',     'segmen' => 'users',     'roles' => ['superadmin', 'admin']],
	['tipe' => 'item',  'label' => 'Data Peserta Magang', 'icon' => 'fas fa-user-graduate', 'url' => 'peserta',   'segmen' => 'peserta',   'roles' => ['superadmin', 'admin', 'staff']],
	['tipe' => 'item',  'label' => 'Data Magang Saya',    'icon' => 'fas fa-id-card',       'url' => 'data-saya', 'segmen' => 'data-saya', 'roles' => ['peserta']],

];

$menuTampil = array_values(array_filter(
	$menu,
	static fn (array $m): bool => in_array($roleAktif, $m['roles'], true)
));

foreach ($menuTampil as $i => $m) {
	if ($m['tipe'] === 'seksi' && (! isset($menuTampil[$i + 1]) || $menuTampil[$i + 1]['tipe'] === 'seksi')) {
		unset($menuTampil[$i]);
	}
}
?>
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="<?= esc($fotoPengguna) ?>" alt="Foto profil" class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseProfil" aria-expanded="false">
								<span>
									<?= esc($namaPengguna) ?>
									<span class="user-level"><?= esc(ucfirst($namaRole)) ?></span>
									<span class="caret"></span>
								</span>
							</a>
							<div class="clearfix"></div>

							<div class="collapse" id="collapseProfil">
								<ul class="nav">
									<li>
										<a href="<?= base_url('profil') ?>">
											<span class="link-collapse">Profil Saya</span>
										</a>
									</li>
									<li>
										<a href="<?= base_url('logout') ?>">
											<span class="link-collapse">Logout</span>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<ul class="nav nav-primary">
<?php foreach ($menuTampil as $m): ?>
<?php if ($m['tipe'] === 'seksi'): ?>
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section"><?= esc($m['label']) ?></h4>
						</li>
<?php else: ?>
						<li class="nav-item<?= $segmenAktif === $m['segmen'] ? ' active' : '' ?>">
							<a href="<?= base_url($m['url']) ?>">
								<i class="<?= esc($m['icon'], 'attr') ?>"></i>
								<p><?= esc($m['label']) ?></p>
							</a>
						</li>
<?php endif; ?>
<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
