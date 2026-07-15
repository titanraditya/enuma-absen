<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('css'); ?>
    <link rel="stylesheet" href="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.css">
    <style>
        /* Kartu log anggota */
        .log-card {
            border-top: 3px solid var(--st, var(--line));
            transition: transform .18s, box-shadow .18s;
        }
        .log-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 2px 4px rgba(16, 34, 56, .05), 0 16px 32px -12px rgba(16, 34, 56, .2);
        }
        .log-card.st-hadir  { --st: #40c057; }
        .log-card.st-online { --st: #22b8cf; }
        .log-card.st-ijin   { --st: #4c9be8; }
        .log-card.st-belum  { --st: #fa5252; }

        /* Area gambar / placeholder */
        .log-media {
            height: 160px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f4f6fa 0%, #e9eef5 100%);
            color: #c3ccd8;
            font-size: 1.7rem;
            border-bottom: 1px solid var(--line);
        }
        .log-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            cursor: pointer;
            transition: transform .25s;
        }
        .log-media:hover img { transform: scale(1.04); }
        .log-media .media-hint {
            position: absolute;
            right: 10px;
            bottom: 10px;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 34, 56, .55);
            color: #fff;
            font-size: .75rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity .18s;
        }
        .log-media:hover .media-hint { opacity: 1; }

        /* Identitas anggota */
        .log-person {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .log-person .avatar {
            position: relative;
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        }
        .log-person .avatar .dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: var(--st, #c3ccd8);
        }
        .log-person .person-info { min-width: 0; }
        .log-person .person-name {
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 3px;
            line-height: 1.2;
        }

        /* Chip status lembut */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .74rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .chip-success { background: #e6f7ec; color: #217a3c; }
        .chip-teal    { background: #e0f6f8; color: #0b7285; }
        .chip-info    { background: var(--brand-soft); color: var(--brand-dark); }
        .chip-danger  { background: #fdeaec; color: #c0293a; }
        .chip-warning { background: #fff3df; color: #a86407; }
        .chip-muted   { background: #eef1f6; color: var(--muted); }

        /* Seksi log terakhir */
        .log-label {
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #97a2b0;
        }
        .log-card .log-desc { white-space: pre-line; }
        .log-card hr { border-color: var(--line); }

        /* Filter segmented */
        .seg-filter {
            display: inline-flex;
            gap: 2px;
            padding: 4px;
            background: #eef1f6;
            border-radius: 999px;
        }
        .seg-filter .seg-btn {
            border: 0;
            background: transparent;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--muted);
            transition: background .15s, color .15s, box-shadow .15s;
        }
        .seg-filter .seg-btn:focus { outline: 0; }
        .seg-filter .seg-btn .seg-count {
            font-size: .72rem;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: 999px;
            margin-left: 4px;
            background: rgba(16, 34, 56, .08);
        }
        .seg-filter .seg-btn.active {
            background: #fff;
            color: var(--ink);
            box-shadow: 0 2px 8px -2px rgba(16, 34, 56, .25);
        }
        .seg-filter .seg-btn.active[data-filter="sudah"] { color: #217a3c; }
        .seg-filter .seg-btn.active[data-filter="sudah"] .seg-count { background: #e6f7ec; }
        .seg-filter .seg-btn.active[data-filter="belum"] { color: #c0293a; }
        .seg-filter .seg-btn.active[data-filter="belum"] .seg-count { background: #fdeaec; }

        /* Kartu yang tersaring keluar (mengalahkan .d-flex Bootstrap) */
        .anggota-card.filter-hide { display: none !important; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--muted);
        }
        .empty-state .empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 1.3rem;
            margin-bottom: 14px;
        }
    </style>
<?php $this->endSection(); ?>

<?php $this->section('action-button'); ?>
    <a href="<?=base_url()?>category/detail/<?=esc($category['id'])?>" class="btn btn-primary btn-sm float-right"><i class="fas fa-arrow-left"></i> Kembali</a>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <?php
        $bulanMap = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $statusMeta = [
            'Hadir'          => ['chip-success', 'fa-check-circle', 'st-hadir'],
            'Hadir (Online)' => ['chip-teal', 'fa-laptop', 'st-online'],
            'Ijin'           => ['chip-info', 'fa-info-circle', 'st-ijin'],
        ];
        $sudahAbsen = count(array_filter($logs, function($l) { return $l['status_today'] != 'Tidak Hadir'; }));
        $belumAbsen = count($logs) - $sudahAbsen;
    ?>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card mb-0">
                <div class="card-header bg-lightblue">
                    <h4 class="card-title">Log Terakhir Anggota &mdash; <?=esc($category['name'])?></h4>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="seg-filter my-1" role="group" aria-label="Filter status absen hari ini">
                            <button type="button" class="seg-btn active" data-filter="semua" onclick="filterAbsen(this)">Semua <span class="seg-count"><?=count($logs)?></span></button>
                            <button type="button" class="seg-btn" data-filter="sudah" onclick="filterAbsen(this)"><i class="fas fa-check mr-1"></i>Sudah absen <span class="seg-count"><?=$sudahAbsen?></span></button>
                            <button type="button" class="seg-btn" data-filter="belum" onclick="filterAbsen(this)"><i class="fas fa-times mr-1"></i>Belum absen <span class="seg-count"><?=$belumAbsen?></span></button>
                        </div>
                        <small class="text-muted my-1"><i class="far fa-clock mr-1"></i>Status &amp; log per <?=date('j').' '.$bulanMap[(int)date('n')].' '.date('Y')?></small>
                    </div>
                </div>
            </div>
        </div>
        <?php if(empty($logs)): ?>
            <div class="col-12">
                <div class="card">
                    <div class="empty-state">
                        <span class="empty-icon"><i class="fas fa-users"></i></span>
                        <p class="mb-0">Belum ada anggota di grup ini.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php $today = new DateTime(date('Y-m-d')); ?>
        <?php foreach($logs as $log): ?>
            <?php
                $hasLog = $log['checkin'] !== null;
                $absenToday = $log['status_today'] != 'Tidak Hadir';
                [$todayChip, $todayIcon, $stClass] = $statusMeta[$log['status_today']] ?? ['chip-danger', 'fa-times-circle', 'st-belum'];
                $imageUrl = ($hasLog && trim((string) $log['image']) !== '')
                    ? base_url().'log/image/'.$log['user_id'].'/'.$log['image']
                    : null;

                if ($hasLog) {
                    $checkinTime = strtotime($log['checkin']);
                    $diffDays = $today->diff(new DateTime(date('Y-m-d', $checkinTime)))->days;
                    $tanggalLengkap = date('j', $checkinTime).' '.$bulanMap[(int)date('n', $checkinTime)].' '.date('Y', $checkinTime).' &bull; '.date('H:i', $checkinTime);
                    if ($diffDays == 0) {
                        [$umurChip, $umurText] = ['chip-success', 'Hari ini'];
                    } elseif ($diffDays == 1) {
                        [$umurChip, $umurText] = ['chip-warning', 'Kemarin'];
                    } elseif ($diffDays <= 7) {
                        [$umurChip, $umurText] = ['chip-warning', $diffDays.' hari lalu'];
                    } else {
                        [$umurChip, $umurText] = ['chip-muted', $diffDays.' hari lalu'];
                    }
                }
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 d-flex align-items-stretch anggota-card" data-absen="<?=$absenToday ? 'sudah' : 'belum'?>">
                <div class="card log-card <?=$stClass?> w-100">
                    <div class="log-media">
                        <?php if($imageUrl): ?>
                            <img src="<?=esc($imageUrl, 'attr')?>" loading="lazy" alt="Bukti log <?=esc($log['member_name'], 'attr')?>" onclick="showImage(this.src)">
                            <span class="media-hint"><i class="fas fa-expand"></i></span>
                        <?php elseif($hasLog): ?>
                            <i class="far fa-file-alt" title="Log tanpa gambar"></i>
                        <?php else: ?>
                            <i class="fas fa-comment-slash" title="Belum pernah menulis log"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="log-person mb-3">
                            <span class="avatar"><?=esc(mb_strtoupper(mb_substr($log['member_name'], 0, 1)))?><span class="dot"></span></span>
                            <div class="person-info">
                                <p class="person-name"><?=esc($log['member_name'])?></p>
                                <?php if($absenToday): ?>
                                    <span class="chip <?=$todayChip?>"><i class="fas <?=$todayIcon?>"></i><?=esc($log['status_today'])?> hari ini</span>
                                <?php else: ?>
                                    <span class="chip chip-danger"><i class="fas fa-times-circle"></i>Belum absen hari ini</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="log-label">Log terakhir</span>
                            <?php if($hasLog): ?>
                                <span class="chip <?=$umurChip?>"><?=$umurText?></span>
                            <?php endif; ?>
                        </div>
                        <?php if($hasLog): ?>
                            <p class="text-muted small mb-2"><i class="far fa-clock mr-1"></i><?=$tanggalLengkap?></p>
                            <?php if(trim((string) $log['activity']) !== ''): ?>
                                <p class="font-weight-bold mb-1"><?=esc($log['activity'])?></p>
                            <?php endif; ?>
                            <?php if(trim((string) $log['description']) !== ''): ?>
                                <p class="log-desc text-muted mb-0"><?=esc($log['description'])?></p>
                            <?php elseif(trim((string) $log['activity']) === ''): ?>
                                <p class="text-muted mb-0"><i>Tidak ada catatan aktivitas.</i></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0"><i>Belum pernah menulis log.</i></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="col-12 d-none" id="filter-kosong">
            <div class="card">
                <div class="empty-state">
                    <span class="empty-icon"><i class="fas fa-filter"></i></span>
                    <p class="mb-0">Tidak ada anggota yang cocok dengan filter ini.</p>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>

<?php $this->section('modal'); ?>
    <div class="modal fade" id="imageModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-image mr-2"></i> Bukti Log</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <img src="" id="image-full" class="w-100">
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>

<?php $this->section('js'); ?>
    <script src="<?=base_url()?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script>
        function showImage(src) {
            $('#image-full').attr('src', src);
            $('#imageModal').modal();
        }

        function filterAbsen(btn) {
            var filter = $(btn).data('filter');

            $('.seg-filter .seg-btn').removeClass('active');
            $(btn).addClass('active');

            var tampil = 0;
            $('.anggota-card').each(function() {
                var cocok = filter == 'semua' || $(this).data('absen') == filter;
                $(this).toggleClass('filter-hide', !cocok);
                if (cocok) tampil++;
            });
            $('#filter-kosong').toggleClass('d-none', tampil > 0);
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
