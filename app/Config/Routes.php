<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Scan
$routes->get('/', 'Scan::index');

$routes->group('scan', function (RouteCollection $routes) {
   $routes->get('', 'Scan::index');
   $routes->get('masuk', 'Scan::index/Masuk');
   $routes->get('pulang', 'Scan::index/Pulang');

   $routes->post('cek', 'Scan::cekKode');
});

// Admin
$routes->group('admin', function (RouteCollection $routes) {
   // Admin dashboard
   $routes->get('', 'Admin\Dashboard::index');
   $routes->get('dashboard', 'Admin\Dashboard::index');

   // data kelas & jurusan
   $routes->resource('jabatan', ['controller' => 'Admin\JabatanController::index']);
   $routes->resource('posisi', ['controller' => 'Admin\PosisiController']);

   // admin lihat data siswa
   $routes->get('karyawan', 'Admin\DataKaryawan::index');
   $routes->post('karyawan', 'Admin\DataKaryawan::ambilDataKaryawan');
   // admin tambah data siswa
   $routes->get('karyawan/create', 'Admin\DataKaryawan::formTambahKaryawan');
   $routes->post('karyawan/create', 'Admin\DataKaryawan::saveKaryawan');
   // admin edit data siswa
   $routes->get('karyawan/edit/(:any)', 'Admin\DataKaryawan::formEditKaryawan/$1');
   $routes->post('karyawan/edit', 'Admin\DataKaryawan::updateKaryawan');
   // admin hapus data siswa
   $routes->delete('karyawan/delete/(:any)', 'Admin\DataKaryawan::delete/$1');




   // admin lihat data absen siswa
   $routes->get('absen-karyawan', 'Admin\DataAbsenKaryawan::index');
   $routes->post('absen-karyawan', 'Admin\DataAbsenKaryawan::ambilDataKaryawan'); // ambil siswa berdasarkan kelas dan tanggal
   $routes->post('absen-karyawan/kehadiran', 'Admin\DataAbsenKaryawan::ambilKehadiran'); // ambil kehadiran siswa
   $routes->post('absen-karyawan/edit', 'Admin\DataAbsenKaryawan::ubahKehadiran'); // ubah kehadiran siswa

   $routes->post('tambah-kelas', 'Admin\DataAbsenKaryawan::tambahJabatan'); // tambah data kelas

   
   // admin generate QR
   $routes->get('generate', 'Admin\GenerateQR::index');
   $routes->post('generate/karyawan-by-kelas', 'Admin\GenerateQR::getKaryawanByJabatan');

   $routes->post('generate/karyawan', 'Admin\QRGenerator::generateQrKaryawan');
   

   // admin buat laporan
   $routes->get('laporan', 'Admin\GenerateLaporan::index');
   $routes->post('laporan/karyawan', 'Admin\GenerateLaporan::generateLaporanKaryawan');
   

   // superadmin lihat data petugas
   $routes->get('petugas', 'Admin\DataPetugas::index');
   $routes->post('petugas', 'Admin\DataPetugas::ambilDataPetugas');
   // superadmin tambah data petugas
   $routes->get('petugas/register', 'Admin\DataPetugas::registerPetugas');
   // superadmin edit data petugas
   $routes->get('petugas/edit/(:any)', 'Admin\DataPetugas::formEditPetugas/$1');
   $routes->post('petugas/edit', 'Admin\DataPetugas::updatePetugas');
   // superadmin hapus data petugas
   $routes->delete('petugas/delete/(:any)', 'Admin\DataPetugas::delete/$1');
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
   require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
