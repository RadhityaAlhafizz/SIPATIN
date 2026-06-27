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

$admin_id  = (int)$_SESSION['admin_id'];
$admin     = $conn->query("SELECT * FROM admin WHERE id=$admin_id LIMIT 1")->fetch_assoc();
$msg       = ''; $msg_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';

    /* ── Update Profil ── */
    if ($act === 'profil') {
        $nama  = trim($_POST['nama']  ?? '');
        $email = trim($_POST['email'] ?? '');
        if (empty($nama) || empty($email)) {
            $msg = 'Nama dan email tidak boleh kosong.'; $msg_type = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Format email tidak valid.'; $msg_type = 'error';
        } else {
            $cek = $conn->prepare("SELECT id FROM admin WHERE email=? AND id!=?");
            $cek->bind_param("si", $email, $admin_id);
            $cek->execute(); $cek->store_result();
            if ($cek->num_rows > 0) {
                $msg = 'Email sudah digunakan admin lain.'; $msg_type = 'error';
            } else {
                $s = $conn->prepare("UPDATE admin SET nama=?, email=? WHERE id=?");
                $s->bind_param("ssi", $nama, $email, $admin_id);
                $s->execute(); $s->close();
                $_SESSION['admin_nama'] = $nama;
                $admin['nama']  = $nama;
                $admin['email'] = $email;
                $msg = 'Profil berhasil diperbarui.';
            }
            $cek->close();
        }

    /* ── Ganti Password ── */
    } elseif ($act === 'password') {
        $lama    = $_POST['password_lama']    ?? '';
        $baru    = $_POST['password_baru']    ?? '';
        $konfirm = $_POST['password_konfirm'] ?? '';
        if (empty($lama) || empty($baru) || empty($konfirm)) {
            $msg = 'Semua field wajib diisi.'; $msg_type = 'error';
        } elseif (!password_verify($lama, $admin['password'])) {
            $msg = 'Password lama tidak sesuai.'; $msg_type = 'error';
        } elseif (strlen($baru) < 8) {
            $msg = 'Password baru minimal 8 karakter.'; $msg_type = 'error';
        } elseif ($baru !== $konfirm) {
            $msg = 'Konfirmasi password tidak cocok.'; $msg_type = 'error';
        } else {
            $hash = password_hash($baru, PASSWORD_BCRYPT);
            $s    = $conn->prepare("UPDATE admin SET password=? WHERE id=?");
            $s->bind_param("si", $hash, $admin_id);
            $s->execute(); $s->close();
            $admin['password'] = $hash;
            $msg = 'Password berhasil diubah.';
        }
    }
}

layoutHead("Pengaturan");
layoutBody();

?>

<?php include '../includes/sidebar.php'; ?>

<main class="lg:ml-52 min-h-screen p-4 lg:p-6">

        <div class="lg:hidden flex items-center gap-3 mb-6">
            <!-- Tombol hamburger — hanya tampil di mobile -->
            <button onclick="openSidebar()"class="lg:hidden p-2 rounded-xl bg-white border border-[#E8E7E1]">
                <?php icon('menu') ?>
            </button>
        </div>

    <div class="mb-6">
        <h1 class="text-xl font-bold text-dark">Pengaturan</h1>
        <p class="text-xs text-muted mt-0.5">Kelola profil dan keamanan akun Anda</p>
    </div>

    <?php if ($msg): ?>
    <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?= json_encode($msg) ?>,<?= json_encode($msg_type) ?>));</script>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- ── Sidebar kiri ── -->
        <div class="w-full lg:w-56 flex-shrink-0 space-y-4">

            <!-- Avatar -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-5 text-center w-full">
                <div class="w-16 h-16 rounded-full bg-[#C8F135] flex items-center justify-center mx-auto mb-3 text-2xl font-bold text-dark" id="avatarCircle">
                    <?= strtoupper(substr($admin['nama'], 0, 1)) ?>
                </div>
                <p class="text-sm font-bold text-dark truncate" id="namaPreview"><?= htmlspecialchars($admin['nama']) ?></p>
                <p class="text-xs text-muted mt-0.5 truncate"><?= htmlspecialchars($admin['email']) ?></p>
                <span class="inline-block mt-2 text-[10px] font-semibold bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full">● Aktif</span>
            </div>

            <!-- Info akun -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-4 gap-2">
                <p class="text-[10px] font-semibold text-muted uppercase tracking-wide mb-3">Info Akun</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">ID Admin</span>
                        <span class="text-xs font-semibold text-dark">#<?= $admin_id ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">Terdaftar</span>
                        <span class="text-xs font-semibold text-dark"><?= date('d/m/Y', strtotime($admin['created_at'])) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-muted">Role</span>
                        <span class="text-xs font-semibold text-[#7B7AFF]">Administrator</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Konten kanan: satu halaman, semua section ── -->
        <div class="flex-1 min-w-0 grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">

            <!-- ═══════════════════
                 PROFIL SAYA
            ═══════════════════ -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-6">
                <div class="pb-5">
                    <h2 class="text-base font-bold text-dark">Profil Saya</h2>
                    <p class="text-xs text-muted mt-0.5">Perbarui nama dan alamat email akun Anda</p>
                </div>

                <form method="POST" class="max-w-md">
                    <input type="hidden" name="action" value="profil">

                    <div class="space-y-4">

                        <!-- Nama -->
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1.5">Nama Lengkap</label>
                            <input
                                type="text" name="nama"
                                value="<?= htmlspecialchars($admin['nama']) ?>"
                                oninput="onNamaInput(this.value)"
                                placeholder="Nama lengkap Anda"
                                required class="input-base">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1.5">Alamat Email</label>
                            <input
                                type="email" name="email"
                                value="<?= htmlspecialchars($admin['email']) ?>"
                                placeholder="email@domain.com"
                                required class="input-base">
                            <p class="text-[10px] text-muted mt-1.5">
                                Email ini digunakan untuk masuk ke panel admin.
                            </p>
                        </div>

                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit" class="btn-accent flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- ═══════════════════
                 GANTI PASSWORD
            ═══════════════════ -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-6">
                <div class="pb-5">
                    <h2 class="text-base font-bold text-dark">Ganti Password</h2>
                    <p class="text-xs text-muted mt-0.5">Pastikan password baru Anda kuat dan tidak mudah ditebak</p>
                </div>

                <form method="POST" class="max-w-md" onsubmit="return validateForm()">
                    <input type="hidden" name="action" value="password">

                    <div class="space-y-4">

                        <!-- Password lama -->
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1.5">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" name="password_lama" id="pwdLama"
                                    placeholder="Masukkan password saat ini"
                                    required class="input-base pr-10">
                                <button type="button" onclick="toggle('pwdLama','eyeLama')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-dark transition-colors">
                                    <svg id="eyeLama" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Password baru -->
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1.5">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password_baru" id="pwdBaru"
                                    placeholder="Min. 8 karakter"
                                    oninput="checkStrength(this.value); checkMatch()"
                                    required class="input-base pr-10">
                                <button type="button" onclick="toggle('pwdBaru','eyeBaru')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-dark transition-colors">
                                    <svg id="eyeBaru" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Strength indicator -->
                            <div class="flex gap-1 mt-2">
                                <div id="sb1" class="h-1 flex-1 rounded-full bg-[#E8E7E1] transition-all duration-300"></div>
                                <div id="sb2" class="h-1 flex-1 rounded-full bg-[#E8E7E1] transition-all duration-300"></div>
                                <div id="sb3" class="h-1 flex-1 rounded-full bg-[#E8E7E1] transition-all duration-300"></div>
                                <div id="sb4" class="h-1 flex-1 rounded-full bg-[#E8E7E1] transition-all duration-300"></div>
                            </div>
                            <p id="strLabel" class="text-[10px] mt-1 h-3"></p>
                        </div>

                        <!-- Konfirmasi -->
                        <div>
                            <label class="block text-xs font-semibold text-muted mb-1.5">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password_konfirm" id="pwdKonfirm"
                                    placeholder="Ulangi password baru"
                                    oninput="checkMatch()"
                                    required class="input-base pr-10">
                                <button type="button" onclick="toggle('pwdKonfirm','eyeKonfirm')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-dark transition-colors">
                                    <svg id="eyeKonfirm" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                            <p id="matchLabel" class="text-[10px] mt-1 h-3"></p>
                        </div>

                    </div>

                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit" class="btn-accent flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                            </svg>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

<script>
/* Live preview nama & avatar */
function onNamaInput(val) {
    document.getElementById('namaPreview').textContent   = val || '—';
    document.getElementById('avatarCircle').textContent  = val ? val.charAt(0).toUpperCase() : '?';
}

/* Toggle show/hide password */
const eyeShow = `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>`;
const eyeHide = `<path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a10.002 10.002 0 01-9.542-5.356 1.651 1.651 0 010-1.185 9.98 9.98 0 015.396-5.222l1.822 1.822A2.5 2.5 0 0010.748 13.93z"/>`;

function toggle(inputId, iconId) {
    const el = document.getElementById(inputId);
    const hidden = el.type === 'password';
    el.type = hidden ? 'text' : 'password';
    document.getElementById(iconId).innerHTML = hidden ? eyeHide : eyeShow;
}

/* Strength bar */
function checkStrength(val) {
    let s = 0;
    if (val.length >= 8)           s++;
    if (/[A-Z]/.test(val))         s++;
    if (/[0-9]/.test(val))         s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Lemah','Cukup','Kuat','Sangat Kuat'];
    for (let i = 1; i <= 4; i++) {
        document.getElementById('sb'+i).style.background = i <= s ? colors[s-1] : '#E8E7E1';
    }
    const lbl = document.getElementById('strLabel');
    lbl.textContent = val.length ? (labels[s-1] || '') : '';
    lbl.style.color = s ? colors[s-1] : '#9B9AB0';
}

/* Match check */
function checkMatch() {
    const baru = document.getElementById('pwdBaru').value;
    const conf = document.getElementById('pwdKonfirm').value;
    const lbl  = document.getElementById('matchLabel');
    if (!conf) { lbl.textContent = ''; return; }
    lbl.textContent = baru === conf ? '✓ Password cocok' : '✗ Password tidak cocok';
    lbl.style.color = baru === conf ? '#22c55e' : '#ef4444';
}

/* Validasi submit */
function validateForm() {
    const baru = document.getElementById('pwdBaru').value;
    const conf = document.getElementById('pwdKonfirm').value;
    if (baru.length < 8)   { showToast('Password minimal 8 karakter.','error'); return false; }
    if (baru !== conf)     { showToast('Konfirmasi password tidak cocok.','error'); return false; }
    return true;
}
</script>

<?php layoutFoot(); $conn->close(); ?>