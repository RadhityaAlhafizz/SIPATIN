<?php
session_start();
require_once '../config/database.php';
require_once '../includes/layout.php';


if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama             = trim($_POST['nama'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $conn = getConnection();

        // Cek email duplikat
        $cek  = $conn->prepare("SELECT id FROM admin WHERE email = ? LIMIT 1");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = 'Email sudah terdaftar, gunakan email lain.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO admin (nama, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $email, $hash);

            if ($stmt->execute()) {
                $success = 'Akun berhasil dibuat! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan saat menyimpan data.';
            }
            $stmt->close();
        }

        $cek->close();
        $conn->close();
    }
}
layoutHead('Register');
layoutBody('fish-bg grid-pattern min-h-screen flex overflow-hidden relative');
?>
    <!-- ===== RIGHT PANEL — FORM ===== -->
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="card-glass rounded-3xl p-8 w-full max-w-sm">

            <h3 class="text-xl font-bold text-white text-center mb-1">Daftar Admin</h3>
            <p class="text-xs text-[#9B9AB0] text-center mb-6">Buat akun untuk mengakses panel admin</p>

            <!-- Alert Error -->
            <?php if ($error): ?>
            <div class="flex items-start gap-2 bg-red-500/10 border border-red-500/25 rounded-xl px-3.5 py-3 mb-5">
                <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-400 text-xs font-medium"><?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>

            <!-- Alert Success -->
            <?php if ($success): ?>
            <div class="flex items-start gap-2 bg-green-500/10 border border-green-500/25 rounded-xl px-3.5 py-3 mb-5">
                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-green-400 text-xs font-medium"><?= htmlspecialchars($success) ?></p>
                    <a href="login.php" class="text-[#C8F135] text-xs font-semibold hover:underline mt-0.5 inline-block">Klik di sini untuk login →</a>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" novalidate>

                <!-- Nama -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Nama</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-[#9B9AB0]" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"/>
                            </svg>
                        </span>
                        <input type="text" name="nama"
                            placeholder="Nama Anda"
                            value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                            class="input-field w-full rounded-xl pl-10 pr-4 py-2.5 text-sm"
                            required>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-[#9B9AB0]" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </span>
                        <input type="email" name="email"
                            placeholder="admin@email.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            class="input-field w-full rounded-xl pl-10 pr-4 py-2.5 text-sm"
                            required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-[#9B9AB0]" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="pwdReg"
                            placeholder="Min. 8 karakter"
                            oninput="checkStrength(this.value)"
                            class="input-field w-full rounded-xl pl-10 pr-10 py-2.5 text-sm"
                            required>
                        <button type="button" onclick="togglePwd('pwdReg','eyeReg')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9B9AB0] hover:text-white transition-colors">
                            <svg id="eyeReg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Password strength bar -->
                    <div class="mt-2 flex gap-1">
                        <div id="s1" class="strength-bar flex-1 bg-white/10"></div>
                        <div id="s2" class="strength-bar flex-1 bg-white/10"></div>
                        <div id="s3" class="strength-bar flex-1 bg-white/10"></div>
                        <div id="s4" class="strength-bar flex-1 bg-white/10"></div>
                    </div>
                    <p id="strengthLabel" class="text-[10px] text-[#9B9AB0] mt-1"></p>
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-[#9B9AB0]" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <input type="password" name="confirm_password" id="pwdConf"
                            placeholder="Ulangi password"
                            oninput="checkMatch()"
                            class="input-field w-full rounded-xl pl-10 pr-10 py-2.5 text-sm"
                            required>
                        <button type="button" onclick="togglePwd('pwdConf','eyeConf')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9B9AB0] hover:text-white transition-colors">
                            <svg id="eyeConf" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <p id="matchLabel" class="text-[10px] mt-1"></p>
                </div>

                <button type="submit" class="btn-primary w-full py-2.5 rounded-xl text-sm">
                    Daftar
                </button>
            </form>

            <p class="text-center text-xs text-[#9B9AB0] mt-5">
                Sudah punya akun?
                <a href="login.php" class="text-[#C8F135] font-semibold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>

    <script>
        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.innerHTML = isHidden
                ? `<path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a10.002 10.002 0 01-9.542-5.356 1.651 1.651 0 010-1.185 9.98 9.98 0 015.396-5.222l1.822 1.822A2.5 2.5 0 0010.748 13.93z"/>`
                : `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>`;
        }

        function checkStrength(val) {
            let score = 0;
            if (val.length >= 8)             score++;
            if (/[A-Z]/.test(val))           score++;
            if (/[0-9]/.test(val))           score++;
            if (/[^A-Za-z0-9]/.test(val))   score++;

            const colors = ['#ef4444','#f97316','#eab308','#C8F135'];
            const labels = ['Lemah','Cukup','Kuat','Sangat Kuat'];
            const labelEl = document.getElementById('strengthLabel');

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('s' + i);
                bar.style.background = i <= score ? colors[score - 1] : 'rgba(255,255,255,0.1)';
            }
            labelEl.textContent = val.length > 0 ? labels[score - 1] || '' : '';
            labelEl.style.color = score > 0 ? colors[score - 1] : '#9B9AB0';
        }

        function checkMatch() {
            const pwd  = document.getElementById('pwdReg').value;
            const conf = document.getElementById('pwdConf').value;
            const lbl  = document.getElementById('matchLabel');
            if (conf.length === 0) { lbl.textContent = ''; return; }
            if (pwd === conf) {
                lbl.textContent = '✓ Password cocok';
                lbl.style.color = '#C8F135';
            } else {
                lbl.textContent = '✗ Password tidak cocok';
                lbl.style.color = '#ef4444';
            }
        }
    </script>
<?php layoutFoot(); ?>
