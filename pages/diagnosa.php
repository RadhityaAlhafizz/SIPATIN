
    <?php require_once '../includes/layout.php'; 
    
    layoutHead('Diagnosa Penyakit');
    layoutBody('bg-[#F5F4EF] min-h-screen');
    ?>
    <style>
        /* Gejala item checked — warna sesuai admin (#C8F135 accent) */
        .gejala-item input[type=checkbox]:checked + label {
            background: #FAFDE8;
            border-color: #C8F135;
            color: #1C1C2E;
        }
        .gejala-item input[type=checkbox]:checked + label .check-icon {
            opacity: 1;
            background: #C8F135;
        }
        .gejala-item input[type=checkbox]:checked + label .kode-tag {
            color: #1C1C2E;
            font-weight: 700;
        }
        .check-icon { transition: all 0.15s ease; opacity: 0; }
        .label-hover:hover { background: #F5F4EF; border-color: #C8F135; }
    </style>

<?php
require_once __DIR__ . '/../config/database.php';
$conn = getConnection();
$gejalaList = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala")->fetch_all(MYSQLI_ASSOC);
?>

<!-- ── NAVBAR ── -->
<nav class="bg-dark border-b border-white/10 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">

        <!-- Kiri: Beranda -->
        <a href="index.php" class="flex items-center gap-2 text-sm text-muted hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-xs"></i> Beranda
        </a>

        <!-- Kanan: Bantuan -->
        <button onclick="document.getElementById('modalBantuan').classList.remove('hidden')"
            class="flex items-center gap-1.5 text-sm text-muted hover:text-white transition-colors">
            <i class="fas fa-question-circle"></i>
            <span class="hidden sm:inline">Bantuan</span>
        </button>

    </div>
</nav>

<!-- Modal Bantuan -->
<div id="modalBantuan" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="bg-dark border border-white/10 rounded-xl max-w-sm w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Cara Menggunakan</h3>
            <button onclick="document.getElementById('modalBantuan').classList.add('hidden')"
                class="text-muted hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ol class="space-y-3 text-sm text-muted list-decimal list-inside leading-relaxed">
            <li>Amati kondisi ikan patin Anda secara seksama</li>
            <li>Centang semua gejala yang sesuai dengan kondisi ikan</li>
            <li>Klik tombol <span class="text-accent font-medium">Diagnosa</span> untuk melihat hasil</li>
        </ol>
        <button onclick="document.getElementById('modalBantuan').classList.add('hidden')"
            class="mt-5 w-full py-2 bg-accent text-dark text-sm font-semibold rounded-lg">
            Mengerti
        </button>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-extrabold text-dark mb-2">Pilih Gejala yang Diamati</h1>
        <p class="text-muted text-sm max-w-lg mx-auto">
            Centang semua gejala yang Anda temukan pada ikan patin Anda.
            Semakin banyak gejala yang dipilih, semakin akurat hasil diagnosa.
        </p>
    </div>

    <form method="POST" action="hasil_diagnosa.php" id="formDiagnosa">
        <div class="grid lg:grid-cols-3 gap-6">

            <!-- KIRI: Form gejala -->
            <div class="lg:col-span-2">

                <!-- Nama pengguna -->
                <div class="bg-white rounded-2xl p-6 border border-[#E8E7E1] mb-5">
                    <label class="block text-sm font-semibold text-dark mb-2">
                        <i class="fas fa-user mr-2 text-muted"></i>Nama Anda
                    </label>
                    <input type="text" name="nama_pengguna" required
                        placeholder="Contoh: Pak Budi / Petani Patin"
                        class="w-full border border-[#E8E7E1] rounded-xl px-4 py-2.5 text-sm
                               focus:outline-none focus:ring-2 focus:ring-[#C8F135] focus:border-[#C8F135]
                               transition-colors font-sans text-dark">
                </div>

                <!-- Daftar Gejala -->
                <div class="bg-white rounded-2xl p-6 border border-[#E8E7E1]">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="font-bold text-dark">Daftar Gejala</h2>
                        <div class="flex gap-2">
                            <button type="button" onclick="pilihSemua(true)"
                                class="text-xs bg-[#C8F135] text-dark hover:bg-[#b8e020] px-3 py-1.5 rounded-lg font-semibold transition-colors">
                                Pilih Semua
                            </button>
                            <button type="button" onclick="pilihSemua(false)"
                                class="text-xs bg-[#F5F4EF] text-muted hover:bg-[#E8E7E1] px-3 py-1.5 rounded-lg font-medium transition-colors">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <?php foreach ($gejalaList as $g): ?>
                        <div class="gejala-item">

                            <input type="checkbox"
                                name="gejala[]"
                                value="<?= $g['id'] ?>"
                                id="g_<?= $g['id'] ?>"
                                class="hidden gejala-check"
                                data-nama="<?= htmlspecialchars($g['nama_gejala'], ENT_QUOTES) ?>"
                                data-kode="<?= htmlspecialchars($g['kode_gejala']) ?>">
                            <label for="g_<?= $g['id'] ?>"
                                class="label-hover flex items-center gap-3 p-3 rounded-xl border border-[#E8E7E1] cursor-pointer transition-all duration-150">
                                <div class="check-icon w-5 h-5 rounded-md flex items-center justify-center flex-shrink-0 bg-[#F5F4EF]">
                                    <i class="fas fa-check text-[#1C1C2E] text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="kode-tag text-xs font-mono text-muted mr-2"><?= $g['kode_gejala'] ?></span>
                                    <span class="text-sm font-medium text-dark"><?= htmlspecialchars($g['nama_gejala'], ENT_QUOTES) ?></span>
                                    <?php if (!empty($g['keterangan'])): ?>
                                    <p class="text-xs text-muted mt-0.5"><?= htmlspecialchars($g['keterangan']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- KANAN: Ringkasan & Submit -->
            <div class="lg:col-span-1">
                <div class="sticky top-20 space-y-4">

                    <!-- Ringkasan -->
                    <div class="bg-white rounded-2xl p-6 border border-[#E8E7E1]">
                        <h3 class="font-bold text-dark mb-4">Ringkasan Pilihan</h3>
                        <div id="ringkasan" class="space-y-2 min-h-16">
                            <p class="text-muted text-sm text-center py-4" id="emptyMsg">
                                <i class="fas fa-hand-pointer block text-2xl mb-2 text-[#E8E7E1]"></i>
                                Belum ada gejala dipilih
                            </p>
                        </div>
                        <div class="border-t border-[#F0EFE9] mt-4 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-muted">Total Gejala Dipilih</span>
                                <span id="counter"
                                    class="font-bold text-dark text-lg bg-[#C8F135] w-8 h-8 rounded-full flex items-center justify-center text-sm">
                                    0
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol submit -->
                    <button type="submit" id="btnDiagnosa"
                        class="w-full bg-dark hover:bg-[#2a2a42] disabled:bg-[#E8E7E1] disabled:text-[#9B9AB0] disabled:cursor-not-allowed
                               text-white py-3.5 rounded-xl font-bold transition-colors duration-200
                               flex items-center justify-center gap-2 text-sm"
                        disabled>
                        <i class="fas fa-search-plus"></i>
                        Mulai Diagnosa
                    </button>

                </div>
            </div>
        </div>
    </form>
</div>

<?php $conn->close(); ?>

<script>
const checks    = document.querySelectorAll('.gejala-check');
const counter   = document.getElementById('counter');
const ringkasan = document.getElementById('ringkasan');
const emptyMsg  = document.getElementById('emptyMsg');
const btn       = document.getElementById('btnDiagnosa');

function updateUI() {
    const selected = Array.from(checks).filter(c => c.checked);
    counter.textContent = selected.length;
    btn.disabled = selected.length === 0;
    emptyMsg.style.display = selected.length === 0 ? 'block' : 'none';

    // Hapus item lama
    ringkasan.querySelectorAll('.ring-item').forEach(i => i.remove());

    selected.forEach(c => {
        const div = document.createElement('div');
        div.className = 'ring-item flex items-center gap-2 text-xs bg-[#F5F4EF] text-dark px-3 py-1.5 rounded-lg border border-[#E8E7E1]';
        div.innerHTML = `
            <span class="w-4 h-4 bg-[#C8F135] rounded flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-dark text-[8px]"></i>
            </span>
            <span class="font-mono text-muted text-[10px]">${c.dataset.kode}</span>
            <span class="font-medium truncate">${c.dataset.nama}</span>`;
        ringkasan.appendChild(div);
    });
}

checks.forEach(c => c.addEventListener('change', updateUI));

function pilihSemua(state) {
    checks.forEach(c => { c.checked = state; });
    updateUI();
}

updateUI();
</script>
<?php layoutFoot();?>