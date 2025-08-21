<?php
include '../config/koneksi.php';

$kelompok = $_POST['kelompok'];
$hari     = $_POST['hari'];
$target   = $_POST['target'];
$cm       = $_POST['cm'];
$mb       = $_POST['mb'];

$stmt = $conn->prepare("INSERT INTO data_resort (kelompok,hari,target,cm,mb) VALUES (?,?,?,?,?)");
$stmt->bind_param("isiii", $kelompok, $hari, $target, $cm, $mb);
$stmt->execute();

header("Location: data_resort.php");
