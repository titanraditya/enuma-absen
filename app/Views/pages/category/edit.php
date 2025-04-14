<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>category/detail/<?=esc($category['id'])?>" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h2 class="card-title">Edit Grup</h2>
                </div>
                <form method="post" action="<?=base_url()?>category/edit/<?=esc($category['id'])?>">
                    <?= csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control<?= session()->getFlashdata('errname') ? ' is-invalid' : ''; ?>" placeholder="Nama" autocomplete="off" value="<?=esc($category['name'])?>" required>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errname')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea type="text" name="description" class="form-control<?= session()->getFlashdata('errdesc') ? ' is-invalid' : ''; ?>" placeholder="Deskripsi" required><?=esc($category['description'])?></textarea>
                            <div class="invalid-feedback">
                                <?= esc(session()->getFlashdata('errdesc')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="makeoverseer" onchange="make_overseer()" name="overseer" value="1" <?php if($category['overseer'] == '1') { echo "checked";}?>>
                                <label for="makeoverseer" class="custom-control-label">Jadikan Pengawas</label>
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
  <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
  <script>
    $(document).ready(function() {
        if ($('#makeoverseer').is(':checked')) {
            $('#pengawas').prop('disabled',true)
        }
    });

    function make_overseer() {
        if ($('#makeoverseer').is(':checked')) {
            $('#pengawas').prop('disabled',true)
        } else {
            $('#pengawas').prop('disabled',false)
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
  </script>
  
<?php $this->endSection(); ?>