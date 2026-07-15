<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>category" class="btn btn-default btn-sm float-right"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-users mr-2 text-muted"></i>Tambah Grup</h4>
                </div>
                <form method="post" action="<?=base_url()?>category/add">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-section-title"><i class="fas fa-info-circle"></i> Informasi Grup</div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control<?= session()->getFlashdata('errname') ? ' is-invalid' : ''; ?>" placeholder="Contoh: Divisi Marketing" autocomplete="off" value="<?=old('name')?>" required>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errname')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" rows="3" class="form-control<?= session()->getFlashdata('errdesc') ? ' is-invalid' : ''; ?>" placeholder="Jelaskan singkat fungsi atau anggota grup ini" required><?=old('description')?></textarea>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errdesc')); ?>
                            </div>
                        </div>
                        <div class="form-section-title mt-4"><i class="fas fa-user-shield"></i> Peran Grup</div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input class="custom-control-input" type="checkbox" id="makeoverseer" name="overseer" value="1" <?php if(old('overseer') == '1') { echo "checked";}?>>
                                <label for="makeoverseer" class="custom-control-label">Jadikan grup pengawas</label>
                            </div>
                            <small class="form-hint d-block mt-1">Anggota grup ini akan muncul di daftar pilihan pengawas dan bisa ditugaskan memantau grup lain.</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="<?=base_url()?>category" class="btn btn-default mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Simpan Grup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
  <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
        });

    <?php if(session()->getFlashdata('error')): ?>
    Toast.fire({
        icon: 'error',
        title: '<?=esc(session()->getFlashdata('error'))?>'
      })
    <?php endif; ?>
  </script>
<?php $this->endSection(); ?>
