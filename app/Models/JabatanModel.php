<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
   protected $DBGroup          = 'default';
   protected $useAutoIncrement = true;
   protected $returnType       = 'array';
   protected $useSoftDeletes   = true;
   protected $protectFields    = true;
   protected $allowedFields    = ['jabatan', 'id_posisi'];

   protected $table = 'tb_jabatan';

   protected $primaryKey = 'id_jabatan';

   public function getAllJabatan()
   {
      return $this->join('tb_posisi', 'tb_jabatan.id_posisi = tb_posisi.id', 'left')->findAll();
   }

   public function tambahJabatan($jabatan, $idPosisi)
   {
      return $this->db->table($this->table)->insert([
         'jabatan' => $jabatan,
         'id_posisi' => $idPosisi
      ]);
   }
}
