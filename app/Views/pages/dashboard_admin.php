<?php $this->extend('layout/main_layout'); ?>

<?php $this->section('content'); ?>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-6 mb-2 order-lg-1 order-2">
            <div class="info-box h-100">
                <span class="info-box-icon bg-lightblue elevation-1"
                ><i class="fas fa-user"></i
                ></span>

                <div class="info-box-content">
                <span class="info-box-text">Total User Aktif</span>
                <h1 class="text-bold">
                    <?=esc($usercount['count_active'])?>
                </h1>
                <small>dari <b><?=esc($usercount['count_all'])?></b> user</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6 mb-2 order-lg-2 order-3">
            <div class="info-box h-100">
                <span class="info-box-icon bg-info elevation-1"
                ><i class="fas fa-list"></i
                ></span>

                <div class="info-box-content">
                <span class="info-box-text">Total Grup</span>
                <h1 class="text-bold">
                    <?=esc($categorycount['total'])?>
                </h1>
                <small>&nbsp;</small>
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