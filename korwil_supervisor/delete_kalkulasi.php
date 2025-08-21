
<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$conn->query("DELETE FROM kalkulasi_km WHERE id=$id");
header("Location: read_kalkulasi.php");
exit();
?>
