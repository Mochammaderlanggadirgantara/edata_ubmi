<?php
session_start();

include '../config/koneksi.php';

$id_kelompok = $_SESSION['id_kelompok'] ?? 0;
$id_cabang = $_SESSION['id_cabang'];
$stmt = $conn->prepare("DELETE FROM catatan_mantri WHERE id_kelompok = ? and id_cabang = ?");
$stmt->bind_param("i", $id_kelompok);

if ($stmt->execute()) {
    echo "sukses";
} else {
    http_response_code(500);
    echo "Gagal menghapus: " . $stmt->error;
}
