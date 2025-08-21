<?php
include '../config/koneksi.php';

$id_kelompok = $_GET['id_kelompok'] ?? '';

if ($id_kelompok) {
    $stmt = $conn->prepare("
        SELECT total
        FROM rekap_bulan_ini
        WHERE id_kelompok = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_kelompok);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "total"  => $row['total']
        ]);
    } else {
        echo json_encode(["status" => "not_found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Parameter kurang"]);
}
