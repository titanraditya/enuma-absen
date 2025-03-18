<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>user" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h2 class="card-title">Edit Anggota</h2>
                </div>
                <form method="post" action="<?=base_url()?>user/edit/<?=$user['id']?>">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" name="name" class="form-control<?= session()->getFlashdata('errname') ? ' is-invalid' : ''; ?>" placeholder="Masukan Nama" autocomplete="off" value="<?=esc($user['name'])?>" required>
                                        <div class="invalid-feedback">
                                            <?= esc(session()->getFlashdata('errname')); ?>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="category" class="form-control select2" required>
                                            <option value="">- Pilih Kategori -</option>
                                            <?php foreach($category as $category): ?>
                                                <option value="<?=esc($category['id'])?>" <?php if($user['category_id'] == $category['id']) { echo "selected";}?>><?=esc($category['name'])?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>ID Fingerprint</label>
                                        <input type="text" name="id_fingerprint" class="form-control<?= session()->getFlashdata('erridfinger') ? ' is-invalid' : ''; ?>" placeholder="Masukkan ID Fingerprint" value="<?=esc($user['id_fingerprint'])?>" autocomplete='off'/>
                                        <div class="invalid-feedback">
                                            <?= esc(session()->getFlashdata('erridfinger')); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="1" <?php if($user['active'] == '1') { echo "selected";}?>>Aktif</option>
                                            <option value="0" <?php if($user['active'] == '0') { echo "selected";}?>>Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="canlogin" onchange="loginform()" name="can_login" value="1" <?php if($user['can_login'] == '1') { echo "checked";}?>>
                                            <label for="canlogin" class="custom-control-label">Login</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control<?= session()->getFlashdata('errusername') ? ' is-invalid' : ''; ?>" placeholder="Masukkan Username" id="username" name="username" autocomplete="off" disabled value="<?=esc($user['username'])?>"/>
                                        <div class="invalid-feedback">
                                            <?= esc(session()->getFlashdata('errusername')); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control<?= session()->getFlashdata('errpassword') ? ' is-invalid' : ''; ?>" placeholder="Masukkan Password" id="password" name="password" autocomplete="off" disabled/>
                                        <div class="invalid-feedback">
                                            <?= esc(session()->getFlashdata('errpassword')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
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
    $(document).ready(function() {
        if ($('#canlogin').is(':checked')) {
            $("#username").prop('disabled', false);
            $("#password").prop('disabled', false);
            $("#password").attr('placeholder', 'Biarkan kosong jika tidak ingin mengganti password');
        }
    });

    $('.select2').select2({
        theme: 'bootstrap4'
    })

    function loginform() {
        if ($('#canlogin').is(':checked')) {
            $("#username").prop('disabled', false);
            $("#password").prop('disabled', false);
        } else {
            $("#username").prop('disabled', true);
            $("#password").prop('disabled', true);
        }
    }
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