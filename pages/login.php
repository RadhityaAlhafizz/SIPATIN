<?php
session_start();
require_once '../config/database.php';
require_once '../includes/layout.php';


if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $conn  = getConnection();
        $stmt  = $conn->prepare("SELECT id, nama, password FROM admin WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Password yang Anda masukkan salah.';
            }
        } else {
            $error = 'Email tidak ditemukan.';
        }

        $stmt->close();
        $conn->close();
    }
}
layoutHead('Login');
layoutBody('fish-bg grid-pattern min-h-screen flex overflow-hidden relative');
?>
    <div class="flex-1 flex items-center justify-center">
        <div class="card-glass rounded-3xl p-8 w-full max-w-sm">
            <div class="text-center justify-center">
                <h3 class="text-xl font-bold text-white mb-1">Masuk sebagai Admin</h3>
                <p class="text-xs text-[#9B9AB0] mb-6">Masukkan Email dan Password anda</p>
            </div>

            <!-- Alert Error -->
            <?php if ($error): ?>
            <div class="flex items-start gap-2 bg-red-500/10 border border-red-500/25 rounded-xl px-3.5 py-3 mb-5">
                <svg class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-400 text-xs font-medium"><?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" novalidate>

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
                            class="input-field w-full rounded-xl pl-10 pr-4 py-2.5 text-white text-sm"
                            required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-[#9B9AB0]" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="pwdLogin"
                            placeholder="••••••••"
                            class="input-field w-full rounded-xl pl-10 pr-10 py-2.5 text-white text-sm"
                            required>
                        <button type="button" onclick="togglePwd('pwdLogin','eyeLogin')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#9B9AB0] hover:text-white transition-colors">
                            <svg id="eyeLogin" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary w-full py-2.5 rounded-xl text-sm">
                    Login
                </button>
                
            </form>

            <p class="text-center text-xs text-[#9B9AB0] mt-5">
                Belum punya akun?
                <a href="register.php" class="text-[#C8F135] font-semibold hover:underline">Daftar Admin</a>
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
    </script>

<?php layoutFoot(); ?>
