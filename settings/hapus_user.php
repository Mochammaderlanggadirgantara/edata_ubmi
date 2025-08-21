<?php
include '../config/koneksi.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $query = "DELETE FROM tuser WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus user.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'msg' => 'ID tidak ditemukan.']);
}
?>
