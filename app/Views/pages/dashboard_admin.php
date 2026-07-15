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
        $inactiveCount = (int) $usercount['count_all'] - (int) $usercount['count_active'];
    ?>
    <div class="dash-hero">
        <span class="hero-date"><i class="far fa-calendar"></i> <?= $today ?></span>
        <h2><?= $greet ?>, <?= esc(session('user_name')) ?> 👋</h2>
        <p class="hero-sub">Sistem Aplikasi Manajemen Absensi Enuma Technology &mdash; kelola anggota, grup, dan kehadiran dari satu tempat.</p>
        <img src="<?=base_url()?>enumatech.png" alt="Enuma Technology" class="hero-logo">
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-12">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-user-check"></i></span>
                    <div>
                        <span class="stat-label">User Aktif</span>
                        <span class="stat-value"><?= esc($usercount['count_active']) ?></span>
                        <span class="stat-sub">dari <?= esc($usercount['count_all']) ?> user terdaftar</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-user-slash"></i></span>
                    <div>
                        <span class="stat-label">User Nonaktif</span>
                        <span class="stat-value"><?= $inactiveCount ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-6">
            <div class="card stat-tile">
                <div class="card-body">
                    <span class="stat-icon"><i class="fas fa-layer-group"></i></span>
                    <div>
                        <span class="stat-label">Total Grup</span>
                        <span class="stat-value"><?= esc($categorycount['total']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-12">
            <a href="<?=base_url()?>user" class="quick-link">
                <span class="ql-icon"><i class="fas fa-users"></i></span>
                <span>
                    <b>Kelola Anggota</b>
                    <small>Tambah, ubah, atau nonaktifkan anggota</small>
                </span>
                <i class="fas fa-arrow-right ql-arrow"></i>
            </a>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <a href="<?=base_url()?>category" class="quick-link">
                <span class="ql-icon"><i class="fas fa-layer-group"></i></span>
                <span>
                    <b>Kelola Grup</b>
                    <small>Atur grup dan pengawasnya</small>
                </span>
                <i class="fas fa-arrow-right ql-arrow"></i>
            </a>
        </div>
        <div class="col-lg-4 col-md-4 col-12">
            <a href="<?=base_url()?>admin" class="quick-link">
                <span class="ql-icon"><i class="fas fa-user-shield"></i></span>
                <span>
                    <b>Kelola Admin</b>
                    <small>Atur akun administrator sistem</small>
                </span>
                <i class="fas fa-arrow-right ql-arrow"></i>
            </a>
        </div>
    </div>
<?php $this->endSection(); ?>
