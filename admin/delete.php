<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['flash_error'] = 'Token keamanan CSRF tidak valid.';
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$projects = get_all_projects();

$found = false;
$deletedTitle = '';
$newProjects = [];

foreach ($projects as $p) {
    if ($p['id'] == $id) {
        $found = true;
        $deletedTitle = $p['title'];

        // Hapus file fisik dengan aman (Anti Path-Traversal)
        if (!empty($p['mediaList']) && is_array($p['mediaList'])) {
            foreach ($p['mediaList'] as $m) {
                if (!empty($m['src'])) {
                    safe_delete_upload($m['src']);
                }
            }
        }
    } else {
        $newProjects[] = $p;
    }
}

if ($found) {
    save_all_projects(array_values($newProjects));
    $_SESSION['flash_success'] = 'Model aquarium "' . $deletedTitle . '" berhasil dihapus.';
} else {
    $_SESSION['flash_error'] = 'Model aquarium tidak ditemukan.';
}

header('Location: index.php');
exit;
