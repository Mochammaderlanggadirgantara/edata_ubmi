<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$conn->query("DELETE FROM rekap_saldo WHERE id=$id");
header("Location: rekap_saldo.php");
?>
