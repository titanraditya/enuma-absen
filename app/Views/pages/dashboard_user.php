<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('content'); ?>
    <?php
        $hour = (int) date('G');
        if ($hour < 11) { $greet = 'Selamat pagi'; }
        elseif ($hour < 15) { $greet = 'Selamat siang'; }
        elseif ($hour < 18) { $greet = 'Selamat sore'; }
        else { $greet = 'Selamat malam'; }
        $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $today = $days[(int) date('w')] . ', ' . date('j') . ' ' . $months[(int) date('n')] . ' ' . date('Y');

        $statusToday = $attendancecount['status_today'] ?? 'Tidak Hadir';
        $statusBadges = [
            'Hadir'          => ['badge-success', 'fa-check-circle'],
            'Hadir (Online)' => ['bg-teal', 'fa-laptop'],
            'Ijin'           => ['badge-info', 'fa-info-circle'],
        ];
        [$statusClass, $statusIcon] = $statusBadges[$statusToday] ?? ['badge-danger', 'fa-times-circle'];
    ?>
    <div class="dash-hero">
        <span class="hero-date"><i class="far fa-calendar"></i> <?= $today ?></span>
        <h2><?= $greet ?>, <?= esc($user['name']) ?> 👋</h2>
        <p class="hero-sub"><?= esc($user['category_name']) ?> &mdash; Sistem Aplikasi Manajemen Absensi Enuma Technology</p>
        <img src="<?=base_url()?>enumatech.png" alt="Enuma Technology" class="hero-logo">
    </div>

    <?php if($statusToday == 'Tidak Hadir'): ?>
        <div class="cta-banner">
            <span class="cta-icon"><i class="fas fa-pen"></i></span>
            <div class="cta-text">
                <b>Kamu belum absen hari ini</b>
                <span>Tulis log aktivitasmu sekarang untuk tercatat Hadir (Online).</span>
            </div>
            <a href="<?=base_url()?>log" class="btn btn-cta"><i class="fas fa-pen mr-1"></i> Tulis Log Sekarang</a>
        </div>
    <?php else: ?>
        <div class="cta-banner cta-done">
            <span class="cta-icon"><i class="fas fa-check"></i></span>
            <div class="cta-text">
                <b>Kamu sudah tercatat <?=esc($statusToday)?> hari ini</b>
                <span>Kamu masih bisa menulis atau memperbarui log aktivitasmu.</span>
            </div>
            <a href="<?=base_url()?>log" class="btn btn-primary"><i class="fas fa-book mr-1"></i> Lihat Log</a>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-12">
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
        <div class="col-lg-4 col-md-4 col-6">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-calendar-check"></i></span>
                    <div>
                        <span class="stat-label">Total Kehadiran</span>
                        <span class="stat-value"><?= $attendancecount ? esc($attendancecount['hadir_count']) : '0' ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-envelope-open"></i></span>
                    <div>
                        <span class="stat-label">Total Izin</span>
                        <span class="stat-value"><?= $attendancecount ? esc($attendancecount['ijin_count']) : '0' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>
