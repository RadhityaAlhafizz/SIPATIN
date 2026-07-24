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
require_once '../config/database.php';
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['gejala'])) {
    header("Location: diagnosa.php"); exit;
}

$gejalaDipilih = array_map('intval', $_POST['gejala']);
$namaPengguna  = trim(htmlspecialchars($_POST['nama_pengguna'] ?? ''));
$ids_str       = implode(',', $gejalaDipilih);

$res = $conn->query(
    "SELECT ba.id_penyakit,
            p.kode_penyakit AS p_kode, p.nama_penyakit AS p_nama, p.jenis AS p_jenis, p.tingkatan AS p_tingkatan,
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
        $infoPenyakit[$pid] = ['kode'=>$r['p_kode'],'nama'=>$r['p_nama'],'jenis'=>$r['p_jenis'],'tingkatan'=>$r['p_tingkatan'],'deskripsi'=>$r['p_deskripsi']];
    if (!in_array($r['g_nama'], $gejalaCocok[$pid] ?? []))
        $gejalaCocok[$pid][] = $r['g_nama'];
    $ada = array_filter($solusiList[$pid] ?? [], fn($s) => $s['kode'] === $r['s_kode']);
    if (empty($ada)) $solusiList[$pid][] = ['kode'=>$r['s_kode'],'desk'=>$r['s_deskripsi']];
}

$hasilDiagnosa = [];
foreach ($infoPenyakit as $pid => $info) {
    $jmlCocok    = count($gejalaCocok[$pid] ?? []);
    $totalGejala = $totalGejalaPenyakit[$pid] ?? 1;
    
    $hasilDiagnosa[] = [
        'kode'=>$info['kode'],'nama'=>$info['nama'],'jenis'=>$info['jenis'],'tingkatan'=>$info['tingkatan'],
        'deskripsi'=>$info['deskripsi'],
        'gejala_cocok'=>$gejalaCocok[$pid]??[],'solusi'=>$solusiList[$pid]??[],
        'jml_cocok'=>$jmlCocok,'total_gejala'=>$totalGejala,
    ];
}
usort($hasilDiagnosa, fn($a,$b) => $b['jml_cocok'] <=> $a['jml_cocok']);

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
      : implode(', ', array_column($hasilDiagnosa, 'nama'));

    $penyakitUtama = $hasilDiagnosa[0]['nama'] ?? null;
    $stLog = $conn->prepare("INSERT INTO log_diagnosa (nama_pengguna,penyakit_utama,gejala_dipilih,hasil_diagnosa) VALUES (?,?,?,?)");
    if ($stLog) { $stLog->bind_param("ssss",$namaPengguna,$penyakitUtama,$logG,$logH); $stLog->execute(); $stLog->close(); }
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
    
    <?php else: ?>
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

    <div class="no-print max-w-6xl mx-auto py-4">
    <div class="flex justify-center">
        <div class="w-full max-w-2xl overflow-hidden rounded-[20px] border border-white/10 bg-white shadow-[0px_10px_1px_rgba(221,_221,_221,_1),_0_10px_20px_rgba(204,_204,_204,_1)]">

            <!-- Top bar merah -->
            <div style="height:4px;background:#E24B4A;"></div>

            <!-- Header -->
            <div class="px-7 pt-6 pb-5 border-b border-[#F0EFE9]">
                <!-- Tags -->
                <div class="flex gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#FCEBEB] text-[#791F1F]">
                        <?= htmlspecialchars($statusUtama['jenis']) ?>
                    </span>
                    <?php
                    $tingkatan = $statusUtama['tingkatan'] ?? '';
                    $tingkatan_color = match($tingkatan) {
                        'Parah'  => 'bg-[#FCEBEB] text-[#791F1F] border border-[#F09595]',
                        'Sedang' => 'bg-[#FFF3E0] text-[#854F0B] border border-[#FAC775]',
                        'Ringan' => 'bg-[#EAF3DE] text-[#3B6D11] border border-[#C0DD97]',
                        default  => 'bg-[#F1EFE8] text-[#5F5E5A]'
                    };
                    ?>
                    <?php if ($tingkatan): ?>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full <?= $tingkatan_color ?>">
                        Tingkat <?= htmlspecialchars($tingkatan) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Nama penyakit -->
                <h2 class="text-2xl font-semibold italic text-[#1C1C2E] mb-1.5">
                    <?= htmlspecialchars($statusUtama['nama']) ?>
                </h2>

                <!-- Deskripsi -->
                <?php if (!empty($statusUtama['deskripsi'])): ?>
                <p class="text-sm text-[#9B9AB0] leading-relaxed">
                    <?= htmlspecialchars($statusUtama['deskripsi']) ?>
                </p>
                <?php endif; ?>
            </div>

            <!-- Body: 2 kolom -->
            <div class="grid grid-cols-2 divide-x divide-[#F0EFE9] px-0">

                <!-- Kolom gejala -->
                <div class="px-7 py-5">
                    <p class="text-[11px] font-semibold text-[#9B9AB0] uppercase tracking-widest mb-3">Gejala yang dipilih</p>
                    <ul class="space-y-2.5">
                        <?php foreach ($gejalaDetail as $g):
                            $cocok = in_array($g['nama'], $gejalaCocokUtamaNama);
                        ?>
                        <li class="flex items-start gap-2">
                            <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded mt-0.5 flex-shrink-0 <?= $cocok ? 'bg-[#FCEBEB] text-[#791F1F]' : 'bg-[#F1EFE8] text-[#9B9AB0]' ?>">
                                <?= htmlspecialchars($g['kode']) ?>
                            </span>
                            <span class="text-sm text-[#444441] leading-snug">
                                <?= htmlspecialchars($g['nama']) ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Kolom solusi -->
                <div class="px-7 py-5">
                    <p class="text-[11px] font-semibold text-[#9B9AB0] uppercase tracking-widest mb-3">Solusi penanganan</p>
                    <?php foreach ($statusUtama['solusi'] as $sol): ?>
                        <?php if (!empty($sol['desk'])): ?>
                        <p class="text-sm text-[#444441] leading-7">
                            <?= htmlspecialchars($sol['desk']) ?>
                        </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-7 py-3.5 border-t border-[#F0EFE9]">
                <span class="text-xs text-dark">
                    <?= $tanggal ?>
                    <?php if (!empty($namaPengguna)): ?>
                     · <?= htmlspecialchars($namaPengguna) ?>
                    <?php endif; ?>
                </span>
            </div>

        </div>
    </div>
</div>
    </div>
    <?php endif; ?>
</div>

</body>
<?php layoutFoot(); ?>