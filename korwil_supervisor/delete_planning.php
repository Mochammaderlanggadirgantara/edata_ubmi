<?php
include '../config/koneksi.php';

// Cek parameter
if (isset($_GET['tabel']) && isset($_GET['id'])) {
    $tabel = $_GET['tabel'];
    $id    = (int)$_GET['id']; // pastikan ID berupa angka

    // Query delete dengan prepared statement
    $stmt = $conn->prepare("DELETE FROM `$tabel` WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Balik ke halaman utama tabel terkait
        header("Location: planning_resort.php?tabel=$tabel&msg=deleted");
        exit;
    } else {
        echo "Error menghapus data: " . $conn->error;
    }
} else {
    echo "Parameter tidak lengkap!";
}
?>
