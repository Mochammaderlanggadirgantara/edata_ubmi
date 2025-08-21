<?php
session_start();
include '../config/koneksi.php';; // dari tabel users
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID berupa integer

    // Hapus data dari database
    $query = "DELETE FROM riwayat_servis WHERE id = $id AND id_cabang=?";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Redirect kembali ke halaman index setelah berhasil
        header("Location: servis_inventaris.php?status=sukses_hapus");
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan.";
}
