<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek jabatan
if ($_SESSION['jabatan'] !== 'pengawas') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Cek ID
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='alert alert-danger'>ID tidak ditemukan.</div>";
    exit();
}

// Hapus data
$stmt = $conn->prepare("DELETE FROM data_antisipasi_masuk WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: data_antisipasi_masuk.php?status=deleted");
    exit();
} else {
    echo "<div class='alert alert-danger'>Gagal menghapus data.</div>";
}
