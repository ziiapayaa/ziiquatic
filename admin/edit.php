<?php
require_once __DIR__ . '/auth.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = get_project_by_id($id);

if (!$project) {
    $_SESSION['flash_error'] = 'Model aquarium tidak ditemukan.';
    header('Location: index.php');
    exit;
}

$error = '';
$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Validasi keamanan CSRF gagal. Silakan muat ulang halaman.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'kecil');
        $sizeBadge = trim($_POST['sizeBadge'] ?? '');
        $placement = trim($_POST['placement'] ?? '');
        $desc = trim($_POST['desc'] ?? '');

        if (empty($title)) {
            $error = 'Judul model aquarium wajib diisi.';
        } else {
            $projects = get_all_projects();
            $targetIndex = -1;

            foreach ($projects as $idx => $p) {
                if ($p['id'] == $id) {
                    $targetIndex = $idx;
                    break;
                }
            }

            if ($targetIndex === -1) {
                $error = 'Data proyek tidak ditemukan saat menyimpan.';
            } else {
                $deleteMediaIndices = isset($_POST['delete_media']) && is_array($_POST['delete_media']) ? array_map('intval', $_POST['delete_media']) : [];
                $existingLabels = $_POST['existing_labels'] ?? [];

                $currentMediaList = $project['mediaList'] ?? [];
                $updatedMediaList = [];

                // 1. Perbarui media yang sudah ada (simpan / hapus)
                foreach ($currentMediaList as $mIdx => $mediaItem) {
                    if (in_array($mIdx, $deleteMediaIndices)) {
                        // Hapus file fisik secara aman (Anti Path-Traversal)
                        if (!empty($mediaItem['src'])) {
                            safe_delete_upload($mediaItem['src']);
                        }
                        continue; // Lewati, tidak dimasukkan ke updatedMediaList
                    }

                    // Update label jika ada perubahan
                    if (isset($existingLabels[$mIdx])) {
                        $mediaItem['label'] = trim(strip_tags((string)$existingLabels[$mIdx]));
                    }
                    $updatedMediaList[] = $mediaItem;
                }

                // 2. Upload File Baru Tambahan
                if (!empty($_FILES['new_media_files']['name'][0])) {
                    $fileCount = count($_FILES['new_media_files']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($_FILES['new_media_files']['error'][$i] === UPLOAD_ERR_OK) {
                            $singleFile = [
                                'name' => $_FILES['new_media_files']['name'][$i],
                                'type' => $_FILES['new_media_files']['type'][$i],
                                'tmp_name' => $_FILES['new_media_files']['tmp_name'][$i],
                                'error' => $_FILES['new_media_files']['error'][$i],
                                'size' => $_FILES['new_media_files']['size'][$i],
                            ];
                            $uploaded = handle_file_upload($singleFile);
                            if ($uploaded) {
                                $label = trim(strip_tags((string)($_POST['new_media_labels'][$i] ?? '')));
                                $updatedMediaList[] = [
                                    'type' => $uploaded['type'],
                                    'label' => !empty($label) ? $label : pathinfo($singleFile['name'], PATHINFO_FILENAME),
                                    'src' => $uploaded['src']
                                ];
                            }
                        }
                    }
                }

                // 3. Tambah URL Baru Tambahan (Validasi Skema URL http/https)
                if (!empty($_POST['new_url_src']) && is_array($_POST['new_url_src'])) {
                    foreach ($_POST['new_url_src'] as $idx => $url) {
                        $cleanUrl = trim((string)$url);
                        if (!empty($cleanUrl) && is_valid_media_url($cleanUrl)) {
                            $urlType = $_POST['new_url_type'][$idx] ?? 'image';
                            $urlLabel = trim(strip_tags((string)($_POST['new_url_label'][$idx] ?? '')));
                            $updatedMediaList[] = [
                                'type' => in_array($urlType, ['video', 'image']) ? $urlType : 'image',
                                'label' => !empty($urlLabel) ? $urlLabel : 'Media Online',
                                'src' => $cleanUrl
                            ];
                        }
                    }
                }

                // Update data proyek
                $projects[$targetIndex]['title'] = $title;
                $projects[$targetIndex]['category'] = 'umum';
                $projects[$targetIndex]['sizeBadge'] = !empty($sizeBadge) ? $sizeBadge : $title;
                $projects[$targetIndex]['placement'] = $placement;
                $projects[$targetIndex]['desc'] = $desc;
                $projects[$targetIndex]['mediaList'] = array_values($updatedMediaList);

                if (save_all_projects($projects)) {
                    $_SESSION['flash_success'] = 'Perubahan pada model "' . $title . '" berhasil disimpan!';
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Gagal menyimpan perubahan ke berkas JSON.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Model: <?= htmlspecialchars($project['title']) ?> — ziiquatic Admin</title>
  
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
      <span class="text-xs sm:text-sm text-stoneink-600 font-bold truncate max-w-[200px] sm:max-w-none">Edit Model #<?= htmlspecialchars($project['id']) ?></span>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-4xl mx-auto px-3.5 sm:px-8 py-5 sm:py-8 flex-1 w-full">
    
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-ivory-300 p-4 sm:p-10 shadow-sm space-y-6 sm:space-y-8">
      
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-stoneink-900 tracking-tight">Edit Model Aquarium</h1>
          <p class="text-xs sm:text-sm text-stoneink-500 mt-1">Perbarui teks, kelola foto/video yang ada, atau unggah foto/video baru.</p>
        </div>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-moss-50 text-moss-800 border border-moss-200 shrink-0">
          ID: #<?= htmlspecialchars($project['id']) ?>
        </span>
      </div>

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

      <form method="POST" action="edit.php?id=<?= urlencode($project['id']) ?>" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <!-- Section 1: Informasi Model -->
        <div class="space-y-4">
          <h2 class="text-xs sm:text-sm font-bold text-stoneink-900 uppercase tracking-wider border-b border-ivory-200 pb-2">1. Informasi Model</h2>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="title" class="block text-xs font-bold text-stoneink-800 mb-1.5">Nama / Judul Ukuran <span class="text-rose-600">*</span></label>
              <input 
                type="text" 
                id="title" 
                name="title" 
                required 
                value="<?= htmlspecialchars($project['title']) ?>"
                class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
              >
            </div>

            <div>
              <label for="sizeBadge" class="block text-xs font-bold text-stoneink-800 mb-1.5">Badge Tampilan Ukuran</label>
              <input 
                type="text" 
                id="sizeBadge" 
                name="sizeBadge" 
                value="<?= htmlspecialchars($project['sizeBadge'] ?? '') ?>"
                class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
              >
            </div>
          </div>

          <div>
            <label for="placement" class="block text-xs font-bold text-stoneink-800 mb-1.5">Rekomendasi Penempatan</label>
            <input 
              type="text" 
              id="placement" 
              name="placement" 
              value="<?= htmlspecialchars($project['placement'] ?? '') ?>"
              placeholder="Contoh: Sangat pas untuk meja resepsionis atau ruang tamu" 
              class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
            >
          </div>

          <div>
            <label for="desc" class="block text-xs font-bold text-stoneink-800 mb-1.5">Deskripsi Detail</label>
            <textarea 
              id="desc" 
              name="desc" 
              rows="3" 
              class="w-full px-4 py-3 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-base sm:text-sm bg-ivory-50/50"
            ><?= htmlspecialchars($project['desc'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Section 2: Media yang Sudah Ada -->
        <div class="space-y-4 pt-4">
          <div class="border-b border-ivory-200 pb-2">
            <h2 class="text-xs sm:text-sm font-bold text-stoneink-900 uppercase tracking-wider">2. Media Foto & Video Saat Ini (<?= count($project['mediaList'] ?? []) ?>)</h2>
            <p class="text-xs text-stoneink-500">Anda dapat mengubah keterangan label atau mencentang "Hapus" pada media yang ingin dibuang.</p>
          </div>

          <?php if (empty($project['mediaList'])): ?>
            <p class="text-xs text-stoneink-500 italic">Belum ada media tersimpan.</p>
          <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <?php foreach ($project['mediaList'] as $mIdx => $media): ?>
                <?php
                  $previewSrc = $media['src'];
                  if (strpos($previewSrc, 'uploads/') === 0) {
                      $previewSrc = '../' . $previewSrc;
                  }
                  $isVideo = ($media['type'] ?? '') === 'video';
                ?>
                <div class="p-3.5 sm:p-4 rounded-2xl border border-ivory-300 bg-ivory-50/60 space-y-3 flex flex-col justify-between shadow-2xs">
                  <div class="space-y-2.5">
                    <div class="aspect-video rounded-xl overflow-hidden bg-stoneink-900 relative">
                      <?php if ($isVideo): ?>
                        <video src="<?= htmlspecialchars($previewSrc) ?>" class="w-full h-full object-cover" muted></video>
                        <span class="absolute top-2 left-2 px-2.5 py-1 rounded-full bg-emerald-600/95 text-white text-[11px] font-bold flex items-center gap-1 shadow-sm">
                          🎬 Video
                        </span>
                      <?php else: ?>
                        <img src="<?= htmlspecialchars($previewSrc) ?>" alt="Preview" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80'">
                        <span class="absolute top-2 left-2 px-2.5 py-1 rounded-full bg-stoneink-900/85 text-white text-[11px] font-bold shadow-sm">
                          📸 Foto
                        </span>
                      <?php endif; ?>
                    </div>

                    <div>
                      <label class="block text-xs font-semibold text-stoneink-700 mb-1">Label Keterangan:</label>
                      <input 
                        type="text" 
                        name="existing_labels[<?= $mIdx ?>]" 
                        value="<?= htmlspecialchars($media['label'] ?? '') ?>"
                        class="w-full px-3 py-2 rounded-xl border border-ivory-300 text-base sm:text-xs bg-white focus:border-moss-700 outline-none"
                      >
                    </div>
                  </div>

                  <div class="pt-2.5 border-t border-ivory-200 flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer text-xs sm:text-sm text-rose-600 font-bold select-none p-1 rounded-lg hover:bg-rose-50 transition-colors">
                      <input type="checkbox" name="delete_media[]" value="<?= $mIdx ?>" class="w-5 h-5 rounded text-rose-600 focus:ring-rose-500">
                      <span>Hapus Media Ini</span>
                    </label>
                    <span class="text-[10px] text-stoneink-400 font-mono truncate max-w-[120px] sm:max-w-[140px]"><?= htmlspecialchars(basename($media['src'])) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Section 3: Upload Tambahan File Foto & Video Baru -->
        <div class="space-y-4 pt-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-ivory-200 pb-2">
            <div>
              <h2 class="text-xs sm:text-sm font-bold text-stoneink-900 uppercase tracking-wider">3. Tambah Foto & Video Baru (Upload File)</h2>
              <p class="text-xs text-stoneink-500">Unggah foto atau klip video baru untuk menambahkan ke galeri model ini.</p>
            </div>
            <button type="button" onclick="addNewFileUploadRow()" class="self-start sm:self-auto text-xs font-bold text-moss-800 hover:text-moss-900 px-3.5 py-2 rounded-xl bg-moss-50 border border-moss-200 transition-colors flex items-center gap-1 active:scale-95">
              + Tambah Baris File
            </button>
          </div>

          <div id="newFileUploadContainer" class="space-y-3">
            <div class="new-file-upload-row p-3.5 sm:p-4 rounded-2xl bg-ivory-50/60 border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
              <div class="flex-1">
                <input 
                  type="file" 
                  name="new_media_files[]" 
                  accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
                  class="w-full text-xs text-stoneink-700 file:mr-3 file:py-2 file:px-3.5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-moss-800 file:text-ivory-50 hover:file:bg-moss-900 cursor-pointer"
                >
              </div>
              <div class="sm:w-1/3">
                <input 
                  type="text" 
                  name="new_media_labels[]" 
                  placeholder="Keterangan foto/video baru" 
                  class="w-full px-3.5 py-2.5 rounded-xl border border-ivory-300 text-base sm:text-xs bg-white focus:border-moss-700 outline-none"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Section 4: Tambah URL Media Online Baru -->
        <div class="space-y-4 pt-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-ivory-200 pb-2">
            <div>
              <h2 class="text-xs sm:text-sm font-bold text-stoneink-900 uppercase tracking-wider">4. Tambah Media Online Baru (URL)</h2>
              <p class="text-xs text-stoneink-500">Opsi untuk menyematkan link foto atau video eksternal.</p>
            </div>
            <button type="button" onclick="addNewMediaUrlRow()" class="self-start sm:self-auto text-xs font-bold text-stoneink-700 hover:text-stoneink-900 px-3.5 py-2 rounded-xl bg-white border border-ivory-300 transition-colors flex items-center gap-1 active:scale-95">
              + Tambah Baris URL
            </button>
          </div>

          <div id="newUrlMediaContainer" class="space-y-3">
            <!-- Dinamis via tombol -->
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-ivory-200 flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-3">
          <a href="index.php" class="w-full sm:w-auto text-center px-6 py-3.5 sm:py-2.5 rounded-full border border-ivory-300 text-stoneink-700 hover:bg-ivory-100 text-sm font-bold transition-colors">
            Batal
          </a>
          <button 
            type="submit" 
            class="w-full sm:w-auto px-7 py-3.5 sm:py-2.5 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-sm font-bold tracking-wide transition-all shadow-md active:scale-95 text-center">
            Simpan Semua Perubahan
          </button>
        </div>

      </form>

    </div>

  </main>

  <script>
    function addNewFileUploadRow() {
      const container = document.getElementById('newFileUploadContainer');
      const row = document.createElement('div');
      row.className = "new-file-upload-row p-3.5 sm:p-4 rounded-2xl bg-ivory-50/60 border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3";
      row.innerHTML = `
        <div class="flex-1">
          <input 
            type="file" 
            name="new_media_files[]" 
            accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
            class="w-full text-xs text-stoneink-700 file:mr-3 file:py-2 file:px-3.5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-moss-800 file:text-ivory-50 hover:file:bg-moss-900 cursor-pointer"
          >
        </div>
        <div class="sm:w-1/3">
          <input 
            type="text" 
            name="new_media_labels[]" 
            placeholder="Keterangan foto/video" 
            class="w-full px-3.5 py-2.5 rounded-xl border border-ivory-300 text-base sm:text-xs bg-white focus:border-moss-700 outline-none"
          >
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-3 py-2 rounded-lg hover:bg-rose-50 self-end sm:self-auto transition-colors">
          Hapus
        </button>
      `;
      container.appendChild(row);
    }

    function addNewMediaUrlRow() {
      const container = document.getElementById('newUrlMediaContainer');
      const row = document.createElement('div');
      row.className = "new-url-media-row p-3.5 sm:p-4 rounded-2xl bg-white border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3";
      row.innerHTML = `
        <div class="sm:w-28">
          <select name="new_url_type[]" class="w-full px-3 py-2.5 rounded-xl border border-ivory-300 text-base sm:text-xs bg-white text-stoneink-800 focus:border-moss-700 outline-none cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23676B65%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_10px_center] bg-no-repeat pr-7">
            <option value="image">📸 Foto</option>
            <option value="video">🎬 Video</option>
          </select>
        </div>
        <div class="flex-1">
          <input 
            type="url" 
            name="new_url_src[]" 
            placeholder="https://images.unsplash.com/... atau URL video" 
            class="w-full px-3.5 py-2.5 rounded-xl border border-ivory-300 text-base sm:text-xs bg-ivory-50/50 focus:border-moss-700 outline-none"
          >
        </div>
        <div class="sm:w-1/3">
          <input 
            type="text" 
            name="new_url_label[]" 
            placeholder="Label media" 
            class="w-full px-3.5 py-2.5 rounded-xl border border-ivory-300 text-base sm:text-xs bg-white focus:border-moss-700 outline-none"
          >
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-3 py-2 rounded-lg hover:bg-rose-50 self-end sm:self-auto transition-colors">
          Hapus
        </button>
      `;
      container.appendChild(row);
    }

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
