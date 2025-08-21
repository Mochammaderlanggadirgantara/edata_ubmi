<?php
include '../config/koneksi.php';
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $query = "DELETE FROM nasabah WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Jika berhasil
        header("Location: database_nasabah.php?success=1");
        exit();
    } else {
        // Jika gagal hapus
        header("Location: database_nasabah.php?error=1");
        exit();
    }

    $stmt->close();
} else {
    // Jika ID tidak ada di URL
    header("Location: database_nasabah.php?error=1");
    exit();
}
