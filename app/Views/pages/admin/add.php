<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>admin" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h2 class="card-title">Tambah Admin</h2>
                </div>
                <form method="post" action="<?=base_url()?>admin/add">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <p class="text-muted">* Wajib Diisi</p>
                        <div class="form-group">
                            <label>Nama *</label>
                            <input type="text" name="name" class="form-control<?= session()->getFlashdata('errname') ? ' is-invalid' : ''; ?>" placeholder="Masukan Nama" autocomplete="off" value="<?=old('name')?>" required>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errname')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" name="username" class="form-control<?= session()->getFlashdata('errusername') ? ' is-invalid' : ''; ?>" placeholder="Masukan Username" autocomplete="off" value="<?=old('username')?>" required/>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errusername')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" name="password" class="form-control<?= session()->getFlashdata('errpassword') ? ' is-invalid' : ''; ?>" placeholder="Masukan Password" autocomplete="off" value="<?=old('password')?>" required />
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errpassword')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Tambah</button>
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