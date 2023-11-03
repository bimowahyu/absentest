<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
         'nik',
         'nama_karyawan',
         'id_jabatan',
         'jenis_kelamin',
         'no_hp',
         'unique_code'
      ];
   }

   protected $table = 'tb_karyawan';

   protected $primaryKey = 'id_karyawan';

   public function cekKaryawan(string $unique_code)
   {
      $this->join(
         'tb_jabatan',
         'tb_jabatan.id_jabatan = tb_karyawan.id_jabatan',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getKaryawanById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllKaryawanWithJabatan($jabatan = null, $posisi = null)
   {
      $query = $this->join(
         'tb_jabatan',
         'tb_jabatan.id_jabatan = tb_karyawan.id_jabatan',
         'LEFT'
      )->join(
         'tb_posisi',
         'tb_jabatan.id_posisi = tb_posisi.id',
         'LEFT'
      );

      if (!empty($jabatan) && !empty($posisi)) {
         $query = $this->where(['jabatan' => $jabatan, 'posisi' => $posisi]);
      } else if (empty($jabatan) && !empty($posisi)) {
         $query = $this->where(['posisi' => $posisi]);
      } else if (!empty($jabatan) && empty($posisi)) {
         $query = $this->where(['jabatan' => $jabatan]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_karyawan')->findAll();
   }

   public function getKaryawanByJabatan($id_jabatan)
   {
      return $this->join(
         'tb_jabatan',
         'tb_jabatan.id_jabatan = tb_karyawan.id_jabatan',
         'LEFT'
      )
         ->join('tb_posisi', 'tb_jabatan.id_posisi = tb_posisi.id', 'left')
         ->where(['tb_karyawan.id_jabatan' => $id_jabatan])->findAll();
   }

   public function saveKaryawan($idKaryawan, $nik, $namaKaryawan, $idJabatan, $jenisKelamin, $noHp)
   {
      return $this->save([
         $this->primaryKey => $idKaryawan,
         'nik' => $nik,
         'nama_karyawan' => $namaKaryawan,
         'id_jabatan' => $idJabatan,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'unique_code' => sha1($namaKaryawan . md5($nik . $noHp . $namaKaryawan)) . substr(sha1($nik . rand(0, 100)), 0, 24)
      ]);
   }
}
