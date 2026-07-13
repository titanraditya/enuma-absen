<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi Enuma Technology</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url()?>assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="icon" type="image/x-icon" href="<?=base_url()?>enumatech.png">

  <style>
    :root {
      --brand: #1c7ed6;
      --brand-dark: #1864ab;
      --ink: #1b2430;
      --muted: #6b7684;
      --line: #e3e8ef;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
      color: var(--ink);
      min-height: 100vh;
      display: flex;
      background: #f4f6fa;
    }

    /* ===== Panel kiri (visual) ===== */
    .visual-panel {
      flex: 1.2;
      position: relative;
      display: none;
      background: url('<?=base_url()?>images/login-background2.jpg') center/cover no-repeat;
      overflow: hidden;
    }
    .visual-panel::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(200deg, rgba(12, 36, 64, .15) 0%, rgba(16, 54, 94, .55) 55%, rgba(10, 32, 58, .85) 100%);
    }
    .visual-caption {
      position: absolute;
      z-index: 1;
      bottom: 48px;
      left: 48px;
      right: 48px;
      color: #fff;
    }
    .visual-caption h2 {
      font-size: 1.75rem;
      font-weight: 800;
      line-height: 1.3;
      margin-bottom: .5rem;
      text-shadow: 0 2px 12px rgba(0,0,0,.35);
    }
    .visual-caption p {
      font-size: .95rem;
      opacity: .85;
      max-width: 420px;
      text-shadow: 0 1px 8px rgba(0,0,0,.4);
    }

    /* ===== Panel kanan (form) ===== */
    .form-panel {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 24px;
    }
    .form-card {
      width: 100%;
      max-width: 400px;
    }
    .brand-logo {
      display: block;
      width: 210px;
      max-width: 70%;
      margin: 0 auto 28px;
    }
    .form-card h1 {
      font-size: 1.5rem;
      font-weight: 800;
      text-align: center;
      margin-bottom: 6px;
    }
    .form-card .subtitle {
      text-align: center;
      color: var(--muted);
      font-size: .92rem;
      margin-bottom: 28px;
    }

    .alert-error {
      display: flex;
      align-items: center;
      gap: 10px;
      background: #fff0f0;
      border: 1px solid #ffc9c9;
      color: #c92a2a;
      font-size: .88rem;
      border-radius: 10px;
      padding: 11px 14px;
      margin-bottom: 20px;
    }

    .field {
      position: relative;
      margin-bottom: 16px;
    }
    .field .icon-left {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #9aa4b2;
      font-size: .9rem;
      pointer-events: none;
      transition: color .2s;
    }
    .field input {
      width: 100%;
      height: 52px;
      padding: 0 48px 0 44px;
      font: inherit;
      font-size: .95rem;
      color: var(--ink);
      background: #fff;
      border: 1.5px solid var(--line);
      border-radius: 12px;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .field input::placeholder { color: #9aa4b2; }
    .field input:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 4px rgba(28, 126, 214, .12);
    }
    .field input:focus ~ .icon-left { color: var(--brand); }

    .toggle-pass {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: none;
      color: #9aa4b2;
      width: 38px;
      height: 38px;
      border-radius: 10px;
      cursor: pointer;
      transition: color .2s, background .2s;
    }
    .toggle-pass:hover { color: var(--brand); background: #f0f6fd; }

    .btn-login {
      width: 100%;
      height: 52px;
      margin-top: 6px;
      font: inherit;
      font-size: 1rem;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
      border: 0;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 8px 20px -6px rgba(28, 126, 214, .5);
      transition: transform .15s, box-shadow .15s, filter .15s;
    }
    .btn-login:hover {
      filter: brightness(1.06);
      transform: translateY(-1px);
      box-shadow: 0 12px 24px -6px rgba(28, 126, 214, .55);
    }
    .btn-login:active { transform: translateY(0); }

    .footer-note {
      text-align: center;
      color: #9aa4b2;
      font-size: .8rem;
      margin-top: 32px;
    }

    /* ===== Responsif ===== */
    @media (min-width: 900px) {
      .visual-panel { display: block; }
    }
    @media (max-width: 899px) {
      body {
        background: url('<?=base_url()?>images/login-background2.jpg') center/cover no-repeat fixed;
      }
      .form-panel { padding: 24px 16px; }
      .form-card {
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(8px);
        border-radius: 20px;
        padding: 36px 28px;
        box-shadow: 0 24px 60px -12px rgba(10, 32, 58, .45);
      }
    }
  </style>
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
          <i class="fas fa-circle-exclamation"></i>
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
