<?php
session_start();
header('Content-Type: application/json');
require '../config/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

$id_cabang = $_SESSION['id_cabang']; // patokan id_cabang

$pendapatan_bulan = $_GET['pendapatan_bulan'] ?? '';
$gaji_bulan       = $_GET['gaji_bulan'] ?? '';

if (!$pendapatan_bulan || !$gaji_bulan) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter kurang']);
    exit();
}

// Cek apakah data ada di turpas_master sesuai id_cabang
$sqlMaster = "SELECT * 
              FROM turpas_master 
              WHERE DATE_FORMAT(pendapatan_bulan, '%Y-%m') = ? 
              AND gaji_bulan = ? 
              AND id_cabang = ?";
$stmt = $conn->prepare($sqlMaster);
$stmt->bind_param('ssi', $pendapatan_bulan, $gaji_bulan, $id_cabang);
$stmt->execute();
$resMaster = $stmt->get_result();

if ($resMaster->num_rows === 0) {
    echo json_encode(['status' => 'empty']); // tidak ada data
    exit();
}

$dataMaster = $resMaster->fetch_assoc();

// Ambil detail sesuai id_master & id_cabang
$sqlDetail = "SELECT * 
              FROM turpas_detail 
              WHERE id_master = ? 
              AND id_cabang = ?";
$stmt2 = $conn->prepare($sqlDetail);
$stmt2->bind_param('ii', $dataMaster['id_master'], $id_cabang);
$stmt2->execute();
$resDetail = $stmt2->get_result();

$dataDetail = [];
while ($row = $resDetail->fetch_assoc()) {
    $dataDetail[] = $row;
}

echo json_encode([
    'status' => 'success',
    'master' => $dataMaster,
    'detail' => $dataDetail
]);
