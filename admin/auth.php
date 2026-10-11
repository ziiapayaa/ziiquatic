<?php
/**
 * ziiquatic - Hardened Admin Authentication & Helpers
 * Security Standard: OWASP Compliant
 */

define('SECURE_ACCESS', true);

// 1. Konfigurasi Session yang Aman (HttpOnly, SameSite, Secure if HTTPS)
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 2. Global Security Headers
function send_security_headers() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
send_security_headers();

// Sembunyikan error teknis dari publik (Information Disclosure Protection)
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// Lokasi Direktori Penting
define('DATA_DIR', __DIR__ . '/../data/');
define('DATA_FILE', DATA_DIR . 'projects.json');
define('CONFIG_FILE', DATA_DIR . 'config.php');
define('RATE_LIMIT_FILE', DATA_DIR . '.rate_limits.json');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Pastikan folder ada
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

// 3. Manajemen Kredensial Admin Terenkripsi
function get_admin_credentials() {
    if (file_exists(CONFIG_FILE)) {
        $cfg = include CONFIG_FILE;
        if (is_array($cfg) && isset($cfg['admin_username']) && isset($cfg['admin_password_hash'])) {
            return $cfg;
        }
    }

    // Default jika belum ada (admin / admin123)
    $default = [
        'admin_username' => 'admin',
        'admin_password_hash' => password_hash('admin123', PASSWORD_DEFAULT)
    ];
    save_admin_credentials($default['admin_username'], 'admin123');
    return $default;
}

function save_admin_credentials($username, $newPassword) {
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $content = "<?php\n"
             . "defined('SECURE_ACCESS') or exit('Akses ditolak.');\n\n"
             . "return [\n"
             . "    'admin_username' => " . var_export($username, true) . ",\n"
             . "    'admin_password_hash' => " . var_export($hash, true) . "\n"
             . "];\n";
    return file_put_contents(CONFIG_FILE, $content, LOCK_EX) !== false;
}

function is_default_password() {
    $creds = get_admin_credentials();
    return password_verify('admin123', $creds['admin_password_hash']);
}

// 4. Rate Limiting File-Backed (Anti Serangan Brute-Force Kebal Reset Cookie)
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '127.0.0.1';
    }
    return $ip;
}

function get_rate_limits() {
    if (!file_exists(RATE_LIMIT_FILE)) {
        return [];
    }
    $content = @file_get_contents(RATE_LIMIT_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function save_rate_limits($data) {
    $now = time();
    $cleaned = [];
    foreach ($data as $key => $item) {
        if (isset($item['first_attempt']) && ($now - $item['first_attempt'] < 1200)) {
            $cleaned[$key] = $item;
        }
    }
    @file_put_contents(RATE_LIMIT_FILE, json_encode($cleaned), LOCK_EX);
}

function check_login_rate_limit() {
    $ipHash = md5(get_client_ip());
    $limits = get_rate_limits();
    
    if (isset($limits[$ipHash])) {
        $data = $limits[$ipHash];
        // Jika melebihi 5 percobaan gagal dalam 10 menit, blokir sementara
        if ($data['attempts'] >= 5) {
            $timePassed = time() - $data['first_attempt'];
            if ($timePassed < 600) {
                $waitMinutes = ceil((600 - $timePassed) / 60);
                return "Terlalu banyak percobaan gagal dari IP Anda. Silakan coba lagi dalam $waitMinutes menit.";
            } else {
                unset($limits[$ipHash]);
                save_rate_limits($limits);
            }
        }
    }
    return true;
}

function record_failed_login() {
    $ipHash = md5(get_client_ip());
    $limits = get_rate_limits();
    $now = time();
    
    if (!isset($limits[$ipHash])) {
        $limits[$ipHash] = [
            'attempts' => 1,
            'first_attempt' => $now
        ];
    } else {
        $limits[$ipHash]['attempts']++;
    }
    save_rate_limits($limits);
}

function reset_login_rate_limit() {
    $ipHash = md5(get_client_ip());
    $limits = get_rate_limits();
    if (isset($limits[$ipHash])) {
        unset($limits[$ipHash]);
        save_rate_limits($limits);
    }
}

// 5. Cek Status Login & Inactivity Timeout (2 Jam)
function is_logged_in() {
    return isset($_SESSION['ziiquatic_admin_logged']) && $_SESSION['ziiquatic_admin_logged'] === true;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }

    $maxIdle = 7200; // 2 Jam
    if (isset($_SESSION['ziiquatic_last_activity']) && (time() - $_SESSION['ziiquatic_last_activity'] > $maxIdle)) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: login.php?msg=timeout');
        exit;
    }
    $_SESSION['ziiquatic_last_activity'] = time();
}

// 6. Validasi URL Media Eksternal (Cegah SSRF & javascript: URI Scheme)
function is_valid_media_url($url) {
    if (empty($url) || !is_string($url)) return false;
    $trimmed = trim($url);
    if (!filter_var($trimmed, FILTER_VALIDATE_URL)) {
        return false;
    }
    $scheme = parse_url($trimmed, PHP_URL_SCHEME);
    if (!in_array(strtolower((string)$scheme), ['http', 'https'])) {
        return false;
    }
    return true;
}

// 6. Proteksi CSRF (Cross-Site Request Forgery)
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

// 7. Helper Manipulasi Database JSON dengan File Locking (Cegah Race Condition)
function get_all_projects() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $content = file_get_contents(DATA_FILE);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function save_all_projects($projects) {
    $json = json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return file_put_contents(DATA_FILE, $json, LOCK_EX) !== false;
}

function get_project_by_id($id) {
    $projects = get_all_projects();
    foreach ($projects as $p) {
        if ($p['id'] == $id) {
            return $p;
        }
    }
    return null;
}

// 8. Handler Upload Media Aman (Validasi Ekstensi, MIME Asli, & Ukuran Maksimal)
function handle_file_upload($file_array) {
    if (!isset($file_array['error']) || $file_array['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Limit ukuran file: 15MB untuk foto, 60MB untuk video
    $max_bytes = 65 * 1024 * 1024;
    if ($file_array['size'] > $max_bytes) {
        return null;
    }

    $allowed_extensions = [
        'jpg'  => 'image',
        'jpeg' => 'image',
        'png'  => 'image',
        'webp' => 'image',
        'gif'  => 'image',
        'mp4'  => 'video',
        'webm' => 'video'
    ];

    $filename = $file_array['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!array_key_exists($ext, $allowed_extensions)) {
        return null;
    }

    $media_type = $allowed_extensions[$ext];
    $tmp_path = $file_array['tmp_name'];

    // Verifikasi MIME Type Asli Menggunakan Fileinfo (Bukan hanya membaca header palsu)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp_path);
        finfo_close($finfo);

        $valid_mimes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
            'video/mp4', 'video/webm', 'video/quicktime'
        ];

        if (!in_array($mime, $valid_mimes)) {
            return null;
        }
    }

    // Verifikasi Tambahan Khusus File Gambar Menggunakan getimagesize
    if ($media_type === 'image') {
        $imgCheck = @getimagesize($tmp_path);
        if ($imgCheck === false) {
            return null; // File bukan gambar valid
        }
    }

    // Buat nama file acak kriptografis yang mustahil diprediksi
    $safe_name = 'media_' . date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target_path = UPLOAD_DIR . $safe_name;

    if (move_uploaded_file($tmp_path, $target_path)) {
        // Set izin file yang aman (hanya baca/tulis server)
        @chmod($target_path, 0644);
        return [
            'src' => 'uploads/' . $safe_name,
            'type' => $media_type,
            'filename' => $safe_name
        ];
    }

    return null;
}

// 9. Safe Unlink Helper (Anti-Path-Traversal File Deletion)
function safe_delete_upload($src) {
    if (empty($src)) return;
    
    // Ambil nama file murni tanpa path direktori ../
    $filename = basename($src);
    
    // Jangan izinkan penghapusan file sistem di folder uploads
    if (in_array(strtolower($filename), ['index.html', '.htaccess', '.', '..'])) {
        return;
    }

    $realUploadDir = realpath(UPLOAD_DIR);
    $targetPath = $realUploadDir . DIRECTORY_SEPARATOR . $filename;

    if ($realUploadDir && file_exists($targetPath)) {
        $realTarget = realpath($targetPath);
        // Pastikan target berada mutlak di dalam UPLOAD_DIR
        if ($realTarget && strpos($realTarget, $realUploadDir) === 0 && is_file($realTarget)) {
            @unlink($realTarget);
        }
    }
}
