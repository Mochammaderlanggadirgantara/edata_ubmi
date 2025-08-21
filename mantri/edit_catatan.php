<?php
session_start();
include '../config/koneksi.php';

$id_kelompok = $_SESSION['id_kelompok'] ?? 0;
$id_cabang   = $_SESSION['id_cabang'] ?? 0; // selalu dari session login

$id         = $_POST['id'];
$hari       = $_POST['hari'];
$urutan     = $_POST['urutan'];
$nama       = $_POST['nama'];
$alamat     = $_POST['alamat'];
$pinjaman   = $_POST['pinjaman'];
$sisa_saldo = $_POST['sisa_saldo'];
$selesai    = $_POST['selesai'];

// 🔹 Cek apakah data milik kelompok & cabang yang login
$check = $conn->prepare("SELECT id FROM catatan_mantri WHERE id = ? AND id_kelompok = ? AND id_cabang = ?");
$check->bind_param("iii", $id, $id_kelompok, $id_cabang);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo "tidak_berhak";
    exit;
}
$check->close();

// 🔹 Lanjut update
$stmt = $conn->prepare("UPDATE catatan_mantri 
    SET hari = ?, urutan = ?, nama = ?, alamat = ?, pinjaman = ?, sisa_saldo = ?, selesai = ? 
    WHERE id = ? AND id_kelompok = ? AND id_cabang = ?");
$stmt->bind_param(
    "sissdiiiii", // s=string, i=int, d=double (untuk angka bisa pakai i/d sesuai kebutuhan)
    $hari,
    $urutan,
    $nama,
    $alamat,
    $pinjaman,
    $sisa_saldo,
    $selesai,
    $id,
    $id_kelompok,
    $id_cabang
);

if ($stmt->execute()) {
    echo "sukses";
} else {
    echo "gagal: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
