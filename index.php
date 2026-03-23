<?php
// index.php — Dashboard utama
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = currentUser();
$activePage = $_GET['page'] ?? 'main';
$allowedPages = ['main', 'menu1', 'users', 'dino'];

// Sanitasi page param
if (!in_array($activePage, $allowedPages, true)) {
    $activePage = 'main';
}
?>
<!DOCTYPE html>
<html lang="id" class="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= APP_NAME ?> — Dashboard</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { poppins: ['Poppins','sans-serif'], grotesk: ['Space Grotesk','sans-serif'] },
          colors: {
            ocean: {
              50:  '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd',
              300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9',
              600: '#0284c7', 700: '#0369a1', 800: '#075985',
              900: '#0c4a6e',
            }
          }
        }
      }
    }
  </script>

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <!-- GSAP -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

  <style>
    * { font-family: 'Poppins', sans-serif; }

    /* ── CSS Variables ── */
    :root {
      --sidebar-w:      260px;
      --sidebar-collapsed: 72px;
      --glass-bg:       rgba(255,255,255,0.65);
      --glass-border:   rgba(255,255,255,0.8);
      --glass-shadow:   0 8px 32px rgba(14,165,233,0.10);
      --accent:         #0ea5e9;
      --accent-light:   #bae6fd;
      --text-primary:   #0f172a;
      --text-secondary: #64748b;
      --bg-page:        #f0f9ff;
      --sidebar-item-hover: rgba(14,165,233,0.12);
      --sidebar-item-active: rgba(14,165,233,0.20);
    }
    .dark {
      --glass-bg:       rgba(10,30,60,0.72);
      --glass-border:   rgba(56,189,248,0.18);
      --glass-shadow:   0 8px 32px rgba(0,0,0,0.35);
      --text-primary:   #e0f2fe;
      --text-secondary: #7dd3fc;
      --bg-page:        #020c1e;
      --sidebar-item-hover: rgba(56,189,248,0.12);
      --sidebar-item-active: rgba(56,189,248,0.22);
    }

    html, body { height: 100%; overflow: hidden; }
    body {
      background: var(--bg-page);
      color: var(--text-primary);
      transition: background .4s, color .4s;
    }
    .dark body, .dark {
      background: var(--bg-page);
    }

    /* ── Background pattern ── */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0;
      background:
        radial-gradient(ellipse 80% 50% at 20% 10%, rgba(14,165,233,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 90%, rgba(56,189,248,0.06) 0%, transparent 60%);
      pointer-events: none;
    }
    .dark body::before {
      background:
        radial-gradient(ellipse 80% 50% at 20% 10%, rgba(14,165,233,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 90%, rgba(2,20,60,0.8) 0%, transparent 60%);
    }

    /* ── Layout ── */
    #app {
      position: relative; z-index: 1;
      display: flex; height: 100vh; overflow: hidden;
    }

    /* ── Sidebar ── */
    #sidebar {
      width: var(--sidebar-w);
      min-width: var(--sidebar-w);
      height: 100vh;
      display: flex; flex-direction: column;
      background: var(--glass-bg);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      border-right: 1px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
      transition: width .35s cubic-bezier(.4,0,.2,1), min-width .35s cubic-bezier(.4,0,.2,1);
      overflow: hidden;
      z-index: 100;
    }
    #sidebar.collapsed {
      width: var(--sidebar-collapsed);
      min-width: var(--sidebar-collapsed);
    }

    /* Sidebar header */
    .sidebar-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 1.25rem 1rem;
      border-bottom: 1px solid var(--glass-border);
      min-height: 70px;
    }
    .sidebar-logo {
      display: flex; align-items: center; gap: .7rem;
      overflow: hidden; white-space: nowrap;
    }
    .logo-icon {
      width: 38px; height: 38px; border-radius: 10px; shrink: 0;
      background: linear-gradient(135deg, #38bdf8, #0ea5e9);
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 12px rgba(14,165,233,0.35);
      flex-shrink: 0;
    }
    .logo-text {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.15rem; font-weight: 700;
      background: linear-gradient(135deg, #0ea5e9, #0369a1);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
      opacity: 1; transition: opacity .3s;
    }
    .dark .logo-text {
      background: linear-gradient(135deg, #7dd3fc, #38bdf8);
      -webkit-background-clip: text; background-clip: text;
    }
    #sidebar.collapsed .logo-text,
    #sidebar.collapsed .menu-label { opacity: 0; width: 0; overflow: hidden; }

    /* Hamburger btn */
    .hamburger-btn {
      padding: 6px; border-radius: 8px; cursor: pointer;
      color: var(--text-secondary);
      background: transparent; border: none;
      transition: background .2s, color .2s;
      flex-shrink: 0;
    }
    .hamburger-btn:hover { background: var(--sidebar-item-hover); color: var(--accent); }

    /* Nav items */
    .nav-section-label {
      font-size: 0.65rem; font-weight: 600; letter-spacing: .1em;
      text-transform: uppercase; color: var(--text-secondary);
      padding: .75rem 1.1rem .3rem;
      white-space: nowrap; overflow: hidden;
      transition: opacity .3s;
    }
    #sidebar.collapsed .nav-section-label { opacity: 0; }

    .nav-item {
      display: flex; align-items: center; gap: .75rem;
      padding: .65rem 1rem; margin: 2px .6rem;
      border-radius: 10px; cursor: pointer;
      color: var(--text-secondary);
      transition: background .2s, color .2s, transform .15s;
      white-space: nowrap; overflow: hidden;
      text-decoration: none;
      position: relative;
    }
    .nav-item:hover {
      background: var(--sidebar-item-hover);
      color: var(--accent); transform: translateX(2px);
    }
    .nav-item.active {
      background: var(--sidebar-item-active);
      color: var(--accent);
      font-weight: 600;
    }
    .nav-item.active::before {
      content: '';
      position: absolute; left: -0.6rem; top: 20%; bottom: 20%;
      width: 3px; border-radius: 4px;
      background: var(--accent);
    }
    .nav-icon { flex-shrink: 0; width: 18px; height: 18px; }
    .menu-label { font-size: .875rem; overflow: hidden; transition: opacity .3s; }
    #sidebar.collapsed .menu-label { opacity: 0; width: 0; }

    /* Tooltip on collapsed */
    #sidebar.collapsed .nav-item { justify-content: center; padding: .65rem; }
    #sidebar.collapsed .nav-item::after {
      content: attr(data-tooltip);
      position: absolute; left: calc(100% + 12px); top: 50%;
      transform: translateY(-50%);
      background: rgba(15,23,42,0.9); color: #e2e8f0;
      font-size: .75rem; padding: 4px 10px; border-radius: 6px;
      white-space: nowrap; pointer-events: none;
      opacity: 0; transition: opacity .2s;
    }
    #sidebar.collapsed .nav-item:hover::after { opacity: 1; }

    /* Sidebar footer (user info) */
    .sidebar-footer {
      margin-top: auto;
      border-top: 1px solid var(--glass-border);
      padding: 1rem;
      display: flex; align-items: center; gap: .75rem;
      overflow: hidden;
    }
    .user-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, #0ea5e9, #0369a1);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: .8rem; font-weight: 600;
      flex-shrink: 0;
    }
    .user-info { overflow: hidden; flex: 1; }
    .user-name { font-size: .82rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-role { font-size: .7rem; color: var(--accent); white-space: nowrap; }
    .logout-btn {
      flex-shrink: 0; padding: 5px; border-radius: 7px; cursor: pointer;
      color: var(--text-secondary); background: transparent; border: none;
      transition: background .2s, color .2s;
    }
    .logout-btn:hover { background: rgba(239,68,68,.15); color: #ef4444; }
    #sidebar.collapsed .user-info,
    #sidebar.collapsed .logout-btn { display: none; }

    /* ── Dark mode toggle ── */
    .dark-toggle {
      display: flex; align-items: center; gap: .5rem;
      padding: .5rem 1rem; margin: .5rem .6rem;
      border-radius: 10px;
    }
    .toggle-track {
      width: 38px; height: 20px; border-radius: 10px;
      background: #cbd5e1; position: relative;
      cursor: pointer; transition: background .3s; flex-shrink: 0;
    }
    .dark .toggle-track { background: #0ea5e9; }
    .toggle-thumb {
      position: absolute; width: 14px; height: 14px; border-radius: 50%;
      background: white; top: 3px; left: 3px; transition: transform .3s;
      box-shadow: 0 1px 3px rgba(0,0,0,.25);
    }
    .dark .toggle-thumb { transform: translateX(18px); }
    .toggle-label { font-size: .78rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; }
    #sidebar.collapsed .toggle-label { opacity: 0; width: 0; }
    #sidebar.collapsed .dark-toggle { justify-content: center; padding: .5rem; }

    /* ── Main content area ── */
    #main-content {
      flex: 1; overflow-y: auto; overflow-x: hidden;
      display: flex; flex-direction: column;
      transition: all .35s;
    }

    /* Topbar */
    #topbar {
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--glass-border);
      padding: .85rem 1.5rem;
      display: flex; align-items: center; justify-between; gap: 1rem;
      min-height: 70px; flex-shrink: 0;
    }
    .topbar-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.05rem; font-weight: 600;
      color: var(--text-primary);
    }
    .topbar-breadcrumb { font-size: .78rem; color: var(--text-secondary); }

    /* Content panel */
    #content-panel {
      flex: 1; padding: 1.75rem;
      overflow-y: auto;
    }

    /* ── Glass card component ── */
    .g-card {
      background: var(--glass-bg);
      backdrop-filter: blur(16px) saturate(180%);
      border: 1px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
      border-radius: 16px;
    }

    /* ── Table styles ── */
    .dash-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .dash-table thead tr th {
      background: rgba(14,165,233,0.08);
      font-size: .78rem; font-weight: 600; letter-spacing: .05em;
      text-transform: uppercase; color: var(--text-secondary);
      padding: .75rem 1rem;
    }
    .dash-table tbody tr { transition: background .15s; }
    .dash-table tbody tr:hover { background: var(--sidebar-item-hover); }
    .dash-table tbody tr td { padding: .75rem 1rem; font-size: .875rem; border-bottom: 1px solid var(--glass-border); }
    .dash-table thead tr th:first-child { border-radius: 8px 0 0 0; }
    .dash-table thead tr th:last-child  { border-radius: 0 8px 0 0; }

    /* Role badges */
    .badge { font-size: .7rem; font-weight: 600; padding: 2px 10px; border-radius: 999px; }
    .badge-admin    { background: rgba(239,68,68,.15); color: #dc2626; }
    .badge-editor   { background: rgba(245,158,11,.15); color: #d97706; }
    .badge-viewer   { background: rgba(14,165,233,.15); color: #0ea5e9; }

    /* Modal */
    #user-modal, #delete-modal {
      position: fixed; inset: 0; z-index: 9999;
      background: rgba(0,0,0,0.5);
      display: flex; align-items: center; justify-content: center;
      opacity: 0; pointer-events: none; visibility: hidden;
    }
    #user-modal.open, #delete-modal.open { opacity: 1 !important; pointer-events: all !important; visibility: visible !important; }
    .modal-box {
      background: rgba(255,255,255,0.9);
      border: 1px solid rgba(14,165,233,0.25);
      border-radius: 20px; padding: 2rem;
      width: 100%; max-width: 480px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      transform: scale(.95) translateY(10px);
      transition: transform .25s;
    }
    .dark .modal-box {
      background: rgba(10,30,60,0.9);
      border-color: rgba(56,189,248,0.25);
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }

    /* Form inputs */
    .dash-input {
      width: 100%;
      background: rgba(255,255,255,0.5);
      border: 1px solid rgba(14,165,233,0.25);
      border-radius: 10px; padding: .6rem .9rem;
      font-size: .875rem; color: var(--text-primary);
      transition: border-color .2s, box-shadow .2s;
    }
    .dark .dash-input {
      background: rgba(5,30,70,0.6);
      color: #e0f2fe; border-color: rgba(56,189,248,0.25);
    }
    .dash-input:focus {
      outline: none;
      border-color: #0ea5e9;
      box-shadow: 0 0 0 3px rgba(14,165,233,0.15);
    }
    .dash-label { font-size: .82rem; font-weight: 500; color: var(--text-secondary); margin-bottom: .35rem; display: block; }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #38bdf8, #0ea5e9);
      color: white; border: none; border-radius: 10px;
      padding: .6rem 1.25rem; font-size: .875rem; font-weight: 600;
      cursor: pointer; transition: opacity .2s, transform .15s;
      box-shadow: 0 4px 12px rgba(14,165,233,.35);
    }
    .btn-primary:hover { opacity: .9; transform: translateY(-1px); }
    .btn-danger  { background: linear-gradient(135deg, #f87171, #ef4444); box-shadow: 0 4px 12px rgba(239,68,68,.3); }
    .btn-outline {
      background: transparent; border: 1px solid rgba(14,165,233,.4);
      color: var(--accent); border-radius: 10px;
      padding: .6rem 1.25rem; font-size: .875rem; font-weight: 500;
      cursor: pointer; transition: background .2s;
    }
    .btn-outline:hover { background: var(--sidebar-item-hover); }

    /* Welcome page */
    .welcome-wave {
      background: linear-gradient(90deg, #0ea5e9, #38bdf8, #7dd3fc, #38bdf8);
      background-size: 200% auto;
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
      animation: shine 8s linear infinite; /* Animasi gelombang laut yang lebih lambat */
    }
    @keyframes shine { to { background-position: 200% center; } }

    /* Scrollbar styling */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(14,165,233,.3); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(14,165,233,.5); }
  </style>
</head>
<body>
<div id="app">

  <!-- ══════════════ SIDEBAR ══════════════ -->
  <aside id="sidebar">

    <!-- Header: logo + hamburger -->
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
            <path d="M3 16 Q6 10 12 10 Q18 10 21 16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
            <path d="M1 20 Q6 13 12 13 Q18 13 23 20" stroke="rgba(255,255,255,.7)" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="6" r="3" fill="white" opacity=".9"/>
          </svg>
        </div>
        <span class="logo-text"><?= APP_NAME ?></span>
      </div>
      <button class="hamburger-btn" id="hamburger-btn" title="Toggle sidebar">
        <i data-lucide="menu" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Dark mode toggle -->
    <div class="dark-toggle">
      <div class="toggle-track" id="dark-toggle" title="Toggle dark mode">
        <div class="toggle-thumb"></div>
      </div>
      <span class="toggle-label" id="toggle-label">Mode Gelap</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2">

      <div class="nav-section-label">Main</div>

      <a class="nav-item <?= $activePage === 'main' ? 'active' : '' ?>"
         href="?page=main" data-tooltip="Dashboard" data-page="main">
        <i data-lucide="layout-dashboard" class="nav-icon"></i>
        <span class="menu-label">Dashboard</span>
      </a>

      <div class="nav-section-label">Menu</div>

      <a class="nav-item <?= $activePage === 'menu1' ? 'active' : '' ?>"
         href="?page=menu1" data-tooltip="Menu 1" data-page="menu1">
        <i data-lucide="layers" class="nav-icon"></i>
        <span class="menu-label">Menu 1</span>
      </a>

      <a class="nav-item <?= $activePage === 'dino' ? 'active' : '' ?>"
         href="?page=dino" data-tooltip="Dino Tools" data-page="dino">
        <i data-lucide="dinosaur" class="nav-icon"></i>
        <span class="menu-label">Dino</span>
      </a>

      <?php if (hasRole('administrator')): ?>
      <div class="nav-section-label">Admin</div>
      <a class="nav-item <?= $activePage === 'users' ? 'active' : '' ?>"
         href="?page=users" data-tooltip="Users" data-page="users">
        <i data-lucide="users" class="nav-icon"></i>
        <span class="menu-label">Users</span>
      </a>
      <?php endif; ?>

    </nav>

    <!-- Footer: user info + logout -->
    <div class="sidebar-footer">
      <div class="user-avatar">
        <?= strtoupper(substr($user['nama'], 0, 2)) ?>
      </div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars($user['nama']) ?></div>
        <div class="user-role"><?= ucfirst($user['role']) ?></div>
      </div>
      <a href="/dashboard/logout.php" class="logout-btn" title="Logout">
        <i data-lucide="log-out" class="w-4 h-4"></i>
      </a>
    </div>

  </aside>
  <!-- ══════════════ END SIDEBAR ══════════════ -->

  <!-- ══════════════ MAIN CONTENT ══════════════ -->
  <div id="main-content">

    <!-- Topbar -->
    <div id="topbar">
      <div>
        <div class="topbar-title" id="topbar-title">
          <?php
            $titles = ['main' => 'Dashboard', 'menu1' => 'Menu 1', 'users' => 'User Management'];
            echo htmlspecialchars($titles[$activePage] ?? 'Dashboard');
          ?>
        </div>
        <div class="topbar-breadcrumb">
          <?= APP_NAME ?> / <?= htmlspecialchars($titles[$activePage] ?? 'Dashboard') ?>
        </div>
      </div>
      <div class="flex items-center gap-3 ml-auto">
        <!-- Current user badge -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl" style="background: var(--sidebar-item-hover);">
          <i data-lucide="user-circle" class="w-4 h-4 text-ocean-500"></i>
          <span class="text-sm font-medium" style="color: var(--text-primary);">
            <?= htmlspecialchars($user['username']) ?>
          </span>
          <span class="badge badge-<?= $user['role'] === 'administrator' ? 'admin' : ($user['role'] === 'editor' ? 'editor' : 'viewer') ?>">
            <?= ucfirst($user['role']) ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Content Panel -->
    <div id="content-panel">
      <?php
        // Include halaman yang sesuai
        $pageFile = __DIR__ . "/pages/{$activePage}.php";
        if (file_exists($pageFile)) {
            include $pageFile;
        } else {
            echo '<p class="text-center text-gray-400 mt-10">Halaman tidak ditemukan.</p>';
        }
      ?>
    </div>

  </div>
  <!-- ══════════════ END MAIN CONTENT ══════════════ -->

</div>

<!-- ══════════════ SCRIPTS ══════════════ -->
<script>
  lucide.createIcons();

  // ── Sidebar toggle ──
  const sidebar = document.getElementById('sidebar');
  const btn     = document.getElementById('hamburger-btn');
  let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

  function applySidebar() {
    sidebar.classList.toggle('collapsed', isCollapsed);
  }
  applySidebar();

  btn.addEventListener('click', () => {
    isCollapsed = !isCollapsed;
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    applySidebar();
  });

  // ── Dark mode toggle ──
  const htmlEl      = document.documentElement;
  const darkToggle  = document.getElementById('dark-toggle');
  const toggleLabel = document.getElementById('toggle-label');
  let isDark = localStorage.getItem('darkMode') === 'true';

  function applyDark() {
    htmlEl.classList.toggle('dark', isDark);
    toggleLabel.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
  }
  applyDark();

  darkToggle.addEventListener('click', () => {
    isDark = !isDark;
    localStorage.setItem('darkMode', isDark);
    applyDark();
  });

  // ── GSAP entrance animation ──
  gsap.from('#sidebar', { x: -30, opacity: 0, duration: .5, ease: 'power2.out' });
  gsap.from('#topbar',  { y: -20, opacity: 0, duration: .4, delay: .2, ease: 'power2.out' });
  gsap.from('#content-panel > *', {
    y: 20, opacity: 0, duration: .4, delay: .3,
    stagger: .07, ease: 'power2.out'
  });
</script>

</body>
</html>
