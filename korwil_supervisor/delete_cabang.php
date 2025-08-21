<?php
include '../config/koneksi.php';

if (isset($_GET['id_cabang'])) {
    $id = $_GET['id_cabang'];
    $query = "DELETE FROM cabang WHERE id_cabang = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: kelola_cabang.php?success=1");
        exit();
    } else {
        echo "Gagal menghapus data: " . $conn->error;
    }
}
