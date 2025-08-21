<?php
include '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM sandi WHERE id_sandi=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: read_sandi.php");
exit();
