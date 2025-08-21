<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Role hanya untuk Pengawas
$allowed_roles = ['pengawas'];
if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = (int)$_GET['id'];

// Hapus data
$sql = "DELETE FROM pelunasan9 WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
if (mysqli_stmt_execute($stmt)) {
    header("Location: pelunasan.php?msg=deleted");
    exit();
} else {
    echo "<div class='alert alert-danger'>Gagal menghapus data: " . mysqli_error($conn) . "</div>";
}
mysqli_stmt_close($stmt);
