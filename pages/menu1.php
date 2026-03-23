<?php
// pages/menu1.php — Placeholder untuk tools/app custom
// Kamu bisa ganti isi section #menu1-content dengan PHP/HTML tools yang udah dibuat
?>

<div class="max-w-5xl mx-auto">

  <!-- Header -->
  <div class="g-card p-6 mb-6">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(14,165,233,.15);">
        <i data-lucide="layers" class="w-5 h-5 text-ocean-500"></i>
      </div>
      <div>
        <h2 class="font-semibold text-lg" style="color: var(--text-primary);">Menu 1</h2>
        <p class="text-sm" style="color: var(--text-secondary);">Tempatkan tools atau halaman PHP kamu di sini</p>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════
       AREA KONTEN — ganti isi div ini dengan
       PHP tools / HTML yang udah kamu buat
       ════════════════════════════════════════ -->
  <div id="menu1-content" class="g-card p-8">

    <!-- Contoh placeholder — hapus bagian ini kalau udah ada konten beneran -->
    <div class="flex flex-col items-center justify-center text-center py-12 gap-4">

      <div class="w-20 h-20 rounded-2xl flex items-center justify-center"
           style="background: linear-gradient(135deg, rgba(14,165,233,.15), rgba(56,189,248,.1)); border: 1px dashed rgba(14,165,233,.3);">
        <i data-lucide="code-2" class="w-9 h-9" style="color: rgba(14,165,233,.5);"></i>
      </div>

      <h3 class="text-xl font-semibold" style="color: var(--text-primary);">Konten Menu 1</h3>

      <p class="text-sm max-w-sm" style="color: var(--text-secondary);">
        Ini adalah placeholder. Untuk nambahin tools atau halaman PHP kamu sendiri,
        edit file <code class="px-1.5 py-0.5 rounded text-xs" style="background: rgba(14,165,233,.1); color: var(--accent);">pages/menu1.php</code>
        dan ganti atau tambahkan konten di dalam div <code class="px-1.5 py-0.5 rounded text-xs" style="background: rgba(14,165,233,.1); color: var(--accent);">#menu1-content</code>.
      </p>

      <p class="text-xs" style="color: var(--text-secondary);">
        Bisa juga pakai <code class="px-1.5 py-0.5 rounded" style="background: rgba(14,165,233,.08); color: var(--accent);">include 'path/ke/tools.php'</code>
        buat ngeload file terpisah.
      </p>

    </div>

    <!--
      ╔══════════════════════════════════════╗
      ║  CONTOH PENGGUNAAN:                  ║
      ║                                      ║
      ║  // Muat file PHP tools lain:        ║
      ║  include __DIR__ . '/../tools/       ║
      ║           kalkulator.php';           ║
      ║                                      ║
      ║  // Atau embed HTML langsung:        ║
      ║  <div class="your-tool-class">       ║
      ║    ... konten tools kamu ...         ║
      ║  </div>                              ║
      ╚══════════════════════════════════════╝
    -->

  </div>
  <!-- ════════════════════ END AREA KONTEN ════════════════════ -->

</div>

<script>lucide.createIcons();</script>
