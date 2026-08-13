<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Halaman depan hanya mengarahkan: ke dashboard kalau sudah masuk,
// ke halaman login kalau belum.
$routes->get('/', 'Home::index');

// Hanya untuk yang BELUM masuk. Yang sudah masuk otomatis dilempar
// ke dashboard oleh GuestFilter.
$routes->group('', ['filter' => 'guest'], static function (RouteCollection $routes): void {
    $routes->get('login', 'Auth::index');
    $routes->post('login', 'Auth::prosesLogin');

    // Pendaftaran akun peserta. Ikut filter 'guest' supaya yang sudah masuk
    // tidak bisa membukanya.
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::prosesRegister');
});

$routes->get('logout', 'Auth::logout');

// Bisa dibuka semua role, asalkan sudah masuk.
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'Dashboard::index');

    // Profil selalu mengubah akun yang sedang masuk, jadi tidak ada id di URL.
    $routes->get('profil', 'Profil::index');
    $routes->post('profil/data', 'Profil::updateData');
    $routes->post('profil/foto', 'Profil::updateFoto');
    $routes->post('profil/foto/hapus', 'Profil::hapusFoto');
    $routes->post('profil/password', 'Profil::updatePassword');
});

// Pengelolaan pengguna aplikasi — khusus admin.
// Menghapus dan menyimpan memakai POST, bukan GET, supaya terlindungi token
// CSRF dan tidak bisa terpicu hanya karena sebuah alamat dibuka.
$routes->group('users', ['filter' => 'role:superadmin,admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'User::index');
    $routes->get('create', 'User::create');
    $routes->post('/', 'User::store');
    $routes->get('edit/(:num)', 'User::edit/$1');
    $routes->post('update/(:num)', 'User::update/$1');
    $routes->post('delete/(:num)', 'User::delete/$1');
});

// Pengelolaan peserta magang — admin dan staff.
$routes->group('peserta', ['filter' => 'role:superadmin,admin,staff'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Peserta::index');
    $routes->get('create', 'Peserta::create');
    $routes->post('/', 'Peserta::store');
    $routes->get('detail/(:num)', 'Peserta::detail/$1');
    $routes->get('edit/(:num)', 'Peserta::edit/$1');
    $routes->post('update/(:num)', 'Peserta::update/$1');
});

// Menghapus peserta dipisah ke grup khusus admin. Staff boleh menambah dan
// mengubah, tapi tidak menghapus — penghapusan permanen dan ikut membuang
// berkas fotonya.
$routes->group('peserta', ['filter' => 'role:superadmin,admin'], static function (RouteCollection $routes): void {
    $routes->post('delete/(:num)', 'Peserta::delete/$1');
});

// Data magang milik sendiri — khusus peserta.
$routes->group('', ['filter' => 'role:peserta'], static function (RouteCollection $routes): void {
    $routes->get('data-saya', 'Peserta::dataSaya');
});
