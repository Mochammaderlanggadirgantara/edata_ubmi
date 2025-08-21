<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$conn->query("DELETE FROM laporan_km WHERE id=$id");
header("Location: laporan_km.php");
