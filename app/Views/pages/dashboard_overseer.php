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
    ?>
    <div class="dash-hero">
        <span class="hero-date"><i class="far fa-calendar"></i> <?= $today ?></span>
        <h2><?= $greet ?>, <?= esc($user['name']) ?> 👋</h2>
        <p class="hero-sub"><?= esc($user['category_name']) ?> &mdash; Sistem Aplikasi Manajemen Absensi Enuma Technology</p>
        <img src="<?=base_url()?>enumatech.png" alt="Enuma Technology" class="hero-logo">
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-12">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-layer-group"></i></span>
                    <div>
                        <span class="stat-label">Grup yang Dikelola</span>
                        <span class="stat-value"><?= esc($categorycount['total']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <a href="<?=base_url()?>log" class="quick-link">
                <span class="ql-icon"><i class="fas fa-book"></i></span>
                <span>
                    <b>Log Kehadiran</b>
                    <small>Pantau kehadiran dan laporan anggota</small>
                </span>
                <i class="fas fa-arrow-right ql-arrow"></i>
            </a>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <a href="<?=base_url()?>category" class="quick-link">
                <span class="ql-icon"><i class="fas fa-layer-group"></i></span>
                <span>
                    <b>Kelola Grup</b>
                    <small>Lihat grup yang kamu awasi</small>
                </span>
                <i class="fas fa-arrow-right ql-arrow"></i>
            </a>
        </div>
    </div>
<?php $this->endSection(); ?>
