<?php
include '../config/koneksi.php';

$id = $_GET['id'] ?? 0;

if ($id) {
    mysqli_query($conn, "DELETE FROM drop_baru WHERE id=$id");
}

header("Location: ../korwil_supervisor/drop_baru.php");
exit;
