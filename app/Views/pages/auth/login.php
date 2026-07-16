<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi Enuma Technology</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="<?=base_url()?>enumatech.png">

  <link rel="stylesheet" href="<?=base_url()?>assets/css/login.css">
</head>
<body>

  <!-- Panel visual -->
  <div class="visual-panel">
    <div class="visual-caption">
      <h2>Sistem Absensi<br>Enuma Technology</h2>
      <p>Pantau kehadiran, jadwal, dan aktivitas harian anggota dalam satu platform.</p>
    </div>
  </div>

  <!-- Panel form -->
  <div class="form-panel">
    <div class="form-card">
      <img class="brand-logo" src="<?=base_url()?>enumatech2.png" alt="Enuma Technology">

      <h1>Selamat Datang 👋</h1>
      <p class="subtitle">Masuk untuk melanjutkan ke sistem absensi</p>

      <?php if(session()->getFlashdata('error')): ?>
        <div class="alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <span><?= session()->getFlashdata('error'); ?></span>
        </div>
      <?php endif; ?>

      <form action="<?=base_url()?>login" method="post">
        <?=csrf_field();?>

        <div class="field">
          <input type="text" name="username" placeholder="Username" autocomplete="username" required autofocus>
          <span class="icon-left"><i class="fas fa-user"></i></span>
        </div>

        <div class="field">
          <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password" required>
          <span class="icon-left"><i class="fas fa-lock"></i></span>
          <button type="button" class="toggle-pass" id="togglePass" aria-label="Tampilkan password">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <button type="submit" class="btn-login">Masuk</button>
      </form>

      <p class="footer-note">&copy; <?=date('Y')?> Enuma Technology</p>
    </div>
  </div>

  <script>
    const toggle = document.getElementById('togglePass');
    const password = document.getElementById('password');
    toggle.addEventListener('click', () => {
      const show = password.type === 'password';
      password.type = show ? 'text' : 'password';
      toggle.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
      toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });
  </script>
</body>
</html>
