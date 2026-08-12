<?php
/**
 * Footer aplikasi.
 *
 * Tautan di sisi kiri (nama aplikasi dan kredit template) dibuang: keduanya
 * tidak menuju ke mana-mana yang berguna bagi pengguna, dan nama aplikasi
 * sudah tertera di logo pada header. Sisa satu baris keterangan saja.
 */
?>
<footer class="footer">
				<div class="container-fluid">
					<?php // .footer .container-fluid memakai display:flex, jadi ml-auto
							// mendorong isinya ke kanan. mx-auto memberi margin otomatis
							// di kedua sisi sehingga posisinya jatuh tepat di tengah. ?>
					<div class="copyright mx-auto text-center">
						<?= date('Y') ?> &middot; Internapps &middot; Sistem Informasi Peserta Magang
					</div>
				</div>
			</footer>
