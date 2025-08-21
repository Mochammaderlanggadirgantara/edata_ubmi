<?php
include '../config/koneksi.php';

$nama = $_POST['nama_user'];
$jabatan = $_POST['jabatan'];
$tgl = $_POST['tgl_masuk'];
$username = $_POST['username'];
$password = $_POST['password'];
$id_kelompok = isset($_POST['id_kelompok']) ? $_POST['id_kelompok'] : null;
$id_cabang = isset($_POST['id_cabang']) ? $_POST['id_cabang'] : null;

// Validasi username
$check = mysqli_query($conn, "SELECT id_user FROM tuser WHERE username='$username'");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Username sudah digunakan']);
    exit;
}

if ($jabatan === 'mantri' && !$id_kelompok) {
    echo json_encode(['status' => 'error', 'msg' => 'Kelompok mantri wajib dipilih.']);
    exit;
}

if ($jabatan !== 'mantri') {
    $id_kelompok = null;
}

if (!$id_cabang) {
    echo json_encode(['status' => 'error', 'msg' => 'Cabang wajib dipilih.']);
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO tuser (nama_user, jabatan, tgl_masuk, username, password, id_kelompok, id_cabang, status, tgl_nonaktif)
          VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif', NULL)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'sssssii', $nama, $jabatan, $tgl, $username, $hashed, $id_kelompok, $id_cabang);

if (mysqli_stmt_execute($stmt)) {
    $new_id = mysqli_insert_id($conn);
    echo json_encode(['status' => 'success', 'id' => $new_id]);
} else {
    echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]);
}
