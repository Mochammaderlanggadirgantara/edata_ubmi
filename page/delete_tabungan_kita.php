<?php
include '../config/koneksi.php';
include '../page/function_tabungan_kita.php';
$id = $_GET['id'];
$conn->query("DELETE FROM tabungan WHERE id=$id");
// Recalculate saldo setelah hapus
recalculateSaldo($conn);
header("Location: tabungan_kita.php");
exit;
