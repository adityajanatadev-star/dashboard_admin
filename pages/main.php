<?php
// pages/main.php — Halaman selamat datang
$user = currentUser();
?>

<div class="max-w-4xl mx-auto">

  <!-- Welcome Hero -->
  <div class="g-card p-8 mb-6 text-center relative overflow-hidden">

    <!-- Decorative background -->
    <div class="absolute inset-0 pointer-events-none" style="
      background: radial-gradient(ellipse 60% 80% at 50% 120%, rgba(14,165,233,0.08) 0%, transparent 70%);
    "></div>

    <!-- Wave SVG decoration -->
    <div class="absolute bottom-0 left-0 right-0 opacity-10" style="height:60px;">
      <svg viewBox="0 0 1200 60" preserveAspectRatio="none" style="width:100%;height:100%;">
        <path d="M0,30 Q200,0 400,30 Q600,60 800,30 Q1000,0 1200,30 L1200,60 L0,60 Z" fill="currentColor" class="text-ocean-400"/>
      </svg>
    </div>

    <div class="relative z-10">
      <!-- Greeting icon -->
      <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-5"
           style="background: linear-gradient(135deg, rgba(56,189,248,0.2), rgba(14,165,233,0.1)); border: 1px solid rgba(14,165,233,0.2);">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <path d="M5 28 Q10 16 20 16 Q30 16 35 28" stroke="#38bdf8" stroke-width="3" stroke-linecap="round"/>
          <path d="M2 34 Q10 22 20 22 Q30 22 38 34" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" opacity=".6"/>
          <circle cx="20" cy="10" r="5.5" fill="#38bdf8"/>
          <path d="M15 10 Q17 7 20 9 Q23 7 25 10" stroke="white" stroke-width="1.5" stroke-linecap="round" fill="none" opacity=".7"/>
        </svg>
      </div>

      <!-- Greeting text -->
      <h1 class="text-3xl md:text-4xl font-bold mb-3">
        <span style="color: var(--text-primary);">Halo, </span>
        <span class="welcome-wave"><?= htmlspecialchars($user['nama']) ?></span>
        <span style="font-size:2rem;"> 👋</span>
      </h1>

      <p class="text-base mb-1" style="color: var(--text-secondary);">
        Selamat datang di <strong style="color: var(--accent);"><?= APP_NAME ?></strong>
      </p>
      <p class="text-sm" style="color: var(--text-secondary);">
        Kamu login sebagai
        <span class="badge <?= $user['role'] === 'administrator' ? 'badge-admin' : ($user['role'] === 'editor' ? 'badge-editor' : 'badge-viewer') ?> ml-1">
          <?= ucfirst($user['role']) ?>
        </span>
      </p>

      <div class="flex items-center justify-center gap-2 mt-4 text-xs" style="color: var(--text-secondary);">
        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
        <span><?= date('l, d F Y — H:i') ?> WIB</span>
      </div>
    </div>
  </div>

  <!-- Stats cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <?php
    // Quick stats dari DB
    $totalUsers = db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $totalAdmin = db()->query("SELECT COUNT(*) FROM users WHERE role='administrator'")->fetchColumn();
    $totalOnline = db()->query("SELECT COUNT(*) FROM users WHERE last_login >= NOW() - INTERVAL 1 HOUR")->fetchColumn();

    $stats = [
      ['icon' => 'users',       'label' => 'Total User',       'value' => $totalUsers,  'color' => '#0ea5e9'],
      ['icon' => 'shield-check','label' => 'Administrator',    'value' => $totalAdmin,  'color' => '#ef4444'],
      ['icon' => 'activity',    'label' => 'Login 1 Jam Ini',  'value' => $totalOnline, 'color' => '#10b981'],
    ];
    foreach ($stats as $s):
    ?>
    <div class="g-card p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
           style="background: <?= $s['color'] ?>22;">
        <i data-lucide="<?= $s['icon'] ?>" class="w-5 h-5" style="color:<?= $s['color'] ?>;"></i>
      </div>
      <div>
        <div class="text-2xl font-bold" style="color: var(--text-primary);"><?= $s['value'] ?></div>
        <div class="text-xs" style="color: var(--text-secondary);"><?= $s['label'] ?></div>
      </div>
    </div>
    <?php endforeach; ?>

  </div>

  <!-- Info card -->
  <div class="g-card p-6">
    <h3 class="font-semibold mb-3 flex items-center gap-2" style="color: var(--text-primary);">
      <i data-lucide="info" class="w-4 h-4 text-ocean-400"></i>
      Info Akun Kamu
    </h3>
    <div class="grid grid-cols-2 gap-3 text-sm">
      <?php
      $stmt = db()->prepare('SELECT username, email, last_login, last_changed_password FROM users WHERE id = ?');
      $stmt->execute([$user['id']]);
      $info = $stmt->fetch();

      $infoRows = [
        ['Username',        $info['username']],
        ['Email',           $info['email']],
        ['Login Terakhir',  $info['last_login'] ? date('d M Y H:i', strtotime($info['last_login'])) : 'Baru pertama kali'],
        ['Ganti Password',  $info['last_changed_password'] ? date('d M Y H:i', strtotime($info['last_changed_password'])) : 'Belum pernah'],
      ];
      foreach ($infoRows as [$label, $val]):
      ?>
      <div>
        <div class="text-xs font-medium mb-0.5" style="color: var(--text-secondary);"><?= $label ?></div>
        <div style="color: var(--text-primary);"><?= htmlspecialchars($val) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<script>lucide.createIcons();</script>
