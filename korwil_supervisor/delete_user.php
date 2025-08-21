<?php
include '../config/koneksi.php'; // sesuaikan dengan lokasi file koneksi kamu

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data user
    $stmt = $conn->prepare("DELETE FROM TUser WHERE id_user = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil dihapus'); window.location.href='data_karyawan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID tidak ditemukan'); window.history.back();</script>";
}
