# Internapps

Sistem Informasi Pengelolaan Data Peserta Magang.

Aplikasi web untuk mencatat dan mengelola data peserta magang: siapa saja pesertanya,
dari kampus dan jurusan mana, kapan mulai dan berakhir magangnya, serta siapa yang
berhak mengubah data tersebut. Setiap peserta juga dapat membuat akun sendiri untuk
melihat data magangnya.

Dibangun dengan CodeIgniter 4 dan template Atlantis Lite.

---

## Daftar Isi

- [Kebutuhan Sistem](#kebutuhan-sistem)
- [Cara Memasang](#cara-memasang)
- [Akun Bawaan](#akun-bawaan)
- [Alur Penggunaan](#alur-penggunaan)
  - [1. Masuk ke Aplikasi](#1-masuk-ke-aplikasi)
  - [2. Mendaftar Akun (Peserta)](#2-mendaftar-akun-peserta)
  - [3. Alur Admin](#3-alur-admin)
  - [4. Alur Staff](#4-alur-staff)
  - [5. Alur Peserta](#5-alur-peserta)
  - [6. Mengatur Profil (Semua Role)](#6-mengatur-profil-semua-role)
  - [7. Keluar](#7-keluar)
- [Peta Halaman dan Hak Akses](#peta-halaman-dan-hak-akses)
- [Aturan Pengisian Data](#aturan-pengisian-data)
- [Status Magang Dihitung Otomatis](#status-magang-dihitung-otomatis)
- [Catatan Penting Sebelum Dipakai](#catatan-penting-sebelum-dipakai)
- [Struktur Folder](#struktur-folder)
- [Kalau Ada Masalah](#kalau-ada-masalah)

---

## Kebutuhan Sistem

| Kebutuhan | Versi |
| --- | --- |
| PHP | 8.3 (dengan ekstensi `intl`, `mbstring`, `mysqlnd`) |
| CodeIgniter | 4.7 |
| Database | MySQL / MariaDB |
| Web server | Apache (disarankan lewat Laragon / XAMPP) |
| Composer | versi terbaru |

Aplikasi ini dirancang untuk dijalankan di **localhost**.

---

## Cara Memasang

**1. Salin project ke folder web server**

Contoh untuk Laragon: `C:\laragon\www\internapps`

**2. Pasang dependensi**

```bash
composer install
```

**3. Siapkan berkas konfigurasi**

Salin `env` menjadi `.env`, lalu sesuaikan isinya:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/internapps/public/'

database.default.hostname = localhost
database.default.database = internapps
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

> Password database **tidak boleh** ditulis di dalam kode program. Semua kredensial
> hanya berada di `.env`, dan berkas itu sudah didaftarkan di `.gitignore`.

**4. Buat database**

Buat database kosong bernama `internapps` dengan charset `utf8mb4`.

```sql
CREATE DATABASE internapps CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

**5. Bangun tabel**

```bash
php spark migrate
```

Perintah ini membuat tiga tabel: `roles`, `users`, dan `peserta_magang`.

**6. Isi data awal**

```bash
php spark db:seed DatabaseSeeder
```

Perintah ini mengisi 3 role, 7 akun, dan 5 contoh data peserta.

**7. Buka aplikasi**

```
http://localhost/internapps/public/
```

---

## Akun Bawaan

| Username | Password | Role |
| --- | --- | --- |
| `admin` | `admin123` | Admin |
| `staff` | `staff123` | Staff |
| `peserta1` | `peserta123` | Peserta |
| `peserta2` | `peserta123` | Peserta |
| `peserta3` | `peserta123` | Peserta |
| `peserta4` | `peserta123` | Peserta |
| `peserta5` | `peserta123` | Peserta |

Akun `peserta1` sampai `peserta5` adalah **akun contoh** yang menyertai data peserta
contoh. Keduanya boleh dihapus setelah data peserta yang sebenarnya dimasukkan.

> **Ganti semua password di atas sebelum aplikasi dipakai.** Password ini hanya untuk
> memudahkan pengujian pertama kali. Cara menggantinya ada di bagian
> [Mengatur Profil](#6-mengatur-profil-semua-role).

Password disimpan di database dalam bentuk *hash* (`password_hash` bawaan PHP),
tidak pernah sebagai teks biasa.

---

## Alur Penggunaan

### 1. Masuk ke Aplikasi

1. Buka `http://localhost/internapps/public/`
2. Halaman akan langsung mengarah ke **Login**
3. Isi **Username** dan **Password**, lalu tekan **Masuk**
4. Aplikasi mengarahkan ke **Dashboard** yang isinya menyesuaikan role Anda

Kalau username atau password salah, muncul pesan **"Username atau password salah."**
Pesan ini sengaja dibuat sama untuk kedua kasus, supaya halaman login tidak bisa
dipakai untuk menebak username mana yang terdaftar.

Halaman apa pun di dalam aplikasi tidak bisa dibuka langsung lewat URL tanpa login —
pengunjung akan dilempar kembali ke halaman login.

### 2. Mendaftar Akun (Peserta)

Pendaftaran **bukan pendaftaran terbuka**. Akun hanya bisa dibuat oleh orang yang
datanya sudah lebih dulu didaftarkan admin atau staff sebagai peserta magang.

1. Di halaman login, klik **"Daftar di sini"**
2. Isi **NIK** (16 digit) dan **Nama Lengkap** — harus sama persis dengan data yang
   sudah didaftarkan admin
3. Tentukan **Username** dan **Password** baru
4. Tekan **Daftar**
5. Kalau cocok, akun terbuat dan otomatis tertaut ke data magang Anda
6. Masuk kembali memakai username dan password yang baru saja dibuat

Pendaftaran akan ditolak apabila:

- NIK dan nama tidak cocok dengan data peserta mana pun
- Data peserta tersebut **sudah** punya akun
- Username sudah dipakai orang lain

Role pendaftar selalu dipaksa menjadi **peserta**. Tidak ada cara mendaftarkan diri
sebagai admin atau staff lewat halaman ini.

### 3. Alur Admin

Admin memegang akses penuh.

**Melihat ringkasan**

Buka **Dashboard**. Di sana ada kartu jumlah peserta per status (total, belum mulai,
sedang magang, selesai), jumlah pengguna per role, serta tabel peserta yang magangnya
sedang berjalan.

**Mengelola data peserta magang**

1. Klik **Data Peserta Magang** di sidebar
2. Tabel dapat dicari dan diurutkan lewat kolom pencarian di atasnya

Menambah peserta:

1. Tekan tombol **Tambah Peserta**
2. Isi NIK, nama, universitas, fakultas, jurusan, tanggal mulai, tanggal berakhir
3. Foto peserta boleh dikosongkan
4. Tekan **Simpan**

Melihat rincian: tekan ikon **mata** pada baris peserta.
Mengubah: tekan ikon **pensil**, ubah seperlunya, lalu **Simpan**.
Menghapus: tekan ikon **silang (✕)** berwarna merah, lalu konfirmasi pada kotak yang
muncul.

> Menghapus data peserta bersifat permanen dan sekaligus membuang berkas fotonya.
> Kalau peserta itu sudah punya akun, akunnya tetap ada tetapi tidak lagi memiliki
> data magang.

**Mengelola pengguna aplikasi**

1. Klik **Data Pengguna** di sidebar
2. **Tambah Pengguna** untuk membuat akun baru — di sini role dapat dipilih bebas
   (admin, staff, atau peserta)
3. Ikon **pensil** untuk mengubah, ikon **silang (✕)** untuk menghapus

Ada dua pembatasan yang sengaja dipasang:

- Admin **tidak dapat menghapus akunnya sendiri**
- Admin terakhir **tidak dapat dihapus**, supaya aplikasi tidak pernah kehilangan
  seluruh administratornya

Saat mengubah pengguna, kolom password boleh dibiarkan kosong apabila passwordnya
tidak ingin diganti.

### 4. Alur Staff

Staff menangani data peserta sehari-hari.

- **Dashboard** — sama seperti admin
- **Data Peserta Magang** — boleh **melihat, menambah, dan mengubah**

Staff **tidak dapat menghapus** data peserta. Tombol hapus tidak ditampilkan sama
sekali, dan di bawah tabel ada keterangan *"Penghapusan data peserta hanya dapat
dilakukan oleh admin."* Penghapusan bersifat permanen, jadi wewenangnya ditahan di
admin.

Menu **Data Pengguna** tidak muncul untuk staff, dan alamatnya pun tidak bisa dibuka
langsung — akan muncul halaman **403 (akses ditolak)**.

### 5. Alur Peserta

Peserta hanya dapat melihat datanya sendiri.

- **Dashboard** — menampilkan ringkasan data magangnya: nama, kampus, jurusan,
  tanggal magang, status, dan sisa hari
- **Data Magang Saya** — menampilkan rincian lengkap dalam bentuk baca-saja

Peserta tidak dapat mengubah data magangnya. Kalau ada yang keliru, hubungi admin
atau staff.

Data yang ditampilkan selalu diambil berdasarkan akun yang sedang masuk, bukan
berdasarkan angka pada alamat browser. Mengganti angka di URL tidak akan menampilkan
data peserta lain.

Kalau akun belum tertaut ke data magang mana pun, halaman menampilkan keterangan
**"Data Magang Belum Tersedia"**.

### 6. Mengatur Profil (Semua Role)

Berlaku untuk admin, staff, maupun peserta.

1. Klik **nama Anda** di bagian atas sidebar
2. Pilih **Profil Saya**

Di halaman ini tersedia empat hal:

**Mengubah data akun** — ubah nama pengguna atau username, lalu **Simpan**.

**Mengganti foto profil** — pilih berkas, lalu unggah.

| Ketentuan foto | Nilai |
| --- | --- |
| Format | JPG, JPEG, PNG, WEBP |
| Ukuran maksimum | 2 MB |

**Menghapus foto profil** — foto kembali ke gambar bawaan.

**Mengganti password** — isi password lama, password baru, dan konfirmasinya.
Password lama wajib benar; ini mencegah penggantian password oleh orang lain yang
kebetulan menemukan komputer Anda dalam keadaan masih masuk.

### 7. Keluar

1. Klik **nama Anda** di bagian atas sidebar
2. Pilih **Logout**

Setelah keluar, seluruh isi sesi dihapus dan ID sesi diterbitkan ulang. Untuk masuk
kembali, username dan password **wajib diketik ulang** — kolom login tidak akan terisi
otomatis. Ini berlaku sama untuk admin, staff, maupun peserta.

---

## Peta Halaman dan Hak Akses

| Halaman | Alamat | Admin | Staff | Peserta |
| --- | --- | :---: | :---: | :---: |
| Login | `/login` | — | — | — |
| Daftar akun | `/register` | — | — | — |
| Dashboard | `/dashboard` | ✅ | ✅ | ✅ |
| Profil saya | `/profil` | ✅ | ✅ | ✅ |
| Daftar pengguna | `/users` | ✅ | ❌ | ❌ |
| Tambah / ubah / hapus pengguna | `/users/...` | ✅ | ❌ | ❌ |
| Daftar peserta | `/peserta` | ✅ | ✅ | ❌ |
| Tambah peserta | `/peserta/create` | ✅ | ✅ | ❌ |
| Ubah peserta | `/peserta/edit/{id}` | ✅ | ✅ | ❌ |
| Hapus peserta | `/peserta/delete/{id}` | ✅ | ❌ | ❌ |
| Data magang saya | `/data-saya` | ❌ | ❌ | ✅ |

Menu yang tidak boleh diakses tidak ditampilkan di sidebar. Namun penyembunyian menu
itu bukan pengamannya — pembatasan yang sebenarnya berada pada *filter* di tiap route,
sehingga membuka alamatnya secara langsung tetap menghasilkan **403**.

---

## Aturan Pengisian Data

**Data peserta magang**

| Kolom | Ketentuan |
| --- | --- |
| NIK | Wajib, tepat 16 digit angka, tidak boleh sama dengan peserta lain |
| Nama peserta | Wajib, 3–100 karakter |
| Nama universitas | Wajib, 3–150 karakter |
| Nama fakultas | Wajib, 2–150 karakter |
| Nama jurusan | Wajib, 2–150 karakter |
| Tanggal mulai magang | Wajib |
| Tanggal berakhir magang | Wajib, tidak boleh lebih awal dari tanggal mulai |
| Foto peserta | Opsional — JPG/JPEG/PNG/WEBP, maksimum 2 MB |

**Data pengguna**

| Kolom | Ketentuan |
| --- | --- |
| Username | Wajib, minimal 3 karakter, hanya huruf/angka/garis bawah/strip, unik |
| Password | Wajib saat menambah, minimal 6 karakter. Saat mengubah boleh dikosongkan |
| Nama pengguna | Wajib |
| Role | Wajib dipilih |

Seluruh aturan di atas diperiksa di sisi server, bukan hanya di browser.

---

## Status Magang Dihitung Otomatis

Status peserta **tidak disimpan** sebagai kolom di database. Statusnya dihitung dari
perbandingan tanggal hari ini dengan tanggal magangnya, sehingga tidak akan pernah
basi atau salah karena lupa diperbarui.

| Keadaan | Status |
| --- | --- |
| Tanggal mulai masih di depan | **Belum Mulai** |
| Hari ini berada di antara tanggal mulai dan berakhir | **Sedang Magang** |
| Tanggal berakhir sudah lewat | **Selesai** |

Zona waktu aplikasi diatur ke `Asia/Makassar` (WITA) pada `app/Config/App.php`, supaya
pergantian hari sesuai waktu setempat.

---

## Catatan Penting Sebelum Dipakai

**Data peserta bawaan hanyalah contoh.**
Lima data peserta hasil `db:seed` bernama "Peserta Contoh Satu" sampai "Lima" dengan
NIK `0000000000000001`–`0000000000000005` dan kampus "Universitas Contoh". Semuanya
data contoh untuk keperluan pengujian, bukan peserta magang sungguhan.

**Data peserta yang sebenarnya dimasukkan lewat aplikasi**, bukan lewat berkas seeder:
masuk sebagai admin, buka menu **Data Peserta Magang**, lalu tekan **Tambah Peserta**.
Alasannya, NIK adalah data pribadi dan tidak seharusnya ikut tersimpan di dalam berkas
kode program yang bisa tersalin ke mana-mana. Hapus dulu kelima data contoh beserta
kelima akunnya supaya isinya tidak tercampur.

**Ganti password bawaan.**
Akun `admin`, `staff`, dan seluruh akun peserta masih memakai password contoh yang
tertulis terbuka di README ini.

**Untuk pemakaian di luar localhost**, ubah `CI_ENVIRONMENT` di `.env` menjadi
`production`, supaya rincian galat tidak ditampilkan ke pengunjung.

---

## Struktur Folder

```
internapps/
├── app/
│   ├── Config/           Konfigurasi (route, filter, keamanan, validasi)
│   ├── Controllers/      Auth, Dashboard, User, Peserta, Profil
│   ├── Database/
│   │   ├── Migrations/   Struktur tabel
│   │   └── Seeds/        Data awal
│   ├── Filters/          AuthFilter, GuestFilter, RoleFilter
│   ├── Helpers/          magang_helper (status, tanggal, foto)
│   ├── Libraries/        UnggahFoto (validasi & penyimpanan berkas)
│   ├── Models/           UserModel, RoleModel, PesertaMagangModel
│   ├── Validation/       Aturan validasi tambahan
│   └── Views/
│       ├── layouts/      Kerangka halaman & partial
│       ├── auth/         Login, daftar akun
│       ├── dashboard/    Dashboard admin/staff dan peserta
│       ├── user/         CRUD pengguna
│       ├── peserta/      CRUD peserta magang
│       ├── profil/       Profil pengguna
│       └── errors/       Halaman 403 dan 404
├── public/
│   ├── assets/           Template Atlantis Lite + logo Internapps
│   ├── uploads/          Foto peserta dan foto profil
│   └── index.php
└── writable/             Log, cache, session
```

---

## Kalau Ada Masalah

**Halaman tampil polos tanpa gaya (CSS tidak termuat)**
Periksa `app.baseURL` di `.env`. Alamatnya harus persis sama dengan yang dibuka di
browser, lengkap dengan garis miring di akhir.

**Muncul "The action you requested is not allowed"**
Halaman dibiarkan terbuka terlalu lama sehingga token keamanannya kedaluwarsa. Muat
ulang halamannya, lalu kirim kembali formulirnya.

**Foto tidak muncul setelah diunggah**
Pastikan folder `public/uploads/peserta` dan `public/uploads/avatar` ada dan dapat
ditulisi.

**Gagal terhubung ke database**
Periksa kembali nama database, username, dan port pada `.env`, serta pastikan layanan
MySQL sedang berjalan.

**Ingin mengulang data dari awal**

```bash
php spark migrate:refresh
php spark db:seed DatabaseSeeder
```

> Perintah ini **menghapus seluruh isi tabel**, termasuk data yang sudah Anda masukkan
> sendiri.

**Melihat catatan galat**
Berkas log ada di `writable/logs/`.
