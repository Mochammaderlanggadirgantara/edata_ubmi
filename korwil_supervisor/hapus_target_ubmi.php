<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/edata_ubmi/config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Cek apakah data dengan ID tersebut ada
$cek = $conn->prepare("SELECT id FROM target_ubmi WHERE id = ?");
$cek->bind_param("i", $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit;
}
$cek->close();

// Hapus data
$stmt = $conn->prepare("DELETE FROM target_ubmi WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect kembali ke halaman utama
    header("Location: data_target_ubmi.php?pesan=hapus_berhasil");
    exit;
} else {
    echo "Gagal menghapus data.";
}
