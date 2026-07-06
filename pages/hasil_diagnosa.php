<?php require_once '../includes/layout.php';
layoutHead('Hasil Diagnosis');
layoutBody();
?>
<style>
    .page-bg { background: #0F0F14; }
    .text-muted { color: #9B9AB0; }

    @keyframes ringFill {
        from { stroke-dashoffset: var(--circ); }
        to   { stroke-dashoffset: var(--offset); }
    }
    .ring-arc { animation: ringFill 1.2s cubic-bezier(.4,0,.2,1) forwards; }

    .gejala-list { list-style: none; padding: 0; margin: 0; }
    .gejala-list li { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 6px; font-size: 13px; color: #101010; line-height: 1.5; }
    .gejala-list li .dash { flex-shrink: 0; }

    /* ══ PRINT ══ */
    @media print {
        .no-print { display: none !important; }

        body, html { background: #fff !important; color: #000 !important; font-family: 'Times New Roman', Times, serif; }

        .page-wrap { max-width: 100% !important; padding: 0 !important; }

        /* Sembunyikan tampilan dark */
        .print-hide { display: none !important; }

        /* Tampilkan konten cetak */
        .print-area { display: block !important; }

        /* Reset warna */
        * { color: #000 !important; background: transparent !important; border-color: #0F0F14 !important; }
    }
</style>

<?php
require_once __DIR__ . '/../config/database.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['gejala'])) {
    header("Location: diagnosa.php"); exit;
}

$gejalaDipilih = array_map('intval', $_POST['gejala']);
$namaPengguna  = trim(htmlspecialchars($_POST['nama_pengguna'] ?? ''));
$ids_str       = implode(',', $gejalaDipilih);

$res = $conn->query(
    "SELECT ba.id_penyakit,
            p.kode_penyakit AS p_kode, p.nama_penyakit AS p_nama, p.jenis AS p_jenis,
            p.deskripsi AS p_deskripsi,
            g.kode_gejala AS g_kode, g.nama_gejala AS g_nama,
            s.kode_solusi AS s_kode, s.deskripsi AS s_deskripsi
     FROM basis_aturan ba
     JOIN penyakit p ON ba.id_penyakit = p.id
     JOIN gejala   g ON ba.id_gejala   = g.id
     JOIN solusi   s ON ba.id_solusi   = s.id
     WHERE ba.id_gejala IN ($ids_str)
     ORDER BY p.kode_penyakit"
);

$resTotalGejala = $conn->query(
    "SELECT id_penyakit, COUNT(DISTINCT id_gejala) AS total_gejala FROM basis_aturan GROUP BY id_penyakit"
);
$totalGejalaPenyakit = [];
while ($tg = $resTotalGejala->fetch_assoc())
    $totalGejalaPenyakit[$tg['id_penyakit']] = (int)$tg['total_gejala'];

$infoPenyakit = []; $gejalaCocok = []; $solusiList = [];
while ($r = $res->fetch_assoc()) {
    $pid = $r['id_penyakit'];
    if (!isset($infoPenyakit[$pid]))
        $infoPenyakit[$pid] = ['kode'=>$r['p_kode'],'nama'=>$r['p_nama'],'jenis'=>$r['p_jenis'],'deskripsi'=>$r['p_deskripsi']];
    if (!in_array($r['g_nama'], $gejalaCocok[$pid] ?? []))
        $gejalaCocok[$pid][] = $r['g_nama'];
    $ada = array_filter($solusiList[$pid] ?? [], fn($s) => $s['kode'] === $r['s_kode']);
    if (empty($ada)) $solusiList[$pid][] = ['kode'=>$r['s_kode'],'desk'=>$r['s_deskripsi']];
}

$hasilDiagnosa = [];
foreach ($infoPenyakit as $pid => $info) {
    $jmlCocok    = count($gejalaCocok[$pid] ?? []);
    $totalGejala = $totalGejalaPenyakit[$pid] ?? 1;
    $persentase  = min(100, round(($jmlCocok / $totalGejala) * 100, 1));
    $hasilDiagnosa[] = [
        'kode'=>$info['kode'],'nama'=>$info['nama'],'jenis'=>$info['jenis'],
        'deskripsi'=>$info['deskripsi'],'persentase'=>$persentase,
        'gejala_cocok'=>$gejalaCocok[$pid]??[],'solusi'=>$solusiList[$pid]??[],
        'jml_cocok'=>$jmlCocok,'total_gejala'=>$totalGejala,
    ];
}
usort($hasilDiagnosa, fn($a,$b) => $b['persentase'] <=> $a['persentase']);

$gejalaDetail = [];
if (!empty($gejalaDipilih)) {
    $resG = $conn->query("SELECT id, kode_gejala, nama_gejala FROM gejala WHERE id IN ($ids_str) ORDER BY kode_gejala");
    while ($g = $resG->fetch_assoc())
        $gejalaDetail[$g['id']] = ['kode'=>$g['kode_gejala'],'nama'=>$g['nama_gejala']];
}
$gejalaCocokUtamaNama = $hasilDiagnosa[0]['gejala_cocok'] ?? [];

$cekTabel = $conn->query("SHOW TABLES LIKE 'log_diagnosa'");
if ($cekTabel && $cekTabel->num_rows > 0) {
    $logG = json_encode(array_values(array_column($gejalaDetail, 'nama')));
    $logH = empty($hasilDiagnosa) ? 'Tidak terdeteksi'
          : implode(', ', array_map(fn($h) => $h['nama'].' ('.$h['persentase'].'%)', $hasilDiagnosa));
    $penyakitUtama = $persentaseUtama = null;
    if (!empty($hasilDiagnosa)) { $penyakitUtama=$hasilDiagnosa[0]['nama']; $persentaseUtama=$hasilDiagnosa[0]['persentase']; }
    $stLog = $conn->prepare("INSERT INTO log_diagnosa (nama_pengguna,penyakit_utama,persentase,gejala_dipilih,hasil_diagnosa) VALUES (?,?,?,?,?)");
    if ($stLog) { $stLog->bind_param("ssdss",$namaPengguna,$penyakitUtama,$persentaseUtama,$logG,$logH); $stLog->execute(); $stLog->close(); }
}

$tanggal     = date('d F Y, H:i');
$statusUtama = $hasilDiagnosa[0] ?? null;
$conn->close();
?>

<!-- ── NAVBAR ── -->
<nav class="no-print bg-dark border-b border-white/10 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center h-14 justify-between">
        <a href="index.php" class="text-sm text-muted hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div class="flex items-center gap-2 ">
            <button onclick="window.print()"
            class="flex items-center gap-2 text-xs bg-accent text-dark font-semibold px-4 py-2 rounded-md transition-colors hover:bg-accent/80">
            <i class="fas fa-print"></i> Cetak / PDF
        </button>
        <a href="diagnosa.php" class="flex items-center gap-2 text-xs bg-muted border border-white/20 text-dark px-4 py-2 rounded-md transition-colors hover:bg-muted/80">
        <i class="fas fa-redo text-xs"></i> Diagnosa Ulang
        </a>
        </div>
    </div>
</nav>

<div class="page-wrap max-w-6xl mx-auto py-4 px-6">

    <?php if (empty($hasilDiagnosa)): ?>
    <!-- Tidak ditemukan -->
    <div class="text-center py-20">
        <i class="fas fa-question-circle text-4xl text-dark mb-4 block"></i>
        <p class="text-dark text-lg font-semibold mb-2">Tidak Ditemukan Diagnosa</p>
        <p class="text-dark text-sm mb-6">Gejala yang dipilih tidak cocok dengan penyakit dalam basis pengetahuan.</p>
        <a href="diagnosa.php" class="text-sm border border-white/20 text-white px-5 py-2 rounded-md hover:border-white/40 transition-colors">
            Diagnosa Ulang
        </a>
    </div>

    <?php else:
        $r      = 80;
        $circ   = 2 * M_PI * $r;
        $offset = $circ - ($circ * $statusUtama['persentase'] / 100);
    ?>

    <div class="print-area" style="display:none;">
        <!-- Kop Surat -->
        <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
            <tr>
                <td style="width:72px; vertical-align:middle; padding-right:16px;">
                    <img src="../assets/logo-patin-SIPATIN.webp" style="width:64px; height:64px; object-fit:contain;">
                </td>
                <td style="vertical-align:middle;">
                    <p style="margin:0; font-size:16px; font-weight:700;">SIPATIN</p>
                    <p style="margin:2px 0 0; font-size:12px; color:#444;">Sistem Pakar Diagnosa Penyakit Ikan Patin</p>
                </td>
            </tr>
        </table>
        <div style="border-top:2px solid #000; margin-bottom:24px;"></div>

        <p style="text-align:center; font-size:14px; font-weight:700; letter-spacing:.04em; margin-bottom:20px;">
            LAPORAN HASIL DIAGNOSIS PENYAKIT IKAN PATIN
        </p>

        <!-- Info Meta -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:20px;">
            <tr>
                <td style="width:120px; padding:3px 0;">Tanggal Cetak</td>
                <td style="width:8px;">:</td>
                <td><?= $tanggal ?></td>
            </tr>
            <tr>
                <td style="padding:3px 0;">Nama Pengguna</td>
                <td>:</td>
                <td><?= htmlspecialchars($namaPengguna) ?: '-' ?></td>
            </tr>
            <tr>
                <td style="padding:3px 0;">Penyakit Utama</td>
                <td>:</td>
                <td><?= htmlspecialchars($statusUtama['nama'] ?? '-') ?></td>
            </tr>
            <tr>
                <td style="padding:3px 0;">Tingkat Kecocokan</td>
                <td>:</td>
                <td><?= $statusUtama['persentase'] ?? 0 ?>% (<?= $statusUtama['jml_cocok'] ?? 0 ?> dari <?= $statusUtama['total_gejala'] ?? 0 ?> gejala)</td>
            </tr>
        </table>
        <!-- Penyakit Utama & Penanganan -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:24px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:left;">PENYAKIT</th>
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:center;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px 10px; font-weight:700;"><?= htmlspecialchars($statusUtama['nama'] ?? '-') ?></td>
                    <td style="border:1px solid #ccc; padding:6px 10px;"><?= htmlspecialchars($statusUtama['deskripsi'] ?? '-') ?></td>
                </tr>
            </tbody>
        </table>

        
        <!-- Tabel Gejala -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:24px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:left; width:40px;">NO</th>
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:left;">GEJALA YANG DIPILIH</th>
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:left; width:80px;">COCOK</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($gejalaDetail as $g):
                    $cocok = in_array($g['nama'], $gejalaCocokUtamaNama);
                ?>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px 10px;"><?= $no++ ?></td>
                    <td style="border:1px solid #ccc; padding:6px 10px;"><?= htmlspecialchars($g['kode']) ?> — <?= htmlspecialchars($g['nama']) ?></td>
                    <td style="border:1px solid #ccc; padding:6px 10px; text-align:center;"><?= $cocok ? '✓' : '✗' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Penanganan -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:24px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:7px 10px; text-align:left;">PENANGANAN</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($statusUtama['solusi'] as $sol): ?>
                    <?php if (!empty($sol['desk'])): ?>
                    <tr>
                        <td style="border:1px solid #ccc; padding:6px 10px; line-height:1.7;"><?= htmlspecialchars($sol['desk']) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="no-print flex items-center justify-between mb-6">
        <div class="flex flex-col items-start">
            <h1 class="text-sm font-semibold text-dark">HASIL DIAGNOSIS</h1>
            <?php if (!empty($namaPengguna)): ?>
            <p class="text-sm text-muted"><?= htmlspecialchars($namaPengguna) ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center">
            <p class="text-xs"><?= $tanggal ?></p>
        </div>
    </div>

    <div class="no-print max-w-6xl mx-auto py-6">
    <!-- Content -->
    <div class="flex flex-col lg:flex-row gap-5 items-start">
        <!-- KIRI -->
        <div class="no-print w-full lg:w-[320px] text-center">
            <div class="relative w-[240px] h-[240px] mx-auto sticky">
                <svg width="240" height="240" viewBox="0 0 240 240" style="transform:rotate(-90deg); display:block;">
                            <circle class="ring-track" cx="120" cy="120" r="<?= $r ?>" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="20"/>
                            <circle class="ring-arc" cx="120" cy="120" r="<?= $r ?>" fill="none" stroke="#9CC40E" stroke-width="20"
                                stroke-linecap="round"
                                stroke-dasharray="<?= $circ ?>"
                                stroke-dashoffset="<?= $circ ?>"
                                style="--circ:<?= $circ ?>; --offset:<?= $offset ?>;"/>
                        </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span id="ringPct"class="text-5xl font-bold">
                        <?= round($statusUtama['persentase']) ?>%
                    </span>
                </div>
            </div>

        </div>
        <!-- KANAN -->
        <div class="flex-1 bg-white rounded-[24px] p-6 card-glass">
            <!-- Penyakit -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2 underline decoration-accent decoration-8 underline-offset-4">
                    Penyakit Utama
                </h3>
                <h2 class="text-2xl font-bold">
                    <?= htmlspecialchars($statusUtama['nama']) ?>
                </h2>
                <!-- Penyebab / jenis -->
                <?php if (!empty($statusUtama['deskripsi'])): ?>
                <p class="text-sm text-dark"><?= htmlspecialchars($statusUtama['deskripsi']) ?></p>
                <?php endif; ?>
            </div>
            <!-- Gejala -->
            <div class="mb-6 ">
                <h3 class="text-lg font-semibold mb-2 underline decoration-accent decoration-8 underline-offset-4">
                    Gejala yang dipilih
                </h3>
                <ul class="gejala-list">
                    <?php foreach ($gejalaDetail as $g):
                        $cocok = in_array($g['nama'], $gejalaCocokUtamaNama);
                    ?>
                    <li class="<?= $cocok ? 'match' : '' ?> text-base">
                        <span class="dash">—</span>
                        <span><?= htmlspecialchars($g['kode']) ?> - <?= htmlspecialchars($g['nama']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Deskripsi -->
            <div>
                <h3 class="text-lg font-semibold mb-2 underline decoration-accent decoration-8 underline-offset-4">
                    Solusi Penanganan
                </h3>
                <div>
                    <?php foreach ($statusUtama['solusi'] as $sol): ?>
                        <?php if (!empty($sol['desk'])): ?>
                        <p class = "text-sm leading-6"><?= htmlspecialchars($sol['desk']) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php endif; ?>
</div>

<script>
(function(){
    var pct1 = <?= $statusUtama ? $statusUtama['persentase'] : 0 ?>;
    <?php if (isset($hasilDiagnosa[1])): ?>
    var pct2 = <?= $hasilDiagnosa[1]['persentase'] ?>;
    <?php else: ?>
    var pct2 = 0;
    <?php endif; ?>

    function countUp(elId, target, duration) {
        var el = document.getElementById(elId);
        if (!el) return;
        var start = null;
        function step(ts) {
            if (!start) start = ts;
            var p = Math.min((ts - start) / duration, 1);
            var ease = p < 0.5 ? 2*p*p : -1+(4-2*p)*p;
            el.textContent = Math.round(ease * target) + '%';
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = target + '%';
        }
        requestAnimationFrame(step);
    }

    window.addEventListener('DOMContentLoaded', function(){
        
        document.querySelectorAll('.ring-arc').forEach(function(arc){
            var offset = arc.style.getPropertyValue('--offset') || arc.dataset.offset;
            setTimeout(function(){
                arc.style.strokeDashoffset = offset;
            }, 100);
        });
        countUp('ringPct',  pct1, 1200);
        if (pct2) countUp('ringPct2', pct2, 1200);
    });
})();
</script>

</body>
<?php layoutFoot(); ?>