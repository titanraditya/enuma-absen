<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
    <a href="<?=base_url()?>category" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h4 class="card-title">Detail Grup</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <table>
                                <tr>
                                    <th style="width:150px">Nama Grup</th>
                                    <td style="width:10px">:</td>
                                    <td><?= esc($category['name'])?></td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th> 
                                    <td style="width:10px">:</td>
                                    <td><?= esc($category['description'])?></td>
                                </tr>
                                <tr>
                                    <th>Grub Pengawas</th> 
                                    <td style="width:10px">:</td>
                                    <td><?php if($category['overseer'] == '1'){echo "<span class='badge badge-success'>Ya</span>";} else { echo "<span class='badge badge-secondary'>Tidak</span>";}?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="d-flex justify-content-between mb-2">
                                <p class="my-auto text-bold">Pengawas :</p>
                                <?php if(session('user_role') == 'admin') {?>
                                    <button class="btn btn-primary btn-sm" onclick="addPengawas()"><i class="fas fa-plus"></i> Tambah</button>
                                <?php }?>
                            </div>
                            <?php if($reg_pengawas) {?>
                            <?php foreach($reg_pengawas as $data): ?>
                                <div class="py-2 px-4 mb-2 border rounded shadow-sm justify-content-between d-flex" style="background-color: #dfe3ea;">
                                    <p class="my-auto text-bold"><?=esc($data['name'])?></p>
                                    <?php if(session('user_role') == 'admin') {?>
                                    <button class="btn btn-danger btn-sm" onclick="deletepengawasModal('<?=esc($data['id'])?>');"><i class="fas fa-trash"></i> Hapus</button>
                                    <?php }?>
                                </div>
                            <?php endforeach; } else { echo "<p>Tidak Ada Pengawas</p>";} ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?php if(session('user_role') == 'admin'):?>
                        <a href="<?=base_url()?>category/edit/<?=esc($category['id'])?>" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> Ubah</a>
                        <button onclick="deleteModalKat('<?=esc($category['id'])?>');" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Jadwal</h2>
                    <button onclick="showAdd()" class="btn btn-sm btn-primary float-right"><i class="fas fa-plus"></i> Tambah</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel-jadwal" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($jadwal as $key => $jadwal):?>
                                <tr>
                                    <td><?=$key + 1?></td>
                                    <td><?=esc($jadwal['day'])?></td>
                                    <td><?php if($jadwal['no_limit'] == '1'){echo "<span class='text-muted'>Tanpa Batas Waktu</span>";} else { echo "Jam ".date("H:i", strtotime($jadwal['start_time']))." - Jam ".date("H:i", strtotime($jadwal['end_time']));}?><?php if($jadwal['online'] == '1') { echo " (Online)";}?></td>
                                    <td>
                                        <button onclick="edit(<?=esc($jadwal['id'])?>)" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></button>
                                        <button onclick="deleteModal('<?=esc($jadwal['id'])?>');" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Anggota</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel-user" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50px">#</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th width="100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($user as $key => $user):?>
                                <tr>
                                    <td><?=$key + 1?></td>
                                    <td><?=esc($user['name'])?></td>
                                    <td><div class="badge <?php if($user['status'] == "Tidak Hadir") { echo "badge-danger";} else if($user['status'] == "Ijin") { echo "badge-info"; } else { echo "badge-success"; }?>"><?=esc($user['status'])?></div></td>
                                    <td>
                                        <a href="<?=base_url()?>category/user/detail/<?=esc($user['id'])?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> show</a><br>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php $this->endSection(); ?>

<?php $this->section('modal'); ?>
  <div class="modal fade" id="katdeleteModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Apakah kamu yakin?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Semua data yang berhubungan dengan kategori ini akan hilang.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>category/delete">
            <?=csrf_field();?>
            <input type='hidden' name="id" id="id_delete_kat">
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
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
          <p>Jadwal yang terpilih akan terhapus.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>schedule/delete">
            <?=csrf_field();?>
            <input type='hidden' name="id" id="id_delete">
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="deletePengawasModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Apakah kamu yakin?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Pengawas yang terpilih akan terhapus.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>category/deletepengawas">
            <?=csrf_field();?>
            <input type='hidden' name="id" id="id_delete_peng">
            <button type="submit" class="btn btn-danger">Hapus</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="addPengawasModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Pengawas</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?=base_url()?>category/addpengawas">
                <?=csrf_field();?>
                <input type="hidden" name="category_id" value="<?=esc($category['id'])?>">
                <div class="form-group">
                    <label>Daftar Pengawas</label>
                    <select class="select2" style="width: 100%;" name="pengawas[]" id="pengawas" multiple="multiple" data-placeholder="Pilih">
                        <?php foreach($pengawas as $row): ?>
                            <option value="<?=esc($row['id'])?>"><?=esc($row['name'])?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Tambah</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="addJadwalModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Jadwal</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?=base_url()?>schedule/store">
                <?=csrf_field();?>
                <input type="hidden" name="category_id" value="<?=esc($category['id'])?>">
                <div class="form-group">
                    <label>Hari</label>
                    <select class="form-control" name="day" id="daySelect">
                    </select>
                </div>
                <div class="justify-content-between d-flex">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="no_limit" onchange="timeform()" name="no_limit" value="1">
                            <label for="no_limit" class="custom-control-label">Tanpa Batas Waktu</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="online" name="online" value="1">
                            <label for="online" class="custom-control-label">Masuk Online</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Waktu Awal</label>
                    <input type="time" class="form-control" name="start_time" id="starttime" required>
                </div>
                <div class="form-group">
                    <label>Waktu Akhir</label>
                    <input type="time" class="form-control" name="end_time" id="endtime" required>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Tambah</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editJadwalModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Ubah Jadwal</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?=base_url()?>schedule/edit">
                <?=csrf_field();?>
                <input type="hidden" name="id" id="id_jadwal">
                <div class="form-group">
                    <label>Hari</label>
                    <h4 id="labelday"></h4>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="online2" name="online" value="1">
                            <label for="online2" class="custom-control-label">Masuk Online</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="no_limit2" onchange="timeform2()" name="no_limit" value="1">
                            <label for="no_limit2" class="custom-control-label">Tanpa Batas Waktu</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Waktu Awal</label>
                    <input type="time" class="form-control" name="start_time" id="starttime2" required>
                </div>
                <div class="form-group">
                    <label>Waktu Akhir</label>
                    <input type="time" class="form-control" name="end_time" id="endtime2" required>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
    <script src="<?=base_url()?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        $('#pengawas').select2({
        })
        inputtanggal();
        document.getElementById('starttime').addEventListener('change', function() {
            let startTime = this.value;
            let endTimeInput = document.getElementById('endtime');
            endTimeInput.min = startTime;
            if (endTimeInput.value < startTime) {
                endTimeInput.value = startTime;
            }
        });

        document.getElementById('endtime').addEventListener('blur', function() {
            let startTime = document.getElementById('starttime').value;
            let endTime = this.value;
            if (endTime < startTime) {
                this.value = startTime;
            }
        });

        document.getElementById('starttime2').addEventListener('change', function() {
            let startTime = this.value;
            let endTimeInput = document.getElementById('endtime2');
            endTimeInput.min = startTime;
            if (endTimeInput.value < startTime) {
                endTimeInput.value = startTime;
            }
        });

        document.getElementById('endtime2').addEventListener('blur', function() {
            let startTime = document.getElementById('starttime2').value;
            let endTime = this.value;
            if (endTime < startTime) {
                this.value = startTime;
            }
        });

        function timeform() {
            if ($('#no_limit').is(':checked')) {
                $("#starttime").prop('disabled', true);
                $("#endtime").prop('disabled', true);
                $("#starttime").val(null);
                $("#endtime").val(null);
            } else {
                $("#starttime").prop('disabled', false);
                $("#endtime").prop('disabled', false);
            }
        }

        function timeform2() {
            if ($('#no_limit2').is(':checked')) {
                $("#starttime2").prop('disabled', true);
                $("#endtime2").prop('disabled', true);
                $("#starttime2").val(null);
                $("#endtime2").val(null);
            } else {
                $("#starttime2").prop('disabled', false);
                $("#endtime2").prop('disabled', false);
            }
        }

        function deleteModalKat($id) {
            $('#katdeleteModal').modal();
            $('#id_delete_kat').val($id);
        }

        function addPengawas($id) {
            $('#addPengawasModal').modal();
        }

        function deleteModal($id) {
            $('#deleteModal').modal();
            $('#id_delete').val($id);
        }

        function deletepengawasModal($id) {
            $('#deletePengawasModal').modal();
            $('#id_delete_peng').val($id);
        }

        function edit(id) {
            fetch("<?=base_url()?>schedule/check/"+id) // Ganti dengan API-mu
                .then(response => response.json())
                .then(data => {
                    // document.getElementById('day').value = data.day;
                    document.getElementById('labelday').innerHTML = data.day;
                    document.getElementById('id_jadwal').value = data.id;
                    if (data.no_limit == '1') {
                        $('#no_limit2').prop('checked', true);
                        $("#starttime2").prop('disabled', true);
                        $("#endtime2").prop('disabled', true);
                        $("#starttime2").val(null);
                        $("#endtime2").val(null);
                    } else {
                        $('#no_limit2').prop('checked', false);
                        $("#starttime2").prop('disabled', false);
                        $("#endtime2").prop('disabled', false);
                        $("#starttime2").val(data.start_time);
                        $("#endtime2").val(data.end_time);
                    }
                    if (data.online == '1') {
                        $('#online2').prop('checked', true);
                    } else {
                        $('#online2').prop('checked', false);
    
                    }
                    
                    $('#editJadwalModal').modal();
                })
                .catch(error => console.error("Error fetching data:", error));
        }

        function inputtanggal() {
            let selectedDaysString = "<?php foreach($jadwal1 as $jadwal1) { echo $jadwal1['day'].","; } ?>";
            
            let allDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

            let selectedDays = selectedDaysString ? selectedDaysString.split(',') : [];
            
            let availableDays = allDays.filter(day => !selectedDays.includes(day));

            let daySelect = document.getElementById("daySelect");
            availableDays.forEach(day => {
                let option = document.createElement("option");
                option.value = day;
                option.textContent = day;
                daySelect.appendChild(option);
            });
        }

        $(document).ready(function() {
            $('#tabel-user').DataTable();
            $('#tabel-jadwal').DataTable();
        });

        function showAdd() {
            $('#addJadwalModal').modal();
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