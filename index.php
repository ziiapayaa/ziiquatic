<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ziiquatic — Studio Dekorasi Aquarium Air Laut & Biota Laut</title>
  
  <!-- Google Fonts: Plus Jakarta Sans & Newsreader -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Custom Tailwind Config -->
  <script src="assets/js/tailwind-config.js"></script>

  <!-- Modular Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?= @filemtime('assets/css/style.css') ?: '2' ?>">

  <!-- Clean URL (Auto-strip ?i= parameter & #hash untuk URL bersih) -->
  <script>
    try {
      const url = new URL(window.location.href);
      if (url.searchParams.has('i') || url.hash) {
        url.searchParams.delete('i');
        const cleanPath = url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
        window.history.replaceState(null, '', cleanPath);
      }
    } catch (e) {}
  </script>
</head>
<body class="bg-ivory-100 text-stoneink-900 antialiased selection:bg-moss-100 selection:text-moss-900">

  <!-- Top Navigation Header -->
  <header class="sticky top-0 z-40 bg-ivory-100/90 backdrop-blur-md border-b border-ivory-300 transition-all duration-200">
    <div class="max-w-7xl mx-auto px-5 sm:px-8 py-3.5 sm:py-4 flex items-center justify-between">
      
      <!-- Brand Logo: ziiquatic -->
      <a href="./" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="flex items-center gap-2.5 group">
        <img src="logo.png" alt="ziiquatic" class="h-7 sm:h-8 w-auto object-contain shrink-0 group-hover:scale-105 transition-transform">
        <div class="flex flex-col">
          <span class="text-xl font-bold tracking-tight text-stoneink-900 lowercase font-sans">
            ziiquatic<span class="text-moss-700">.</span>
          </span>
          <span class="text-[10px] uppercase tracking-widest text-stoneink-500 font-medium">Marine & Reef Atelier</span>
        </div>
      </a>

      <!-- ORDER Button (Desktop & Mobile) -->
      <a href="https://wa.me/6281958645167?text=Halo%20ziiquatic%2C%20saya%20ingin%20melakukan%20pemesanan%20%2F%20order%20aquarium%20air%20laut." 
         target="_blank" 
         class="px-5 py-2 rounded-full bg-stoneink-900 hover:bg-stoneink-800 text-white text-[11px] font-bold tracking-widest uppercase transition-all shadow-sm active:scale-95">
        ORDER
      </a>

    </div>
  </header>

  <!-- Hero Section -->
  <section id="heroSection" class="min-h-[calc(100dvh-60px)] min-h-[calc(100vh-60px)] flex flex-col justify-center py-8 sm:py-20">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 text-center space-y-6">
      
      <h1 class="text-3xl sm:text-5xl lg:text-[3.5rem] font-bold tracking-tight text-stoneink-900 leading-[1.18]">
        Hadirkan keajaiban terumbu karang <span class="font-serif italic font-normal text-moss-800">di sudut meja</span> dan ruang prestisius Anda.
      </h1>

      <p class="text-sm sm:text-base text-stoneink-700 leading-relaxed max-w-2xl mx-auto font-normal">
        <strong>ziiquatic</strong> melayani <strong>penyediaan lengkap satu pintu (One-Stop Marine Solution)</strong> khusus aquarium air laut. Mulai dari tangki kaca rimless, kabinet custom interior, instalasi mesin filtrasi, live rock, hingga seluruh biota karang dan ikan—semua kami sediakan <em>terima beres</em>.
      </p>

      <!-- CTAs -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
        <a href="#karya" onclick="document.getElementById('karya')?.scrollIntoView({behavior: 'smooth'}); return false;" class="w-full sm:w-auto px-7 py-3 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 text-xs sm:text-sm font-semibold tracking-wide text-center transition-all shadow-sm">
          Lihat Pilihan Ukuran ↓
        </a>
        <a href="https://wa.me/6281958645167?text=Halo%20ziiquatic%2C%20mau%20konsultasi%20pembuatan%20aquarium%20air%20laut%20lengkap." target="_blank" class="w-full sm:w-auto px-7 py-3 rounded-full bg-white hover:bg-ivory-200 text-stoneink-800 border border-ivory-300 text-xs sm:text-sm font-semibold text-center transition-all flex items-center justify-center gap-2">
          <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
          Konsultasi WhatsApp
        </a>
      </div>

    </div>
  </section>

  <!-- Penyediaan Semua Bahan, Alat & Biota (One-Stop Solution) -->
  <section id="penyediaan" class="py-12 sm:py-20 bg-ivory-200/60 border-y border-ivory-300">
    <div class="max-w-7xl mx-auto px-5 sm:px-8">
      
      <div class="max-w-2xl mb-10">
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white text-moss-800 border border-ivory-300 inline-block mb-2">
          One-Stop Marine Solution
        </span>
        <h2 class="text-2xl sm:text-3xl font-bold text-stoneink-900 mt-1">Kami Sediakan Lengkap dari Nol Sampai Jadi</h2>
        <p class="text-xs sm:text-sm text-stoneink-600 mt-2">
          Anda tidak perlu repot mencari toko kaca, tukang kabinet, atau penjual biota terpisah. Semua komponen disiapkan langsung dan terintegrasi oleh tim ziiquatic.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        
        <!-- Poin 1: Tangki Kaca -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            01
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Tangki Kaca & Aquaria Kustom</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Kaca rimless kaca bening (Optic Clear) dengan lem silikon presisi. Mulai dari nano cube 25cm hingga instalasi 150cm+.
          </p>
        </div>

        <!-- Poin 2: Kabinet & Meja -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            02
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Kabinet & Furniture Custom</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Rangka kokoh berlapis HPL anti-air. Desain kabinet diselaraskan dengan estetika interior ruangan Anda.
          </p>
        </div>

        <!-- Poin 3: Mesin & Filtrasi -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            03
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Sistem Mesin & Filtrasi Lengkap</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Sump filter bawah, protein skimmer efisiensi tinggi, pompa sirkulasi senyap, wavemaker, hingga lampu LED spektrum karang.
          </p>
        </div>

        <!-- Poin 4: Hardscape & Air Laut -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            04
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Live Rock, Pasir & Air Laut Matang</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Batu karang hidup berkualitas tinggi yang sudah dikarantina, pasir laut alami, serta air laut dengan salinitas yang telah diuji akurat.
          </p>
        </div>

        <!-- Poin 5: Biota Laut & Living Coral -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            05
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Biota Laut Sehat & Terkarantina</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Penyediaan koral mekar, anemon laut, clownfish (nemo), dory, udang pembersih, dan keong pembersih lumut yang aktif dan sehat.
          </p>
        </div>

        <!-- Poin 6: Perakitan & Cycling -->
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <div class="w-9 h-9 rounded-xl bg-ivory-100 flex items-center justify-center text-moss-800 font-bold text-base border border-ivory-300">
            06
          </div>
          <h3 class="text-sm sm:text-base font-bold text-stoneink-900">Pemasangan di Lokasi & Cycling Aman</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Instalasi jalur pipa yang rapi dan aman dari kebocoran, pengaturan bakteri pengurai (cycling), hingga panduan perawatan harian.
          </p>
        </div>

      </div>

    </div>
  </section>

  <!-- Portofolio Pilihan Ukuran Aquarium -->
  <section id="karya" class="py-12 sm:py-20">
    <div class="max-w-7xl mx-auto px-5 sm:px-8">
      
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-3 mb-6">
        <div>
          <span class="text-xs font-bold text-moss-700 tracking-wider uppercase">Pilihan Ukuran Aquarium</span>
          <h2 class="text-2xl sm:text-3xl font-bold text-stoneink-900 mt-1">Koleksi Ukuran yang Kami Sediakan</h2>
          <p class="text-xs sm:text-sm text-stoneink-600 mt-1">
            Pilih model sesuai ketersediaan meja dan ruangan Anda. Semua ukuran dapat dipesan paket lengkap terima beres.
          </p>
        </div>

        <div class="text-xs text-stoneink-500 font-medium">
          Total <span id="galleryCount" class="font-bold text-stoneink-800">6</span> model tersedia
        </div>
      </div>

      <!-- Gallery Grid -->
      <div id="portfolioGrid" class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 pt-2">
        <!-- Rendered via JavaScript dynamically -->
      </div>

      <!-- Tombol Lihat Semua (Muncul jika model lebih dari 6) -->
      <div id="loadMoreContainer" class="hidden pt-10 text-center">
        <button 
          id="toggleShowAllBtn" 
          onclick="toggleShowAll()" 
          class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-white border border-ivory-300 hover:border-moss-700 hover:bg-moss-50 text-stoneink-900 hover:text-moss-900 text-xs sm:text-sm font-semibold transition-all shadow-sm active:scale-95"
        >
          <span id="toggleBtnText">Lihat Semua Koleksi</span>
        </button>
      </div>

    </div>
  </section>

  <!-- Alur Pemesanan via WhatsApp -->
  <section id="alur" class="py-12 sm:py-20 bg-white border-y border-ivory-300">
    <div class="max-w-7xl mx-auto px-5 sm:px-8">
      
      <div class="max-w-2xl mx-auto text-center mb-10">
        <span class="text-xs font-bold text-moss-700 tracking-wider uppercase">Aman & Terencana</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-stoneink-900 mt-1">Tahapan Pembuatan Aquarium Air Laut</h2>
        <p class="text-xs sm:text-sm text-stoneink-600 mt-2">Seluruh proses kami rancang terukur dan transparan melalui konsultasi langsung via WhatsApp.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-ivory-50 p-5 rounded-2xl border border-ivory-300">
          <span class="text-[11px] font-bold text-moss-700 bg-ivory-200 px-2 py-0.5 rounded-md inline-block mb-2.5">Langkah 01</span>
          <h3 class="text-sm font-bold text-stoneink-900 mb-1.5">Konsultasi Ruangan</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Kirimkan foto sudut meja atau ruang kantor Anda. Kami evaluasi ukuran tangki, posisi ventilasi, dan titik daya listrik.
          </p>
        </div>

        <div class="bg-ivory-50 p-5 rounded-2xl border border-ivory-300">
          <span class="text-[11px] font-bold text-moss-700 bg-ivory-200 px-2 py-0.5 rounded-md inline-block mb-2.5">Langkah 02</span>
          <h3 class="text-sm font-bold text-stoneink-900 mb-1.5">Pilih Konsep Biota</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Menentukan gaya: Nano reef simpel sepasang nemo, soft coral yang tahan banting, atau living reef berwarna-warni.
          </p>
        </div>

        <div class="bg-ivory-50 p-5 rounded-2xl border border-ivory-300">
          <span class="text-[11px] font-bold text-moss-700 bg-ivory-200 px-2 py-0.5 rounded-md inline-block mb-2.5">Langkah 03</span>
          <h3 class="text-sm font-bold text-stoneink-900 mb-1.5">Rincian Penawaran WA</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Kami kirimkan rincian tangki kaca, filtrasi, live rock, lampu reef, garam, dan jasa instalasi terima beres.
          </p>
        </div>

        <div class="bg-ivory-50 p-5 rounded-2xl border border-ivory-300">
          <span class="text-[11px] font-bold text-moss-700 bg-ivory-200 px-2 py-0.5 rounded-md inline-block mb-2.5">Langkah 04</span>
          <h3 class="text-sm font-bold text-stoneink-900 mb-1.5">Cycling & Aklimatisasi</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Penataan live rock di lokasi, pematangan parameter air laut, dan pelepasan biota secara bertahap dan aman.
          </p>
        </div>

      </div>

    </div>
  </section>

  <!-- Empat Pilar Keunggulan -->
  <section id="keunggulan" class="py-12 sm:py-20 bg-ivory-200/50 border-t border-ivory-300">
    <div class="max-w-7xl mx-auto px-5 sm:px-8">
      
      <div class="max-w-2xl mx-auto text-center mb-10">
        <span class="text-xs font-bold text-moss-700 tracking-wider uppercase">Standar Kualitas</span>
        <h2 class="text-2xl sm:text-3xl font-bold text-stoneink-900 mt-1">Mengapa Memilih ziiquatic?</h2>
        <p class="text-xs sm:text-sm text-stoneink-600 mt-2">
          Komitmen kami untuk menghadirkan ekosistem air laut yang stabil, sehat, dan aman dinikmati dalam jangka panjang.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <span class="text-xl font-bold text-moss-800">01</span>
          <h3 class="text-sm font-bold text-stoneink-900">Karang Bebas Hama</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Batu karang hidup berkualitas tinggi yang sudah dikarantina, bebas hama dan parasit berbahaya.
          </p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <span class="text-xl font-bold text-moss-800">02</span>
          <h3 class="text-sm font-bold text-stoneink-900">Air Laut Teruji</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Siklus nitrogen, salinitas, kalsium, dan alkalinitas dipersiapkan presisi sebelum biota masuk.
          </p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <span class="text-xl font-bold text-moss-800">03</span>
          <h3 class="text-sm font-bold text-stoneink-900">Edukasi Praktis</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Panduan tertulis tentang takaran pakan, penggunaan refraktometer salinitas, dan tips top-off air.
          </p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-ivory-300 space-y-2">
          <span class="text-xl font-bold text-moss-800">04</span>
          <h3 class="text-sm font-bold text-stoneink-900">Garansi Aklimatisasi</h3>
          <p class="text-xs text-stoneink-600 leading-relaxed">
            Pendampingan intensif selama masa kritis adaptasi koral dan ikan di minggu-minggu pertama.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- Footer (Centered & Clean Atelier) -->
  <footer id="footerSection" class="bg-white border-t border-ivory-300 text-stoneink-700">
    <!-- Main Content Area -->
    <div class="max-w-2xl mx-auto px-5 sm:px-8 pt-12 pb-10 text-center space-y-6">
      
      <!-- Brand Logo & Studio Desc -->
      <div class="flex flex-col items-center space-y-3">
        <div class="flex items-center justify-center gap-2.5">
          <img src="logo.png" alt="ziiquatic" class="h-8 sm:h-9 w-auto object-contain">
          <span class="text-2xl font-bold tracking-tight text-stoneink-900 lowercase font-sans">
            ziiquatic<span class="text-moss-700">.</span>
          </span>
        </div>
        <p class="text-xs sm:text-sm text-stoneink-600 leading-relaxed max-w-lg mx-auto">
          Studio spesialis dekorasi aquarium air laut, living reef, nano marine tank, dan instalasi terumbu karang kustom. Melayani instalasi baru, perbaikan ekosistem, dan pendampingan berkala.
        </p>
      </div>

      <!-- Social Media & Contact Logos -->
      <div class="flex items-center justify-center gap-3.5 pt-1">
        <!-- WhatsApp Icon Button -->
        <a href="https://wa.me/6281958645167?text=Halo%20ziiquatic%2C%20saya%20ingin%20konsultasi%20pembuatan%20aquarium%20air%20laut." 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="WhatsApp ziiquatic" 
           title="WhatsApp: 081958645167"
           class="w-11 h-11 rounded-full bg-ivory-100 hover:bg-moss-800 text-stoneink-800 hover:text-white border border-ivory-300 hover:border-moss-800 flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 active:scale-95 group">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
          </svg>
        </a>

        <!-- Instagram Icon Button -->
        <a href="https://www.instagram.com/ziiquatic?rpxt=MWtoMm5udXBjeWdzMg==" 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="Instagram @ziiquatic" 
           title="Instagram @ziiquatic"
           class="w-11 h-11 rounded-full bg-ivory-100 hover:bg-moss-800 text-stoneink-800 hover:text-white border border-ivory-300 hover:border-moss-800 flex items-center justify-center transition-all duration-200 shadow-sm hover:scale-105 active:scale-95 group">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
        </a>
      </div>

    </div>

    <!-- Copyright Bottom Bar (Presisi Vertikal Sempurna: py-6) -->
    <div class="border-t border-ivory-200 py-6 text-center text-[11px] text-stoneink-500 space-y-1">
      <p>© 2026 ziiquatic. Seluruh hak cipta dilindungi.</p>
      <p>Marine & Reef Atelier</p>
    </div>
  </footer>

  <!-- Project Detail Modal -->
  <div id="projectModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-stoneink-900/60 backdrop-blur-sm">
    <div class="bg-white border border-ivory-300 rounded-3xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-xl relative">
      
      <!-- Container Media (Foto & Video) -->
      <div class="relative bg-stoneink-900 overflow-hidden rounded-t-3xl select-none">
        
        <!-- Media Viewport -->
        <div id="modalMediaViewport" class="h-60 sm:h-72 w-full flex items-center justify-center relative bg-stoneink-900">
          <img id="modalMainImg" src="" alt="Dokumentasi Aquarium" class="w-full h-full object-cover transition-opacity duration-300">
          <video id="modalMainVideo" src="" class="w-full h-full object-cover hidden" playsinline controls loop muted></video>
        </div>

        <!-- Badges on Media -->
        <div class="absolute bottom-3 right-3 pointer-events-none">
          <span id="modalMediaBadge" class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-stoneink-900/80 text-ivory-100 backdrop-blur-md border border-white/20">
            1/1
          </span>
        </div>

        <!-- Tombol Tutup X -->
        <button onclick="closeModal()" aria-label="Tutup" class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/85 hover:bg-white text-stoneink-900 flex items-center justify-center shadow-md transition-transform active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Navigation Arrows -->
        <button onclick="prevMedia()" aria-label="Sebelumnya" class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/85 hover:bg-white text-stoneink-900 flex items-center justify-center shadow-md transition-transform active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button onclick="nextMedia()" aria-label="Selanjutnya" class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/85 hover:bg-white text-stoneink-900 flex items-center justify-center shadow-md transition-transform active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <!-- Detail Modal Ringkas & Bersih -->
      <div class="p-5 sm:p-6 space-y-3.5">
        <div>
          <h3 id="modalTitle" class="text-lg sm:text-xl font-bold text-stoneink-900">Aquarium</h3>
          <p id="modalPlacement" class="text-xs text-stoneink-600 mt-1">Penempatan ruangan</p>
        </div>

        <p id="modalDesc" class="text-xs sm:text-sm text-stoneink-700 leading-relaxed">
          Deskripsi singkat model.
        </p>

        <!-- WhatsApp Action Button -->
        <div class="pt-2">
          <a id="modalWaBtn" href="#" target="_blank" class="w-full py-3 rounded-full bg-moss-800 hover:bg-moss-900 text-ivory-50 font-semibold text-xs sm:text-sm text-center flex items-center justify-center gap-2 shadow-sm transition-all">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
            Tanya Biaya Ukuran Ini di WhatsApp
          </a>
        </div>

      </div>

    </div>
  </div>

  <!-- Modular Scripts (Auto-cache-busting) -->
  <script src="assets/js/main.js?v=<?= @filemtime('assets/js/main.js') ?: '2' ?>"></script>
</body>
</html>
