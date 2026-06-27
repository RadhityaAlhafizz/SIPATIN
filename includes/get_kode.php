<?php
require_once '../config/database.php';
$conn = getConnection();

$jenis = $_GET['jenis'] ?? '';

$config = [
    'penyakit' => ['prefix' => 'P', 'table' => 'penyakit', 'kolom' => 'kode_penyakit'],
    'gejala'   => ['prefix' => 'G', 'table' => 'gejala',   'kolom' => 'kode_gejala'],
    'solusi'   => ['prefix' => 'S', 'table' => 'solusi',   'kolom' => 'kode_solusi'],
];

if (!isset($config[$jenis])) {
    echo json_encode(['kode' => '']);
    exit;
}

$cfg    = $config[$jenis];
$prefix = $cfg['prefix'];
$table  = $cfg['table'];
$kolom  = $cfg['kolom'];

// Ambil kode terakhir
$res = $conn->query(
    "SELECT $kolom FROM $table 
     WHERE $kolom LIKE '$prefix%' 
     ORDER BY $kolom DESC LIMIT 1"
);

if ($res && $res->num_rows > 0) {
    $last   = $res->fetch_assoc()[$kolom];       // contoh: P005
    $angka  = (int) substr($last, strlen($prefix)); // → 5
    $kode   = $prefix . str_pad($angka + 1, 3, '0', STR_PAD_LEFT); // → P006
} else {
    $kode = $prefix . '001'; // data pertama
}

echo json_encode(['kode' => $kode]);
$conn->close();
?>