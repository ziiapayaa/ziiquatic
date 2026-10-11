<?php
require_once __DIR__ . '/auth.php';
require_login();

$error = '';
$success = '';
$csrf_token = generate_csrf_token();
$creds = get_admin_credentials();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Validasi keamanan CSRF gagal. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        if (empty($username)) {
            $error = 'Username tidak boleh kosong.';
        } elseif (!preg_match('/^[a-zA-Z0-9_\-\.]{3,30}$/', $username)) {
            $error = 'Username hanya boleh 3-30 karakter alfanumerik (huruf, angka, _, -, .).';
        } elseif (!password_verify($currentPassword, $creds['admin_password_hash'])) {
            $error = 'Password saat ini yang Anda masukkan salah.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Password baru minimal harus 8 karakter demi keamanan.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Konfirmasi password baru tidak cocok.';
        } elseif ($newPassword === 'admin123') {
            $error = 'Jangan gunakan password default "admin123". Gunakan password yang kuat.';
        } else {
            if (save_admin_credentials($username, $newPassword)) {
                $_SESSION['ziiquatic_admin_user'] = $username;
                // Regenerate session ID untuk keamanan setelah ganti password
                session_regenerate_id(true);
                $_SESSION['flash_success'] = 'Kredensial akun admin berhasil diperbarui! Password baru sudah aktif.';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Gagal menyimpan konfigurasi ke file server. Periksa hak izin berkas data/config.php.';
            }
        }
    }
}

$isDefault = is_default_password();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan Akun & Keamanan — ziiquatic Admin</title>
  
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
    <div class="max-w-4xl mx-auto px-4 sm:px-8 py-3 flex items-center justify-between gap-3">
      <a href="index.php" class="px-3.5 py-1.5 rounded-full bg-ivory-100 hover:bg-ivory-200 border border-ivory-300 text-xs font-bold text-stoneink-900 flex items-center gap-1.5 transition-all active:scale-95 shadow-xs">
        <svg class="w-4 h-4 text-moss-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali</span>
      </a>
      <span class="text-xs sm:text-sm text-stoneink-600 font-bold">Keamanan & Password</span>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-2xl mx-auto px-3.5 sm:px-8 py-5 sm:py-8 flex-1 w-full">
    
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-ivory-300 p-4 sm:p-10 shadow-sm space-y-6">
      
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-stoneink-900 tracking-tight">Pengaturan Keamanan Admin</h1>
        <p class="text-xs sm:text-sm text-stoneink-500 mt-1">Ubah username dan password admin agar aman dari pihak luar.</p>
      </div>

      <?php if ($isDefault): ?>
        <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
          <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <div>
            <span class="font-bold block">Peringatan: Password Masih Default!</span>
            Website Anda saat ini masih memakai password default <code>admin123</code>. Ganti sekarang agar panel admin tidak dapat diakses oleh pihak luar.
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="flash-alert p-3.5 sm:p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-center justify-between gap-2 transition-all duration-300">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span><?= htmlspecialchars($error) ?></span>
          </div>
          <button type="button" onclick="dismissFlashAlert(this)" class="p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-100/60 transition-colors" title="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      <?php endif; ?>

      <form method="POST" action="settings.php" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div>
          <label for="username" class="block text-xs font-bold text-stoneink-800 mb-1.5">Username Admin</label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            required 
            value="<?= htmlspecialchars($creds['admin_username']) ?>" 
            class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50 font-mono"
          >
          <p class="text-[11px] text-stoneink-500 mt-1">Username untuk login ke dashboard.</p>
        </div>

        <div class="pt-2 border-t border-ivory-200">
          <label for="current_password" class="block text-xs font-bold text-stoneink-800 mb-1.5">Password Saat Ini <span class="text-rose-600">*</span></label>
          <input 
            type="password" 
            id="current_password" 
            name="current_password" 
            required 
            placeholder="Masukkan password saat ini (default: admin123)" 
            class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="new_password" class="block text-xs font-bold text-stoneink-800 mb-1.5">Password Baru <span class="text-rose-600">*</span></label>
            <input 
              type="password" 
              id="new_password" 
              name="new_password" 
              required 
              minlength="8"
              placeholder="Minimal 8 karakter" 
              class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
            >
          </div>

          <div>
            <label for="confirm_password" class="block text-xs font-bold text-stoneink-800 mb-1.5">Ulangi Password Baru <span class="text-rose-600">*</span></label>
            <input 
              type="password" 
              id="confirm_password" 
              name="confirm_password" 
              required 
              minlength="8"
              placeholder="Ketik ulang password baru" 
              class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
            >
          </div>
        </div>

        <div class="pt-4 border-t border-ivory-200 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-3">
          <a href="index.php" class="w-full sm:w-auto text-center px-6 py-3.5 sm:py-2.5 rounded-full border border-ivory-300 text-stoneink-700 hover:bg-ivory-100 text-sm font-bold transition-colors">
            Batal
          </a>
          <button 
            type="submit" 
            class="w-full sm:w-auto px-7 py-3.5 sm:py-2.5 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-sm font-bold tracking-wide transition-all shadow-md active:scale-95 text-center">
            Simpan Password Baru
          </button>
        </div>

      </form>

    </div>

  </main>

  <footer class="bg-white border-t border-ivory-300 py-4 text-center text-xs text-stoneink-500">
    <p>© 2026 ziiquatic Studio Admin Panel • Marine & Reef Atelier</p>
  </footer>

  <script>
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
        }, 4000);

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
