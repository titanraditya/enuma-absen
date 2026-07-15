<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>user" class="btn btn-default btn-sm float-right"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-user-plus mr-2 text-muted"></i>Tambah Anggota</h4>
                </div>
                <form method="post" action="<?=base_url()?>user/add">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-section-title"><i class="fas fa-address-card"></i> Data Anggota</div>
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control<?= session()->getFlashdata('errname') ? ' is-invalid' : ''; ?>" placeholder="Masukkan nama lengkap" autocomplete="off" value="<?=old('name')?>" required>
                                    <div class="invalid-feedback">
                                        <?= esc(session()->getFlashdata('errname')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Grup</label>
                                    <select name="category" class="form-control select2" required width="100%">
                                        <option value="">- Pilih Grup -</option>
                                        <?php foreach($category as $category): ?>
                                            <option value="<?=esc($category['id'])?>" <?php if(old('category') == $category['id']) { echo "selected";}?>><?=esc($category['name'])?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1" <?php if(old('status') == '1') { echo "selected";}?>>Aktif</option>
                                        <option value="0" <?php if(old('status') == '0') { echo "selected";}?>>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label>ID Fingerprint <span class="form-hint font-weight-normal">(opsional)</span></label>
                                    <input type="text" name="id_fingerprint" class="form-control<?= session()->getFlashdata('erridfinger') ? ' is-invalid' : ''; ?>" placeholder="Kosongkan jika tidak dipakai" value="<?=esc(old('id_fingerprint'))?>" autocomplete='off'/>
                                    <div class="invalid-feedback">
                                        <?= esc(session()->getFlashdata('erridfinger')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12 mt-md-0 mt-4">
                                <div class="form-section-title"><i class="fas fa-key"></i> Akses Aplikasi</div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input class="custom-control-input" type="checkbox" id="canlogin" onchange="loginform()" name="can_login" value="1" <?php if(old('can_login') == '1') { echo "checked";}?>>
                                        <label for="canlogin" class="custom-control-label">Bisa login ke aplikasi</label>
                                    </div>
                                    <small class="form-hint d-block mt-1">Aktifkan agar anggota bisa masuk dan mengisi log absensi online sendiri.</small>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control<?= session()->getFlashdata('errusername') ? ' is-invalid' : ''; ?>" placeholder="Masukkan username" id="username" name="username" autocomplete="off" disabled value="<?=esc(old('username'))?>"/>
                                    <div class="invalid-feedback">
                                        <?= esc(session()->getFlashdata('errusername')); ?>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label>Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control<?= session()->getFlashdata('errpassword') ? ' is-invalid' : ''; ?>" placeholder="Masukkan password" id="password" name="password" autocomplete="off" disabled value="<?=esc(old('password'))?>"/>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-toggle-pass" data-target="password" tabindex="-1"><i class="fas fa-eye"></i></button>
                                        </div>
                                        <div class="invalid-feedback">
                                            <?= esc(session()->getFlashdata('errpassword')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="<?=base_url()?>user" class="btn btn-default mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Simpan Anggota</button>
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
        loginform();
    });

    $('.select2').select2({
        theme: 'bootstrap4'
    })

    function loginform() {
        var canLogin = $('#canlogin').is(':checked');
        $("#username, #password").prop('disabled', !canLogin);
        $(".btn-toggle-pass").prop('disabled', !canLogin);
    }

    $('.btn-toggle-pass').on('click', function () {
        var input = $('#' + $(this).data('target'));
        var show = input.attr('type') === 'password';
        input.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye', !show).toggleClass('fa-eye-slash', show);
    });

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
