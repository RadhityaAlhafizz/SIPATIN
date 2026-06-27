<?php
require_once '../includes/auth.php';
requireAdmin();

require_once '../config/database.php';
require_once '../includes/layout.php';

layoutHead('Dashboard');
layoutBody();

$conn = getConnection();

// Ambil jumlah data untuk sidebar & stat cards
$jumlah_penyakit  = $conn->query("SELECT COUNT(*) AS total FROM penyakit")->fetch_assoc()['total'];
$jumlah_gejala    = $conn->query("SELECT COUNT(*) AS total FROM gejala")->fetch_assoc()['total'];
$jumlah_aturan    = $conn->query("SELECT COUNT(*) AS total FROM basis_aturan")->fetch_assoc()['total'];
$jumlah_solusi    = $conn->query("SELECT COUNT(*) AS total FROM solusi")->fetch_assoc()['total'];
$jumlah_diagnosa = $conn->query("SELECT COUNT(*) AS total FROM log_diagnosa")->fetch_assoc()['total'];

?>
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content (offset sidebar) -->
    <main class="min-h-screen p-4 lg:ml-52 lg:p-6">
        
        <!-- Tambah tombol hamburger sebelum judul halaman -->
        <div class="lg:hidden flex items-center gap-3 mb-6">
            <!-- Tombol hamburger — hanya tampil di mobile -->
            <button onclick="openSidebar()"class="lg:hidden p-2 rounded-xl bg-white border border-[#E8E7E1]">
                <?php icon('menu') ?>
            </button>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-dark">Selamat datang, <?= adminName(); ?> </h1>
                <p class="text-xs text-muted mt-0.5"><?= date('l, d F Y') ?></p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

            <!-- Penyakit -->
            <div class="bg-white rounded-2xl p-4 border border-[#E8E7E1]">
                <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center mb-3">
                    <?php icon ('penyakit')?>
                </div>
                <p class="text-2xl font-bold text-[#1C1C2E]"><?= $jumlah_penyakit ?></p>
                <p class="text-xs text-dark mt-1 font-medium">Data Penyakit</p>
            </div>

            <!-- Gejala -->
            <div class="bg-white rounded-2xl p-4 border border-[#E8E7E1]">
                <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center mb-3">
                    <?php icon ('gejala')?>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-[#1C1C2E]"><?= $jumlah_gejala ?></p>
                <p class="text-xs text-dark mt-1 font-medium">Data Gejala</p>
            </div>

            <!-- Basis Aturan -->
            <div class="bg-white rounded-2xl p-4 border border-[#E8E7E1]">
                <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center mb-3">
                    <?php icon ('basis aturan')?>
                </div>
                <p class="text-2xl font-bold text-[#1C1C2E]"><?= $jumlah_aturan ?></p>
                <p class="text-xs text-dark mt-1 font-medium">Basis Aturan</p>
            </div>

            <!-- Solusi -->
            <div class="bg-white rounded-2xl p-4 border border-[#E8E7E1]">
                <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center mb-3">
                    <?php icon ('solusi')?>
                </div>
                <p class="text-2xl font-bold text-[#1C1C2E]"><?= $jumlah_solusi ?></p>
                <p class="text-xs text-dark mt-1 font-medium">Data Solusi</p>
            </div>

        </div>

        <!-- Riwayat Diagnosa -->
        <div class="bg-white rounded-2xl p-5 border border-[#E8E7E1]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-[#1C1C2E]">
                    Riwayat Diagnosa Terbaru
                </h2>
        </div>

            <?php
            $riwayat = $conn->query("
                SELECT
                    nama_pengguna,
                    penyakit_utama,
                    persentase,
                    created_at
                FROM log_diagnosa
                ORDER BY created_at DESC
                LIMIT 10
            ");
            ?>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#E8E7E1]">
                            <th class="text-left py-3">Nama</th>
                            <th class="text-left py-3">Penyakit Utama</th>
                            <th class="text-left py-3">Persentase</th>
                            <th class="text-left py-3">Tanggal</th>
                        </tr>
                    </thead>

                 <tbody>
                <?php if($riwayat->num_rows > 0): ?>
                    <?php while($row = $riwayat->fetch_assoc()): ?>
                    <tr class="border-b border-[#F3F3F3] hover:bg-[#FAFAFA]">
                        <td class="py-3">
                            <?= htmlspecialchars($row['nama_pengguna']) ?>
                        </td>

                        <td class="py-3 text-[#555]">
                            <?= htmlspecialchars($row['penyakit_utama']) ?>
                        </td>

                        <td class="py-3 text-xs text-[#9B9AB0]">
                            <?= number_format($row['persentase'],1) ?>%
                        </td>

                        <td class="py-3 text-xs text-[#9B9AB0]">
                            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-[#9B9AB0]">
                            Belum ada riwayat diagnosa.
                        </td>
                    </tr>
                <?php endif; ?>
                 </tbody>
                </table>
                </div>
            </div>

    </main>
<?php layoutFoot(); ?>
