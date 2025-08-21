<?php
include '../config/koneksi.php';

$id         = $_POST['id_user'];
$nama       = $_POST['nama_user'];
$jabatan    = $_POST['jabatan'];
$tgl        = $_POST['tgl_masuk'];
$username   = $_POST['username'];
$password   = isset($_POST['password']) ? $_POST['password'] : '';
$id_kelompok = isset($_POST['id_kelompok']) && $jabatan === 'mantri' ? $_POST['id_kelompok'] : null;
$id_cabang = isset($_POST['id_cabang']) ? $_POST['id_cabang'] : null;
$status = $_POST['status'];
$tgl_nonaktif = ($status === 'tidak aktif') ? date('Y-m-d') : null;

// Validasi tanggal tidak boleh di masa depan
if (strtotime($tgl) > strtotime(date('Y-m-d'))) {
    echo json_encode(['status' => 'error', 'msg' => 'Tanggal masuk tidak boleh melebihi hari ini.']);
    exit;
}

// Validasi kelompok jika jabatan mantri
if ($jabatan === 'mantri' && empty($id_kelompok)) {
    echo json_encode(['status' => 'error', 'msg' => 'Kelompok wajib dipilih untuk jabatan Mantri.']);
    exit;
}

if (!$id_cabang) {
    echo json_encode(['status' => 'error', 'msg' => 'Cabang wajib dipilih.']);
    exit;
}

// Cek duplikat username, selain dari user yang sedang diedit
$cekQuery = $conn->prepare("SELECT id_user FROM tuser WHERE username = ? AND id_user != ?");
$cekQuery->bind_param("si", $username, $id);
$cekQuery->execute();
$cekQuery->store_result();

if ($cekQuery->num_rows > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Username sudah digunakan oleh user lain.']);
    exit;
}
$cekQuery->close();

// Proses update
if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = $conn->prepare("UPDATE tuser 
    SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, password=?, id_kelompok=?, id_cabang=?, status=?, tgl_nonaktif=? 
    WHERE id_user=?");
    $query->bind_param("sssssiissi", $nama, $jabatan, $tgl, $username, $hashed, $id_kelompok, $id_cabang, $status, $tgl_nonaktif, $id);
} else {
    $query = $conn->prepare("UPDATE tuser 
    SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, id_kelompok=?, id_cabang=?, status=?, tgl_nonaktif=? 
    WHERE id_user=?");
    $query->bind_param("ssssiissi", $nama, $jabatan, $tgl, $username, $id_kelompok, $id_cabang, $status, $tgl_nonaktif, $id);
}

if ($query->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $conn->error]);
}
