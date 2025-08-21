<?php
include '../config/koneksi.php';
$id = $_GET['id'];

$conn->query("DELETE FROM data_resort WHERE id=$id");

header("Location: index.php");
