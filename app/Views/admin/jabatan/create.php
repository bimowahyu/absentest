<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title"><b>Form Tambah Jabatan</b></h4>
          </div>
          <div class="card-body mx-5 my-3">

            <form action="<?= base_url('admin/posisi'); ?>" method="post">
              <?= csrf_field() ?>
              <?php $validation = \Config\Services::validation(); ?>

              <?php if (session()->getFlashdata('msg')) : ?>
                <div class="pb-2">
                  <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <i class="material-icons">close</i>
                    </button>
                    <?= session()->getFlashdata('msg') ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="form-group mt-4">
                <label for="kelas">Tingkat</label>
                <input type="text" id="kelas" class="form-control <?= $validation->getError('kelas') ? 'is-invalid' : ''; ?>" name="jabatan" placeholder="'Karyawan', 'Freelance', 'Magang'" , value="<?= old('jabatan') ?? $oldInput['jabatan']  ?? '' ?>" required>
                <div class="invalid-feedback">
                  <?= $validation->getError('kelas'); ?>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <label for="idJurusan">Poaisi</label>
                  <select class="custom-select <?= $validation->getError('idPosisi') ? 'is-invalid' : ''; ?>" id="idPosisi" name="idJurusan">
                    <option value="">--Pilih Posisi--</option>
                    <?php foreach ($jurusan as $value) : ?>
                      <option value="<?= $value['id']; ?>" <?= old('idPosisi') ?? $oldInput['idPosisi'] ?? '' == $value['id'] ? 'selected' : ''; ?>>
                        <?= $value['posisi']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">
                    <?= $validation->getError('idPosisi'); ?>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary mt-4">Simpan</button>
            </form>

            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>