<?php
session_start(); // jangan lupa session_start()

include '../config/koneksi.php';

$id_cabang = $_SESSION['id_cabang']; // selalu dari session login

if (isset($_GET['hari'])) {
    $hari = $_GET['hari'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS jumlah 
        FROM catatan_mantri 
        WHERE hari = ? AND id_cabang = ?
    ");
    $stmt->bind_param("si", $hari, $id_cabang);

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo $data['jumlah'] + 1;
}
