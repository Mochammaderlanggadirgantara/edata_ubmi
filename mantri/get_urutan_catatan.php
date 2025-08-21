<?php
session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang   = $_SESSION['id_cabang'];

include '../config/koneksi.php';

if (isset($_GET['hari'])) {
    $hari = $_GET['hari'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS jumlah 
        FROM catatan_mantri 
        WHERE hari = ? AND id_kelompok = ? AND id_cabang = ?
    ");
    $stmt->bind_param("sii", $hari, $id_kelompok, $id_cabang);

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo $data['jumlah'] + 1;
}
