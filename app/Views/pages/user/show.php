<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <!-- <link rel="stylesheet" href="<?=base_url()?>assets/plugins/datatables/responsive.bootstrap4.css"/> -->
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <style>
        .log-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid var(--line);
        }
        .log-field-label {
            display: block;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #97a2b0;
            margin-bottom: 3px;
        }
        .log-desc { white-space: pre-line; }
        .log-ss {
            border-radius: 10px;
            border: 1px solid var(--line);
        }
    </style>
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
    <a href="<?=base_url()?>category/detail/<?=esc($user['category_id'])?>" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <?php
        $statusToday = $attendancecount['status_today'] ?? 'Tidak Hadir';
        $statusBadges = [
            'Hadir'          => ['badge-success', 'fa-check-circle'],
            'Hadir (Online)' => ['bg-teal', 'fa-laptop'],
            'Ijin'           => ['badge-info', 'fa-info-circle'],
        ];
        [$statusClass, $statusIcon] = $statusBadges[$statusToday] ?? ['badge-danger', 'fa-times-circle'];
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center flex-wrap" style="gap: 14px;">
                    <span class="profile-avatar"><?=esc(mb_strtoupper(mb_substr($user['name'], 0, 1)))?></span>
                    <div>
                        <h5 class="font-weight-bold mb-1"><?=esc($user['name'])?></h5>
                        <span class="badge badge-primary"><i class="fas fa-users mr-1"></i><?=esc($user['category_name'])?></span>
                    </div>
                    <div class="ml-auto">
                        <?php if($statusToday == 'Tidak Hadir'): ?>
                            <button onclick="ijinModal();" class="btn btn-info text-white"><i class="fas fa-pen mr-1"></i> Beri Izin</button>
                        <?php elseif($statusToday == 'Ijin'): ?>
                            <button onclick="cancelijinModal();" class="btn btn-default"><i class="fas fa-undo mr-1"></i> Batalkan Izin</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-user-clock"></i></span>
                    <div>
                        <span class="stat-label">Status hari ini</span>
                        <span class="badge <?=$statusClass?> stat-badge"><i class="fas <?=$statusIcon?> mr-1"></i><?=esc($statusToday)?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
            $statTiles = [
                ['fa-calendar-check', 'Total hadir',  'count_hadir'],
                ['fa-laptop',         'Hadir online', 'count_online'],
                ['fa-envelope-open',  'Total izin',   'count_ijin'],
            ];
        ?>
        <?php foreach ($statTiles as [$tileIcon, $tileLabel, $tileKey]): ?>
        <div class="col-lg-3 col-md-6 col-4">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas <?=$tileIcon?>"></i></span>
                    <div>
                        <span class="stat-label"><?=$tileLabel?></span>
                        <span class="stat-value"><?= esc($attendancecount[$tileKey] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-lightblue">
                    <h4 class="card-title">Kehadiran dan Laporan Anggota</h4>
                    <?php if (session('user_role') == 'admin'): ?>
                    <button onclick="addLog()" class="btn btn-sm btn-outline-primary bg-white float-right"><i class="fas fa-plus"></i> Tambah</button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row filter-bar mb-3">
                        <div class="col-xl-3 col-md-4 col-6">
                            <div class="form-group">
                                <label>Dari</label>
                                <input type="date" class="form-control" id="start_date" onchange="pilih_custom()">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-4 col-6">
                            <div class="form-group">
                                <label>Sampai</label>
                                <input type="date" class="form-control" id="end_date" onchange="pilih_custom()">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-4 col-6">
                            <div class="form-group">
                                <label>Periode</label>
                                <select class="form-control" id="filter" onchange="filter()">
                                    <option value="all">Semua</option>
                                    <option value="7">7 Hari Terakhir</option>
                                    <option value="30">30 Hari Terakhir</option>
                                    <option value="thismonth">Bulan Ini</option>
                                    <option value="lastmonth">Bulan Kemarin</option>
                                    <option value="custom">Kustom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-12 col-6 d-flex align-items-end">
                            <div class="form-group">
                                <button class="btn btn-default" onclick="printModal()"><i class="fas fa-print mr-1"></i> Cetak Laporan</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tabel-absensi" class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th width="100px">Tanggal</th>
                                    <th width="100px">Status</th>
                                    <th width="100px">Keterangan</th>
                                    <th width="200px">Waktu</th>
                                    <th width="180px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>

<?php $this->section('modal'); ?>
  <div class="modal fade" id="ijinModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="title-ijin">Beri Ijin?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id='content-ijin'>Klik Ya jika akan memberi ijin.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>category/user/ijin" id="form-ijin">
            <?=csrf_field();?>
            <input type='hidden' name="id" value="<?=esc($user['id'])?>">
            <button type="submit" class="btn btn-info">Ya</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Hapus Absen?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id='content-delete'></p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <form method="post" action="<?=base_url()?>category/user/logdelete">
            <?=csrf_field();?>
            <input type='hidden' name="id" id="hapuslog">
            <button type="submit" class="btn btn-danger">Ya</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="printModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-print"></i> Pengaturan Cetak</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" method="POST" id="print_form" target="_blank">
            <input type="hidden" name="id" value="<?=esc($user['id'])?>">
            <input type="hidden" name="min" id="min">
            <input type="hidden" name="max" id="max">
            <div class="form-group">
                <label>Filter</label>
                <p id="filter-text">Semua</p>
            </div>
            <div class="form-group">
                <label>Urutkan data dari</label>
                <select name="order_by" class="form-control">
                    <option value="1">Tanggal - ASC</option>
                    <option value="2">Tanggal - DESC</option>
                    <option value="3">Status - ASC</option>
                    <option value="4">Status - DESC</option>
                </select>
            </div>
            <div class="form-group">
                <label>Data yang ditampilkan</label>
                <select name="data_show" class="form-control">
                    <option value="1">Absensi dan Log</option>
                    <option value="2">Absensi</option>
                    <option value="3">Log</option>
                </select>
            </div>
            <div class="form-group">
                <label>Format</label>
                <select name="format" class="form-control" id="format_print">
                    <option value="pdf">Pdf</option>
                    <option value="xls">Excel</option>
                </select>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="button" onclick="print()" class="btn btn-info"><i class="fas fa-print"></i> Print</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="showLogModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="overlay" id="overlayshowlog">
            <i class="fas fa-2x fa-sync fa-spin"></i>
        </div>
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-info mr-2"></i> Detail Log</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id='detail_log'>
                <div class="log-meta">
                    <span class="text-muted"><i class="far fa-calendar-alt mr-1"></i><span id="date-text"></span></span>
                    <span id="status-text"></span>
                </div>
                <div class="mb-3">
                    <span class="log-field-label">Aktivitas</span>
                    <p class="font-weight-bold mb-0" id="activity-text"></p>
                </div>
                <div class="mb-3">
                    <span class="log-field-label">Deskripsi</span>
                    <p class="log-desc mb-0" id="description-text"></p>
                </div>
                <div id="ss_wrap">
                    <span class="log-field-label">Screenshot Bukti</span>
                    <img src="" id="ss_image" class="log-ss w-100">
                </div>
            </div>
            <div id='error_log'>
                <p id="error_msg" class="text-center"></p>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Oke</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="overlay" id="overlaylogedit">
            <i class="fas fa-2x fa-sync fa-spin"></i>
        </div>
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-pen mr-2"></i> Edit Log</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id='edit_log'>
            <div class="log-meta">
                <span class="text-muted"><i class="far fa-calendar-alt mr-1"></i><span id="date-edit"></span></span>
                <span id="status-edit"></span>
            </div>
            <form method="post" action="<?=base_url()?>log/edit" id="form-editlog" enctype="multipart/form-data">
              <?=csrf_field();?>
              <input type="hidden" name="id" id="id_edit">
              <div class="form-group">
                  <label>Aktivitas</label>
                  <input type="text" id="activity_edit" name="activity" class="form-control" placeholder="Apa yang dikerjakan hari itu?" autocomplete="off">
                  <div class="invalid-feedback" id="err-activity-edit">
                  </div>
              </div>
              <div class="form-group mb-0">
                  <label>Deskripsi <span class="form-hint font-weight-normal">(opsional)</span></label>
                  <textarea id="description_edit" name="description" rows="3" class="form-control" placeholder="Rincian aktivitas"></textarea>
              </div>
            </form>
          </div>
          <div id='error_edit'>
            <p id="erroredit_msg" class="text-center mt-2"></p>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btn-submit-edit" onclick="editlogSubmit()"><i class="fas fa-check mr-1"></i> Simpan Perubahan</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="addLogModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fas fa-pen mr-2"></i> Tambah Log</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id='add_log'>
            <form method="post" action="<?=base_url()?>category/user/addlog" id="form-addlog" enctype="multipart/form-data">
              <?=csrf_field();?>
              <input type="hidden" name="id" value="<?=esc($user['id'])?>">
              <div class="form-group">
                <label>Tanggal</label>
                <input type="date" class="form-control" name="date" id="date-add" required>
                <div class="invalid-feedback" id="err-date-add">
                </div>
              </div>
              <div class="form-group">
                <label>Waktu</label>
                <input type="time" class="form-control" name="time" id="time-add" required>
                <div class="invalid-feedback" id="err-time-add">
                </div>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="Hadir">Hadir</option>
                    <option value="Ijin">Ijin</option>
                </select>
              </div>
              <div class="form-group">
                  <label>Aktivitas</label>
                  <input type="text" id="activity_add" name="activity" class="form-control" autocomplete="off">
                  <div class="invalid-feedback" id="err-activity-add">
                  </div>
              </div>
              <label>Deskripsi</label>
              <textarea id="description_add" name="description" class="form-control"></textarea>
            </form>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-info" id="btn-submit-add" onclick="addLogSubmit()">Simpan</button>
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
        const BASE_URL = '<?= base_url() ?>';
        const ATTENDANCE_DATA_URL = BASE_URL + 'attendance/data/<?= esc($user['id']) ?>/';
        const LOG_IMAGE_URL = BASE_URL + 'log/image/';

        const STATUS_BADGES = {
            'Hadir':          ['badge-success', 'fa-check-circle'],
            'Hadir (Online)': ['bg-teal', 'fa-laptop'],
            'Ijin':           ['badge-info', 'fa-info-circle']
        };

        const KETERANGAN_BADGES = {
            'Terlambat':    'bg-warning',
            'Pulang Cepat': 'bg-orange',
            'Lembur':       'bg-purple'
        };

        // ---------- Helper ----------

        function ajaxErrorMessage(xhr, status, error) {
            if (status === 'timeout')     return 'TimeoutError: Permintaan melebihi waktu yang ditentukan.';
            if (status === 'abort')       return 'AbortError: Permintaan dibatalkan.';
            if (status === 'parsererror') return 'ParseError: Gagal mem-parsing data JSON.';
            if (xhr.status === 0)         return 'NetworkError: Tidak dapat terhubung ke server. Periksa koneksi internet.';
            if (xhr.status >= 400 && xhr.status < 600) return `ServerError (Status ${xhr.status}): ${error}`;
            return `UnexpectedError: ${status} - ${error}`;
        }

        function fetchLog(id, onSuccess, onError) {
            $.ajax({
                url: BASE_URL + 'log/getdata/' + id,
                type: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: onSuccess,
                error: onError
            });
        }

        function setFieldError(el, errSelector, message) {
            el.classList.toggle('is-invalid', message !== '');
            $(errSelector).html(message);
        }

        function renderStatus(status) {
            const badge = STATUS_BADGES[status];
            if (!badge) {
                return '<span class="badge badge-secondary">' + status + '</span>';
            }
            return '<span class="badge ' + badge[0] + '"><i class="fas ' + badge[1] + ' mr-1"></i>' + status + '</span>';
        }

        function renderKeterangan(data) {
            if (!data || data === '-') {
                return '<span class="text-muted">-</span>';
            }
            return data.split(', ').map(function (item) {
                return '<span class="badge ' + (KETERANGAN_BADGES[item] || 'badge-secondary') + ' mr-1">' + item + '</span>';
            }).join('');
        }

        // ---------- Modal detail log ----------

        function showLog(id) {
            $('#overlayshowlog').show();
            $('#showLogModal').modal();
            fetchLog(id, function (response) {
                $('#error_log').hide();
                $('#detail_log').show();
                $('#date-text').text(response.date);
                $('#status-text').html(renderStatus(response.status_id));
                $('#activity-text').text(response.activity || '-');
                $('#description-text').text(response.description || '-');
                if (response.image) {
                    $('#ss_image').attr('src', LOG_IMAGE_URL + response.user_id + '/' + response.image);
                    $('#ss_wrap').show();
                } else {
                    $('#ss_wrap').hide();
                }
                $('#overlayshowlog').hide();
            }, function (xhr, status, error) {
                $('#error_msg').html(ajaxErrorMessage(xhr, status, error));
                $('#error_log').show();
                $('#detail_log').hide();
                $('#overlayshowlog').hide();
            });
        }

        // ---------- Modal edit log ----------

        function editLog(id) {
            $('#overlaylogedit').show();
            $('#editModal').modal();
            fetchLog(id, function (response) {
                $('#error_edit').hide();
                $('#edit_log').show();
                $('#date-edit').text(response.date);
                $('#status-edit').html(renderStatus(response.status_id));
                $('#id_edit').val(response.id);
                $('#activity_edit').val(response.activity);
                $('#description_edit').val(response.description);
                setFieldError(document.getElementById('activity_edit'), '#err-activity-edit', '');
                $('#btn-submit-edit').show();
                $('#overlaylogedit').hide();
            }, function (xhr, status, error) {
                $('#erroredit_msg').html(ajaxErrorMessage(xhr, status, error));
                $('#error_edit').show();
                $('#edit_log').hide();
                $('#btn-submit-edit').hide();
                $('#overlaylogedit').hide();
            });
        }

        function editlogSubmit() {
            const activity = document.getElementById('activity_edit');
            const valid = activity.value !== '';
            setFieldError(activity, '#err-activity-edit', valid ? '' : 'Aktivitas belum di isi');
            if (valid) {
                document.getElementById('form-editlog').submit();
            }
        }

        // ---------- Modal tambah & hapus log ----------

        function addLog() {
            $('#addLogModal').modal();
        }

        function addLogSubmit() {
            const date = document.getElementById('date-add');
            const time = document.getElementById('time-add');

            const dateValid = date.value !== '';
            setFieldError(date, '#err-date-add', dateValid ? '' : 'Tanggal belum di isi');

            const timeValid = time.value !== '';
            setFieldError(time, '#err-time-add', timeValid ? '' : 'Waktu belum di isi');

            if (dateValid && timeValid) {
                document.getElementById('form-addlog').submit();
            }
        }

        function deleteLog(id, tgl) {
            $('#content-delete').html('Tanggal ' + tgl + ' akan dihapus. <br>Apakah anda yakin?');
            $('#hapuslog').val(id);
            $('#deleteModal').modal();
        }

        // ---------- Modal izin ----------

        function openIjinModal(route, title, content) {
            $('#form-ijin').attr('action', BASE_URL + route);
            $('#title-ijin').html(title);
            $('#content-ijin').html(content);
            $('#ijinModal').modal();
        }

        function ijinModal() {
            openIjinModal('category/user/ijin', 'Beri Ijin?', 'Klik Ya jika akan memberi ijin.');
        }

        function cancelijinModal() {
            openIjinModal('category/user/cancelijin', 'Batalkan Ijin?', 'Klik Ya jika akan ingin membatalkan ijin.');
        }

        // ---------- Filter periode & cetak ----------

        function reloadTable(range) {
            $('#tabel-absensi').DataTable().ajax.url(ATTENDANCE_DATA_URL + range).load();
        }

        function setPrintRange(min, max, labelHtml) {
            document.getElementById('min').value = min;
            document.getElementById('max').value = max;
            document.getElementById('filter-text').innerHTML = labelHtml;
        }

        function fmtDate(date) {
            return date.toISOString().split('T')[0];
        }

        function filter() {
            const input = $('#filter').val();

            if (input === 'all') {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                setPrintRange('', '', 'Semua');
                reloadTable('all');
                return;
            }
            if (input === 'custom') {
                return; // rentang kustom diproses lewat pilih_custom()
            }

            const labels = {
                '7':         '7 Hari Terakhir',
                '30':        '30 Hari Terakhir',
                'thismonth': 'Bulan Ini',
                'lastmonth': 'Bulan Kemarin'
            };
            const start = new Date();
            const end = new Date();
            if (input === '7') {
                start.setDate(start.getDate() - 7);
            } else if (input === '30') {
                start.setDate(start.getDate() - 30);
            } else if (input === 'thismonth') {
                start.setDate(1);
            } else if (input === 'lastmonth') {
                start.setDate(1);
                start.setMonth(start.getMonth() - 1);
                end.setDate(0);
            }

            document.getElementById('start_date').valueAsDate = start;
            document.getElementById('end_date').valueAsDate = end;
            setPrintRange(fmtDate(start), fmtDate(end),
                labels[input] + ' <br>( ' + fmtDate(start) + ' sd ' + fmtDate(end) + ' )');
            reloadTable(fmtDate(start) + ':' + fmtDate(end));
        }

        function pilih_custom() {
            document.getElementById('filter').value = 'custom';
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            setPrintRange(start, end,
                'Kustom <br>( ' + (start || 'awal') + ' sd. ' + (end || 'sekarang') + ' )');
            reloadTable((start || 'awal') + ':' + (end || 'sekarang'));
        }

        function printModal() {
            $('#printModal').modal();
        }

        function print() {
            const route = $('#format_print').val() === 'xls' ? 'attendance/printxls' : 'attendance/printpdf';
            $('#print_form').attr('action', BASE_URL + route);
            document.getElementById('print_form').submit();
        }

        // ---------- Notifikasi flash ----------

        const Toast = Swal.mixin({
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

        // ---------- Inisialisasi ----------

        $(function () {
            $('#tabel-absensi').DataTable({
                processing: true,
                order: [[0, 'desc']],
                searching: false,
                language: {
                    processing: "Memuat...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Tidak ada data pada periode ini",
                    emptyTable: "Belum ada data absensi",
                    paginate: { previous: "&laquo;", next: "&raquo;" }
                },
                ajax: {
                    url: ATTENDANCE_DATA_URL + 'all',
                    dataSrc: ''
                },
                columns: [
                    {data:'tanggal'},
                    {data:'status', render: renderStatus},
                    {data:'keterangan', render: renderKeterangan},
                    {data:'waktu'},
                    {data:'id',
                       orderable: false,
                       render: function (data, type, row) {
                            return '<a class="btn btn-sm btn-info text-white" onClick="showLog(\'' + data + '\')"><i class="fas fa-eye"></i> Lihat</a> ' +
                                   '<a class="btn btn-sm btn-secondary" onClick="editLog(\'' + data + '\')"><i class="fas fa-edit"></i> Edit</a> ' +
                                   '<a class="btn btn-sm btn-danger text-white" onClick="deleteLog(\'' + data + '\', \'' + row.tanggal + '\')"><i class="fas fa-trash"></i></a>';
                        }
                    },
                ],
            });
        });
    </script>
<?php $this->endSection(); ?>