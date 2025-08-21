<?php
include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM label_setting WHERE id='$id'");

header("Location: create_kalkulasi.php");
exit;
