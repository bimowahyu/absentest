<?php

namespace App\Controllers\Admin;

use App\Models\JabatanModel;

use App\Models\KaryawanModel;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\PresensiKaryawanModel;
use CodeIgniter\I18n\Time;

class DataAbsenKaryawan extends BaseController
{
   protected JabatanModel $jabatanModel;

   protected KaryawanModel $karyawanModel;

   protected KehadiranModel $kehadiranModel;

   protected PresensiKaryawanModel $presensiKaryawan;

   protected string $currentDate;

   public function __construct()
   {
      $this->currentDate = Time::today()->toDateString();

      $this->karyawanModel = new KaryawanModel();

      $this->kehadiranModel = new KehadiranModel();

      $this->jabatanModel = new JabatanModel();

      $this->presensiKaryawan = new PresensiKaryawanModel();
   }

   public function index()
   {
      $jabatan = $this->jabatanModel->getAllJabatan();

      $data = [
         'title' => 'Data Absen Karyawan',
         'ctx' => 'absen-kayawan',
         'jabatan' => $jabatan
      ];

      return view('admin/absen/absen-karyawan', $data);
   }

   public function ambilDataKaryawan()
   {
      // ambil variabel POST
      $jabatan = $this->request->getVar('jabatan');
      $idJabatan = $this->request->getVar('id_jabatan');
      $tanggal = $this->request->getVar('tanggal');

      $lewat = Time::parse($tanggal)->isAfter(Time::today());

      $result = $this->presensiKaryawan->getPresensiByJabatanTanggal($idJabatan, $tanggal);

      $data = [
         'jabatan' => $jabatan,
         'data' => $result,
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'lewat' => $lewat
      ];

      return view('admin/absen/list-ab', $data);
   }

   public function ambilKehadiran()
   {
      $idPresensi = $this->request->getVar('id_presensi');
      $idKaryawan = $this->request->getVar('id_karyawan');

      $data = [
         'presensi' => $this->presensiKaryawan->getPresensiById($idPresensi),
         'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
         'data' => $this->karyawanModel->getKaryawanById($idKaryawan)
      ];

      return view('admin/absen/ubah-kehadiran-modal', $data);
   }

   public function ubahKehadiran()
   {
      // ambil variabel POST
      $idKehadiran = $this->request->getVar('id_kehadiran');
      $idKaryawan = $this->request->getVar('id_karyawan');
      $idJabatan = $this->request->getVar('id_jabatan');
      $tanggal = $this->request->getVar('tanggal');
      $jamMasuk = $this->request->getVar('jam_masuk');
      $jamKeluar = $this->request->getVar('jam_keluar');
      $keterangan = $this->request->getVar('keterangan');

      $cek = $this->presensiKaryawan->cekAbsen($idKaryawan, $tanggal);

      $result = $this->presensiKaryawan->updatePresensi(
         $cek == false ? NULL : $cek,
         $idKaryawan,
         $idJabatan,
         $tanggal,
         $idKehadiran,
         $jamMasuk ?? NULL,
         $jamKeluar ?? NULL,
         $keterangan
      );

      $response['nama_karyawan'] = $this->karyawanModel->getKaryawanById($idKaryawan)['nama_karyawan'];

      if ($result) {
         $response['status'] = TRUE;
      } else {
         $response['status'] = FALSE;
      }

      return $this->response->setJSON($response);
   }
}
