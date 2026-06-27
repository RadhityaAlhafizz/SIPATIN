<?php
// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF'], '.php');
function navActive($page, $current) {
    return $page === $current ? 'bg-[#C8F135] text-dark' : 'text-muted hover:bg-white/10 hover:text-white';
}
function badgeActive($page, $current) {
    return $page === $current ? 'bg-dark text-[#C8F135]' : 'bg-[#C8F135] text-dark';
}
?>

<aside id="sidebar" class="w-52 bg-dark flex flex-col rounded-xl px-3 py-5 gap-1 fixed top-1 left-1 bottom-1 z-30 -translate-x-[120%] lg:translate-x-0 transition-transform duration-300">

    <!-- Logo -->
    <div class="flex items-center gap-2 mb-5 px-2">
        <span class="text-white font-bold text-sm">Sistem Pakar Diagnosa Penyakit Ikan Patin</span>
    </div>

    <!-- Dashboard -->
    <a href="../pages/dashboard.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('dashboard', $current) ?>">
        <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <rect x="2" y="2" width="7" height="7" rx="2"/>
            <rect x="11" y="2" width="7" height="7" rx="2"/>
            <rect x="2" y="11" width="7" height="7" rx="2"/>
            <rect x="11" y="11" width="7" height="7" rx="2"/>
        </svg>
        Dashboard
    </a>

    <!-- Data Master Label -->
    <p class="text-[10px] text-[#5C5B74] uppercase tracking-widest px-3 pt-3 pb-1">Data Master</p>

    <!-- Data Penyakit -->
    <a href="penyakit.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('penyakit', $current) ?>">
        <?php icon ('penyakit')?>
        Data Penyakit
    </a>

    <!-- Data Gejala -->
    <a href="gejala.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('gejala', $current) ?>">
        <?php icon ('gejala')?>
        Data Gejala
    </a>

    <!-- Basis Aturan -->
    <a href="basis_aturan.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('basis_aturan', $current) ?>">
        <?php icon ('basis aturan')?>
        Basis Aturan
        
    </a>

    <!-- Data Solusi -->
    <a href="solusi.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('solusi', $current) ?>">
        <?php icon ('solusi')?>
        Data Solusi
    </a>

    <!-- Lainnya Label -->
    <p class="text-[10px] text-[#5C5B74] uppercase tracking-widest px-3 pt-3 pb-1">Lainnya</p>

    <a href="pengaturan.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('pengaturan', $current) ?>">
        <?php icon ('pengguna')?>
        Pengaturan
    </a>
    
    <a href="laporan.php" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= navActive('laporan', $current) ?>">
        <?php icon ('laporan')?>
        Laporan
    </a>

    <button onclick="openModal('modalLogout')" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-red-500 font-medium transition-colors  <?= navActive('logout', $current) ?>">
        <?php icon ('logout')?>
        Logout
    </button>

</aside>
    <!--Confirm Logout-->
    <div id="modalLogout" class="modal-backdrop">
    <div class="modal-card max-w-sm text-center">
        <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
        </div>
        <h3 class="font-bold text-dark mb-5">Apakah anda yakin ingin Logout?</h3>
   
        <div class="flex gap-2">
            <a href="../pages/logout.php" class="flex-1 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition-colors">Ya, Logout</a>
            <button type="button" onclick="closeModal('modalLogout')" class="btn-ghost flex-1">Batal</button>
        </div>
    </div>
    </div>
<div id="sidebarOverlay" 
class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"
onclick="closeSidebar()">
</div>