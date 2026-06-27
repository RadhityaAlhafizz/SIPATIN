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

$msg = ''; $msg_type = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act  = $_POST['action'] ?? '';
    $kode = trim($_POST['kode_solusi'] ?? '');
    $desk = trim($_POST['deskripsi'] ?? '');

    if ($act === 'tambah') {
        $s = $conn->prepare("INSERT INTO solusi (kode_solusi,deskripsi) VALUES (?,?)");
        $s->bind_param("ss", $kode,$desk);
        if ($s->execute()) { $msg = "Solusi berhasil ditambahkan."; }
        else { $msg = "Kode sudah digunakan."; $msg_type='error'; }
        $s->close();
    } elseif ($act === 'edit') {
        $id = (int)$_POST['id'];
        $s  = $conn->prepare("UPDATE solusi SET kode_solusi=?,deskripsi=? WHERE id=?");
        $s->bind_param("ssi", $kode,$desk,$id);
        $s->execute(); $s->close();
        $msg = "Data solusi berhasil diperbarui.";
    } elseif ($act === 'hapus') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM solusi WHERE id=$id");
        $msg = "Data solusi berhasil dihapus."; $msg_type='error';
    }
    $jumlah_solusi = $conn->query("SELECT COUNT(*) t FROM solusi")->fetch_assoc()['t'];
}

$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM solusi";
if ($search) {
    $esc = $conn->real_escape_string($search);
    $sql .= " WHERE kode_solusi LIKE '%$esc%'";
}
$sql .= " ORDER BY kode_solusi ASC";
$rows = $conn->query($sql);

layoutHead("Data Solusi");
layoutBody();

?>

<?php include '../includes/sidebar.php'; ?>

<main class="lg:ml-52 min-h-screen p-6">

    <div class="lg:hidden flex items-center gap-3 mb-6">
            <!-- Tombol hamburger — hanya tampil di mobile -->
            <button onclick="openSidebar()"class="lg:hidden p-2 rounded-xl bg-white border border-[#E8E7E1]">
                <?php icon('menu') ?>
            </button>
        </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-dark">Data Solusi</h1>
            <p class="text-xs text-muted mt-0.5">Kelola data solusi penanganan penyakit ikan patin</p>
        </div>
        <button onclick="openModal('modalTambah'); getKodeOtomatis('solusi', 'inputKodeSolusi')" class="btn-accent flex items-center gap-2">
            <?php icon('add') ?>           
            Tambah
        </button>
    </div>

    <?php if ($msg): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?= json_encode($msg) ?>,<?= json_encode($msg_type) ?>));</script>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-[#E8E7E1] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#F0EFE9]">
            <p class="text-sm font-semibold text-[#1C1C2E]">Total: <span class="text-[#5E35B1]"><?= $jumlah_solusi ?></span> solusi</p>
            <form method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-muted" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/></svg>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari solusi..." class="input-base pl-9 py-2 w-full text-xs">
                </div>
                <button type="submit" class="btn-accent py-2 px-4 text-xs">Cari</button>
                <?php if ($search): ?><a href="solusi.php" class="btn-ghost py-2 px-3 text-xs">Reset</a><?php endif; ?>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[10px] uppercase tracking-wide text-muted bg-[#FAFAF8]">
                        <th class="text-left px-5 py-3 font-semibold w-24">Kode</th>
                        <th class="text-left px-5 py-3 font-semibold">Deskripsi / Cara Penggunaan</th>
                        <th class="text-left px-5 py-3 font-semibold w-24">Dibuat</th>
                        <th class="text-center px-5 py-3 font-semibold w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F7F6F2]">
                <?php if ($rows->num_rows === 0): ?>
                    <tr><td colspan="5" class="text-center py-12 text-muted text-sm">Tidak ada data ditemukan</td></tr>
                <?php else: while ($r = $rows->fetch_assoc()): ?>
                    <tr class="hover:bg-[#FAFAF8] transition-colors fade-in">
                        <td class="px-5 py-3 font-bold text-[#5E35B1] text-xs"><?= htmlspecialchars($r['kode_solusi']) ?></td>
                        <td class="px-5 py-3 text-xs text-muted max-w-xs">
                            <span class="line-clamp-2"><?= htmlspecialchars($r['deskripsi'] ?? '-') ?></span>
                        </td>
                        <td class="px-5 py-3 text-xs text-muted"><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick='openEdit(<?= json_encode($r) ?>)' class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/></svg>
                                </button>
                                <button onclick="confirmHapus(<?= $r['id'] ?>, '<?= addslashes($r['kode_solusi']) ?>')" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
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
            <div><h3 class="font-bold text-[#1C1C2E]">Tambah Solusi</h3><p class="text-xs text-[#9B9AB0] mt-0.5">Isi data solusi baru</p></div>
            <button onclick="closeModal('modalTambah')"><?php icon('close') ?></button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="action" value="tambah">
            <div>
                <label class="block text-xs font-semibold text-muted mb-1">Kode Solusi</label>
                <input type="text" name="kode_solusi" id="inputKodeSolusi" required class="input-base">
            </div>
            <div>
                <label class="block text-xs font-semibold text-muted mb-1">Deskripsi / Cara Penggunaan</label>
                <textarea name="deskripsi" rows="4" placeholder="Jelaskan dosis dan cara penggunaan solusi ini..." class="input-base resize-none"></textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-accent flex-1">Simpan</button>
                <button type="button" onclick="closeModal('modalTambah')" class="btn-ghost flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal-backdrop">
    <div class="modal-card">
        <div class="flex items-center justify-between mb-5">
            <div><h3 class="font-bold text-[#1C1C2E]">Edit Solusi</h3><p class="text-xs text-muted mt-0.5">Perbarui data solusi</p></div>
            <button onclick="closeModal('modalEdit')"><?php icon('close') ?></button>
        </div>
        <form method="POST" class="space-y-3">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div>
                <label class="block text-xs font-semibold text-muted mb-1">Kode Solusi</label>
                <input type="text" name="kode_solusi" id="editKode" required class="input-base">
            </div>
            <div>
                <label class="block text-xs font-semibold text-muted mb-1">Deskripsi / Cara Penggunaan</label>
                <textarea name="deskripsi" id="editDesk" rows="4" class="input-base resize-none"></textarea>
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
        <h3 class="font-bold text-[#1C1C2E] mb-1">Hapus Solusi?</h3>
        <p class="text-xs text-muted mb-5">Solusi <strong id="hapusNama" class="text-dark"></strong> akan dihapus permanen.</p>
        <form method="POST" class="flex gap-2">
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id" id="hapusId">
            <button type="submit" class="flex-1 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition-colors">Ya, Hapus</button>
            <button type="button" onclick="closeModal('modalHapus')" class="btn-ghost flex-1">Batal</button>
        </form>
    </div>
</div>

<script>

// Fungsi untuk membuka modal edit dan mengisi data
function openEdit(d) {
    document.getElementById('editId').value   = d.id;
    document.getElementById('editKode').value = d.kode_solusi;
    document.getElementById('editDesk').value = d.deskripsi || '';
    openModal('modalEdit');
}

// Fungsi untuk menampilkan konfirmasi hapus
function confirmHapus(id, nama) {
    document.getElementById('hapusId').value = id;
    openModal('modalHapus');
}

// Fungsi untuk mendapatkan kode otomatis saat menambah data baru
function getKodeOtomatis(jenis, inputId) {
    fetch(`../includes/get_kode.php?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById(inputId).value = data.kode || '';
        });
}
</script>
<?php layoutFoot(); $conn->close(); ?>
