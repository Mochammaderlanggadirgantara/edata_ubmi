<?php
include '../config/koneksi.php';

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$kelompok = $_GET['kelompok'] ?? '';
$hari = $_GET['hari'] ?? '';
$minggu = $_GET['minggu'] ?? '';

$t_jadi_prev = 0;

if ($bulan && $tahun && $kelompok && $hari && $minggu) {
    $stmt = $conn->prepare("SELECT t_jadi FROM targetmantri_babat1 
        WHERE bulan=? AND tahun=? AND kelompok=? AND minggu=? AND hari=? 
        ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("sisss", $bulan, $tahun, $kelompok, $minggu, $hari);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $t_jadi_prev = $row['t_jadi'];
    }
    $stmt->close();
}

echo $t_jadi_prev;
$conn->close();
