<?php
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$info = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'timeout') {
    $info = 'Sesi Anda telah berakhir demi keamanan karena tidak ada aktivitas. Silakan login kembali.';
}
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Cek Rate Limiting (Anti Brute-Force)
    $rateCheck = check_login_rate_limit();
    if ($rateCheck !== true) {
        $error = $rateCheck;
    } 
    // 2. Cek CSRF Token
    elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Sesi form kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $creds = get_admin_credentials();

        if ($username === $creds['admin_username'] && password_verify($password, $creds['admin_password_hash'])) {
            // Login Berhasil: Reset Rate Limit & Regenerate Session ID (Anti Session Fixation)
            reset_login_rate_limit();
            session_regenerate_id(true);
            $_SESSION['ziiquatic_admin_logged'] = true;
            $_SESSION['ziiquatic_admin_user'] = $username;
            header('Location: index.php');
            exit;
        } else {
            record_failed_login();
            $error = 'Username atau password yang Anda masukkan salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Panel Admin — ziiquatic</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../assets/js/tailwind-config.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-ivory-100 text-stoneink-900 min-h-screen flex items-center justify-center p-4">

  <div class="max-w-md w-full bg-white border border-ivory-300 rounded-3xl p-8 sm:p-10 shadow-lg relative">
    
    <!-- Brand Header -->
    <div class="text-center space-y-2 mb-8">
      <img src="../logo.png" alt="ziiquatic" class="h-14 w-auto mx-auto mb-2 object-contain">
      <h1 class="text-2xl font-bold text-stoneink-900 tracking-tight font-sans">
        ziiquatic<span class="text-moss-700">.</span> Admin
      </h1>
      <p class="text-xs text-stoneink-500">Panel pengelolaan konten & galeri portofolio.</p>
    </div>

    <?php if (!empty($info)): ?>
      <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($info) ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="login.php" class="space-y-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div>
        <label for="username" class="block text-xs font-semibold text-stoneink-800 uppercase tracking-wider mb-2">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          required 
          autocomplete="username"
          placeholder="admin" 
          class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50 transition-all"
        >
      </div>

      <div>
        <label for="password" class="block text-xs font-semibold text-stoneink-800 uppercase tracking-wider mb-2">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
          autocomplete="current-password"
          placeholder="••••••••" 
          class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50 transition-all"
        >
      </div>

      <button 
        type="submit" 
        class="w-full py-3.5 rounded-xl bg-moss-800 hover:bg-moss-900 text-ivory-50 text-sm font-semibold tracking-wide transition-all shadow-sm active:scale-[0.99] mt-2">
        Masuk ke Dashboard
      </button>
    </form>

    <div class="mt-8 pt-6 border-t border-ivory-200 text-center text-xs text-stoneink-500">
      <a href="../" class="inline-block text-moss-700 hover:text-moss-900 hover:underline">← Kembali ke Halaman Utama</a>
    </div>

  </div>

</body>
</html>
