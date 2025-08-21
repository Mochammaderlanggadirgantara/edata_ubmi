<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id_kelompok'], $_SESSION['id_cabang'])) {
        http_response_code(403);
        echo "Akses ditolak.";
        exit;
    }

    $id_kelompok = $_SESSION['id_kelompok'];
    $id_cabang   = $_SESSION['id_cabang'];

    $stmt = $conn->prepare("DELETE FROM data_statistik WHERE id_kelompok = ? AND id_cabang = ?");
    $stmt->bind_param("ii", $id_kelompok, $id_cabang);

    if ($stmt->execute()) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "Gagal menghapus data.";
    }
    $stmt->close();
}
