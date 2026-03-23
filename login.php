<?php
// login.php
require_once __DIR__ . '/includes/auth.php';

// Kalau udah login, langsung redirect ke dashboard
if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF sederhana: cek origin / referer
    $result = attemptLogin(
        $_POST['username'] ?? '',
        $_POST['password'] ?? ''
    );
    if ($result['success']) {
        header('Location: /dashboard/index.php');
        exit;
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — <?= htmlspecialchars(APP_NAME) ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet"/>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <style>
    * { font-family: 'Poppins', sans-serif; }

    /* ── Ocean canvas background ── */
    #ocean-canvas {
      position: fixed;
      inset: 0;
      z-index: 0;
    }

    /* ── Glassmorphism card ── */
    .glass-card {
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.25);
      box-shadow:
        0 8px 32px rgba(0, 80, 160, 0.25),
        inset 0 1px 0 rgba(255,255,255,0.3);
    }

    .glass-input {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
      color: white;
    }
    .glass-input::placeholder { color: rgba(255,255,255,0.5); }
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.22);
      border-color: rgba(100, 210, 255, 0.7);
      box-shadow: 0 0 0 3px rgba(100, 210, 255, 0.2);
      outline: none;
    }

    .glass-btn {
      background: linear-gradient(135deg, rgba(56, 189, 248, 0.8), rgba(14, 165, 233, 0.9));
      border: 1px solid rgba(255,255,255,0.3);
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
    }
    .glass-btn:hover {
      background: linear-gradient(135deg, rgba(56, 189, 248, 1), rgba(14, 165, 233, 1));
      box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6);
      transform: translateY(-1px);
    }
    .glass-btn:active { transform: translateY(0); }

    .logo-glow {
      text-shadow: 0 0 20px rgba(100, 210, 255, 0.8), 0 0 40px rgba(56, 189, 248, 0.4);
    }

    .wave-text {
      background: linear-gradient(90deg, #bae6fd, #e0f2fe, #7dd3fc, #38bdf8);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: shine 3s linear infinite;
    }
    @keyframes shine {
      to { background-position: 200% center; }
    }

    .error-glass {
      background: rgba(239, 68, 68, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.4);
      backdrop-filter: blur(8px);
    }

    /* Password toggle icon */
    .toggle-pw { cursor: pointer; color: rgba(255,255,255,0.6); transition: color .2s; }
    .toggle-pw:hover { color: #7dd3fc; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center overflow-hidden">

  <!-- Ocean Wave Canvas -->
  <canvas id="ocean-canvas"></canvas>

  <!-- Overlay gradient -->
  <div class="fixed inset-0 z-10" style="background: linear-gradient(180deg, rgba(2,20,60,0.55) 0%, rgba(1,40,80,0.4) 100%);"></div>

  <!-- Login Card -->
  <div class="relative z-20 w-full max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 md:p-10 animate__animated animate__fadeInUp animate__faster">

      <!-- Logo & Title -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
             style="background: linear-gradient(135deg, rgba(56,189,248,0.4), rgba(14,165,233,0.2)); border: 1px solid rgba(255,255,255,0.3);">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
            <path d="M4 20 Q8 12 16 12 Q24 12 28 20" stroke="#7dd3fc" stroke-width="2.5" stroke-linecap="round" fill="none"/>
            <path d="M2 24 Q8 16 16 16 Q24 16 30 24" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" fill="none" opacity=".7"/>
            <circle cx="16" cy="8" r="4" fill="#38bdf8" opacity=".9"/>
          </svg>
        </div>
        <h1 class="text-3xl font-bold logo-glow text-white mb-1">
          <span class="wave-text"><?= APP_NAME ?></span>
        </h1>
        <p class="text-sm text-blue-200/70 font-light">Selamat datang kembali 👋</p>
      </div>

      <!-- Error Alert -->
      <?php if ($error): ?>
      <div class="error-glass rounded-xl px-4 py-3 mb-5 flex items-center gap-2 animate__animated animate__shakeX">
        <i data-lucide="alert-circle" class="w-4 h-4 text-red-300 shrink-0"></i>
        <p class="text-red-200 text-sm"><?= htmlspecialchars($error) ?></p>
      </div>
      <?php endif; ?>

      <!-- Form -->
      <form method="POST" action="/dashboard/login.php" autocomplete="off">

        <!-- Username -->
        <div class="mb-4">
          <label class="block text-blue-100/80 text-sm font-medium mb-1.5">Username</label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-300/60">
              <i data-lucide="user" class="w-4 h-4"></i>
            </span>
            <input
              type="text"
              name="username"
              placeholder="Masukkan username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              class="glass-input w-full rounded-xl pl-10 pr-4 py-3 text-sm"
              required
              autocomplete="username"
            />
          </div>
        </div>

        <!-- Password -->
        <div class="mb-6">
          <label class="block text-blue-100/80 text-sm font-medium mb-1.5">Password</label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-300/60">
              <i data-lucide="lock" class="w-4 h-4"></i>
            </span>
            <input
              id="pw-input"
              type="password"
              name="password"
              placeholder="Masukkan password"
              class="glass-input w-full rounded-xl pl-10 pr-10 py-3 text-sm"
              required
              autocomplete="current-password"
            />
            <span class="toggle-pw absolute right-3.5 top-1/2 -translate-y-1/2" onclick="togglePw()">
              <i id="pw-eye" data-lucide="eye" class="w-4 h-4"></i>
            </span>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="glass-btn w-full text-white font-semibold py-3 rounded-xl text-sm tracking-wide">
          Masuk ke Dashboard
        </button>

      </form>

      <p class="text-center text-blue-200/40 text-xs mt-6">
        <?= APP_NAME ?> v<?= APP_VERSION ?> &mdash; Powered by PHP Native
      </p>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Init Lucide icons
    lucide.createIcons();

    // Toggle password visibility
    function togglePw() {
      const input = document.getElementById('pw-input');
      const icon  = document.getElementById('pw-eye');
      if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
      } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    }

    // ── Ocean Wave Animation ──────────────────
    const canvas = document.getElementById('ocean-canvas');
    const ctx    = canvas.getContext('2d');
    let W, H, t = 0;

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Wave config: [amplitude, frequency, speed, opacity, yOffset] - Kecepatan sedang untuk animasi natural
    const waves = [
      { A: 60,  f: 0.008, spd: 0.016, op: 0.20, y: 0.70, color: [20, 100, 200]  }, // Kecepatan sedang
      { A: 45,  f: 0.010, spd: 0.018, op: 0.18, y: 0.75, color: [10, 140, 220]  }, // Kecepatan sedang
      { A: 35,  f: 0.013, spd: 0.015, op: 0.22, y: 0.65, color: [30, 80, 180]   }, // Kecepatan sedang
      { A: 25,  f: 0.016, spd: 0.012, op: 0.15, y: 0.80, color: [50, 170, 230]  }, // Kecepatan sedang
      { A: 50,  f: 0.007, spd: 0.019, op: 0.12, y: 0.55, color: [5,  60,  150]  }, // Kecepatan sedang
    ];

    function drawWave(w, time) {
      ctx.beginPath();
      ctx.moveTo(0, H);
      for (let x = 0; x <= W; x += 4) {
        const y = w.y * H + Math.sin(x * w.f + time * w.spd * 6) * w.A  // Kecepatan dikurangi
                           + Math.sin(x * w.f * 1.7 + time * w.spd * 4) * (w.A * 0.4);  // Kecepatan dikurangi
        ctx.lineTo(x, y);
      }
      ctx.lineTo(W, H);
      ctx.closePath();
      const [r, g, b] = w.color;
      ctx.fillStyle = `rgba(${r},${g},${b},${w.op})`;
      ctx.fill();
    }

    function draw() {
      // Sky-to-deep-ocean gradient
      const grad = ctx.createLinearGradient(0, 0, 0, H);
      grad.addColorStop(0,   '#020c1e');
      grad.addColorStop(0.4, '#031930');
      grad.addColorStop(0.7, '#042847');
      grad.addColorStop(1,   '#053560');
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, W, H);

      // Stars (subtle dots)
      if (t === 0) {
        canvas._stars = Array.from({length: 120}, () => ({
          x: Math.random() * W,
          y: Math.random() * H * 0.55,
          r: Math.random() * 1.2 + 0.3,
          o: Math.random() * 0.6 + 0.2,
        }));
      }
      (canvas._stars || []).forEach(s => {
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(200,230,255,${s.o * (0.7 + 0.3 * Math.sin(t * 0.02 + s.x))})`;
        ctx.fill();
      });

      // Draw waves back to front
      waves.forEach(w => drawWave(w, t));

      // Shimmer highlight on top wave
      const shimmer = ctx.createLinearGradient(0, H * 0.6, 0, H);
      shimmer.addColorStop(0, 'rgba(100,200,255,0.06)');
      shimmer.addColorStop(1, 'rgba(0,60,120,0.0)');
      ctx.fillStyle = shimmer;
      ctx.fillRect(0, H * 0.6, W, H * 0.4);

      t++;
      requestAnimationFrame(draw);
    }
    draw();
  </script>
</body>
</html>
