<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use DateTime;
use DateInterval;
use DatePeriod;

// use App\Models\GuruModel;
use App\Models\JabatanModel;
// use App\Models\PresensiGuruModel;
use App\Models\KaryawanModel;
use App\Models\PresensiKaryawanModel;

class GenerateLaporan extends BaseController
{
   protected KaryawanModel $karyawanModel;
   protected JabatanModel $jabatanModel;

   // protected GuruModel $guruModel;

   protected PresensiKaryawanModel $presensiKaryawanModel;
   // protected PresensiGuruModel $presensiGuruModel;

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
      $this->jabatanModel = new JabatanModel();

      // $this->guruModel = new GuruModel();

      $this->presensiKaryawanModel = new PresensiKaryawanModel();
      // $this->presensiGuruModel = new PresensiGuruModel();
   }

   public function index()
   {
      $jabatan = $this->jabatanModel->getAllJabatan();
      // $guru = $this->guruModel->getAllGuru();

      $karyawanPerjabatan = [];

      foreach ($jabatan as $value) {
         array_push($karyawanPerjabatan, $this->karyawanModel->getKaryawanByJabatan($value['id_jabatan']));
      }

      $data = [
         'title' => 'Generate Laporan',
         'ctx' => 'laporan',
         'karyawanPerJabatan' => $karyawanPerjabatan,
         'jabatan' => $jabatan
         // 'guru' => $guru
      ];

      return view('admin/generate-laporan/generate-laporan', $data);
   }

   public function generateLaporanKaryawan()
   {
      $idJabatan = $this->request->getVar('jabatan');
      $karyawan = $this->karyawanModel->getKaryawanByJabatan($idJabatan);
      $type = $this->request->getVar('type');
  
      if (empty($karyawan)) {
          session()->setFlashdata([
              'msg' => 'Data karyawan kosong!',
              'error' => true
          ]);
          return redirect()->to('/admin/laporan');
      }
  
      $jabatan = $this->jabatanModel->where(['id_jabatan' => $idJabatan])->join('tb_posisi', 'tb_jabatan.id_posisi = tb_posisi.id', 'left')->first();
  
      $bulan = $this->request->getVar('tanggalKaryawan');
  
      // Tanggal awal dan akhir dalam bulan yang dipilih
      $begin = new DateTime($bulan);
      $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
  
      $arrayTanggal = [];
      $dataAbsen = [];
  
      // Loop untuk setiap hari dalam bulan
      while ($begin < $end) {
          $lewat = Time::parse($begin->format('Y-m-d'))->isAfter(Time::today());
  
          $absenByTanggal = $this->presensiKaryawanModel
              ->getPresensiByJabatanTanggal($idJabatan, $begin->format('Y-m-d'));
  
          $absenByTanggal['lewat'] = $lewat;
  
          array_push($dataAbsen, $absenByTanggal);
          array_push($arrayTanggal, $begin);
          $begin->modify('+1 day');
      }
  
      $laki = 0;
  
      foreach ($karyawan as $value) {
          if ($value['jenis_kelamin'] != 'Perempuan') {
              $laki++;
          }
      }
  
      $data = [
          'tanggal' => $arrayTanggal,
          'bulan' => $bulan,
          'listAbsen' => $dataAbsen,
          'listKaryawan' => $karyawan,
          'jumlahKaryawan' => [
              'laki' => $laki,
              'perempuan' => count($karyawan) - $laki
          ],
          'jabatan' => $jabatan,
          'grup' => "jabatan" . $jabatan['jabatan'] . " " . $jabatan['posisi']
      ];
  
      if ($type == 'doc') {
          $this->response->setHeader('Content-type', 'application/vnd.ms-word');
          $this->response->setHeader('Content-Disposition', 'attachment;Filename=laporan_absen_' . $jabatan['jabatan'] . " " . $jabatan['posisi'] . '_' . $bulan . '.doc');
          return view('admin/generate-laporan/laporan-karyawan', $data);
      }
  
      return view('admin/generate-laporan/laporan-karyawan', $data) . view('admin/generate-laporan/topdf');
  }
   // {
   //    $idJabatan = $this->request->getVar('jabatan');
   //    $karyawan = $this->karyawanModel->getSiswaByJabatan($idJabatan);
   //    $type = $this->request->getVar('type');

   //    if (empty($karyawan)) {
   //       session()->setFlashdata([
   //          'msg' => 'Data karyawan kosong!',
   //          'error' => true
   //       ]);
   //       return redirect()->to('/admin/laporan');
   //    }

   //    $jabatan = $this->jabatanModel->where(['id_jabatan' => $idJabatan])->join('tb_posisi', 'tb_jabatan.id_posisi = tb_posisi.id', 'left')->first();

   //    $bulan = $this->request->getVar('tanggalKaryawan');

   //    // hari pertama dalam 1 bulan
   //    $begin = new DateTime($bulan);
   //    // tanggal terakhir dalam 1 bulan
   //    $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
   //    // interval 1 hari
   //    $interval = DateInterval::createFromDateString('1 day');
   //    // buat array dari semua hari di bulan
   //    $period = new DatePeriod($begin, $interval, $end);

   //    $arrayTanggal = [];
   //    $dataAbsen = [];

   //    foreach ($period as $value) {
   //       // kecualikan hari sabtu dan minggu
   //       if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
   //          $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

   //          $absenByTanggal = $this->presensiKaryawanModel
   //             ->getPresensiByJabatanTanggal($idJabatan, $value->format('Y-m-d'));

   //          $absenByTanggal['lewat'] = $lewat;

   //          array_push($dataAbsen, $absenByTanggal);
   //          array_push($arrayTanggal, $value);
   //       }
   //    }

   //   $laki = 0;
     
   //    foreach ($karyawan as $value) {
   //       if ($value['jenis_kelamin'] != 'Perempuan') {
   //          $laki++;
   //       }
   //    }

   //    $data = [
   //       'tanggal' => $arrayTanggal,
   //       'bulan' => $begin->format('F'),
   //       'listAbsen' => $dataAbsen,
   //       'listKaryawan' => $karyawan,
   //       'jumlahKaryawan' => [
   //          'laki' => $laki,
   //          'perempuan' => count($karyawan) - $laki
   //       ],
   //       'jabatan' => $jabatan,
   //       'grup' => "jabatan" . $jabatan['jabatan'] . " " . $jabatan['posisi']
   //    ];


   //    if ($type == 'doc') {
   //       $this->response->setHeader('Content-type', 'application/vnd.ms-word');
   //       $this->response->setHeader('Content-Disposition', 'attachment;Filename=laporan_absen_' . $jabatan['jabatan'] . " " . $jabatan['posisi'] . '_' . $begin->format('F-Y') . '.doc');

   //       return view('admin/generate-laporan/laporan-siswa', $data);
   //    }

   //    return view('admin/generate-laporan/laporan-siswa', $data) . view('admin/generate-laporan/topdf');
   // }


   // public function generateLaporanGuru()
   // {
   //    $guru = $this->guruModel->getAllGuru();
   //    $type = $this->request->getVar('type');

   //    if (empty($guru)) {
   //       session()->setFlashdata([
   //          'msg' => 'Data guru kosong!',
   //          'error' => true
   //       ]);
   //       return redirect()->to('/admin/laporan');
   //    }

   //    $bulan = $this->request->getVar('tanggalGuru');

   //    // hari pertama dalam 1 bulan
   //    $begin = new DateTime($bulan);
   //    // tanggal terakhir dalam 1 bulan
   //    $end = (new DateTime($begin->format('Y-m-t')))->modify('+1 day');
   //    // interval 1 hari
   //    $interval = DateInterval::createFromDateString('1 day');
   //    // buat array dari semua hari di bulan
   //    $period = new DatePeriod($begin, $interval, $end);

   //    $arrayTanggal = [];
   //    $dataAbsen = [];

   //    foreach ($period as $value) {
   //       // kecualikan hari sabtu dan minggu
   //       if (!($value->format('D') == 'Sat' || $value->format('D') == 'Sun')) {
   //          $lewat = Time::parse($value->format('Y-m-d'))->isAfter(Time::today());

   //          $absenByTanggal = $this->presensiGuruModel
   //             ->getPresensiByTanggal($value->format('Y-m-d'));

   //          $absenByTanggal['lewat'] = $lewat;

   //          array_push($dataAbsen, $absenByTanggal);
   //          array_push($arrayTanggal, $value);
   //       }
   //    }

   //    $laki = 0;

   //    foreach ($guru as $value) {
   //       if ($value['jenis_kelamin'] != 'Perempuan') {
   //          $laki++;
   //       }
   //    }

   //    $data = [
   //       'tanggal' => $arrayTanggal,
   //       'bulan' => $begin->format('F'),
   //       'listAbsen' => $dataAbsen,
   //       'listGuru' => $guru,
   //       'jumlahGuru' => [
   //          'laki' => $laki,
   //          'perempuan' => count($guru) - $laki
   //       ],
   //       'grup' => 'guru'
   //    ];

   //    if ($type == 'doc') {
   //       $this->response->setHeader('Content-type', 'application/vnd.ms-word');
   //       $this->response->setHeader('Content-Disposition', 'attachment;Filename=laporan_absen_guru_' . $begin->format('F-Y') . '.doc');

   //       return view('admin/generate-laporan/laporan-guru', $data);
   //    }

   //    return view('admin/generate-laporan/laporan-guru', $data) . view('admin/generate-laporan/topdf');
   // }
}
