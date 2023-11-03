<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-body">
                  <div class="row justify-content-between">
                     <div class="col">
                        <div class="pt-3 pl-3">
                           <h4><b>Daftar Jabatan</b></h4>
                           <p>Silakan pilih Jabatan</p>
                        </div>
                     </div>
                  </div>

                  <div class="card-body pt-1 px-3">
                     <div class="row">
                     <?php foreach ($jabatan as $value) : ?>
                     <?php
                     $idJabatan = $value['id_jabatan'];
                     $namaJabatan = $value['jabatan'] . ' ' . $value['posisi'];
                    // $idkaryawan = 'ganti_dengan_id_karyawan_yang_anda_ambil'; // Definisikan $idkaryawan di sini atau sesuaikan sesuai kebutuhan Anda
                            ?>
                         <div class="col-md-3">
                             <button id="jabatan-<?= $idJabatan; ?>" onclick="getKaryawan(<?= $idJabatan; ?>, '<?= $namaJabatan; ?>')" class="btn btn-primary w-100">
                       <?= $namaJabatan; ?>
                            </button>
                          </div>
                        <?php endforeach; ?>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-3">
                        <div class="pt-3 pl-3 pb-2">
                           <h4><b>Tanggal</b></h4>
                           <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="onDateChange()">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card" id="dataKaryawan">
         <div class="card-body">
            <div class="row justify-content-between">
               <div class="col-auto me-auto">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen Karyawan</b></h4>
                     <p>Daftar karyawan muncul disini</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal tambah kelas -->
   <div class="modal fade" id="tambahJabatanModal" tabindex="-1" aria-labelledby="tambahJabatanModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <form id="formTambahJabatan" action="#">
               <div class="modal-header">
                  <h5 class="modal-title" id="modalUbahKehadiran">Tambah Data Jabatan</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div class="container-fluid">
                     <div class="form-group mt-2">
                        <label for="jabatan">Jabatan</label>
                        <select class="custom-select" id="jabatan" name="Jabatan" required>
                           <option value="">--Pilih Jabatan--</option>
                           <option value="Karyawan">Karyawan</option>
                           <option value="Freelance">Freelance</option>
                           <option value="Magang">Magang</option>
                        </select>
                     </div>
                     <div class="form-group mt-4">
                        <label for="posisi">posisi</label>
                        <input type="text" id="posisi" class="form-control" name="posisi" placeholder="Posisi" required>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                  <button type="submit" onclick="tambahDataJabatan()" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- Modal ubah kehadiran -->
   <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="modalUbahKehadiran" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalUbahKehadiran">Ubah kehadiran</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="modalFormUbahKaryawan"></div>
         </div>
      </div>
   </div>
</div>
<script>
   var lastIdJabatan;
   var lastJabatan;

   function onDateChange() {
      if (lastIdJabatan != null && lastJabatan != null) getKaryawan(lastIdJabatan, lastJabatan);
   }

   function getKaryawan(idJabatan, jabatan) {
      var tanggal = $('#tanggal').val();

      updateBtn(idJabatan);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-karyawan'); ?>",
         type: 'post',
         data: {
            'jabatan': jabatan,
            'id_jabatan': idJabatan,
            'tanggal': tanggal
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataKaryawan').html(response);

            $('html, body').animate({
               scrollTop: $("#dataKaryawan").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataKaryawan').html(thrown);
         }
      });

      lastIdJabatan = idJabatan;
      lastJabatan = jabatan;
   }

   function updateBtn(id_btn) {
      for (let index = 1; index <= <?= count($jabatan); ?>; index++) {
         if (index != id_btn) {
            $('#jabatan-' + index).removeClass('btn-success');
            $('#jabatan-' + index).addClass('btn-primary');
         } else {
            $('#jabatan-' + index).removeClass('btn-primary');
            $('#jabatan-' + index).addClass('btn-success');
         }
      }
   }

   function getDataKehadiran(idPresensi, idKaryawan) {
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-karyawan/kehadiran'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_karyawan': idKaryawan
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#modalFormUbahKaryawan').html(response);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#modalFormUbahKaryawan').html(thrown);
         }
      });
   }

   function ubahKehadiran() {
      var tanggal = $('#tanggal').val();

      var form = $('#formUbah').serializeArray();

      form.push({
         name: 'tanggal',
         value: tanggal
      });

      console.log(form);

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-karyawan/edit'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getKaryawan(lastIdJabatan, lastJabatan);
               alert('Berhasil ubah kehadiran : ' + response['nama_karyawan']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_karyawan']);
            }
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }

   function tambahDataJabatan() {
      var form = $('#formTambahJabatan').serializeArray();

      jQuery.ajax({
         url: "<?= base_url('/admin/tambah-posisi'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               getKaryawan(lastIdJabatan, lastJabatan);
               alert('Berhasil tambah jabatan : ' + response['jabatan']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['jabatan']);
            }
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal menambah jabatan\n' + thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>