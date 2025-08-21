<?php
include '../config/koneksi.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nama_kelompok = trim($_POST['nama_kelompok']);

// Validasi
if (empty($id) || empty($nama_kelompok)) {
    echo json_encode(['status' => 'error', 'msg' => 'ID dan nama kelompok tidak boleh kosong.']);
    exit;
}

// Cek duplikat nama kelompok selain yang sedang diedit
$cek = $conn->prepare("SELECT id FROM kelompok_mantri WHERE nama_kelompok = ? AND id != ?");
$cek->bind_param("si", $nama_kelompok, $id);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Nama kelompok sudah digunakan.']);
    exit;
}
$cek->close();

// Update data
$query = $conn->prepare("UPDATE kelompok_mantri SET nama_kelompok = ? WHERE id = ?");
$query->bind_param("si", $nama_kelompok, $id);

if ($query->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $conn->error]);
}
