<?php
/**
 * Wadah isi halaman: judul, breadcrumb, pesan hasil aksi, lalu section content.
 *
 * @var string $page_title Judul halaman, opsional (ada nilai cadangan)
 */
?>
<div class="main-panel">
			<div class="content">
				<?php $banner = $this->renderSection('banner'); ?>
				<?= $banner ?>
				<div class="page-inner<?= trim($banner) !== '' ? ' mt--5' : '' ?>">
					<?php if (trim($banner) === ''): ?>
					<div class="page-header">
						<h4 class="page-title"><?= esc($page_title ?? 'Dashboard') ?></h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="<?= base_url('dashboard') ?>">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-right-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#"><?= esc($page_title ?? 'Dashboard') ?></a>
							</li>
						</ul>
					</div>
					<?php endif; ?>
					<?= $this->include('layouts/partials/flash') ?>
					<?= $this->renderSection('content') ?>
				</div>
			</div>