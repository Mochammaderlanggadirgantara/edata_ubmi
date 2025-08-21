<?php
include 'config/koneksi.php';
$id = $_GET['id'];
$conn->query("DELETE FROM index_bulanan WHERE id=$id");
header("Location: index.php");
?>
