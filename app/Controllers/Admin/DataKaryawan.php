<?php

namespace App\Controllers\Admin;

use App\Models\KaryawanModel;
use App\Models\JabatanModel;

use App\Controllers\BaseController;
use App\Models\PosisiModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataKaryawan extends BaseController
{
   protected KaryawanModel $karyawanModel;
   protected JabatanModel $jabatanModel;
   protected PosisiModel $posisiModel;

   protected $karyawanValidationRules = [
      'nik' => [
         'rules' => 'required|max_length[20]|min_length[4]',
         'errors' => [
            'required' => 'NIK harus diisi.',
            'is_unique' => 'NIK ini telah terdaftar.',
            'min_length[4]' => 'Panjang NIK minimal 4 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_jabatan' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Kelas harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->karyawanModel = new KaryawanModel();
      $this->jabatanModel = new JabatanModel();
      $this->posisiModel = new PosisiModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Karyawan',
         'ctx' => 'karyawan',
         'jabatan' => $this->jabatanModel->getAllJabatan(),
         'posisi' => $this->posisiModel->findAll()
      ];

      return view('admin/data/data-karyawan', $data);
   }

   public function ambilDataKaryawan()
   {
      $jabatan = $this->request->getVar('jabatan') ?? null;
      $posisi = $this->request->getVar('posisi') ?? null;

      $result = $this->karyawanModel->getAllKaryawanWithJabatan($jabatan, $posisi);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-karyawan', $data);
   }

   public function formTambahKaryawan()
   {
      $jabatan = $this->jabatanModel->getAllJabatan();

      $data = [
         'ctx' => 'karyawan',
         'jabatan' => $jabatan,
         'title' => 'Tambah Data karyawan'
      ];

      return view('admin/data/create/create-data-karyawan', $data);
   }

   public function saveKaryawan()
   {
      // validasi
      if (!$this->validate($this->karyawanValidationRules)) {
         $jabatan = $this->jabatanModel->getAllJabatan();

         $data = [
            'ctx' => 'karyawan',
            'jabatan' => $jabatan,
            'title' => 'Tambah Data Karyawan',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-karyawan', $data);
      }

      $nik = $this->request->getVar('nik');
      $namaKaryawan = $this->request->getVar('nama');
      $idJabatan = intval($this->request->getVar('id_jabatan'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->karyawanModel->saveKaryawan(NULL, $nik, $namaKaryawan, $idJabatan, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan/create');
   }

   public function formEditKaryawan($id)
   {
      $karyawan = $this->karyawanModel->getKaryawanById($id);
      $jabatan = $this->jabatanModel->getAllJabatan();

      if (empty($karyawan) || empty($jabatan)) {
         throw new PageNotFoundException('Data karyawan dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $karyawan,
         'jabatan' => $jabatan,
         'ctx' => 'karyawan',
         'title' => 'Edit Karyawan',
      ];

      return view('admin/data/edit/edit-data-karyawan', $data);
   }

   public function updateKaryawan()
   {
      $idKaryawan = $this->request->getVar('id');

      $karyawanLama = $this->karyawanModel->getKaryawanById($idKaryawan);

      if ($karyawanLama['nik'] != $this->request->getVar('nik')) {
         $this->karyawanValidationRules['nik']['rules'] = 'required|max_length[20]|min_length[4]|is_unique[tb_karyawan.nik]';
      }

      // validasi
      if (!$this->validate($this->karyawanValidationRules)) {
         $karyawan = $this->karyawanModel->getKaryawanById($idKaryawan);
         $jabatan = $this->jabatanModel->getAllJabatan();

         $data = [
            'data' => $karyawan,
            'jabatan' => $jabatan,
            'ctx' => 'karyawan',
            'title' => 'Edit Karyawan',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-karyawan', $data);
      }

      $nik = $this->request->getVar('nik');
      $namaKaryawan = $this->request->getVar('nama');
      $idJabatan = intval($this->request->getVar('id_jabatan'));
      $jenisKelamin = $this->request->getVar('jk');
      $noHp = $this->request->getVar('no_hp');

      $result = $this->karyawanModel->saveKaryawan($idKaryawan, $nik, $namaKaryawan, $idJabatan, $jenisKelamin, $noHp);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan/edit/' . $idKaryawan);
   }

   public function delete($id)
   {
      $result = $this->karyawanModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/karyawan');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/karyawan');
   }
}
