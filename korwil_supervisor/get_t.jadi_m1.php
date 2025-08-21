<?php
include '../config/koneksi.php';

$bulan    = $_GET['bulan'] ?? '';
$tahun    = $_GET['tahun'] ?? '';
$kelompok = $_GET['kelompok'] ?? '';
$hari     = $_GET['hari'] ?? '';

if ($bulan && $tahun && $kelompok && $hari) {
    $stmt = $conn->prepare("SELECT t_jadi FROM targetmantri_babat1 
                             WHERE bulan=? AND tahun=? AND kelompok=? AND hari=? AND minggu=1 
                             LIMIT 1");
    // ✅ semua pakai string (ssss)
    $stmt->bind_param("ssss", $bulan, $tahun, $kelompok, $hari);
    $stmt->execute();
    $stmt->bind_result($tjadi);
    if ($stmt->fetch()) {
        echo $tjadi;  // misal 1890
    } else {
        echo 0;       // kalau tidak ada data
    }
    $stmt->close();
} else {
    echo 0;
}
