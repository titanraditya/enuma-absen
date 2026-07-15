<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?> | Absensi Enuma Technology</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <?= $this->renderSection('css'); ?>
  <link rel="stylesheet" href="<?=base_url()?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/css/modern.css">
  <link rel="icon" type="image/x-icon" href="<?=base_url()?>enumatech.png">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <span class="navbar-app-title ml-2 d-none d-sm-inline">Absensi App</span>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link user-chip" data-toggle="dropdown" href="#">
          <span class="avatar"><?= strtoupper(substr(session('user_name') ?? '?', 0, 1)); ?></span>
          <span class="chip-name d-none d-sm-inline"><?= session('user_name'); ?></span>
          <i class="fas fa-chevron-down" style="font-size:.65rem"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <span class="dropdown-item dropdown-header text-left">
            <b><?= session('user_name'); ?></b><br>
            <small class="text-muted">@<?= session('username'); ?> &middot; <?= ucfirst(session('user_role')); ?></small>
          </span>
          <div class="dropdown-divider"></div>
          <a href="<?= base_url('profile') ?>" class="dropdown-item">
            <i class="fas fa-user-cog mr-2 text-muted"></i> Pengaturan Akun
          </a>
          <a href="#" class="dropdown-item text-danger" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-lightblue">
    <!-- Brand Logo -->
    <a href="<?=base_url()?>" class="brand-link">
      <img src="<?=base_url()?>enumatech.png" alt="Enuma Technology" class="brand-image img-circle">
      <span class="brand-text">Enuma Technology</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <?php $uri = current_url(true);?>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="<?=base_url()?>dashboard" class="nav-link <?php if($uri->getSegment(1) == 'dashboard') { echo "active"; } ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <?php if(session('user_role') != 'admin') { ?>
            <li class="nav-item">
            <a href="<?=base_url()?>log" class="nav-link <?php if($uri->getSegment(1) == 'log') { echo "active"; } ?>">
              <i class="nav-icon fas fa-book"></i>
              <p>Log</p>
            </a>
            </li>
          <?php } ?>
          <?php if(session('user_role') != 'user') { ?>
          <li class="nav-item">
            <a href="<?=base_url()?>category" class="nav-link <?php if($uri->getSegment(1) == 'category') { echo "active"; } ?>">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>Grup</p>
            </a>
          </li>
          <?php } ?>
          <?php if(session('user_role') == 'admin') {?>
            <li class="nav-item">
              <a href="<?=base_url()?>user" class="nav-link <?php if($uri->getSegment(1) == 'user') { echo "active"; } ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>Anggota</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url()?>admin" class="nav-link <?php if($uri->getSegment(1) == 'admin') { echo "active"; } ?>">
                <i class="nav-icon fas fa-user-shield"></i>
                <p>Admin</p>
              </a>
            </li>
          <?php } ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2 align-items-center">
          <div class="col-sm-6">
            <p class="m-0 h4"><b><?= $subtitle; ?></b></p>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <?= $this->renderSection('action-button'); ?>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?= $this->renderSection('content'); ?>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>&copy; <?=date('Y')?> <a href="https://enumatechnology.com">Enuma Technology</a>.</strong>
    All rights reserved.
  </footer>

  <div class="modal fade" id="logoutModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center pt-4">
          <div class="mb-3">
            <i class="fas fa-sign-out-alt text-danger" style="font-size:2rem"></i>
          </div>
          <h5 class="font-weight-bold mb-1">Keluar dari sistem?</h5>
          <p class="text-muted mb-0">Sesi kamu akan diakhiri.</p>
        </div>
        <div class="modal-footer justify-content-center border-0 pb-4">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <a href="<?=base_url()?>logout" class="btn btn-danger">Log Out</a>
        </div>
      </div>
    </div>
  </div>
  <?= $this->renderSection('modal'); ?>

</div>
<!-- ./wrapper -->

<script src="<?=base_url()?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?=base_url()?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<?= $this->renderSection('js'); ?>
<script src="<?=base_url()?>assets/js/adminlte.js"></script>
</body>
</html>
