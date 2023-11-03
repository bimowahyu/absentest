<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')) : ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-success">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/karyawan/create'); ?>">
               <i class="material-icons mr-2">add</i> Tambah data karyawan
            </a>
            <div class="card">
               <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col-md-2">
                           <h4 class="card-title"><b>Daftar Karyawan</b></h4>
                           <p class="card-category">-------</p>
                        </div>
                        <div class="col-md-4">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Posisi:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="posisi = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php
                                 $tempJabatan = [];
                                 foreach ($jabatan as $value) : ?>
                                    <?php if (!in_array($value['jabatan'], $tempJabatan)) : ?>
                                       <li class="nav-item">
                                          <a class="nav-link" onclick="jabatan = '<?= $value['jabatan']; ?>'; trig()" href="#" data-toggle="tab">
                                             <i class="material-icons">-</i> <?= $value['jabatan']; ?>
                                             <div class="ripple-container"></div>
                                          </a>
                                       </li>
                                       <?php array_push($tempJabatan, $value['jabatan']) ?>
                                    <?php endif; ?>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Posisi:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="posisi = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php foreach ($posisi as $value) : ?>
                                    <li class="nav-item">
                                       <a class="nav-link" onclick="posisi = '<?= $value['posisi']; ?>'; trig();" href="#" data-toggle="tab">
                                          <i class="material-icons">work</i> <?= $value['posisi']; ?>
                                          <div class="ripple-container"></div>
                                       </a>
                                    </li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="dataKaryawan">
                  <p class="text-center mt-3">Daftar Karyawan muncul disini</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var jabatan = null;
   var posisi = null;

   getDataKaryawan(jabatan, posisi);

   function trig() {
      getDataKaryawan(jabatan, posisi);
   }

   function getDataKaryawan(_jabatan = null, _posisi = null) {
      jQuery.ajax({
         url: "<?= base_url('/admin/karyawan'); ?>",
         type: 'post',
         data: {
            'jabatan': _jabatan,
            'posisi': _posisi
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
   }
</script>
<?= $this->endSection() ?>