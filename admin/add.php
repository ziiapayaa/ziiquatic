<?php
require_once __DIR__ . '/auth.php';
require_login();

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

            // Hitung ID baru
            $maxId = 0;
            foreach ($projects as $p) {
                if (isset($p['id']) && $p['id'] > $maxId) {
                    $maxId = (int)$p['id'];
                }
            }
            $newId = $maxId + 1;

            $mediaList = [];

            // 1. Proses Upload File Fisik (Foto / Video)
            if (!empty($_FILES['media_files']['name'][0])) {
                $fileCount = count($_FILES['media_files']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['media_files']['error'][$i] === UPLOAD_ERR_OK) {
                        $singleFile = [
                            'name' => $_FILES['media_files']['name'][$i],
                            'type' => $_FILES['media_files']['type'][$i],
                            'tmp_name' => $_FILES['media_files']['tmp_name'][$i],
                            'error' => $_FILES['media_files']['error'][$i],
                            'size' => $_FILES['media_files']['size'][$i],
                        ];
                        $uploaded = handle_file_upload($singleFile);
                        if ($uploaded) {
                            $label = trim($_POST['media_file_labels'][$i] ?? '');
                            $mediaList[] = [
                                'type' => $uploaded['type'],
                                'label' => !empty($label) ? $label : pathinfo($singleFile['name'], PATHINFO_FILENAME),
                                'src' => $uploaded['src']
                            ];
                        }
                    }
                }
            }

            // 2. Proses Input Media URL (Link Eksternal Validasi Skema http/https)
            if (!empty($_POST['url_src']) && is_array($_POST['url_src'])) {
                foreach ($_POST['url_src'] as $idx => $url) {
                    $cleanUrl = trim((string)$url);
                    if (!empty($cleanUrl) && is_valid_media_url($cleanUrl)) {
                        $urlType = $_POST['url_type'][$idx] ?? 'image';
                        $urlLabel = trim(strip_tags((string)($_POST['url_label'][$idx] ?? '')));
                        $mediaList[] = [
                            'type' => in_array($urlType, ['video', 'image']) ? $urlType : 'image',
                            'label' => !empty($urlLabel) ? $urlLabel : 'Media Online',
                            'src' => $cleanUrl
                        ];
                    }
                }
            }

            // Jika tidak ada media yang diupload sama sekali, sediakan fallback default
            if (empty($mediaList)) {
                $mediaList[] = [
                    'type' => 'image',
                    'label' => 'Tampak Depan',
                    'src' => 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1000&q=80'
                ];
            }

            $newProject = [
                'id' => $newId,
                'title' => $title,
                'category' => 'umum',
                'sizeBadge' => !empty($sizeBadge) ? $sizeBadge : $title,
                'placement' => $placement,
                'desc' => $desc,
                'mediaList' => $mediaList
            ];

            $projects[] = $newProject;
            if (save_all_projects($projects)) {
                $_SESSION['flash_success'] = 'Model aquarium "' . $title . '" berhasil ditambahkan!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Gagal menyimpan data ke berkas JSON. Periksa hak izin folder.';
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
  <title>Tambah Model Aquarium — ziiquatic Admin</title>
  
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

  <!-- Top Admin Navbar -->
  <header class="bg-white border-b border-ivory-300 sticky top-0 z-30 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 py-3.5 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="index.php" class="text-xs font-semibold text-moss-800 hover:text-moss-900 flex items-center gap-1.5">
          ← Kembali ke Dashboard
        </a>
      </div>
      <span class="text-xs text-stoneink-500 font-medium">Tambah Model Baru</span>
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-4xl mx-auto px-4 sm:px-8 py-8 flex-1 w-full">
    
    <div class="bg-white rounded-3xl border border-ivory-300 p-6 sm:p-10 shadow-sm space-y-8">
      
      <div>
        <h1 class="text-2xl font-bold text-stoneink-900 tracking-tight">Tambah Model Aquarium</h1>
        <p class="text-xs sm:text-sm text-stoneink-500 mt-1">Masukkan informasi ukuran, penempatan ruangan, serta foto atau video dokumentasinya.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash-alert p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center justify-between gap-2 transition-all duration-300">
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

      <form method="POST" action="add.php" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <!-- Section 1: Info Dasar -->
        <div class="space-y-4">
          <h2 class="text-sm font-bold text-stoneink-900 uppercase tracking-wider border-b border-ivory-200 pb-2">1. Informasi Model</h2>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="title" class="block text-xs font-semibold text-stoneink-800 mb-1.5">Nama / Judul Ukuran <span class="text-rose-600">*</span></label>
              <input 
                type="text" 
                id="title" 
                name="title" 
                required 
                placeholder="Contoh: Aquarium 75 cm" 
                class="w-full px-4 py-2.5 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50"
              >
            </div>

            <div>
              <label for="sizeBadge" class="block text-xs font-semibold text-stoneink-800 mb-1.5">Badge Tampilan Ukuran</label>
              <input 
                type="text" 
                id="sizeBadge" 
                name="sizeBadge" 
                placeholder="Contoh: Ukuran 75 cm" 
                class="w-full px-4 py-2.5 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50"
              >
            </div>
          </div>

          <div>
            <label for="placement" class="block text-xs font-semibold text-stoneink-800 mb-1.5">Rekomendasi Penempatan</label>
            <input 
              type="text" 
              id="placement" 
              name="placement" 
              placeholder="Contoh: Sangat pas untuk meja resepsionis atau ruang tamu" 
              class="w-full px-4 py-2.5 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50"
            >
          </div>

          <div>
            <label for="desc" class="block text-xs font-semibold text-stoneink-800 mb-1.5">Deskripsi Detail</label>
            <textarea 
              id="desc" 
              name="desc" 
              rows="3" 
              placeholder="Deskripsi spesifikasi, stabilitas ekosistem, jenis biota yang cocok..." 
              class="w-full px-4 py-2.5 rounded-xl border border-ivory-300 focus:border-moss-700 focus:ring-2 focus:ring-moss-100 outline-none text-sm bg-ivory-50/50"
            ></textarea>
          </div>
        </div>

        <!-- Section 2: Upload File Foto & Video -->
        <div class="space-y-4 pt-4">
          <div class="flex items-center justify-between border-b border-ivory-200 pb-2">
            <div>
              <h2 class="text-sm font-bold text-stoneink-900 uppercase tracking-wider">2. Upload Foto & Video Langsung</h2>
              <p class="text-xs text-stoneink-500">Pilih file foto (.jpg, .png, .webp) atau video (.mp4, .webm) dari komputer Anda.</p>
            </div>
            <button type="button" onclick="addFileUploadRow()" class="text-xs font-semibold text-moss-800 hover:text-moss-900 px-3 py-1.5 rounded-lg bg-moss-50 border border-moss-200 transition-colors">
              + Tambah Baris File
            </button>
          </div>

          <div id="fileUploadContainer" class="space-y-3">
            <div class="file-upload-row p-3 rounded-2xl bg-ivory-50/60 border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
              <div class="flex-1">
                <input 
                  type="file" 
                  name="media_files[]" 
                  accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
                  class="w-full text-xs text-stoneink-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-moss-800 file:text-ivory-50 hover:file:bg-moss-900 cursor-pointer"
                >
              </div>
              <div class="sm:w-1/3">
                <input 
                  type="text" 
                  name="media_file_labels[]" 
                  placeholder="Keterangan (misal: Tampak Depan)" 
                  class="w-full px-3 py-1.5 rounded-xl border border-ivory-300 text-xs bg-white focus:border-moss-700 outline-none"
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Opsi Tambah URL Media Online -->
        <div class="space-y-4 pt-4">
          <div class="flex items-center justify-between border-b border-ivory-200 pb-2">
            <div>
              <h2 class="text-sm font-bold text-stoneink-900 uppercase tracking-wider">3. Opsi: Link URL Media Online (Opsional)</h2>
              <p class="text-xs text-stoneink-500">Jika file foto/video sudah ada di online (Unsplash, link video langsung), Anda bisa memasukkan URL-nya di sini.</p>
            </div>
            <button type="button" onclick="addUrlMediaRow()" class="text-xs font-semibold text-stoneink-700 hover:text-stoneink-900 px-3 py-1.5 rounded-lg bg-white border border-ivory-300 transition-colors">
              + Tambah Baris URL
            </button>
          </div>

          <div id="urlMediaContainer" class="space-y-3">
            <!-- Dinamis via tombol di atas -->
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-ivory-200 flex items-center justify-end gap-3">
          <a href="index.php" class="px-5 py-2.5 rounded-full border border-ivory-300 text-stoneink-700 hover:bg-ivory-100 text-xs sm:text-sm font-semibold transition-colors">
            Batal
          </a>
          <button 
            type="submit" 
            class="px-6 py-2.5 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-xs sm:text-sm font-semibold tracking-wide transition-all shadow-sm active:scale-95">
            Simpan Model & Media
          </button>
        </div>

      </form>

    </div>

  </main>

  <script>
    function addFileUploadRow() {
      const container = document.getElementById('fileUploadContainer');
      const row = document.createElement('div');
      row.className = "file-upload-row p-3 rounded-2xl bg-ivory-50/60 border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3";
      row.innerHTML = `
        <div class="flex-1">
          <input 
            type="file" 
            name="media_files[]" 
            accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime" 
            class="w-full text-xs text-stoneink-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-moss-800 file:text-ivory-50 hover:file:bg-moss-900 cursor-pointer"
          >
        </div>
        <div class="sm:w-1/3">
          <input 
            type="text" 
            name="media_file_labels[]" 
            placeholder="Keterangan (misal: Detail Karang)" 
            class="w-full px-3 py-1.5 rounded-xl border border-ivory-300 text-xs bg-white focus:border-moss-700 outline-none"
          >
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs font-semibold px-2 py-1">
          Hapus
        </button>
      `;
      container.appendChild(row);
    }

    function addUrlMediaRow() {
      const container = document.getElementById('urlMediaContainer');
      const row = document.createElement('div');
      row.className = "url-media-row p-3 rounded-2xl bg-white border border-ivory-300 flex flex-col sm:flex-row items-stretch sm:items-center gap-3";
      row.innerHTML = `
        <div class="sm:w-28">
          <select name="url_type[]" class="w-full px-3 py-1.5 rounded-xl border border-ivory-300 text-xs bg-white text-stoneink-800 focus:border-moss-700 outline-none cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23676B65%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_10px_center] bg-no-repeat pr-7">
            <option value="image">📸 Foto</option>
            <option value="video">🎬 Video</option>
          </select>
        </div>
        <div class="flex-1">
          <input 
            type="url" 
            name="url_src[]" 
            placeholder="https://images.unsplash.com/... atau URL video" 
            class="w-full px-3 py-1.5 rounded-xl border border-ivory-300 text-xs bg-ivory-50/50 focus:border-moss-700 outline-none"
          >
        </div>
        <div class="sm:w-1/3">
          <input 
            type="text" 
            name="url_label[]" 
            placeholder="Label (misal: Lampu Malam)" 
            class="w-full px-3 py-1.5 rounded-xl border border-ivory-300 text-xs bg-white focus:border-moss-700 outline-none"
          >
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-xs font-semibold px-2 py-1">
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
