<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_cabang'])) {
    echo json_encode(["status" => "error", "message" => "Cabang tidak ditemukan."]);
    exit();
}

$id_cabang = $_SESSION['id_cabang'];

$stmt = $conn->prepare("DELETE FROM kalkulasi_kas WHERE id_cabang = ?");
$stmt->bind_param("i", $id_cabang);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data berhasil dihapus."]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus data."]);
}

$stmt->close();
$conn->close();
