<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
    <?php if(session('user_role') == 'admin') {?>
        <a href="<?=base_url()?>category/add" class="btn btn-primary btn-sm float-right"><i class="fas fa-plus"></i> Tambah</a>
    <?php } ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <?php $isAdmin = session('user_role') == 'admin'; ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h4 class="card-title">Daftar Grup</h4>
                </div>
                <div class="card-body">
                    <?php if($isAdmin): ?>
                    <div class="form-row mb-3">
                        <div class="form-group col-md-4 mb-2 mb-md-0">
                            <label for="filter-jenis" class="small font-weight-bold mb-1">Jenis Grup</label>
                            <select id="filter-jenis" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="biasa">Grup Biasa</option>
                                <option value="pengawas">Grup Pengawas</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-2 mb-md-0">
                            <label for="filter-pengawas" class="small font-weight-bold mb-1">Pengawas</label>
                            <select id="filter-pengawas" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="__none__">Belum ada pengawas</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-0">
                            <label for="filter-anggota" class="small font-weight-bold mb-1">Anggota</label>
                            <select id="filter-anggota" class="form-control form-control-sm">
                                <option value="">Semua</option>
                                <option value="ada">Ada anggota</option>
                                <option value="kosong">Kosong</option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="category-table" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="50px">#</th>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <?php if($isAdmin): ?>
                                        <th>Pengawas</th>
                                    <?php endif; ?>
                                    <th width="130px">Anggota</th>
                                    <th width="190px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($category as $category): ?>
                                <tr data-jenis="<?=$category['overseer'] == '1' ? 'pengawas' : 'biasa'?>"
                                    data-pengawas="<?=esc($category['pengawas_names'] ?? '')?>"
                                    data-anggota="<?=esc($category['total'])?>">
                                    <td><?=$no?></td>
                                    <td class="font-weight-bold">
                                        <?=esc($category['name'])?>
                                        <?php if($category['overseer'] == '1'): ?>
                                            <span class="badge badge-success ml-1"><i class="fas fa-user-shield mr-1"></i>Grup Pengawas</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(trim((string) $category['description']) !== ''): ?>
                                            <?=esc($category['description'])?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if($isAdmin): ?>
                                        <td>
                                            <?php if(!empty($category['pengawas_names'])): ?>
                                                <?=esc($category['pengawas_names'])?>
                                            <?php else: ?>
                                                <span class="text-muted"><i>Belum ada</i></span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td><span class="badge badge-primary"><i class="fas fa-users mr-1"></i><?=esc($category['total'])?> orang</span></td>
                                    <td>
                                        <a href="<?=base_url()?>category/detail/<?=esc($category['id'])?>" class="btn btn-info btn-sm text-white"><i class="fas fa-eye"></i> Detail</a>
                                        <a href="<?=base_url()?>category/logs/<?=esc($category['id'])?>" class="btn btn-secondary btn-sm"><i class="fas fa-clipboard-list"></i> Log</a>
                                    </td>
                                </tr>
                                <?php $no++; endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php $this->endSection(); ?>

<?php $this->section('modal'); ?>
  
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
    <script src="<?=base_url()?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        var isAdmin = <?=$isAdmin ? 'true' : 'false'?>;
        $(document).ready(function() {
            var table = $('#category-table').DataTable({
                columnDefs: [
                    { orderable: false, targets: [isAdmin ? 5 : 4] }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ grup",
                    infoFiltered: "(difilter dari _MAX_ total grup)",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Tidak ada grup yang cocok",
                    emptyTable: "Belum ada grup",
                    paginate: { previous: "&laquo;", next: "&raquo;" }
                }
            });

            if (isAdmin) {
                var namaPengawas = {};
                table.rows().nodes().each(function(tr) {
                    var val = $(tr).attr('data-pengawas');
                    if (val) {
                        val.split(', ').forEach(function(nama) {
                            namaPengawas[nama] = true;
                        });
                    }
                });
                Object.keys(namaPengawas).sort().forEach(function(nama) {
                    $('#filter-pengawas').append($('<option>').val(nama).text(nama));
                });

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (settings.nTable.id !== 'category-table') return true;
                    var $tr = $(table.row(dataIndex).node());

                    var jenis = $('#filter-jenis').val();
                    if (jenis && $tr.attr('data-jenis') !== jenis) return false;

                    var pengawas = $('#filter-pengawas').val();
                    if (pengawas) {
                        var daftar = $tr.attr('data-pengawas') || '';
                        if (pengawas === '__none__') {
                            if (daftar !== '') return false;
                        } else if (daftar.split(', ').indexOf(pengawas) === -1) {
                            return false;
                        }
                    }

                    var anggota = $('#filter-anggota').val();
                    var total = parseInt($tr.attr('data-anggota'), 10) || 0;
                    if (anggota === 'ada' && total === 0) return false;
                    if (anggota === 'kosong' && total !== 0) return false;

                    return true;
                });

                $('#filter-jenis, #filter-pengawas, #filter-anggota').on('change', function() {
                    table.draw();
                });
            }
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