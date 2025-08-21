<?php
session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang = $_SESSION['id_cabang'];
include '../config/koneksi.php';
// dari tabel users
$hari = $_POST['hari'] ?? '';
$urutan = $_POST['urutan'] ?? '';
$nama = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$pinjaman = $_POST['pinjaman'] ?? '';
$sisa_saldo = $_POST['sisa_saldo'] ?? '';
$selesai = $_POST['selesai'] ?? '';

$stmt = $conn->prepare("INSERT INTO catatan_mantri 
(hari, urutan, nama, alamat, pinjaman, sisa_saldo, selesai, id_kelompok, id_cabang) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("sisssiiii", $hari, $urutan, $nama, $alamat, $pinjaman, $sisa_saldo, $selesai, $id_kelompok, $id_cabang);

if ($stmt->execute()) {
    echo "sukses";
} else {
    http_response_code(500);
    echo "Gagal menyimpan: " . $stmt->error;
}

// Fungsi untuk merapikan ulang urutan berdasarkan hari
function reorder_urutan($conn, $hari, $id_kelompok)
{
    $sql = "SELECT id FROM catatan_mantri 
            WHERE hari = ? AND id_kelompok = ? 
            ORDER BY urutan ASC, id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hari, $id_kelompok);
    $stmt->execute();
    $result = $stmt->get_result();

    $urutan = 1;
    while ($row = $result->fetch_assoc()) {
        $update = $conn->prepare("UPDATE catatan_mantri SET urutan = ? WHERE id = ?");
        $update->bind_param("ii", $urutan, $row['id']);
        $update->execute();
        $urutan++;
    }
}
