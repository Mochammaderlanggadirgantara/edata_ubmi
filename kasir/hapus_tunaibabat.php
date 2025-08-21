<?php
session_start();
include '../config/koneksi.php';

$response = ['success' => false, 'message' => ''];
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
if (isset($_GET['klp'])) {
    $klp = $_GET['klp'];
    $stmt = $conn->prepare("DELETE FROM tunai_babat1 WHERE klp = ? AND id_cabang=?");
    $stmt->bind_param("s", $klp);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'KLP tidak ditemukan.';
}

header('Content-Type: application/json');
echo json_encode($response);
