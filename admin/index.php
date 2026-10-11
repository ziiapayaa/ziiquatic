<?php
require_once __DIR__ . '/auth.php';
require_login();

$projects = get_all_projects();

// Hitung statistik
$totalProjects = count($projects);
$totalImages = 0;
$totalVideos = 0;
$categoryCounts = ['kecil' => 0, 'sedang' => 0, 'besar' => 0];

foreach ($projects as $p) {
    if (isset($p['category']) && isset($categoryCounts[$p['category']])) {
        $categoryCounts[$p['category']]++;
    }
    if (isset($p['mediaList']) && is_array($p['mediaList'])) {
        foreach ($p['mediaList'] as $m) {
            if (($m['type'] ?? '') === 'video') {
                $totalVideos++;
            } else {
                $totalImages++;
            }
        }
    }
}

$csrf_token = generate_csrf_token();
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin — ziiquatic</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../assets/js/tailwind-config.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-ivory-100 text-stoneink-900 min-h-screen antialiased flex flex-col">

  <!-- Top Admin Navbar (Mobile-First) -->
  <header class="bg-white border-b border-ivory-300 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3 flex items-center justify-between gap-3">
      
      <!-- Brand Logo -->
      <a href="index.php" class="flex items-center gap-2 group shrink-0">
        <img src="../logo.png" alt="ziiquatic" class="h-7 sm:h-8 w-auto object-contain shrink-0">
        <span class="text-base sm:text-lg font-bold tracking-tight text-stoneink-900 lowercase font-sans">
          ziiquatic<span class="text-moss-700">.</span>
        </span>
        <span class="text-[10px] sm:text-xs font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-800 border border-moss-100">
          Admin
        </span>
      </a>

      <!-- Desktop Nav Buttons (md ke atas) -->
      <nav class="hidden md:flex items-center gap-2.5 text-xs font-medium">
        <a href="settings.php" class="px-3.5 py-2 rounded-full border border-ivory-300 text-stoneink-700 hover:text-stoneink-900 hover:bg-ivory-50 transition-all flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-stoneink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          Ganti Password
        </a>
        <a href="../" target="_blank" class="px-3.5 py-2 rounded-full border border-ivory-300 text-stoneink-700 hover:text-stoneink-900 hover:bg-ivory-50 transition-all flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Lihat Website
        </a>
        <a href="logout.php" class="px-3.5 py-2 rounded-full bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors">
          Keluar
        </a>
      </nav>

      <!-- Mobile Controls (Hamburger & Quick Web Link) -->
      <div class="flex md:hidden items-center gap-2">
        <a href="../" target="_blank" title="Lihat Website" class="w-9 h-9 rounded-full bg-ivory-100 border border-ivory-300 flex items-center justify-center text-stoneink-700 active:scale-95 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
        <button 
          id="mobileMenuBtn" 
          type="button" 
          onclick="toggleMobileMenu()" 
          aria-label="Buka Menu" 
          class="w-9 h-9 rounded-full bg-ivory-100 border border-ivory-300 flex items-center justify-center text-stoneink-800 active:scale-95 shadow-sm"
        >
          <svg id="hamburgerIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <svg id="closeIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileMenuDropdown" class="hidden md:hidden border-t border-ivory-200 bg-white px-4 py-3 space-y-2 shadow-lg">
      <a href="../" target="_blank" class="w-full py-3 px-4 rounded-xl bg-ivory-50 text-stoneink-900 text-xs font-bold flex items-center gap-3 active:bg-ivory-100 border border-ivory-200">
        <svg class="w-4 h-4 text-moss-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        <span>Lihat Tampilan Website Publik ↗</span>
      </a>
      <a href="settings.php" class="w-full py-3 px-4 rounded-xl bg-ivory-50 text-stoneink-900 text-xs font-bold flex items-center gap-3 active:bg-ivory-100 border border-ivory-200">
        <svg class="w-4 h-4 text-moss-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        <span>Ganti Password Admin</span>
      </a>
      <a href="logout.php" class="w-full py-3 px-4 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold flex items-center gap-3 active:bg-rose-100 border border-rose-200">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        <span>Keluar dari Akun (Logout)</span>
      </a>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-7xl mx-auto px-4 sm:px-8 py-5 sm:py-8 flex-1 w-full space-y-6 sm:space-y-8">
    
    <!-- Peringatan Password Default Sebelum Hosting -->
    <?php if (is_default_password()): ?>
      <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="flex items-start sm:items-center gap-2.5">
          <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <div>
            <span class="font-bold">Peringatan Keamanan:</span> Password admin masih default (<code class="bg-amber-100 px-1 py-0.5 rounded font-mono">admin123</code>). Segera ubah sekarang!
          </div>
        </div>
        <a href="settings.php" class="w-full sm:w-auto px-4 py-2.5 rounded-full bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs whitespace-nowrap shadow-sm text-center">
          Ganti Password Sekarang →
        </a>
      </div>
    <?php endif; ?>
    
    <!-- Flash Messages (Auto-dismissing) -->
    <?php if (!empty($flash_success)): ?>
      <div class="flash-alert p-3.5 sm:p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between gap-3 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-2.5">
          <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="font-medium"><?= htmlspecialchars($flash_success) ?></span>
        </div>
        <button type="button" onclick="dismissFlashAlert(this)" class="p-1 rounded-lg text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100/60 transition-colors" title="Tutup">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    <?php endif; ?>

    <?php if (!empty($flash_error)): ?>
      <div class="flash-alert p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center justify-between gap-3 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-2.5">
          <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <span class="font-medium"><?= htmlspecialchars($flash_error) ?></span>
        </div>
        <button type="button" onclick="dismissFlashAlert(this)" class="p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-100/60 transition-colors" title="Tutup">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    <?php endif; ?>

    <!-- Overview Stats Grid (Mobile Compact & Clear) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
      
      <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-ivory-300 shadow-sm flex flex-col justify-between">
        <span class="text-[10px] sm:text-xs font-bold text-stoneink-500 uppercase tracking-wider">Total Model</span>
        <div class="text-xl sm:text-3xl font-extrabold text-stoneink-900 mt-1"><?= $totalProjects ?></div>
        <p class="text-[10px] sm:text-[11px] text-stoneink-500 mt-0.5">Ukuran aktif</p>
      </div>

      <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-ivory-300 shadow-sm flex flex-col justify-between">
        <span class="text-[10px] sm:text-xs font-bold text-stoneink-500 uppercase tracking-wider">Total Foto</span>
        <div class="text-xl sm:text-3xl font-extrabold text-moss-800 mt-1"><?= $totalImages ?></div>
        <p class="text-[10px] sm:text-[11px] text-stoneink-500 mt-0.5">Foto tersimpan</p>
      </div>

      <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-ivory-300 shadow-sm flex flex-col justify-between">
        <span class="text-[10px] sm:text-xs font-bold text-stoneink-500 uppercase tracking-wider">Total Video</span>
        <div class="text-xl sm:text-3xl font-extrabold text-emerald-700 mt-1"><?= $totalVideos ?></div>
        <p class="text-[10px] sm:text-[11px] text-stoneink-500 mt-0.5">Video biota</p>
      </div>

      <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-ivory-300 shadow-sm flex flex-col justify-between">
        <span class="text-[10px] sm:text-xs font-bold text-stoneink-500 uppercase tracking-wider">Di Beranda</span>
        <div class="text-xl sm:text-3xl font-extrabold text-stoneink-900 mt-1">Maks 6</div>
        <p class="text-[10px] sm:text-[11px] text-stoneink-500 mt-0.5">Awal tampil</p>
      </div>

    </div>

    <!-- Header List & Add Button (Mobile-First Prominent) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 pt-1">
      <div>
        <h2 class="text-lg sm:text-2xl font-bold text-stoneink-900 tracking-tight">Koleksi Portofolio & Media</h2>
        <p class="text-xs sm:text-sm text-stoneink-500 mt-0.5">Kelola foto dan video aquarium yang tampil di website.</p>
      </div>

      <a href="add.php" class="w-full sm:w-auto px-6 py-3.5 sm:py-3 rounded-2xl sm:rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-sm font-bold tracking-wide transition-all shadow-md flex items-center justify-center gap-2 active:scale-98">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        <span>+ Tambah Model Baru</span>
      </a>
    </div>

    <!-- Items Grid / List -->
    <?php if (empty($projects)): ?>
      <div class="bg-white rounded-3xl border border-ivory-300 p-12 text-center space-y-4">
        <div class="w-16 h-16 rounded-full bg-ivory-100 flex items-center justify-center text-moss-800 mx-auto text-2xl font-bold">
          ?
        </div>
        <h3 class="text-base font-bold text-stoneink-900">Belum ada data portofolio</h3>
        <p class="text-xs text-stoneink-500 max-w-sm mx-auto">Mulai tambahkan produk ukuran aquarium pertama Anda beserta dokumentasi foto atau videonya.</p>
        <a href="add.php" class="inline-block px-5 py-2.5 rounded-full bg-moss-800 text-ivory-50 text-xs font-semibold shadow-sm">
          + Tambah Sekarang
        </a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($projects as $item): ?>
          <?php
            $cover = !empty($item['mediaList'][0]['src']) ? $item['mediaList'][0]['src'] : 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80';
            // Perbaiki path relative jika dari uploads/
            if (strpos($cover, 'uploads/') === 0) {
              $cover = '../' . $cover;
            }
            $mediaCount = count($item['mediaList'] ?? []);
            $videoCount = 0;
            if (!empty($item['mediaList'])) {
                foreach ($item['mediaList'] as $m) {
                    if (($m['type'] ?? '') === 'video') $videoCount++;
                }
            }
            $imgCount = $mediaCount - $videoCount;
          ?>
          <div class="bg-white rounded-2xl border border-ivory-300 p-4 shadow-sm hover:border-stoneink-400 transition-all flex flex-col justify-between space-y-4">
            
            <div class="space-y-3">
              <!-- Cover Image & Badges -->
              <div class="aspect-[16/10] rounded-xl overflow-hidden bg-stoneink-900 relative">
                <img 
                  src="<?= htmlspecialchars($cover) ?>" 
                  alt="<?= htmlspecialchars($item['title']) ?>" 
                  class="w-full h-full object-cover"
                  onerror="this.src='https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80'"
                >
                <span class="absolute top-2.5 left-2.5 bg-white/95 backdrop-blur-md px-2.5 py-0.5 rounded-full text-[10px] font-bold text-stoneink-900 border border-ivory-300 shadow-sm">
                  <?= htmlspecialchars($item['sizeBadge'] ?? $item['title']) ?>
                </span>
                <span class="absolute bottom-2.5 right-2.5 bg-stoneink-900/80 backdrop-blur-md px-2 py-0.5 rounded-full text-[9px] font-medium text-ivory-100 flex items-center gap-1 shadow-sm">
                  <span>📸 <?= $imgCount ?></span>
                  <?php if ($videoCount > 0): ?>
                    <span>• 🎬 <?= $videoCount ?></span>
                  <?php endif; ?>
                </span>
              </div>

              <!-- Title & ID -->
              <div>
                <div class="flex items-center justify-between gap-2 mb-1">
                  <span class="text-[10px] text-stoneink-400 font-mono">ID: #<?= htmlspecialchars($item['id']) ?></span>
                </div>
                <h3 class="text-sm font-bold text-stoneink-900 leading-snug">
                  <?= htmlspecialchars($item['title']) ?>
                </h3>
                <p class="text-xs text-stoneink-500 mt-1 line-clamp-1">
                  <?= htmlspecialchars($item['placement'] ?? '') ?>
                </p>
                <p class="text-xs text-stoneink-600 mt-1.5 line-clamp-2 leading-relaxed">
                  <?= htmlspecialchars($item['desc'] ?? '') ?>
                </p>
              </div>
            </div>

            <!-- Action Buttons (Touch-friendly & Clear) -->
            <div class="pt-3 border-t border-ivory-200 flex items-center justify-between gap-2.5">
              <a href="edit.php?id=<?= urlencode($item['id']) ?>" class="flex-1 py-2.5 px-3 rounded-xl bg-ivory-100 hover:bg-ivory-200 text-stoneink-900 text-xs sm:text-sm font-bold text-center border border-ivory-300 transition-colors flex items-center justify-center gap-1.5 active:scale-95 shadow-xs">
                <svg class="w-4 h-4 text-moss-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit / Foto</span>
              </a>

              <button 
                type="button" 
                onclick="openDeleteModal(<?= (int)$item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>')"
                class="py-2.5 px-3.5 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors flex items-center justify-center gap-1 text-xs font-semibold active:scale-95 shadow-xs" 
                title="Hapus Model Ini"
              >
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="inline">Hapus</span>
              </button>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <!-- Admin Footer -->
  <footer class="bg-white border-t border-ivory-300 py-5 text-center text-xs text-stoneink-500">
    <p>© 2026 ziiquatic Studio Admin Panel • Marine & Reef Atelier</p>
  </footer>

  <!-- Custom Delete Confirmation Modal -->
  <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-stoneink-900/60 backdrop-blur-sm transition-opacity duration-200">
    <div id="deleteModalContent" class="bg-white rounded-3xl border border-ivory-300 max-w-sm w-full p-6 shadow-2xl space-y-5 transform transition-all duration-200 scale-95 opacity-0">
      
      <div class="flex items-start gap-3.5">
        <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
        <div>
          <h3 class="text-base font-bold text-stoneink-900 leading-snug">Hapus Model Aquarium?</h3>
          <p class="text-xs text-stoneink-500 mt-1 leading-relaxed">
            Model <span id="deleteItemTitle" class="font-bold text-stoneink-800"></span> beserta seluruh file fotonya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
          </p>
        </div>
      </div>

      <form id="deleteForm" method="POST" action="delete.php" class="pt-2 flex items-center justify-end gap-2.5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="id" id="deleteTargetId" value="">
        
        <button 
          type="button" 
          onclick="closeDeleteModal()" 
          class="px-4 py-2.5 rounded-full border border-ivory-300 hover:bg-ivory-100 text-stoneink-700 text-xs font-semibold transition-colors"
        >
          Batal
        </button>
        
        <button 
          type="submit" 
          class="px-5 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-all active:scale-95"
        >
          Ya, Hapus
        </button>
      </form>

    </div>
  </div>

  <script>
    // Toggle Menu Mobile Drawer
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileMenuDropdown');
      const hamIcon = document.getElementById('hamburgerIcon');
      const closeIcon = document.getElementById('closeIcon');
      if (!menu) return;
      const isHidden = menu.classList.contains('hidden');
      if (isHidden) {
        menu.classList.remove('hidden');
        hamIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
      } else {
        menu.classList.add('hidden');
        hamIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
      }
    }
    // Logika Modal Hapus Custom
    function openDeleteModal(id, title) {
      document.getElementById('deleteTargetId').value = id;
      document.getElementById('deleteItemTitle').textContent = `"${title}"`;
      const modal = document.getElementById('deleteModal');
      const content = document.getElementById('deleteModalContent');
      modal.classList.remove('hidden');
      setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
      }, 10);
      document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
      const modal = document.getElementById('deleteModal');
      const content = document.getElementById('deleteModalContent');
      content.classList.remove('scale-100', 'opacity-100');
      content.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      }, 150);
    }

    const deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
      deleteModalEl.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeDeleteModal();
    });

    // Logika Notifikasi Hilang Otomatis (Auto-dismiss setelah 4 detik)
    function dismissFlashAlert(btn) {
      const alert = btn ? btn.closest('.flash-alert') : document.querySelector('.flash-alert');
      if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 350);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.flash-alert');
      alerts.forEach(alert => {
        let timer = setTimeout(() => {
          const closeBtn = alert.querySelector('button');
          dismissFlashAlert(closeBtn);
        }, 4000); // 4 detik

        // Pause timer jika user hover kursor
        alert.addEventListener('mouseenter', () => clearTimeout(timer));
        alert.addEventListener('mouseleave', () => {
          timer = setTimeout(() => {
            const closeBtn = alert.querySelector('button');
            dismissFlashAlert(closeBtn);
          }, 2000);
        });
      });
    });
  </script>

</body>
</html>
