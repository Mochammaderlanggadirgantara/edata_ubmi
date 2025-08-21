<?php
include '../config/koneksi.php';

$nama_kelompok = trim($_POST['nama_kelompok']);

// Validasi kosong
if (empty($nama_kelompok)) {
    echo json_encode(['status' => 'error', 'msg' => 'Nama kelompok tidak boleh kosong.']);
    exit;
}

// Cek duplikat
$check = mysqli_prepare($conn, "SELECT id FROM kelompok_mantri WHERE nama_kelompok = ?");
mysqli_stmt_bind_param($check, 's', $nama_kelompok);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Nama kelompok sudah terdaftar.']);
    exit;
}

// Simpan ke DB
$query = "INSERT INTO kelompok_mantri (nama_kelompok) VALUES (?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $nama_kelompok);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]);
}
