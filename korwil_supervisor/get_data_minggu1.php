<?php
include 'config.php'; // koneksi

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$hari = $_GET['hari'] ?? '';
$kelompok = $_GET['kelompok'] ?? '';

if ($bulan && $tahun && $hari && $kelompok) {
    $sql = "SELECT target, cm, mb FROM target_ubmi 
            WHERE bulan=? AND tahun=? AND hari=? AND kelompok=? AND minggu=1
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $bulan, $tahun, $hari, $kelompok);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode($result ?: []);
}
