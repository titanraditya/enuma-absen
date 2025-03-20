<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12 mb-2 order-lg-1 order-2">
            <div class="info-box h-100">
                

                <div class="info-box-content">
                <span class="info-box-text">Selamat Datang</span>
                <h5 class="text-bold">
                    <?=esc($user['name'])?>
                </h5>
                <small><?=esc($user['category_name'])?></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 col-6 mb-2 order-lg-2 order-3">
            <div class="info-box h-100">
                <span class="info-box-icon bg-info elevation-1"
                ><i class="fas fa-list"></i
                ></span>

                <div class="info-box-content">
                <span class="info-box-text">Total Kehadiran</span>
                <h1 class="text-bold">
                    <?php if($attendancecount) { echo esc($attendancecount['hadir_count']); } else { echo "0";}?>
                </h1>
                <small>Ijin <?php if($attendancecount) { echo esc($attendancecount['ijin_count']); } else { echo "0";}?></small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-2 order-lg-3 order-1">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-start px-5">
                    <img src="<?=base_url()?>enumatech.png" class="img-md">
                    <div class="ml-3">
                        <p class="p-0 m-0 h3 font-weight-bold">Enuma Technology</p>
                        <p class="p-0 m-0">Sistem Aplikasi Manajemen Absensi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>