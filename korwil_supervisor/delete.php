<?php
include '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM TUser WHERE id_user = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
?>