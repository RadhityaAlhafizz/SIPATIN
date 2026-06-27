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

/* ── Ambil semua data untuk JSON ke JS ── */

/* Penyakit */
$q = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit");
$data_penyakit = $q->fetch_all(MYSQLI_ASSOC);

/* Gejala */
$q = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala");
$data_gejala = $q->fetch_all(MYSQLI_ASSOC);

/* Solusi */
$q = $conn->query("SELECT * FROM solusi ORDER BY kode_solusi");
$data_solusi = $q->fetch_all(MYSQLI_ASSOC);

/* Basis Aturan */
$q = $conn->query(
    "SELECT ba.id,
            p.kode_penyakit AS p_kode, p.nama_penyakit AS p_nama, p.jenis AS p_jenis,
            g.kode_gejala AS g_kode, g.nama_gejala AS g_nama,
            s.kode_solusi AS s_kode
     FROM basis_aturan ba
     JOIN penyakit p ON ba.id_penyakit = p.id
     JOIN gejala   g ON ba.id_gejala   = g.id
     JOIN solusi   s ON ba.id_solusi   = s.id
     ORDER BY p.kode_penyakit, g.kode_gejala"
);
$data_aturan = $q->fetch_all(MYSQLI_ASSOC);

$admin_nama = $_SESSION['admin_nama'] ?? 'Administrator';

$conn->close();

layoutHead("Cetak Laporan");
layoutBody();

?>

<?php include '../includes/sidebar.php'; ?>


<main class="lg:ml-52 min-h-screen p-4 lg:p-6 overflow-x-hidden">

        <div class="lg:hidden flex items-center gap-3 mb-6">
            <!-- Tombol hamburger — hanya tampil di mobile -->
            <button onclick="openSidebar()"class="lg:hidden p-2 rounded-xl bg-white border border-[#E8E7E1]">
                <?php icon('menu') ?>
            </button>
        </div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1C1C2E]">Cetak Laporan</h1>
            <p class="text-xs text-[#9B9AB0] mt-0.5">Pilih data yang ingin dicetak lalu pratinjau sebelum mencetak</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-5 items-start">

        <!-- ═══════════════════════
             PANEL KIRI: Kontrol
        ═══════════════════════ -->
        <div class="w-full lg:w-64 flex-shrink-0 space-y-4">

            <!-- Pilih Jenis Laporan -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-5">
                <h3 class="text-sm font-bold text-[#1C1C2E] mb-4">Pilih Laporan</h3>

                <div class="space-y-2.5">
                    <?php
                    $menu = [
                        'penyakit'  => ['label'=>'Data Penyakit',  'count'=>$jumlah_penyakit, 'color'=>'#FEE9E9','text'=>'#C0392B'],
                        'gejala'    => ['label'=>'Data Gejala',    'count'=>$jumlah_gejala,   'color'=>'#FFF3E0','text'=>'#E07A10'],
                        'aturan'    => ['label'=>'Basis Aturan',   'count'=>$jumlah_aturan,   'color'=>'#E8F5E9','text'=>'#1B7A48'],
                        'solusi'    => ['label'=>'Data Solusi',    'count'=>$jumlah_solusi,   'color'=>'#EDE7F6','text'=>'#5E35B1'],
                    ];
                    foreach ($menu as $key => $m):
                    ?>
                    <label class="flex items-center justify-between p-3 rounded-xl border-2 border-transparent cursor-pointer transition-all hover:border-[#C8F135]/50 hover:bg-[#F5F4EF]"
                           id="lbl_<?= $key ?>"
                           onclick="selectJenis('<?= $key ?>')">
                        <div class="flex items-center gap-2.5">
                            <input type="radio" name="jenis" value="<?= $key ?>" class="accent-[#1C1C2E]"
                                   id="radio_<?= $key ?>"
                                   <?= $key==='penyakit'?'checked':'' ?>>
                            <span class="text-xs sm:text-sm font-medium text-[#1C1C2E]"><?= $m['label'] ?></span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                              style="background:<?= $m['color'] ?>;color:<?= $m['text'] ?>">
                            <?= $m['count'] ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Filter & Opsi -->
            <div class="bg-white rounded-2xl border border-[#E8E7E1] p-5">
                <h3 class="text-sm font-bold text-[#1C1C2E] mb-4">Opsi Cetak</h3>

                <!-- Filter jenis penyakit (hanya muncul kalau pilih penyakit) -->
                <div id="filterPenyakit" class="mb-3">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Filter Jenis</label>
                    <select id="selJenis" onchange="renderPreview()"
                        class="input-base text-xs py-2">
                        <option value="">Semua Jenis</option>
                        <option value="Bakteri">Bakteri</option>
                        <option value="Virus">Virus</option>
                        <option value="Jamur">Jamur</option>
                        <option value="Parasit">Parasit</option>
                    </select>
                </div>

                <!-- Filter penyakit untuk aturan -->
                <div id="filterAturan" class="mb-3 hidden">
                    <label class="block text-xs font-semibold text-[#9B9AB0] mb-1.5">Filter Penyakit</label>
                    <select id="selPenyakitAturan" onchange="renderPreview()"
                        class="input-base text-xs py-2">
                        <option value="">Semua Penyakit</option>
                        <?php foreach ($data_penyakit as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['kode'].' - '.$p['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tampilkan nomor urut -->
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-medium text-[#9B9AB0]">Nomor Urut</label>
                    <input type="checkbox" id="chkNomor" checked onchange="renderPreview()"
                        class="w-4 h-4 accent-[#1C1C2E] cursor-pointer">
                </div>

                <!-- Tampilkan tanggal cetak -->
                <div class="flex items-center justify-between">
                    <label class="text-xs font-medium text-[#9B9AB0]">Tanggal Cetak</label>
                    <input type="checkbox" id="chkTanggal" checked onchange="renderPreview()"
                        class="w-4 h-4 accent-[#1C1C2E] cursor-pointer">
                </div>
            </div>

            <!-- Tombol Cetak -->
            <button onclick="doCetak()" 
                    class="w-full btn-accent flex items-center justify-center gap-2 py-3 text-sm">
                
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M8 3a2 2 0 0 0-2 2v3h12V5a2 2 0 0 0-2-2H8Zm-3 7a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h1v-4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v4h1a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2H5Zm4 11a1 1 0 0 1-1-1v-4h8v4a1 1 0 0 1-1 1H9Z" clip-rule="evenodd"/>
                </svg>
                Cetak / Simpan PDF
            </button>

        </div>

        <!-- ═══════════════════════
             PANEL KANAN: Preview
        ═══════════════════════ -->
        <div class="hidden lg:block flex-1 min-w-0">
            <div class="bg-white rounded-2xl border border-[#E8E7E1] overflow-hidden">
                <!-- Toolbar preview -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 py-3 border-b border-[#F0EFE9] bg-[#FAFAF8]">
                        <span class="ml-2 text-xs text-[#9B9AB0] font-medium">Preview Laporan</span>
                    <span id="previewCount" class="text-xs text-[#9B9AB0]">— baris</span>
                </div>

                <!-- Area preview — diskalakan -->
                <div class="overflow-x-auto overflow-y-auto bg-[#f0eee8] p-2 sm:p-4 lg:p-6" style="max-height:72vh;">
        
                    <div id="previewScale"
                         class="mx-auto"
                         style="width:794px;transform-origin:top left;background:#fff;box-shadow:0 4px 24px rgba(0,0,0,.12);">
                        <div id="printArea"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ══════════════════════════════════
     TEMPLATE LAPORAN
══════════════════════════════════ -->
<template id="tplReport">
    <div style="font-family:Arial,sans-serif;color:#000;padding:40px 48px;">

        <!-- KOP: Logo + Judul -->
        <table style="width:100%;border-collapse:collapse;margin-bottom:12px;">
            <tr>
                <td style="width:72px;vertical-align:middle;padding-right:16px;">
                    <img src="../assets/logo-patin-SIPATIN.webp" style="width:64px;height:64px;object-fit:contain;">
                </td>
                <td style="vertical-align:middle;text-align:center;">
                    <div style="font-size:15px;font-weight:700;text-transform:uppercase;" id="tpl_judul"></div>
                    <div style="font-size:11px;margin-top:4px;">Sistem Pakar Diagnosa Ikan Patin &mdash; Metode Forward Chaining</div>
                </td>
                <td style="width:72px;"></td>
            </tr>
        </table>
        <div style="border-bottom:2px solid #000;margin-bottom:16px;"></div>

        <!-- META: 2 kolom teks biasa -->
        <div style="font-size:11px;margin-bottom:16px;line-height:2;" id="tpl_meta"></div>

        <!-- TABEL -->
        <div id="tpl_tabel"></div>

        <!-- RINGKASAN (opsional, diisi JS) -->
        <div id="tpl_ringkasan"></div>

        <!-- FOOTER TTD -->
        <div style="margin-top:40px;text-align:right;font-size:11px;">
            <div id="tpl_tgl_footer"></div>
            <div style="margin-top:4px;">Admin Sistem,</div>
            <div style="margin-top:48px;border-bottom:1px solid #000;display:inline-block;min-width:140px;"></div>
            <div style="margin-top:4px;font-weight:700;" id="tpl_ttd"></div>
        </div>

    </div>
</template>

<script>
/* ══════════════════════════
   DATA dari PHP
══════════════════════════ */
const DATA = {
    penyakit: <?= json_encode($data_penyakit) ?>,
    gejala:   <?= json_encode($data_gejala)   ?>,
    aturan:   <?= json_encode($data_aturan)   ?>,
    solusi:   <?= json_encode($data_solusi)   ?>,
};
const ADMIN = <?= json_encode($admin_nama) ?>;

/* ══════════════════════════
   STATE
══════════════════════════ */
let currentJenis = 'penyakit';

/* ══════════════════════════
   PILIH JENIS
══════════════════════════ */
function selectJenis(jenis) {
    currentJenis = jenis;

    // Update radio
    document.querySelectorAll('input[name="jenis"]').forEach(r => r.checked = r.value === jenis);

    // Highlight label
    document.querySelectorAll('[id^="lbl_"]').forEach(el => {
        el.style.borderColor = 'transparent';
        el.style.background  = '';
    });
    const lbl = document.getElementById('lbl_' + jenis);
    if (lbl) { lbl.style.borderColor = '#C8F135'; lbl.style.background = '#FAFDE8'; }

    // Tampilkan filter yang relevan
    document.getElementById('filterPenyakit').style.display  = jenis === 'penyakit' ? '' : 'none';
    document.getElementById('filterAturan').style.display    = jenis === 'aturan'   ? '' : 'none';

    renderPreview();
}

/* ══════════════════════════
   RENDER PREVIEW
══════════════════════════ */
function renderPreview() {
    const area   = document.getElementById('printArea');
    const tpl    = document.getElementById('tplReport');
    const clone  = tpl.content.cloneNode(true);
    const wrap   = document.createElement('div');
    wrap.appendChild(clone);

    const showNo   = document.getElementById('chkNomor').checked;
    const showDate = document.getElementById('chkTanggal').checked;
    const now      = new Date();
    const tgl      = now.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});
    const jam      = now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});

    wrap.querySelector('#tpl_ttd').textContent = ADMIN;
    wrap.querySelector('#tpl_tgl_footer').textContent = tgl;

    let rows = [], tabelHtml = '', judulText = '', filterLabel = 'Semua', ringkasanHtml = '';

    /* ── PENYAKIT ── */
    if (currentJenis === 'penyakit') {
        judulText = 'LAPORAN DATA PENYAKIT IKAN PATIN';
        const fv  = document.getElementById('selJenis').value;
        filterLabel = fv || 'Semua Jenis';
        rows = fv ? DATA.penyakit.filter(r => r.jenis === fv) : DATA.penyakit;

        tabelHtml = tableWrap(
            [showNo?'No':null,'Kode','Nama Penyakit','Jenis','Deskripsi','Tgl Dibuat'].filter(Boolean),
            rows.map((r,i) => [
                showNo?(i+1):null,
                r.kode_penyakit,
                r.nama_penyakit,
                r.jenis,
                r.deskripsi||'-',
                fmtDate(r.created_at),
            ].filter((_,idx)=>showNo||idx!==0))
        );

        // Ringkasan per jenis
        const jenisList = ['Virus','Bakteri','Jamur','Parasit'];
        let lines = jenisList.map(j => {
            const n = DATA.penyakit.filter(p => (!fv||p.jenis===fv) && p.jenis===j).length;
            return `<div>${j.padEnd(8,' ')} : ${n} penyakit</div>`;
        }).join('');
        ringkasanHtml = `<div style="margin-top:20px;font-size:11px;font-family:Arial,sans-serif;">
            <strong>Ringkasan per Jenis:</strong>
            <div style="margin-top:6px;line-height:1.8;">${lines}</div>
        </div>`;
    }

    /* ── GEJALA ── */
    else if (currentJenis === 'gejala') {
        judulText = 'LAPORAN DATA GEJALA PENYAKIT IKAN PATIN';
        rows = DATA.gejala;
        tabelHtml = tableWrap(
            [showNo?'No':null,'Kode','Nama Gejala','Tgl Dibuat'].filter(Boolean),
            rows.map((r,i) => [
                showNo?(i+1):null,
                r.kode_gejala, r.nama_gejala||'-', fmtDate(r.created_at),
            ].filter((_,idx)=>showNo||idx!==0))
        );
    }

    /* ── SOLUSI ── */
    else if (currentJenis === 'solusi') {
        judulText = 'LAPORAN DATA SOLUSI PENANGANAN PENYAKIT IKAN PATIN';
        rows = DATA.solusi;
        tabelHtml = tableWrap(
            [showNo?'No':null,'Kode','Deskripsi','Tgl Dibuat'].filter(Boolean),
            rows.map((r,i) => [
                showNo?(i+1):null,
                r.kode_solusi, r.deskripsi||'-', fmtDate(r.created_at),
            ].filter((_,idx)=>showNo||idx!==0))
        );
    }

    /* ── BASIS ATURAN ── */
    else if (currentJenis === 'aturan') {
        judulText = 'LAPORAN BASIS ATURAN SISTEM PAKAR IKAN PATIN';
        const fp  = document.getElementById('selPenyakitAturan').value;
        filterLabel = fp ? (DATA.penyakit.find(p=>p.id==fp)?.nama||'Filter') : 'Semua Penyakit';
        rows = fp ? DATA.aturan.filter(r => {
            const m = DATA.penyakit.find(p => p.id==fp);
            return m && r.p_kode === m.kode;
        }) : DATA.aturan;

        tabelHtml = tableWrap(
            [showNo?'No':null,'Gejala (IF)','Penyakit (THEN)','Solusi'].filter(Boolean),
            rows.map((r,i) => [
                showNo?(i+1):null,
                `${r.g_kode} — ${r.g_nama}`,
                `${r.p_kode} — ${r.p_nama} (${r.p_jenis})`,
                r.s_kode,
            ].filter((_,idx)=>showNo||idx!==0))
        );
    }

    // Meta info 2 kolom
    const metaHtml = `
        <table style="font-size:11px;border:none;font-family:Arial,sans-serif;">
            <tr><td style="padding:1px 0;width:110px; ">Tanggal Cetak</td><td>: ${showDate ? tgl+', '+jam+' WIB' : tgl}</td></tr>
            <tr><td style="padding:1px 0;">Dicetak Oleh</td><td>: ${esc(ADMIN)}</td></tr>
            <tr><td style="padding:1px 0;">Filter</td><td>: ${esc(filterLabel)}</td></tr>
            <tr><td style="padding:1px 0;">Total Data</td><td>: ${rows.length} data</td></tr>
        </table>`;

    wrap.querySelector('#tpl_judul').textContent    = judulText;
    wrap.querySelector('#tpl_meta').innerHTML       = metaHtml;
    wrap.querySelector('#tpl_tabel').innerHTML      = tabelHtml;
    wrap.querySelector('#tpl_ringkasan').innerHTML  = ringkasanHtml;

    area.innerHTML = '';
    area.appendChild(wrap);
    document.getElementById('previewCount').textContent = rows.length + ' baris';
    scalePreview();
}

/* ══════════════════════════
   HELPER: Tabel HTML
══════════════════════════ */
function tableWrap(headers, bodyRows) {
    const thStyle = `padding:8px 10px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#0F0F14;border:1px solid #1C1C2E;`;
    const tdStyle = `padding:9px 10px;font-size:12px;border:1px solid #1C1C2E ;vertical-align:top;`;

    let th = headers.map(h => `<th style="${thStyle}">${h||''}</th>`).join('');
    let tb = bodyRows.map((cols, ri) => {
        const bg = ri % 2 === 1 ? 'background:#FAFAF8;' : '';
        let tds = cols.map(c => `<td style="${tdStyle}${bg}">${c ?? ''}</td>`).join('');
        return `<tr>${tds}</tr>`;
    }).join('');

    return `<table style="width:100%;border-collapse:collapse;">
        <thead><tr>${th}</tr></thead>
        <tbody>${tb || '<tr><td colspan="'+(headers.length)+'" style="text-align:center;padding:24px;color:#9B9AB0;">Tidak ada data</td></tr>'}</tbody>
    </table>`;
}

/* ══════════════════════════
   HELPER: Escape & format
══════════════════════════ */
function esc(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function fmtDate(s) {
    if (!s) return '—';
    const d = new Date(s);
    return d.toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'});
}

/* ══════════════════════════
   SCALE PREVIEW
══════════════════════════ */
function scalePreview() {
    const wrap      = document.getElementById('previewScale');
    const container = wrap.parentElement;
    const available = container.clientWidth - 48; // kurangi padding 2x24
    const scale     = Math.min(available / 794, 1);
    wrap.style.transform       = `scale(${scale})`;
    wrap.style.transformOrigin = 'top left';
    wrap.style.width           = '794px';
    // sesuaikan tinggi container agar tidak terpotong
    const innerH = wrap.querySelector('#printArea')
                       ? wrap.querySelector('#printArea').scrollHeight
                       : wrap.scrollHeight;
    wrap.parentElement.style.minHeight = (innerH * scale + 48) + 'px';
}

window.addEventListener('resize', scalePreview);

/* ══════════════════════════
   CETAK — buka window baru agar tidak kosong
══════════════════════════ */
function doCetak() {
    const konten = document.getElementById('printArea').innerHTML;
    if (!konten.trim()) { alert('Pilih laporan terlebih dahulu.'); return; }

    const pw = window.open('', '_blank');
    pw.document.write(`<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Sistem Pakar Ikan Patin</title>
<style>
*  { box-sizing:border-box; margin:0; padding:0; }
body { font-family:Arial,sans-serif; color:#1C1C2E; background:#fff; }
@media print {
    @page { margin:1cm; }
    body  { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
</style>
</head>
<body>${konten}</body>
</html>`);
    pw.document.close();
    
    pw.onload = () => {
        pw.focus();
        pw.print();

        // cek berkala apakah dialog print sudah ditutup
        const closeCheck = setInterval(() => {

        if (pw.document.visibilityState === 'visible') {
            clearInterval(closeCheck);

            setTimeout(() => {
                pw.close();
            }, 300);
        }

    }, 500);
    };
}
/* ══════════════════════════
   INIT
══════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    selectJenis('penyakit');
});
</script>

<?php layoutFoot(); ?>
