<?php
include '../config/koneksi.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Cek apakah kelompok sedang digunakan oleh user mantri
    $cek = $conn->prepare("SELECT id_user FROM tuser WHERE id_kelompok = ?");
    $cek->bind_param('i', $id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'msg' => 'Kelompok ini masih digunakan oleh user mantri dan tidak bisa dihapus.'
        ]);
        exit;
    }

    // Lanjutkan hapus
    $query = "DELETE FROM kelompok_mantri WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus kelompok.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'msg' => 'ID tidak ditemukan.']);
}
