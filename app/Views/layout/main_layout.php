<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?> | Absensi Enuma Technology</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <?= $this->renderSection('css'); ?>
  <link rel="stylesheet" href="<?=base_url()?>assets/css/adminlte.min.css">
  <link rel="icon" type="image/x-icon" href="<?=base_url()?>enumatech.png">
  <style>
    .content-wrapper {
     background-image: url('<?=base_url()?>images/menu-background2.jpg');
     background-size: 500px 500px; 
    }
  </style>
  
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-dark bg-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <form class="form-inline ml-3">
      <h5 class="m-0 text-light">Absensi App</h5>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <span class="mr-2">
            <?php echo session('username'); ?>
          </span>
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">Hi, <?=session('user_name')?></span>
          <div class="dropdown-divider"></div>
          <a href="<?= base_url('profile') ?>" class="dropdown-item">
            <i class="fas fa-cogs mr-2"></i> User Setting
          </a>
          <a href="<?=base_url('logout')?>" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-power-off mr-2"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-lightblue elevation-4">
    <!-- Brand Logo -->
    <a href="<?=base_url()?>" class="brand-link bg-dark">
      <img src="<?=base_url()?>enumatech.png" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Enuma Technology</span>
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
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php if(session('user_role') != 'admin') { ?>
            <li class="nav-item">
            <a href="<?=base_url()?>log" class="nav-link <?php if($uri->getSegment(1) == 'log') { echo "active"; } ?>">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Log
              </p>
            </a>
            </li>
          <?php } ?>
          <?php if(session('user_role') != 'user') { ?>
          <li class="nav-item">
            <a href="<?=base_url()?>category" class="nav-link <?php if($uri->getSegment(1) == 'category') { echo "active"; } ?>">
              <i class="nav-icon fas fa-list"></i>
              <p>
                Grup
              </p>
            </a>
          </li>
          <?php } ?>
          <?php if(session('user_role') == 'admin') {?>
            <li class="nav-item">
              <a href="<?=base_url()?>user" class="nav-link <?php if($uri->getSegment(1) == 'user') { echo "active"; } ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Anggota
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url()?>admin" class="nav-link <?php if($uri->getSegment(1) == 'admin') { echo "active"; } ?>">
                <i class="nav-icon fas fa-user-secret"></i>
                <p>
                  Admin
                </p>
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
        <div class="row mb-2">
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
    <strong>Copyright &copy; <?=date('Y')?> <a href="https://enumatechnology.com">Enuma Technology</a>.</strong>
    All rights reserved.
  </footer>

  <div class="modal fade" id="logoutModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Apakah kamu yakin?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Pilih Log Out untuk mengahiri sesi.</p>
        </div>
        <div class="modal-footer justify-content-between">
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
