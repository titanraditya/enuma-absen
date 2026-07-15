<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-lg-4 col-md-5 col-12">
            <div class="card profile-card">
                <div class="card-body">
                    <div class="profile-avatar"><?= strtoupper(substr($user['name'] ?? '?', 0, 1)); ?></div>
                    <div class="profile-name"><?= esc($user['name']) ?></div>
                    <div class="profile-username">@<?= esc($user['username']) ?></div>
                    <span class="profile-role"><i class="fas fa-id-badge mr-1"></i><?= ucfirst(session('user_role')) ?></span>

                    <ul class="profile-detail">
                        <li>
                            <span class="pd-icon"><i class="fas fa-user"></i></span>
                            <span>
                                <span class="pd-label">Nama Lengkap</span>
                                <span class="pd-value"><?= esc($user['name']) ?></span>
                            </span>
                        </li>
                        <li>
                            <span class="pd-icon"><i class="fas fa-layer-group"></i></span>
                            <span>
                                <span class="pd-label">Grup</span>
                                <span class="pd-value"><?= esc($user['category_name'] ?? '—') ?></span>
                            </span>
                        </li>
                        <li>
                            <span class="pd-icon"><i class="fas fa-at"></i></span>
                            <span>
                                <span class="pd-label">Username</span>
                                <span class="pd-value"><?= esc($user['username']) ?></span>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-key mr-2 text-muted"></i>Ganti Password</h4>
                </div>
                <form action="<?=base_url()?>profile/changepass" method="POST">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <p class="text-muted mb-4">Gunakan password yang kuat dan tidak dipakai di aplikasi lain. Setelah diganti, gunakan password baru saat login berikutnya.</p>
                        <div class="form-group">
                            <label>Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control<?= session()->getFlashdata('erroldpassword') ? ' is-invalid' : ''; ?>" name="old_password" id="old_password" placeholder="Masukkan password lama" required value="<?=old('old_password')?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-toggle-pass" data-target="old_password" tabindex="-1"><i class="fas fa-eye"></i></button>
                                </div>
                                <div class="invalid-feedback">
                                    <?= esc(session()->getFlashdata('erroldpassword')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control<?= session()->getFlashdata('errnewpassword') ? ' is-invalid' : ''; ?>" name="new_password" id="new_password" placeholder="Masukkan password baru" required value="<?=old('new_password')?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-toggle-pass" data-target="new_password" tabindex="-1"><i class="fas fa-eye"></i></button>
                                </div>
                                <div class="invalid-feedback">
                                    <?= esc(session()->getFlashdata('errnewpassword')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Simpan Password Baru</button>
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
    <?php if(session()->getFlashdata('success')): ?>
    Toast.fire({
        icon: 'success',
        title: '<?=esc(session()->getFlashdata('success'))?>'
      })
    <?php endif; ?>

    $('.btn-toggle-pass').on('click', function () {
        var input = $('#' + $(this).data('target'));
        var show = input.attr('type') === 'password';
        input.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye', !show).toggleClass('fa-eye-slash', show);
    });
  </script>
<?php $this->endSection(); ?>
