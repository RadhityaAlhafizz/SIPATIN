<?php
session_start();
require_once '../includes/auth.php'; requireAdmin();
require_once '../config/database.php';
require_once '../includes/layout.php';
$conn = getConnection();

$jumlah_penyakit = $conn->query("SELECT COUNT(*) t FROM penyakit")->fetch_assoc()['t'];
$jumlah_gejala   = $conn->query("SELECT COUNT(*) t FROM gejala")->fetch_assoc()['t'];
$jumlah_aturan   = $conn->query("SELECT COUNT(*) t FROM basis_aturan")->fetch_assoc()['t'];
$jumlah_solusi   = $conn->query("SELECT COUNT(*) t FROM solusi")->fetch_assoc()['t'];

/* ── Data dropdown ── */
$list_penyakit = $conn->query("SELECT id,kode_penyakit,nama_penyakit FROM penyakit ORDER BY kode_penyakit ASC")->fetch_all(MYSQLI_ASSOC);
$list_gejala   = $conn->query("SELECT id,kode_gejala,nama_gejala FROM gejala ORDER BY kode_gejala")->fetch_all(MYSQLI_ASSOC);
$list_solusi   = $conn->query("SELECT id,kode_solusi FROM solusi ORDER BY kode_solusi")->fetch_all(MYSQLI_ASSOC);

$msg = ''; $msg_type = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act    = $_POST['action'] ?? '';
    $id_p   = (int)$_POST['id_penyakit'];
    $id_g   = (int)$_POST['id_gejala'];
    $id_s   = (int)$_POST['id_solusi'];
    
    
    if ($act === 'tambah') {
        /* Cegah duplikat kombinasi penyakit+gejala */
        $cek = $conn->prepare("SELECT id FROM basis_aturan WHERE id_penyakit=? AND id_gejala=?");
        $cek->bind_param("ii", $id_p, $id_g);
        $cek->execute(); $cek->store_result();
        if ($cek->num_rows > 0) {
            $msg = "Kombinasi penyakit dan gejala ini sudah ada."; $msg_type='error';
        } else {
            $s = $conn->prepare("INSERT INTO basis_aturan (id_penyakit,id_gejala,id_solusi) VALUES (?,?,?)");
            $s->bind_param("iii", $id_p,$id_g,$id_s);
            $s->execute(); $s->close();
            $msg = "Aturan berhasil ditambahkan.";
        }
        $cek->close();
    } elseif ($act === 'edit') {
        $id = (int)$_POST['id'];
        $s2 = $conn->prepare("UPDATE basis_aturan SET id_penyakit=?,id_gejala=?,id_solusi=? WHERE id=?");
        $s2->bind_param("iiii", $id_p,$id_g,$id_s,$id);
        $s2->execute(); $s2->close();
        $msg = "Aturan berhasil diperbarui.";
    } elseif ($act === 'hapus') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM basis_aturan WHERE id=$id");
        $msg = "Aturan berhasil dihapus."; $msg_type='error';
    }
    $jumlah_aturan = $conn->query("SELECT COUNT(*) t FROM basis_aturan")->fetch_assoc()['t'];
}

/* ── Ambil data dengan JOIN ── */
$filter_p = (int)($_GET['penyakit'] ?? 0);
$sql = "SELECT ba.id,
               p.kode_penyakit AS p_kode, p.nama_penyakit AS p_nama, p.jenis AS p_jenis,
               g.kode_gejala AS g_kode, g.nama_gejala AS g_nama,
               s.kode_solusi AS s_kode,
               ba.id_penyakit, ba.id_gejala, ba.id_solusi
        FROM basis_aturan ba
        JOIN penyakit p ON ba.id_penyakit = p.id
        JOIN gejala   g ON ba.id_gejala   = g.id
        JOIN solusi   s ON ba.id_solusi   = s.id";
if ($filter_p) $sql .= " WHERE ba.id_penyakit=$filter_p";
$sql .= " ORDER BY p.kode_penyakit, g.kode_gejala";
$rows = $conn->query($sql);

layoutHead("Basis Aturan");
layoutBody();

?>

<?php include '../includes/sidebar.php'; ?>

<main class="lg:ml-52 min-h-screen p-4 lg:p-6">

        <div class="lg:hidden flex items-center gap-3 mb-6">
            <button onclick="openSidebar()"class="lg:hidden p-2 rounded-xl bg-white border border-[#E8E7E1]">
                <?php icon('menu') ?>
            </button>
        </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1C1C2E]">Basis Aturan</h1>
            <p class="text-xs text-[#9B9AB0] mt-0.5">Kelola relasi penyakit → gejala → solusi (Forward Chaining)</p>
        </div>
        <button onclick="openModal('modalTambah')" class="btn-accent flex items-center gap-2">
            <?php icon('add') ?>
            Tambah
        </button>
    </div>

    <?php if ($msg): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?= json_encode($msg) ?>,<?= json_encode($msg_type) ?>));</script>
    <?php endif; ?>


    <div class="bg-white rounded-2xl border border-[#E8E7E1] overflow-hidden">
        <!-- Toolbar -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#F0EFE9]">
            <form method="GET" class="flex items-center gap-2">
                <label class="text-xs font-semibold text-[#9B9AB0]">Filter Penyakit:</label>
                <select name="penyakit" onchange="this.form.submit()" class="input-base py-2 text-xs w-full">
                    <option value="">Semua Penyakit</option>
                    <?php foreach ($list_penyakit as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filter_p == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['kode_penyakit'].' - '.$p['nama_penyakit']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($filter_p): ?><a href="basis_aturan.php" class="btn-ghost py-2 px-3 text-xs">Reset</a><?php endif; ?>
            </form>
            <p class="text-xs text-[#9B9AB0]">Menampilkan <?= $rows->num_rows ?> aturan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wide text-[#9B9AB0] bg-[#FAFAF8]">
                        <th class="text-left px-5 py-3 font-semibold">IF — Gejala</th>
                        <th class="text-center px-3 py-3 font-semibold w-8">→</th>
                        <th class="text-left px-5 py-3 font-semibold">THEN — Penyakit</th>
                        <th class="text-left px-5 py-3 font-semibold">Solusi</th>
                        <th class="text-center px-5 py-3 font-semibold w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F7F6F2]">
                <?php if ($rows->num_rows === 0): ?>
                    <tr><td colspan="6" class="text-center py-12 text-[#9B9AB0] text-sm">Tidak ada aturan ditemukan</td></tr>
                <?php else: while ($r = $rows->fetch_assoc()): 
                    $jl = strtolower($r['p_jenis']); ?>
                    <tr class="hover:bg-[#FAFAF8] transition-colors fade-in">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-[#E07A10] bg-orange-50 px-1.5 py-0.5 rounded"><?= $r['g_kode'] ?></span>
                                <span class="text-xs font-medium text-[#1C1C2E]"><?= htmlspecialchars($r['g_nama']) ?></span>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center text-[#9B9AB0] font-bold">→</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-[#7B7AFF] bg-purple-50 px-1.5 py-0.5 rounded"><?= $r['p_kode'] ?></span>
                                <div>
                                    <p class="text-xs font-semibold text-[#1C1C2E]"><?= htmlspecialchars($r['p_nama']) ?></p>
                                    <span class="pill pill-<?= $jl ?> text-[10px]"><?= $r['p_jenis'] ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold text-[#5E35B1] bg-purple-50 px-1.5 py-0.5 rounded"><?= $r['s_kode'] ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick='openEdit(<?= json_encode($r) ?>)' class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/></svg>
                                </button>
                                <button onclick="confirmHapus(<?= $r['id'] ?>, '<?= addslashes($r['g_nama']) ?>', '<?= addslashes($r['p_nama']) ?>')" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal-backdrop">
    <div class="modal-card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-[#1C1C2E]">Tambah Aturan</h3>
                <p class="text-xs text-[#9B9AB0] mt-0.5">IF [Gejala] THEN [Penyakit] → [Solusi]</p>
            </div>
            <button onclick="closeModal('modalTambah')"><?php icon('close') ?></button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="action" value="tambah">

            <div class="bg-[#F5F4EF] rounded-xl p-3 text-xs text-[#9B9AB0]">
                <span class="font-semibold text-[#1C1C2E]">Format:</span>
                IF <span class="text-[#E07A10] font-semibold">Gejala</span> THEN diagnosa
                <span class="text-[#7B7AFF] font-semibold">Penyakit</span> dengan solusi
                <span class="text-[#5E35B1] font-semibold">Solusi</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">IF — Gejala</label>
                <select name="id_gejala" required class="input-base">
                    <option value="">Pilih gejala...</option>
                    <?php foreach ($list_gejala as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['kode_gejala'].' — '.$g['nama_gejala']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">THEN — Penyakit</label>
                <select name="id_penyakit" required class="input-base">
                    <option value="">Pilih penyakit...</option>
                    <?php foreach ($list_penyakit as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['kode_penyakit'].' — '.$p['nama_penyakit']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">Solusi yang Direkomendasikan</label>
                <select name="id_solusi" required class="input-base">
                    <option value="">Pilih solusi...</option>
                    <?php foreach ($list_solusi as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['kode_solusi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-accent flex-1">Simpan Aturan</button>
                <button type="button" onclick="closeModal('modalTambah')" class="btn-ghost flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal-backdrop">
    <div class="modal-card">
        <div class="flex items-center justify-between mb-5">
            <div><h3 class="font-bold text-[#1C1C2E]">Edit Aturan</h3><p class="text-xs text-[#9B9AB0] mt-0.5">Perbarui relasi aturan</p></div>
            <button onclick="closeModal('modalTambah')"><?php icon('close') ?></button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">

            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">IF — Gejala</label>
                <select name="id_gejala" id="editGejala" required class="input-base">
                    <?php foreach ($list_gejala as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['kode_gejala'].' — '.$g['nama_gejala']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">THEN — Penyakit</label>
                <select name="id_penyakit" id="editPenyakit" required class="input-base">
                    <?php foreach ($list_penyakit as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['kode_penyakit'].' — '.$p['nama_penyakit']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#9B9AB0] mb-1">Solusi</label>
                <select name="id_solusi" id="editSolusi" required class="input-base">
                    <?php foreach ($list_solusi as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['kode_solusi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-accent flex-1">Simpan Perubahan</button>
                <button type="button" onclick="closeModal('modalEdit')" class="btn-ghost flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div id="modalHapus" class="modal-backdrop">
    <div class="modal-card max-w-sm text-center">
        <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
        </div>
        <h3 class="font-bold text-[#1C1C2E] mb-1">Hapus Aturan?</h3>
        <p class="text-xs text-[#9B9AB0] mb-5">Aturan <strong id="hapusDesc" class="text-[#1C1C2E]"></strong> akan dihapus permanen.</p>
        <form method="POST" class="flex gap-2">
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id" id="hapusId">
            <button type="submit" class="flex-1 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition-colors">Ya, Hapus</button>
            <button type="button" onclick="closeModal('modalHapus')" class="btn-ghost flex-1">Batal</button>
        </form>
    </div>
</div>

<script>
function openEdit(d) {
    document.getElementById('editId').value          = d.id;
    document.getElementById('editGejala').value      = d.id_gejala;
    document.getElementById('editPenyakit').value    = d.id_penyakit;
    document.getElementById('editSolusi').value      = d.id_solusi;
    openModal('modalEdit');
}
function confirmHapus(id, gejala, penyakit) {
    document.getElementById('hapusId').value = id;
    document.getElementById('hapusDesc').textContent = gejala + ' → ' + penyakit;
    openModal('modalHapus');
}
</script>
<?php layoutFoot(); $conn->close(); ?>
