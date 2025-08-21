<?php
session_start();
include '../config/koneksi.php';
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
header('Content-Type: application/json');
$id_cabang = $_SESSION['id_cabang'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klp = $_POST['klp'];
    $kasbon = $_POST['kasbon'];
    $drop_uang = $_POST['drop_uang'];
    $su_lapangan = $_POST['su_lapangan'];
    $transferan = $_POST['transferan'];
    $persen_9 = $_POST['persen_9'];
    $sisa_uang = $_POST['sisa_uang'];
    $tunai = $_POST['tunai'];
    $min_plus = $_POST['min_plus'];

    $stmt = $conn->prepare("UPDATE tunai_babat1 SET kasbon=?, drop_uang=?, su_lapangan=?, transferan=?, persen_9=?, sisa_uang=?, tunai=?, min_plus=? WHERE klp=? AND id_cabang=?");
    $stmt->bind_param("iiiiiiiis", $kasbon, $drop_uang, $su_lapangan, $transferan, $persen_9, $sisa_uang, $tunai, $min_plus, $klp);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Data berhasil diperbarui!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
}
