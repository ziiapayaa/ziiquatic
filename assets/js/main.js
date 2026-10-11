/**
 * ziiquatic — Studio Dekorasi Aquarium Air Laut
 * Main Frontend Logic
 */

// Global State
let portfolioProjects = [];
let activeProjectMedia = [];
let currentMediaIndex = 0;
let isShowingAll = false;
const MAX_INITIAL_ITEMS = 6;

// Security Escaping & Sanitization Helpers (Anti-XSS Protection)
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function sanitizeMediaUrl(url) {
  if (!url || typeof url !== 'string') return '';
  const clean = url.trim();
  // Hanya izinkan http, https, atau path uploads/ lokal (Blokir javascript:, data:, dll)
  if (/^https?:\/\//i.test(clean) || clean.startsWith('uploads/')) {
    return encodeURI(clean).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }
  return 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80';
}

// Fallback data jika dibuka tanpa web server (file:///)
const defaultPortfolioProjects = [
  {
    id: 1,
    title: "Aquarium 25 – 35 cm",
    category: "kecil",
    sizeBadge: "Ukuran 25 – 35 cm",
    placement: "Sangat pas untuk meja kerja, meja belajar, atau kamar tidur",
    desc: "Ukuran ringkas dan hemat tempat. Perawatan sangat praktis, cocok untuk menikmati keindahan terumbu karang dan sepasang ikan nemo di meja kerja.",
    mediaList: [
      { type: 'image', label: 'Tampak Depan', src: 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Detail Nemo & Karang', src: 'https://images.unsplash.com/photo-1535591273668-578e31182c4f?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Lampu Malam (Blue Light)', src: 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?auto=format&fit=crop&w=1000&q=80' }
    ]
  },
  {
    id: 2,
    title: "Aquarium 40 cm",
    category: "kecil",
    sizeBadge: "Ukuran 40 cm",
    placement: "Cocok untuk meja resepsionis, bufet samping, atau sudut santai",
    desc: "Memberikan ruang renang yang lebih leluasa untuk beberapa jenis ikan hias kecil dan karang mekar warna-warni tanpa memakan banyak ruang.",
    mediaList: [
      { type: 'image', label: 'Tampak Depan', src: 'https://images.unsplash.com/photo-1508873881324-c92a3fc536ba?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Sudut Samping', src: 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Anemon & Karang', src: 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?auto=format&fit=crop&w=1000&q=80' }
    ]
  },
  {
    id: 3,
    title: "Aquarium 60 cm",
    category: "sedang",
    sizeBadge: "Ukuran 60 cm",
    placement: "Ideal untuk ruang tamu, ruang kerja pribadi, atau bufet keluarga",
    desc: "Ukuran paling favorit dan proporsional. Ekosistem air laut sangat stabil, terumbu karang leluasa mekar, dan tampil elegan menyatu dengan ruangan.",
    mediaList: [
      { type: 'image', label: 'Tampak Ruangan', src: 'https://images.unsplash.com/photo-1534043464124-3be32fe00099?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Close-up Karang Mekar', src: 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=1000&q=80' },
      { type: 'video', label: 'Video Gerak Arus', src: 'https://assets.mixkit.co/videos/preview/mixkit-small-fish-swimming-in-a-coral-reef-43224-large.mp4' }
    ]
  },
  {
    id: 4,
    title: "Aquarium 90 cm",
    category: "sedang",
    sizeBadge: "Ukuran 90 cm",
    placement: "Pilihan pas untuk ruang keluarga, lobi kantor, atau partisi ruangan",
    desc: "Dilengkapi meja kabinet interior elegan dan sistem filter tersembunyi di bagian bawah. Memberikan pemandangan karang laut yang megah dan menawan.",
    mediaList: [
      { type: 'image', label: 'Pemandangan Penuh', src: 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Kerapian Kabinet & Sump', src: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Formasi Batuan Karang', src: 'https://images.unsplash.com/photo-1508873881324-c92a3fc536ba?auto=format&fit=crop&w=1000&q=80' }
    ]
  },
  {
    id: 5,
    title: "Aquarium 120 cm",
    category: "besar",
    sizeBadge: "Ukuran 120 cm",
    placement: "Cocok untuk ruang rapat direksi, lobi perusahaan, atau ruang tamu utama",
    desc: "Instalasi skala besar yang memukau. Menghadirkan miniatur pemandangan samudra utuh dengan formasi batuan karang bertingkat dan ikan hias yang aktif.",
    mediaList: [
      { type: 'image', label: 'Tampak Depan Utama', src: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Detail Coral Garden', src: 'https://images.unsplash.com/photo-1534043464124-3be32fe00099?auto=format&fit=crop&w=1000&q=80' },
      { type: 'video', label: 'Video Ikan Berenang', src: 'https://assets.mixkit.co/videos/preview/mixkit-tropical-fish-underwater-4127-large.mp4' }
    ]
  },
  {
    id: 6,
    title: "Aquarium 150 cm+",
    category: "besar",
    sizeBadge: "Ukuran 150 cm+",
    placement: "Karya arsitektural untuk vila, hunian mewah, restoran, atau lounge eksekutif",
    desc: "Dibuat khusus mengikuti dimensi ruangan Anda. Menjadi pusat perhatian utama dengan peredam getaran senyap dan perlengkapan otomatis lengkap.",
    mediaList: [
      { type: 'image', label: 'Instalasi Lengkap', src: 'https://images.unsplash.com/photo-1524704654690-b56c05c78a00?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Perspektif Sudut Ruang', src: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1000&q=80' },
      { type: 'image', label: 'Detail Ekosistem Laut', src: 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?auto=format&fit=crop&w=1000&q=80' }
    ]
  }
];

// Inisialisasi Ambil Data Proyek
async function fetchPortfolioData() {
  try {
    const res = await fetch('api/get_projects.php');
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data) && data.length > 0) {
        portfolioProjects = data;
      } else {
        portfolioProjects = defaultPortfolioProjects;
      }
    } else {
      portfolioProjects = defaultPortfolioProjects;
    }
  } catch (err) {
    console.warn('Menggunakan fallback data portofolio:', err);
    portfolioProjects = defaultPortfolioProjects;
  }
  renderGallery();
}

// Render Kartu Galeri (Maksimal 6 secara default, dengan tombol Lihat Semua)
function renderGallery() {
  const container = document.getElementById('portfolioGrid');
  if (!container) return;
  container.innerHTML = '';

  const total = portfolioProjects.length;
  const countEl = document.getElementById('galleryCount');
  if (countEl) countEl.innerText = total;

  if (total === 0) {
    container.innerHTML = `
      <div class="col-span-full py-12 text-center text-stoneink-500 text-sm bg-white rounded-2xl border border-ivory-300 p-8">
        Belum ada portofolio yang ditampilkan.
      </div>
    `;
    const moreContainer = document.getElementById('loadMoreContainer');
    if (moreContainer) moreContainer.classList.add('hidden');
    return;
  }

  // Tampilkan maksimal 6 item jika belum klik "Lihat Semua"
  const itemsToRender = isShowingAll ? portfolioProjects : portfolioProjects.slice(0, MAX_INITIAL_ITEMS);

  itemsToRender.forEach(item => {
    const safeId = parseInt(item.id, 10) || 0;
    const safeTitle = escapeHtml(item.title || 'Model Aquarium');
    const safeBadge = escapeHtml(item.sizeBadge || item.title || 'Spesifikasi');
    const coverMedia = item.mediaList && item.mediaList.length > 0 ? item.mediaList[0] : null;
    const coverSrc = coverMedia && coverMedia.src ? coverMedia.src : '';
    const safeCoverImg = sanitizeMediaUrl(coverSrc);
    const mediaCount = item.mediaList && Array.isArray(item.mediaList) ? item.mediaList.length : 0;
    const hasVideo = item.mediaList && Array.isArray(item.mediaList) ? item.mediaList.some(m => m.type === 'video') : false;

    const waText = encodeURIComponent(`Halo ziiquatic, saya ingin tanya estimasi biaya dan konsultasi untuk "${item.title}". Apakah bisa dipesan paket lengkap terima beres?`);
    const waLink = `https://wa.me/6281958645167?text=${waText}`;

    const card = document.createElement('div');
    card.className = "bg-white rounded-2xl p-2.5 sm:p-4 border border-ivory-300 hover:border-stoneink-400 transition-all flex flex-col justify-between group shadow-sm";

    card.innerHTML = `
      <div>
        <div class="aspect-square rounded-xl overflow-hidden bg-ivory-200 relative mb-2 sm:mb-3 cursor-pointer" onclick="openModal(${safeId})">
          <img 
            src="${safeCoverImg}" 
            alt="${safeTitle}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            onerror="this.src='https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80'"
          >
          <span class="absolute top-2 left-2 bg-white/95 backdrop-blur-md px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold text-stoneink-900 border border-ivory-300 shadow-sm">
            ${safeBadge}
          </span>
          <span class="absolute bottom-2 right-2 bg-stoneink-900/80 backdrop-blur-md px-1.5 py-0.5 rounded-full text-[8px] sm:text-[9px] font-semibold text-ivory-100 flex items-center gap-1 shadow-sm">
            ${hasVideo ? '▶ ' : ''}${mediaCount} Media
          </span>
        </div>

        <div class="px-0.5">
          <h3 class="text-xs sm:text-sm font-bold text-stoneink-900 group-hover:text-moss-800 transition-colors line-clamp-1 leading-tight cursor-pointer" onclick="openModal(${safeId})">
            ${safeTitle}
          </h3>
        </div>
      </div>

      <div class="pt-2 sm:pt-3 mt-2 border-t border-ivory-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-1.5 px-0.5">
        <button onclick="openModal(${safeId})" class="text-[10px] sm:text-xs font-semibold text-stoneink-700 hover:text-stoneink-900 text-center sm:text-left py-0.5">
          Lihat ${mediaCount} Media
        </button>
        <a href="${waLink}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto px-2.5 py-1.5 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-[10px] sm:text-xs font-semibold text-center flex items-center justify-center gap-1 transition-all shadow-sm">
          Tanya WA →
        </a>
      </div>
    `;
    container.appendChild(card);
  });

  // Perbarui kondisi tombol "Lihat Semua Model"
  updateLoadMoreButton(total);
}

// Logika Tombol Lihat Semua
function updateLoadMoreButton(total) {
  const moreContainer = document.getElementById('loadMoreContainer');
  const btnText = document.getElementById('toggleBtnText');
  if (!moreContainer) return;

  if (total > MAX_INITIAL_ITEMS) {
    moreContainer.classList.remove('hidden');
    if (btnText) {
      if (isShowingAll) {
        btnText.innerHTML = `Tampilkan Lebih Sedikit`;
      } else {
        btnText.innerHTML = `Lihat Semua Koleksi`;
      }
    }
  } else {
    moreContainer.classList.add('hidden');
  }
}

function toggleShowAll() {
  isShowingAll = !isShowingAll;
  renderGallery();
  if (!isShowingAll) {
    const section = document.getElementById('karya');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    }
  }
}

// Buka Modal Detail Proyek
function openModal(projectId) {
  const item = portfolioProjects.find(p => p.id == projectId);
  if (!item) return;

  activeProjectMedia = item.mediaList || [];
  currentMediaIndex = 0;

  document.getElementById('modalTitle').innerText = item.title || '';
  document.getElementById('modalPlacement').innerText = item.placement || '';
  document.getElementById('modalDesc').innerText = item.desc || '';

  const waText = encodeURIComponent(`Halo ziiquatic, saya melihat contoh "${item.title}". Mau konsultasi pembuatan untuk ukuran ini.`);
  document.getElementById('modalWaBtn').href = `https://wa.me/6281958645167?text=${waText}`;

  renderMediaView();

  document.getElementById('projectModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

// Tampilkan Media Aktif di Modal
function renderMediaView() {
  const imgEl = document.getElementById('modalMainImg');
  const videoEl = document.getElementById('modalMainVideo');
  const badgeEl = document.getElementById('modalMediaBadge');

  if (!activeProjectMedia.length) {
    imgEl.src = 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?auto=format&fit=crop&w=800&q=80';
    imgEl.classList.remove('hidden');
    videoEl.classList.add('hidden');
    badgeEl.innerText = '0/0';
    return;
  }

  const current = activeProjectMedia[currentMediaIndex];
  badgeEl.innerText = `${currentMediaIndex + 1}/${activeProjectMedia.length}`;

  if (current.type === 'video') {
    imgEl.classList.add('hidden');
    videoEl.classList.remove('hidden');
    videoEl.src = sanitizeMediaUrl(current.src);
    videoEl.play().catch(() => {});
  } else {
    videoEl.pause();
    videoEl.classList.add('hidden');
    imgEl.classList.remove('hidden');
    imgEl.src = sanitizeMediaUrl(current.src);
  }
}

// Navigasi Media
function nextMedia() {
  if (!activeProjectMedia.length) return;
  currentMediaIndex = (currentMediaIndex + 1) % activeProjectMedia.length;
  renderMediaView();
}

function prevMedia() {
  if (!activeProjectMedia.length) return;
  currentMediaIndex = (currentMediaIndex - 1 + activeProjectMedia.length) % activeProjectMedia.length;
  renderMediaView();
}

function closeModal() {
  const videoEl = document.getElementById('modalMainVideo');
  if (videoEl) videoEl.pause();
  document.getElementById('projectModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

// Event Keyboard untuk Modal (ESC & Arrow keys)
document.addEventListener('keydown', function(e) {
  const modal = document.getElementById('projectModal');
  if (!modal || modal.classList.contains('hidden')) return;

  if (e.key === 'Escape') closeModal();
  if (e.key === 'ArrowRight') nextMedia();
  if (e.key === 'ArrowLeft') prevMedia();
});

// Event Listener Saat Halaman Dimuat
document.addEventListener('DOMContentLoaded', () => {
  fetchPortfolioData();

  // Tutup modal jika area background diklik
  const modal = document.getElementById('projectModal');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
  }
});
