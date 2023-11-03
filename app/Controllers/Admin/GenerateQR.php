<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;


use App\Models\JabatanModel;
use App\Models\KaryawanModel;

class GenerateQR extends BaseController
{
   protected KaryawanModel $karyawanModel;
   protected JabatanModel $jabatanModel;

   // protected GuruModel $guruModel;

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
      $this->jabatanModel = new JabatanModel();

      // $this->guruModel = new GuruModel();
   }

   public function index()
   {
      $karyawan = $this->karyawanModel->getAllKaryawanWithJabatan();
      $jabatan = $this->jabatanModel->getAllJabatan();
      // $guru = $this->guruModel->getAllGuru();

      $data = [
         'title' => 'Generate QR Code',
         'ctx' => 'qr',
         'karyawan' => $karyawan,
         'jabatan' => $jabatan
         // 'guru' => $guru
      ];

      return view('admin/generate-qr/generate-qr', $data);
   }

   public function getKaryawanByJabatan()
   {
      $idJabatan = $this->request->getVar('idJabatan');

      $karyawan = $this->karyawanModel->getKaryawanByJabatan($idJabatan);

      return $this->response->setJSON($karyawan);
   }
}
