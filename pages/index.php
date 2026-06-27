    <?php 
        require_once '../includes/layout.php'; 
        layoutHead('Beranda');
    ?>
    <style>
        * { scroll-behavior: smooth; }
        .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .wave-divider {line-height: 0;}
        .wave-divider svg { display: block; width: 100%; margin-bottom: -1px;}
        .fact-card { transition: all 0.25s ease; border-left: 3px solid transparent; }
        .fact-card:hover { border-left-color: #C8F135; background: #f0f7ff; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-up { animation: fadeInUp 0.6s ease forwards; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .opacity-0-init { opacity: 0; }
        nav.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,0.1); background: white !important; }

    </style>
</head>
<body class="bg-[#F5F4EF] min-h-screen">

<?php
// ── Koneksi database — sesuai struktur folder proyek ──
require_once '../config/database.php';
$conn = getConnection();

// Ambil jumlah data real dari database
$jml_penyakit = $conn->query("SELECT COUNT(*) t FROM penyakit")->fetch_assoc()['t'];
$jml_gejala   = $conn->query("SELECT COUNT(*) t FROM gejala")->fetch_assoc()['t'];
$jml_aturan   = $conn->query("SELECT COUNT(*) t FROM basis_aturan")->fetch_assoc()['t'];
?>

<!-- ══════════════════════════════
     NAVBAR
══════════════════════════════ -->
<nav id="navbar" class="fixed top-0 w-full z-50 transition-all duration-300 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                    <img src="../assets/logo-patin-SIPATIN.webp">
                </div>
                <span class="font-bold text-white text-lg nav-brand transition-colors duration-300">SIPATIN</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#beranda"  class="text-muted hover:text-white font-medium nav-link transition-colors duration-300 text-sm">Beranda</a>
                <a href="#tentang"  class="text-muted hover:text-white font-medium nav-link transition-colors duration-300 text-sm">Tentang</a>
                <a href="#fakta"    class="text-muted hover:text-white font-medium nav-link transition-colors duration-300 text-sm">Fakta Ikan Patin</a>
                <a href="#penyakit" class="text-muted hover:text-white font-medium nav-link transition-colors duration-300 text-sm">Penyakit</a>
                <!-- Link diagnosa — sesuai struktur proyek (section #diagnosa di halaman yang sama) -->
                <a href="diagnosa.php" class="btn-accent">
                    <i class="fas fa-stethoscope mr-2"></i>Mulai Diagnosa
                </a>
            </div>
            <button id="mobileMenuBtn" class="md:hidden text-white nav-link transition-colors duration-300">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden bg-white shadow-lg">
        <div class="px-4 py-3 space-y-2">
            <a href="#beranda"  class="block py-2 text-dark font-medium text-sm">Beranda</a>
            <a href="#fakta"    class="block py-2 text-dark font-medium text-sm">Fakta Ikan Patin</a>
            <a href="#penyakit" class="block py-2 text-dark font-medium text-sm">Penyakit</a>
            <a href="diagnosa.php" class="block py-2 bg-accent text-dark text-center rounded-lg font-semibold text-sm">Mulai Diagnosa</a>
        </div>
    </div>
</nav>

<!-- ══════════════════════════════
     HERO
══════════════════════════════ -->
<section id="beranda" class="bg-dark min-h-screen flex items-center relative overflow-hidden pt-16">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="opacity-0-init animate-fade-up">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-5">
                    Diagnosa Penyakit<br>
                    <span class="text-accent">Ikan Patin</span><br>
                    Lebih Cepat &amp; Mudah
                </h1>
                <p class="text-white/75 text-base mb-8 leading-relaxed">
                    Platform diagnosis  yang membantu pembudidaya ikan patin mengidentifikasi dan menangani penyakit dengan cepat menggunakan teknologi sistem pakar.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="diagnosa.php" class="btn-accent px-8 py-3.5 rounded-xl font-bold text-base transition-all duration-200 flex items-center justify-center gap-2">
                         Mulai Diagnosa - Gratis
                    </a>
                    <a href="#tentang" class="bg-white/10 hover:bg-white/20 backdrop-blur text-white px-8 py-3.5 rounded-xl font-semibold text-base transition-all duration-200 flex items-center justify-center gap-2 border border-white/20">
                        <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex justify-center animate-fade-up delay-200">
                <img src="../assets/hero-patin.jpg" alt="Ilustrasi Ikan Patin" class="w-full max-w-md rounded-2xl shadow-lg transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
            </div>

        </div>

    </div>

    <div class="absolute bottom-0 left-0 w-full wave-divider">
        <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="#F5F4EF"/>
        </svg>
    </div>
</section>

<!-- ══════════════════════════════
     TENTANG
══════════════════════════════ -->
<section id="tentang" class="py-20 bg-gray"> 
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cara Penggunaan -->
        <div class="bg-white rounded-3xl p-8 border border-[#E8E7E1]">
            <h3 class="font-bold text-gray-900 text-xl mb-7 text-center">Cara Menggunakan Sistem</h3>
            <div class="grid md:grid-cols-4 gap-6">
                <?php $steps = [
                    ['icon'=>'fas fa-hand-pointer', 'num'=>'1','title'=>'Pilih Gejala',    'desc'=>'Pilih gejala-gejala yang Anda amati pada ikan patin dari daftar yang tersedia'],
                    ['icon'=>'fas fa-cogs',          'num'=>'2','title'=>'Proses Analisis', 'desc'=>'Sistem memproses gejala menggunakan mesin inferensi forward chaining secara otomatis'],
                    ['icon'=>'fas fa-file-medical',  'num'=>'3','title'=>'Lihat Hasil',     'desc'=>'Hasil diagnosa penyakit beserta tingkat kepercayaan ditampilkan secara detail'],
                    ['icon'=>'fas fa-prescription-bottle-medical','num'=>'4','title'=>'Terapkan Solusi','desc'=>'Ikuti panduan penanganan dan pencegahan yang diberikan oleh sistem pakar'],
                ];
                foreach($steps as $i => $step): ?>
                <div class="text-center relative">
                    <?php if($i < 3): ?>
                    <div class="hidden md:block absolute top-6 left-1/2 w-full h-0.5 bg-accent z-0"></div>
                    <?php endif; ?>
                    <div class="relative z-10 w-12 h-12 bg-dark rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold"><?= $step['num'] ?></div>
                    <h4 class="font-semibold text-gray-800 mb-2"><?= $step['title'] ?></h4>
                    <p class="text-gray-500 text-xs leading-relaxed"><?= $step['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════
     FAKTA IKAN PATIN
══════════════════════════════ -->
<section id="fakta" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-darkAccent font-semibold text-sm uppercase tracking-widest">Pengetahuan</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Fakta Menarik Ikan Patin</h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">Ikan patin adalah komoditas perikanan air tawar unggulan Indonesia dengan berbagai keunggulan</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 mb-10">
            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-500"></i> Fakta Umum
                </h3>
                <div class="space-y-3">
                    <?php $faktaUmum = [
                        ['icon'=>'🌍','title'=>'Habitat Asli',    'desc'=>'Ikan patin (Pangasius hypophthalmus) berasal dari Sungai Mekong di Asia Tenggara dan merupakan ikan air tawar tropis yang dapat hidup di berbagai kondisi kolam budidaya.'],
                        ['icon'=>'📏','title'=>'Ukuran Besar',    'desc'=>'Ikan patin dapat tumbuh hingga 130 cm dengan berat mencapai 44 kg dalam kondisi liar. Namun untuk budidaya konsumsi, biasanya dipanen pada ukuran 500–800 gram.'],
                        ['icon'=>'⚡','title'=>'Pertumbuhan Cepat','desc'=>'Dalam 6 bulan budidaya intensif, ikan patin bisa mencapai berat 600–800 gram — salah satu ikan yang paling cepat tumbuh di kelasnya.'],
                        ['icon'=>'🌊','title'=>'Daya Tahan Tinggi', 'desc'=>'Ikan patin mampu bertahan dalam kondisi oksigen rendah dan kualitas air yang kurang baik, menjadikannya ideal untuk budidaya padat.'],
                    ]; foreach($faktaUmum as $f): ?>
                    <div class="fact-card flex gap-4 p-4 rounded-xl border border-gray-100">
                        <div class="text-2xl flex-shrink-0"><?= $f['icon'] ?></div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm mb-1"><?= $f['title'] ?></div>
                            <div class="text-gray-500 text-xs leading-relaxed"><?= $f['desc'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-500"></i> Nilai Gizi &amp; Ekonomi
                </h3>
                <div class="space-y-3">
                    <?php $faktaGizi = [
                        ['icon'=>'🥩','title'=>'Kaya Protein',    'desc'=>'Daging ikan patin mengandung protein 15–20% dengan kandungan asam amino esensial yang lengkap, sumber protein hewani yang baik dan terjangkau.'],
                        ['icon'=>'🫀','title'=>'Omega-3 Tinggi',  'desc'=>'Ikan patin mengandung asam lemak omega-3 dan omega-6 yang bermanfaat bagi kesehatan jantung, otak, dan sistem imun.'],
                        ['icon'=>'💰','title'=>'Komoditas Ekspor', 'desc'=>'Indonesia adalah salah satu pengekspor ikan patin terbesar dunia. Produk fillet beku banyak diekspor ke Amerika Serikat, Eropa, dan Asia.'],
                        ['icon'=>'📈','title'=>'Produksi Nasional','desc'=>'Produksi ikan patin Indonesia mencapai lebih dari 600.000 ton per tahun, menjadikannya komoditas budidaya perikanan terpenting nasional.'],
                    ]; foreach($faktaGizi as $f): ?>
                    <div class="fact-card flex gap-4 p-4 rounded-xl border border-gray-100">
                        <div class="text-2xl flex-shrink-0"><?= $f['icon'] ?></div>
                        <div>
                            <div class="font-semibold text-gray-800 text-sm mb-1"><?= $f['title'] ?></div>
                            <div class="text-gray-500 text-xs leading-relaxed"><?= $f['desc'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Fakta Budidaya -->
        <div class="bg-dark rounded-3xl p-8 border border-primary-100">
            <h3 class="font-bold text-accent text-lg mb-6 flex items-center gap-2">
                <i class="fas fa-water text-accent"></i> Fakta Budidaya Ikan Patin
            </h3>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $faktaBudidaya = [
                    ['label'=>'Suhu Optimal',   'value'=>'26–30°C',       'icon'=>'fas fa-thermometer-half','color'=>'red'],
                    ['label'=>'pH Air Ideal',   'value'=>'6.5 – 8.5',     'icon'=>'fas fa-flask',          'color'=>'purple'],
                    ['label'=>'Oksigen Terlarut','value'=>'> 5 mg/L',      'icon'=>'fas fa-wind',           'color'=>'blue'],
                    ['label'=>'Kedalaman Kolam','value'=>'1.0 – 1.5 m',   'icon'=>'fas fa-ruler-vertical', 'color'=>'teal'],
                    ['label'=>'Padat Tebar',    'value'=>'20–40 ekor/m²', 'icon'=>'fas fa-fish',           'color'=>'green'],
                    ['label'=>'Masa Panen',     'value'=>'5–6 Bulan',      'icon'=>'fas fa-calendar-check', 'color'=>'yellow'],
                ]; foreach($faktaBudidaya as $f): ?>
                <div class="bg-accent rounded-xl p-4 flex items-center gap-4 transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg ">
                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="<?= $f['icon'] ?> text-primary-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-dark text-xs"><?= $f['label'] ?></div>
                        <div class="text-dark font-bold text-sm"><?= $f['value'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════
     PENYAKIT — data dari database
══════════════════════════════ -->
<section id="penyakit" class="py-20 bg-#F5F4EF">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="text-red-500 font-semibold text-sm uppercase tracking-widest">Waspada</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Penyakit Umum Ikan Patin</h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">Kenali penyakit-penyakit yang sering menyerang ikan patin agar dapat dicegah dan ditangani lebih awal</p>
        </div>

        <?php
        // ── Ambil data penyakit — kolom sesuai tabel: kode, nama, jenis, deskripsi ──
        $stmt   = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit ASC LIMIT 6");
        $daftar = $stmt->fetch_all(MYSQLI_ASSOC);

        $colors = ['blue','teal','green','purple','red','orange'];
        $icons  = [
            'Bakteri' => 'fas fa-bacterium',
            'Virus'   => 'fas fa-virus',
            'Jamur'   => 'fas fa-cloud',
            'Parasit' => 'fas fa-bug',
        ];
        $badgeClass = [
            'Bakteri' => 'bg-orange-50 text-orange-600',
            'Virus'   => 'bg-red-50 text-red-600',
            'Jamur'   => 'bg-green-50 text-green-600',
            'Parasit' => 'bg-purple-50 text-purple-600',
        ];
        ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($daftar as $i => $p):
                $c    = $colors[$i % count($colors)];
                $icon = $icons[$p['jenis']] ?? 'fas fa-circle-dot';
            ?>
            <div class="bg-white rounded-2xl p-6 card-hover border border-[#E8E7E1]">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 bg-<?= $c ?>-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="<?= $icon ?> text-<?= $c ?>-600 text-sm"></i>
                    </div>
                    <div>
                        <!-- Kolom sesuai tabel: kode, nama -->
                        <span class="text-xs font-mono text-accent bg-dark px-2 py-0.5 rounded">
                            <?= htmlspecialchars($p['kode_penyakit']) ?>
                        </span>
                        <h4 class="font-bold text-gray-800 mt-1 text-sm">
                            <?= htmlspecialchars($p['nama_penyakit']) ?>
                        </h4>
                    </div>
                </div>
                <p class="text-gray-500 text-xs leading-relaxed line-clamp-3">
                    <?= htmlspecialchars($p['deskripsi'] ?? '-') ?>
                </p>
                <div class="mt-3">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?= $badgeClass[$p['jenis']] ?? 'bg-gray-100 text-gray-500' ?>">
                        <?= htmlspecialchars($p['jenis']) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>




<!-- ══════════════════════════════
     FOOTER
══════════════════════════════ -->
<footer class="bg-dark py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                    <img src="../assets/logo-patin-SIPATIN.webp">
                    </div>
                    <span class="font-bold text-white text-lg">SIPATIN</span>
                </div>
                <p class="text-muted text-sm leading-relaxed">Sistem pakar diagnosa penyakit ikan patin berbasis forward chaining untuk membantu pembudidaya ikan.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Menu Cepat</h4>
                <ul class="space-y-2">
                    <li><a href="#beranda"  class="text-muted hover:text-white text-sm transition-colors">Beranda</a></li>
                    <li><a href="#tentang"  class="text-muted hover:text-white text-sm transition-colors">Tentang</a></li>
                    <li><a href="#fakta"    class="text-muted hover:text-white text-sm transition-colors">Fakta Ikan Patin</a></li>
                    <li><a href="diagnosa.php" class="text-muted hover:text-white text-sm transition-colors">Diagnosa Penyakit</a></li>

                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Informasi</h4>
                <ul class="space-y-2 text-muted text-sm"> 
                    <!-- Jumlah real dari database -->
                    <li class="flex items-center gap-2"><i class="fas fa-database text-accent"></i> <?= $jml_penyakit ?> Penyakit Teridentifikasi</li>
                    <li class="flex items-center gap-2"><i class="fas fa-list text-accent"></i> <?= $jml_gejala ?> Parameter Gejala</li>
                    <li class="flex items-center gap-2"><i class="fas fa-code-branch text-accent"></i> Metode Forward Chaining</li>
                    <li class="flex items-center gap-2"><i class="fas fa-lock-open text-accent"></i> Akses Gratis &amp; Terbuka</li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<?php $conn->close(); ?>

<script>

/* ── Navbar scroll ── */
const navbar  = document.getElementById('navbar');
const navLinks = document.querySelectorAll('.nav-link');
const navBrand = document.querySelector('.nav-brand');
window.addEventListener('scroll', () => {
    if (window.scrollY > 80) {
        navbar.classList.add('scrolled');
        navLinks.forEach(l => l.style.color = '#0f0f14');
        navBrand.style.color = '#111827';
    } else {
        navbar.classList.remove('scrolled');
        navLinks.forEach(l => l.style.color = 'rgba(255,255,255,0.9)');
        navBrand.style.color = 'white';
    }
});


/* ── Animasi card saat masuk viewport ── */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity    = '1';
            entry.target.style.transform  = 'translateY(0)';
            entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.card-hover, .fact-card').forEach(el => {
    el.style.opacity   = '0';
    el.style.transform = 'translateY(15px)';
    observer.observe(el);
});

</script>
<?php layoutFoot(); ?>
</body>
</html>