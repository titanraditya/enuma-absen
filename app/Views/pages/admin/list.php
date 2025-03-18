<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
  <a href="<?=base_url()?>admin/add" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h4 class="card-title">Daftar Admin</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="admin-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($admin as $key => $admin):?>
                                    <tr>
                                        <td><?=$key + 1?></td>
                                        <td><?=esc($admin['name'])?></td>
                                        <td>
                                            <?php if($admin['id'] != '1'):?>
                                                <a href="<?=base_url()?>admin/edit/<?=esc($admin['id'])?>" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                                                <button onclick="deleteModal('<?=esc($admin['id'])?>');" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php $this->endSection(); ?>

<?php $this->section('modal'); ?>
  <div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Apakah kamu yakin?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Data yang dihapus tidak bisa kembali.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>admin/delete">
            <?=csrf_field();?>
            <input type='hidden' name="id" id="id_delete">
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
    <script src="<?=base_url()?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        function deleteModal($id) {
            $('#deleteModal').modal();
            $('#id_delete').val($id);
        }

        $(document).ready(function() {
            $('#admin-table').DataTable();
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

        <?php if(session()->getFlashdata('success')): ?>
        Toast.fire({
            icon: 'success',
            title: '<?=esc(session()->getFlashdata('success'))?>'
        })
        <?php endif; ?>
    </script>
<?php $this->endSection(); ?>