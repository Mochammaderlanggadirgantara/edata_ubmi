<?php
include '../config/koneksi.php';

$id       = $_POST['id'];
$kelompok = $_POST['kelompok'];
$hari     = $_POST['hari'];
$target   = $_POST['target'];
$cm       = $_POST['cm'];
$mb       = $_POST['mb'];

$stmt = $conn->prepare("UPDATE data_resort SET kelompok=?, hari=?, target=?, cm=?, mb=? WHERE id=?");
$stmt->bind_param("isiiii", $kelompok, $hari, $target, $cm, $mb, $id);
$stmt->execute();

header("Location: index.php");
