<?php
include '../config/koneksi.php';

$bulan    = $_GET['bulan'] ?? '';
$tahun    = $_GET['tahun'] ?? '';
$kelompok = $_GET['kelompok'] ?? '';

if ($bulan && $tahun && $kelompok) {
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(t_jadi),0) AS total_t_jadi,
            COALESCE(SUM(cm),0) AS total_cm,
            COALESCE(SUM(mb),0) AS total_mb
        FROM target_ubmi
        WHERE bulan = ?
          AND tahun = ?
          AND kelompok = ?
    ");
    $stmt->bind_param("sis", $bulan, $tahun, $kelompok);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "total_t_jadi" => $data['total_t_jadi'],
        "total_cm"     => $data['total_cm'],
        "total_mb"     => $data['total_mb']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Parameter kurang"
    ]);
}
