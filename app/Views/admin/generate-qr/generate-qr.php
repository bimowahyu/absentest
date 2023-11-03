<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<style>
   .progress-karyawan {
      height: 5px;
      border-radius: 0px;
      background-color: rgb(186, 124, 222);
   }

   .progress-guru {
      height: 5px;
      border-radius: 0px;
      background-color: rgb(58, 192, 85);
   }

   .my-progress-bar {
      height: 5px;
      border-radius: 0px;
   }
</style>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-header card-header-danger">
                  <h4 class="card-title"><b>Generate QR Code</b></h4>
                  <p class="card-category">Generate QR berdasarkan kode unik data karyawan</p>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="card">
                           <div class="card-body">
                              <h4 class="text-primary"><b>Data Karyawan</b></h4>
                              <p>Total jumlah karyawan : <b><?= count($karyawan); ?></b>
                                 <br>
                                 <a href="<?= base_url('admin/karyawan'); ?>">Lihat data</a>
                              </p>
                              <button onclick="generateAllQrKaryawan()" class="btn btn-primary pl-3 py-4">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 64px;">qr_code</i>
                                    </div>
                                    <div class="col">
                                       <h3 class="d-inline">Generate All</h3>
                                       <div id="progressKaryawan" class="d-none">
                                          <span id="progressTextKaryawan"></span>
                                          <i id="progressSelesaiKaryawan" class="material-icons d-none" class="d-none">check</i>
                                          <div class="progress progress-karyawan">
                                             <div id="progressBarKaryawan" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                              <hr>
                              <br>
                              <h4 class="text-primary"><b>Generate per jabatan</b></h4>
                              <select name="id_jabatan" id="kelasSelect" class="custom-select mb-3">
                                 <option value="">--Pilih jabatan--</option>
                                 <?php foreach ($jabatan as $value) : ?>
                                    <option id="idJabatan<?= $value['id_jabatan']; ?>" value="<?= $value['id_jabatan']; ?>">
                                       <?= $value['jabatan'] . ' ' . $value['posisi']; ?>
                                    </option>
                                 <?php endforeach; ?>
                              </select>
                              <button onclick="generateQrKaryawanByJabatan()" class="btn btn-primary pl-3">
                                 <div class="row align-items-center">
                                    <div class="col">
                                       <i class="material-icons" style="font-size: 32px;">qr_code</i>
                                    </div>
                                    <div class="col">
                                       <div class="text-start">
                                          <h4 class="d-inline">Generate per jabatan</h4>
                                       </div>
                                       <div id="progressKelas" class="d-none">
                                          <span id="progressTextKelas"></span>
                                          <i id="progressSelesaiKelas" class="material-icons d-none" class="d-none">check</i>
                                          <div class="progress progress-karyawan d-none" id="progressBarBgKelas">
                                             <div id="progressBarKelas" class="progress-bar my-progress-bar bg-white" style="width: 0%;" role="progressbar" aria-valuenow="" aria-valuemin="" aria-valuemax=""></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </button>
                              <br>
                              <div class="text-danger mt-2" id="textErrorKelas"><b></b></div>
                              <!-- <br>
                              <p>Untuk generate qr code per masing-masing siswa kunjungi <a href="<?= base_url('admin/karyawan'); ?>">data siswa</a></p> -->
                           </div>
                        </div>
                     </div>
                    
                        <p class="text-danger"><i class="material-icons" style="font-size: 16px;">warning</i> File image QR Code tersimpan di [folder website]/public/uploads/</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   

   const dataKaryawan = [
      <?php foreach ($karyawan as $value) {
         echo "{
                  'nama' : '$value[nama_karyawan]',
                  'unique_code' : '$value[unique_code]',
                  'jabatan' : '$value[jabatan] $value[posisi]',
                  'nomor' :'$value[nik]'
               },";
      }; ?>
   ];

   var dataKaryawanPerJabatan = [];

   function generateAllQrKaryawan() {
      var i = 1;
      $('#progressKaryawan').removeClass('d-none');
      $('#progressBarKaryawan')
         .attr('aria-valuenow', '0')
         .attr('aria-valuemin', '0')
         .attr('aria-valuemax', dataKaryawan.length)
         .attr('style', 'width: 0%;');

      dataKaryawan.forEach(element => {
         jQuery.ajax({
            url: "<?= base_url('admin/generate/karyawan'); ?>",
            type: 'post',
            data: {
               nama: element['nama'],
               unique_code: element['unique_code'],
               jabatan: element['jabatan'],
               nomor: element['nomor']
            },
            success: function(response) {
               if (i != dataKaryawan.length) {
                  $('#progressTextKaryawan').html('Progres: ' + i + '/' + dataKaryawan.length);
               } else {
                  $('#progressTextKaryawan').html('Progres: ' + i + '/' + dataKaryawan.length + ' selesai');
                  $('#progressSelesaiKaryawan').removeClass('d-none');
               }

               $('#progressBarKaryawan')
                  .attr('aria-valuenow', i)
                  .attr('style', 'width: ' + (i / dataKaryawan.length) * 100 + '%;');
               i++;
            }
         });
      });
   }

   function generateQrKaryawanByJabatan() {
      var i = 1;

      idJabatan = $('#jabatanSelect').val();

      if (idJabatan == '') {
         $('#progressJabatan').addClass('d-none');
         $('#textErrorJabatan').html('Pilih jabatan terlebih dahulu');
         return;
      }

      jabatan = $('#idJabatan' + idJabatan).html();

      jQuery.ajax({
         url: "<?= base_url('admin/generate/karyawan-by-posisi'); ?>",
         type: 'post',
         data: {
            idJabatan: idJabatan
         },
         success: function(response) {
            dataKaryawanPerJabatan = response;

            if (dataKaryawanPerJabatan.length < 1) {
               $('#progressJabatan').addClass('d-none');
               $('#textErrorJabatan').html('Data karyawan ' + jabatan + ' tidak ditemukan');
               return;
            }

            $('#textErrorKelas').html('')

            $('#progressKelas').removeClass('d-none');
            $('#progressBarBgJabatan')
               .removeClass('d-none');
            $('#progressBarJabatan')
               .removeClass('d-none')
               .attr('aria-valuenow', '0')
               .attr('aria-valuemin', '0')
               .attr('aria-valuemax', dataKaryawanPerJabatan.length)
               .attr('style', 'width: 0%;');

            dataKaryawanPerJabatan.forEach(element => {
               jQuery.ajax({
                  url: "<?= base_url('admin/generate/karyawan'); ?>",
                  type: 'post',
                  data: {
                     nama: element['nama_karyawan'],
                     unique_code: element['unique_code'],
                     jabatan: element['jabatan'] + ' ' + element['posisi'],
                     nomor: element['nik']
                  },
                  success: function(response) {
                     if (i != dataKaryawanPerJabatan.length) {
                        $('#progressTextJabatan').html('Progres: ' + i + '/' + dataKaryawanPerJabatan.length);
                     } else {
                        $('#progressTextJabatan').html('Progres: ' + i + '/' + dataKaryawanPerJabatan.length + ' selesai');
                        $('#progressSelesaiJabatan').removeClass('d-none');
                     }

                     $('#progressBarJabatan')
                        .attr('aria-valuenow', i)
                        .attr('style', 'width: ' + (i / dataKaryawanPerJabatan.length) * 100 + '%;');
                     i++;
                  },
                  error: function(xhr, status, thrown) {
                     console.log(xhr + status + thrown);
                  }
               });
            });
         }
      });
   }

  
</script>
<?= $this->endSection() ?>