<?php
// pages/users.php — User Management (admin only)
if (!hasRole('administrator')) {
    echo '<div class="g-card p-8 text-center">
            <i data-lucide="shield-off" class="w-12 h-12 mx-auto mb-3 text-red-400"></i>
            <p class="font-semibold text-red-400">Akses Ditolak</p>
            <p class="text-sm mt-1" style="color:var(--text-secondary)">Halaman ini hanya untuk Administrator.</p>
          </div>';
    echo '<script>lucide.createIcons();</script>';
    return;
}
?>

<div class="max-w-6xl mx-auto">

  <!-- Header -->
  <div class="g-card p-5 mb-5 flex items-center justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(14,165,233,.15);">
        <i data-lucide="users" class="w-5 h-5 text-ocean-500"></i>
      </div>
      <div>
        <h2 class="font-semibold text-lg" style="color: var(--text-primary);">User Management</h2>
        <p class="text-sm" style="color: var(--text-secondary);">Kelola semua user di sini</p>
      </div>
    </div>
    <button class="btn-primary flex items-center gap-2 text-sm" onclick="openModal('create')">
      <i data-lucide="user-plus" class="w-4 h-4"></i>
      Tambah User
    </button>
  </div>

  <!-- Alert area -->
  <div id="alert-area" class="mb-4 hidden">
    <div id="alert-box" class="rounded-xl px-4 py-3 flex items-center gap-2 text-sm"></div>
  </div>

  <!-- Table card -->
  <div class="g-card overflow-hidden">
    <!-- Search bar -->
    <div class="px-5 py-4 border-b" style="border-color: var(--glass-border);">
      <div class="relative max-w-xs">
        <span class="absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--text-secondary);">
          <i data-lucide="search" class="w-4 h-4"></i>
        </span>
        <input type="text" id="search-input" placeholder="Cari username / nama / email..."
               class="dash-input pl-9 text-sm" oninput="filterTable()" />
      </div>
    </div>

    <!-- Table wrapper -->
    <div class="overflow-x-auto">
      <table class="dash-table" id="users-table">
        <thead>
          <tr>
            <th class="text-left">No</th>
            <th class="text-left">Username</th>
            <th class="text-left">Nama</th>
            <th class="text-left">Email</th>
            <th class="text-left">Role</th>
            <th class="text-left">Last Login</th>
            <th class="text-left">Ganti PW</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="users-tbody">
          <tr><td colspan="8" class="text-center py-8" style="color:var(--text-secondary);">
            <i data-lucide="loader" class="w-5 h-5 mx-auto mb-2 animate-spin"></i>
            Loading data...
          </td></tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination / count info -->
    <div class="px-5 py-3 border-t flex items-center justify-between text-xs"
         style="border-color: var(--glass-border); color: var(--text-secondary);">
      <span id="count-info">Memuat...</span>
      <span>Total: <strong id="total-count">-</strong> user</span>
    </div>
  </div>

</div>

<!-- ════════════════ MODAL ════════════════ -->
<div class="modal-overlay" id="user-modal">
  <div class="modal-box">

    <!-- Modal header -->
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-semibold text-base flex items-center gap-2" style="color: var(--text-primary);" id="modal-title">
        <i data-lucide="user-plus" class="w-4 h-4 text-ocean-400"></i>
        Tambah User
      </h3>
      <button onclick="closeModal()" class="btn-outline px-2 py-1 text-xs">✕</button>
    </div>

    <!-- Form -->
    <form id="user-form" onsubmit="submitForm(event)">
      <input type="hidden" id="f-id" value=""/>
      <input type="hidden" id="f-mode" value="create"/>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="dash-label">Username <span class="text-red-400">*</span></label>
          <input class="dash-input" id="f-username" type="text" placeholder="contoh: budi99" required maxlength="50"/>
        </div>
        <div>
          <label class="dash-label">Nama Lengkap <span class="text-red-400">*</span></label>
          <input class="dash-input" id="f-nama" type="text" placeholder="Budi Santoso" required maxlength="100"/>
        </div>
      </div>

      <div class="mb-4">
        <label class="dash-label">Email <span class="text-red-400">*</span></label>
        <input class="dash-input" id="f-email" type="email" placeholder="budi@example.com" required/>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="dash-label" id="pw-label">
            Password <span class="text-red-400">*</span>
          </label>
          <input class="dash-input" id="f-password" type="password" placeholder="Min. 6 karakter"/>
          <p class="text-xs mt-1" style="color:var(--text-secondary);" id="pw-hint">Kosongkan kalau tidak ingin ganti.</p>
        </div>
        <div>
          <label class="dash-label">Role <span class="text-red-400">*</span></label>
          <select class="dash-input" id="f-role" required>
            <option value="">— Pilih role —</option>
            <option value="administrator">Administrator</option>
            <option value="editor">Editor</option>
            <option value="viewer">Viewer</option>
          </select>
        </div>
      </div>

      <!-- Form error -->
      <div id="form-error" class="hidden rounded-xl px-4 py-3 mb-4 text-sm" style="background: rgba(239,68,68,.15); color: #ef4444;"></div>

      <div class="flex gap-3 justify-end">
        <button type="button" class="btn-outline text-sm" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-primary text-sm flex items-center gap-2" id="submit-btn">
          <i data-lucide="save" class="w-4 h-4"></i>
          <span id="submit-label">Simpan</span>
        </button>
      </div>
    </form>

  </div>
</div>

<!-- ════════════════ DELETE CONFIRM MODAL ════════════════ -->
<div class="modal-overlay" id="delete-modal">
  <div class="modal-box max-w-sm text-center">
    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
         style="background: rgba(239,68,68,.15);">
      <i data-lucide="trash-2" class="w-7 h-7 text-red-400"></i>
    </div>
    <h3 class="font-semibold text-base mb-2" style="color: var(--text-primary);">Hapus User?</h3>
    <p class="text-sm mb-5" style="color: var(--text-secondary);">
      Kamu yakin mau hapus user <strong id="delete-username" style="color:var(--text-primary);">-</strong>?
      Aksi ini ga bisa dibatalin.
    </p>
    <div class="flex gap-3 justify-center">
      <button class="btn-outline text-sm" onclick="closeDeleteModal()">Batal</button>
      <button class="btn-primary btn-danger text-sm flex items-center gap-2" onclick="confirmDelete()">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Ya, Hapus
      </button>
    </div>
  </div>
</div>

<!-- ════════════════ SCRIPTS ════════════════ -->
<script>
  let allUsers = [];
  let deleteTargetId = null;
  const currentUserId = <?= (int)currentUser()['id'] ?>;

  // ── Load users ──────────────────────────────
  async function loadUsers() {
    try {
      const res  = await fetch('/dashboard/api/users.php');
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      allUsers = json.data;
      renderTable(allUsers);
    } catch (e) {
      showAlert('error', 'Gagal load data: ' + e.message);
    }
  }

  function renderTable(data) {
    const tbody = document.getElementById('users-tbody');
    document.getElementById('total-count').textContent = data.length;
    document.getElementById('count-info').textContent  = `Menampilkan ${data.length} dari ${allUsers.length} user`;

    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10" style="color:var(--text-secondary);">
        <i data-lucide="inbox" style="width:2rem;height:2rem;margin:0 auto .5rem;display:block;"></i>
        Tidak ada data.
      </td></tr>`;
      lucide.createIcons();
      return;
    }

    tbody.innerHTML = data.map((u, i) => `
      <tr>
        <td class="text-center" style="color:var(--text-secondary); width:42px;">${i + 1}</td>
        <td>
          <div class="flex items-center gap-2">
            <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#38bdf8,#0369a1);
                        display:flex;align-items:center;justify-content:center;color:white;font-size:.72rem;font-weight:600;flex-shrink:0;">
              ${u.nama.slice(0,2).toUpperCase()}
            </div>
            <span style="color:var(--text-primary);font-weight:500;">${esc(u.username)}</span>
            ${u.id == currentUserId ? '<span class="badge badge-viewer text-xs">Kamu</span>' : ''}
          </div>
        </td>
        <td style="color:var(--text-primary);">${esc(u.nama)}</td>
        <td style="color:var(--text-secondary);font-size:.82rem;">${esc(u.email)}</td>
        <td><span class="badge badge-${u.role === 'administrator' ? 'admin' : (u.role === 'editor' ? 'editor' : 'viewer')}">${ucfirst(u.role)}</span></td>
        <td style="color:var(--text-secondary);font-size:.78rem;">${formatDate(u.last_login)}</td>
        <td style="color:var(--text-secondary);font-size:.78rem;">${formatDate(u.last_changed_password)}</td>
        <td>
          <div class="flex gap-1 justify-center">
            <button class="btn-outline px-2 py-1 text-xs flex items-center gap-1"
                    onclick="openModal('edit', ${JSON.stringify(u).replace(/"/g,'&quot;').replace(/'/g,'&#39;')})">
              <i data-lucide="pencil" style="width:12px;height:12px;"></i> Edit
            </button>
            ${u.id != currentUserId ? `
            <button class="btn-primary btn-danger px-2 py-1 text-xs flex items-center gap-1"
                    onclick="openDeleteModal(${u.id}, '${u.username.replace(/'/g, '\\\'')}')">
              <i data-lucide="trash-2" style="width:12px;height:12px;"></i> Hapus
            </button>` : ''}
          </div>
        </td>
      </tr>
    `).join('');
    lucide.createIcons();
  }

  // ── Filter / Search ──────────────────────────
  function filterTable() {
    const q = document.getElementById('search-input').value.toLowerCase();
    if (!q) { renderTable(allUsers); return; }
    renderTable(allUsers.filter(u =>
      u.username.toLowerCase().includes(q) ||
      u.nama.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q)
    ));
  }

  // ── Modal ────────────────────────────────────
  function openModal(mode, user = null) {
    document.getElementById('f-mode').value     = mode;
    document.getElementById('modal-title').innerHTML =
      `<i data-lucide="${mode==='create'?'user-plus':'user-check'}" class="w-4 h-4 text-ocean-400"></i>
       ${mode === 'create' ? 'Tambah User' : 'Edit User'}`;

    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('f-id').value       = user?.id ?? '';
    document.getElementById('f-username').value = user?.username ?? '';
    document.getElementById('f-username').disabled = mode === 'edit';
    document.getElementById('f-nama').value     = user?.nama ?? '';
    document.getElementById('f-email').value    = user?.email ?? '';
    document.getElementById('f-password').value = '';
    document.getElementById('f-role').value     = user?.role ?? '';

    const pwHint  = document.getElementById('pw-hint');
    const pwLabel = document.getElementById('pw-label');
    if (mode === 'create') {
      pwHint.classList.add('hidden');
      pwLabel.innerHTML = 'Password <span class="text-red-400">*</span>';
      document.getElementById('f-password').required = true;
    } else {
      pwHint.classList.remove('hidden');
      pwLabel.innerHTML = 'Password Baru';
      document.getElementById('f-password').required = false;
    }

    const submitLabel = document.getElementById('submit-label');
    if (submitLabel) submitLabel.textContent = mode === 'create' ? 'Buat User' : 'Simpan';
    document.getElementById('user-modal').classList.add('open');
    document.getElementById('user-modal').style.opacity = '1'; // Force opacity
    document.getElementById('user-modal').style.pointerEvents = 'all'; // Force pointer events
    document.getElementById('user-modal').style.visibility = 'visible'; // Force visibility
    lucide.createIcons();
  }

  function closeModal() {
    const modal = document.getElementById('user-modal');
    modal.classList.remove('open');
    modal.style.opacity = '0'; // Reset opacity
    modal.style.pointerEvents = 'none'; // Reset pointer events
    modal.style.visibility = 'hidden'; // Reset visibility
  }

  // ── Submit form ──────────────────────────────
  async function submitForm(e) {
    e.preventDefault();
    const mode = document.getElementById('f-mode').value;
    const errorBox = document.getElementById('form-error');
    errorBox.classList.add('hidden');

    const payload = {
      id:       parseInt(document.getElementById('f-id').value) || undefined,
      username: document.getElementById('f-username').value,
      nama:     document.getElementById('f-nama').value,
      email:    document.getElementById('f-email').value,
      password: document.getElementById('f-password').value,
      role:     document.getElementById('f-role').value,
    };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Menyimpan...';
    lucide.createIcons();

    try {
      const res  = await fetch('/dashboard/api/users.php', {
        method: mode === 'create' ? 'POST' : 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const json = await res.json();

      if (!json.success) throw new Error(json.message);

      closeModal();
      showAlert('success', json.message);
      await loadUsers();
    } catch (err) {
      errorBox.textContent = err.message;
      errorBox.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i><span>Simpan</span>';
      lucide.createIcons();
    }
  }

  // ── Delete ───────────────────────────────────
  function openDeleteModal(id, username) {
    deleteTargetId = id;
    document.getElementById('delete-username').textContent = username;
    document.getElementById('delete-modal').classList.add('open');
  }
  function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('open');
    deleteTargetId = null;
  }
  async function confirmDelete() {
    if (!deleteTargetId) return;
    try {
      const res  = await fetch('/dashboard/api/users.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: deleteTargetId }),
      });
      const json = await res.json();
      if (!json.success) throw new Error(json.message);
      closeDeleteModal();
      showAlert('success', json.message);
      await loadUsers();
    } catch (err) {
      closeDeleteModal();
      showAlert('error', err.message);
    }
  }

  // ── Alert ────────────────────────────────────
  function showAlert(type, msg) {
    const area = document.getElementById('alert-area');
    const box  = document.getElementById('alert-box');
    box.className = `rounded-xl px-4 py-3 flex items-center gap-2 text-sm ${
      type === 'success'
        ? 'bg-emerald-500/10 border border-emerald-400/30 text-emerald-400'
        : 'bg-red-500/10 border border-red-400/30 text-red-400'
    }`;
    box.innerHTML = `
      <i data-lucide="${type==='success'?'check-circle':'x-circle'}" style="width:16px;height:16px;flex-shrink:0;"></i>
      ${esc(msg)}
    `;
    area.classList.remove('hidden');
    lucide.createIcons();
    setTimeout(() => area.classList.add('hidden'), 4000);
  }

  // ── Helpers ──────────────────────────────────
  function esc(s) {
    return String(s ?? '')
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
  function ucfirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
  function formatDate(d) {
    if (!d) return '<span style="color:var(--text-secondary);opacity:.4;">—</span>';
    const dt = new Date(d);
    return dt.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
      + ' ' + dt.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
  }

  // ── Init ─────────────────────────────────────
  loadUsers();
  lucide.createIcons();
</script>
