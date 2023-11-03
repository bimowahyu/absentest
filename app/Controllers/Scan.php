<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\KaryawanModel;
use App\Models\PresensiKaryawanModel;
use App\Models\TipeUser;

class Scan extends BaseController
{
   protected KaryawanModel $karyawanModel;
   // protected GuruModel $guruModel;

   protected PresensiKaryawanModel $presensiKaryawanModel;
   // protected PresensiGuruModel $presensiGuruModel;

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
     // $this->guruModel = new GuruModel();
      $this->presensiKaryawanModel = new PresensiKaryawanModel();
     // $this->presensiGuruModel = new PresensiGuruModel();
   }

   public function index($t = 'Masuk')
   {
      $data = ['waktu' => $t, 'title' => 'Absensi Karyawan Berbasis QR Code'];
      return view('scan/scan', $data);
   }

   public function cekKode()
   { 
      // ambil variabel POST
      $uniqueCode = $this->request->getVar('unique_code');
      log_message('info', 'Nilai uniqueCode: ' . $uniqueCode);
      $waktuAbsen = $this->request->getVar('waktu');

      $status = false;
      $type = TipeUser::Karyawan;

      // cek data siswa di database
      $result = $this->karyawanModel->cekKaryawan($uniqueCode);

      

      // if (!$status) { // data tidak ditemukan
      //    return $this->showErrorView('Data tidak ditemukan');
      // }

      // jika data ditemukan
      switch ($waktuAbsen) {
         case 'masuk':
            return $this->absenMasuk($type, $result);
            break;

         case 'pulang':
            return $this->absenPulang($type, $result);
            break;

         default:
            return $this->showErrorView('Data tidak valid');
            break;
      }
   }



   public function absenMasuk($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'masuk';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen masuk
      switch ($type) {
        case TipeUser::Karyawan:
            $idKaryawan =  $result['id_karyawan'];
            $idJabatan =  $result['id_jabatan'];
            $data['type'] = TipeUser::Karyawan;

            $sudahAbsen = $this->presensiKaryawanModel->cekAbsen($idKaryawan, Time::today()->toDateString());

            if ($sudahAbsen != false) {
               $data['presensi'] = $this->presensiKaryawanModel->getPresensiById($sudahAbsen);
               return $this->showErrorView('Anda sudah absen hari ini', $data);
            }

            $this->presensiKaryawanModel->absenMasuk($idKaryawan, $date, $time, $idJabatan);

            $data['presensi'] = $this->presensiKaryawanModel->getPresensiByIdKaryawanTanggal($idKaryawan, $date);

            return view('scan/scan-result', $data);

         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function absenPulang($type, $result)
   {
      // data ditemukan
      $data['data'] = $result;
      $data['waktu'] = 'pulang';

      $date = Time::today()->toDateString();
      $time = Time::now()->toTimeString();

      // absen pulang
      switch ($type) {
        

         case TipeUser::Karyawan:
            $idKaryawan =  $result['id_karyawan'];
            $data['type'] = TipeUser::Karyawan;

            $sudahAbsen = $this->presensiKaryawanModel->cekAbsen($idKaryawan, $date);

            if ($sudahAbsen == false) {
               return $this->showErrorView('Anda belum absen hari ini', $data);
            }

            $this->presensiKaryawanModel->absenKeluar($sudahAbsen, $time);

            $data['presensi'] = $this->presensiKaryawanModel->getPresensiById($sudahAbsen);

            return view('scan/scan-result', $data);
         default:
            return $this->showErrorView('Tipe tidak valid');
      }
   }

   public function showErrorView(string $msg = 'no error message', $data = NULL)
   {
      $errdata = $data ?? [];
      $errdata['msg'] = $msg;

      return view('scan/error-scan-result', $errdata);
   }
}
