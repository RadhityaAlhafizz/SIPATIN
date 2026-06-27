<?php
require_once 'icons.php';
// Helper: render head & open body+wrapper

function layoutHead(string $title): void { ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Sistem Pakar Ikan Patin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: '#0F0F14',
                        accent: '#C8F135',
                        darkAccent: '#9CC40E',
                        muted: '#9B9AB0',
                    },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            serif: ['Merriweather', 'serif']
                        }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="../assets/logo-patin-SIPATIN.webp">

    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #F5F4EF; }
        .fish-bg {
            background-color: #0F0f14;
            background-image:
                radial-gradient(ellipse 80% 50% at 20% 40%, rgba(0,180,140,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 80% 70%, rgba(200,241,53,0.07) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 60% 20%, rgba(0,120,200,0.10) 0%, transparent 50%);
        }
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .card-glass {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .input-field {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            color: #fff;
            transition: border-color 0.2s, background 0.2s;
        }
        .input-field::placeholder { color: rgba(255,255,255,0.25); }
        .input-field:focus {
            outline: none;
            border-color: #C8F135;
            background: rgba(200,241,53,0.04);
        }
        .input-field.error-field { border-color: rgba(239,68,68,0.5); }
        .btn-primary {
            background: #C8F135;
            color: #0F1A2E;
            font-weight: 700;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(200,241,53,0.30); }
        .btn-primary:active { transform: translateY(0); }
        
        @keyframes rise {
            0%   { transform: translateY(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.4; }
            100% { transform: translateY(-100vh); opacity: 0; }
        }
        .strength-bar { height: 3px; border-radius: 2px; transition: width 0.3s, background 0.3s; }
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:50; align-items:center; justify-content:center; }
        .modal-backdrop.open { display:flex; }
        .modal-card { background:#fff; border-radius:20px; padding: 20px; width:calc(100% - 24px); max-width:420px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,0.15); } @media (min-width: 640px){.modal-card{padding:28px;} }
        .input-base { width:100%; border:1.5px solid #E8E7E1; border-radius:10px; padding:9px 12px; font-size:13px; color:#1C1C2E; font-family:inherit; transition:border-color .2s; background:#fff; }
        .input-base:focus { outline:none; border-color:#C8F135; }
        select.input-base { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 20 20' fill='%239B9AB0'%3E%3Cpath d='M5 8l5 5 5-5'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
        .btn-accent { background:#C8F135; color:#1C1C2E; font-weight:700; border:none; border-radius:10px; padding:9px 18px; font-size:13px; cursor:pointer; font-family:inherit; transition:transform .15s,box-shadow .15s; }
        .btn-accent:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(200,241,53,.35); }
        .btn-ghost { background:transparent; border:1.5px solid #E8E7E1; border-radius:10px; padding:8px 16px; font-size:13px; color:#9B9AB0; cursor:pointer; font-family:inherit; transition:background .2s; }
        .btn-ghost:hover { background:#F5F4EF; }
        .pill { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .pill-virus    { background:#FEE9E9; color:#C0392B; }
        .pill-bakteri  { background:#FFF3E0; color:#E07A10; }
        .pill-jamur    { background:#E8F5E9; color:#1B7A48; }
        .pill-parasit  { background:#EDE7F6; color:#5E35B1; }
        .toast { position:fixed; bottom:24px; right:24px; z-index:999; background:#1C1C2E; color:#fff; padding:12px 20px; border-radius:12px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px; transform:translateY(80px); opacity:0; transition:transform .3s,opacity .3s; pointer-events:none; }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast.success::before { content:'✓'; color:#C8F135; }
        .toast.error::before { content:'✕'; color:#ef4444; }
        tr.fade-in { animation: rowIn .25s ease; }
        @keyframes rowIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
        ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-track { background:transparent; } ::-webkit-scrollbar-thumb { background:#D0CFC9; border-radius:10px; }
    </style>
</head>
<?php }
function layoutBody(string $class = 'bg-[#F5F4EF] font-sans overflow-x-hidden'): void { ?>
<body class="<?= $class ?>">
<?php }


function layoutFoot(): void { ?>
<script>
function showToast(msg, type='success') {
    let t = document.getElementById('globalToast');
    t.className = 'toast ' + type;
    t.querySelector('span').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-backdrop')) e.target.classList.remove('open');
});

function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-[120%]');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
}

function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-[120%]');
    document.getElementById('sidebarOverlay').classList.add('hidden');
}

// Mobile Menu
document.getElementById('mobileMenuBtn').addEventListener('click', () => {
    document.getElementById('mobileMenu').classList.toggle('hidden');
});

// Tutup sidebar otomatis saat layar membesar ke desktop
window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        closeSidebar();
        document.getElementById('sidebarOverlay').classList.add('hidden');
    }
});

</script>
<div id="globalToast" class="toast"><span></span></div>
</body></html>
<?php }
