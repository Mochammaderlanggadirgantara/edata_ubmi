<?php
session_start();
include '../config/koneksi.php';

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['alert'] = ['type' => 'danger', 'msg' => 'ID tidak ditemukan!'];
    header("Location: data_resort.php");
    exit();
}

$id = intval($_GET['id']);

// Hapus dengan prepared statement
$stmt = $conn->prepare("DELETE FROM targetmantri_babat1 WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Data berhasil dihapus.'];
} else {
    $_SESSION['alert'] = ['type' => 'danger', 'msg' => 'Gagal menghapus data!'];
}

$stmt->close();
$conn->close();

// Redirect balik ke halaman utama
header("Location: data_resort.php");
exit();
?>
