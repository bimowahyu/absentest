<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\JabatanModel;
use App\Models\PetugasModel;
use App\Models\PresensiKaryawanModel;
use CodeIgniter\I18n\Time;

class Dashboard extends BaseController
{
   protected KaryawanModel $karyawanModel;
   
   protected JabatanModel $jabatanModel;

   protected PresensiKaryawanModel $presensiKaryawanModel;

   protected PetugasModel $petugasModel;

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
      $this->jabatanModel = new JabatanModel();
      $this->presensiKaryawanModel = new PresensiKaryawanModel();
      $this->petugasModel = new PetugasModel();
   }

   public function index()
   {
      $now = Time::now();

      $dateRange = [];
      $karyawanKehadiranArray = [];
      

      for ($i = 6; $i >= 0; $i--) {
         $date = $now->subDays($i)->toDateString();
         if ($i == 0) {
            $formattedDate = "Hari ini";
         } else {
            $t = $now->subDays($i);
            $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
         }
         array_push($dateRange, $formattedDate);
         array_push(
            $karyawanKehadiranArray,
            count($this->presensiKaryawanModel
               ->join('tb_karyawan', 'tb_presensi_karyawan.id_karyawan = tb_karyawan.id_karyawan', 'left')
               ->where(['tb_presensi_karyawan.tanggal' => "$date", 'tb_presensi_karyawan.id_kehadiran' => '1'])->findAll())
         );
        
      }

      $today = $now->toDateString();

      $data = [
         'title' => 'Dashboard',
         'ctx' => 'dashboard',
         'karyawan' => $this->karyawanModel->getAllKaryawanWithJabatan(),
         'jabatan' => $this->jabatanModel->getAllJabatan(),
         'dateRange' => $dateRange,
         'dateNow' => $now->toLocalizedString('d MMMM Y'),
         'grafikKehadiranKaryawan' => $karyawanKehadiranArray,
   
         'jumlahKehadiranKaryawan' => [
            'hadir' => count($this->presensiKaryawanModel->getPresensiByKehadiran('1', $today)),
            'sakit' => count($this->presensiKaryawanModel->getPresensiByKehadiran('2', $today)),
            'izin' => count($this->presensiKaryawanModel->getPresensiByKehadiran('3', $today)),
            'alfa' => count($this->presensiKaryawanModel->getPresensiByKehadiran('4', $today))
         ],
         
         'petugas' => $this->petugasModel->getAllPetugas()
      ];

      return view('admin/dashboard', $data);
   }
}
