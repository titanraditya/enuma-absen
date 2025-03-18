<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-md-6 col-12 mb-3">
            <div class="card h-100">
                <div class="card-header bg-lightblue">
                    <h2 class="card-title">User Profile</h2>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-bold">Nama :</h6>
                    <h6><?=esc($user['name'])?></h6>
                    <h6 class="text-bold mt-4">Grup :</h6>
                    <h6><?=esc($user['category_name'])?></h6>
                    <h6 class="text-bold mt-4">username :</h6>
                    <h6><?=esc($user['username'])?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h2 class="card-title">Ganti Password</h2>
                </div>
                <form action="<?=base_url()?>profile/changepass" method="POST">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" class="form-control<?= session()->getFlashdata('erroldpassword') ? ' is-invalid' : ''; ?>" name="old_password" placeholder="Masukkan password lama" required value="<?=old('old_password')?>">
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('erroldpassword')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" class="form-control<?= session()->getFlashdata('errnewpassword') ? ' is-invalid' : ''; ?>" name="new_password" placeholder="Masukkan password baru" required value="<?=old('new_password')?>">
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errnewpassword')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <input type="submit" class="btn btn-danger" value="Ganti Password">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
  <script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
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
    <?php if(session()->getFlashdata('success')): ?>
    Toast.fire({
        icon: 'success',
        title: '<?=esc(session()->getFlashdata('success'))?>'
      })
    <?php endif; ?>
  </script>
  
<?php $this->endSection(); ?>