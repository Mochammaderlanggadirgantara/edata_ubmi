<?php
session_start();
include '../config/koneksi.php';

$id         = $_GET['id'];
$id_kelompok = $_SESSION['id_kelompok'] ?? 0;
$id_cabang   = $_SESSION['id_cabang']; // selalu dari session login

// Pastikan data yang ingin dihapus milik kelompok & cabang yang sedang login
$hari = '';
$stmt = $conn->prepare("SELECT hari FROM catatan_mantri WHERE id = ? AND id_kelompok = ? AND id_cabang = ?");
$stmt->bind_param("iii", $id, $id_kelompok, $id_cabang); // <-- sebelumnya salah (cuma 2)
$stmt->execute();
$stmt->bind_result($hari);

if (!$stmt->fetch()) {
    // Data tidak ditemukan atau bukan milik kelompok ini
    echo "tidak_berhak";
    exit;
}
$stmt->close();

// Lanjut hapus
$stmt = $conn->prepare("DELETE FROM catatan_mantri WHERE id = ? AND id_kelompok = ? AND id_cabang = ?");
$stmt->bind_param("iii", $id, $id_kelompok, $id_cabang); // <-- tambahkan id_cabang
if ($stmt->execute()) {
    reorder_urutan($conn, $hari, $id_kelompok, $id_cabang);
    echo "sukses";
} else {
    echo "gagal";
}

// Fungsi untuk merapikan ulang urutan
function reorder_urutan($conn, $hari, $id_kelompok, $id_cabang)
{
    $sql = "SELECT id FROM catatan_mantri WHERE hari = ? AND id_kelompok = ? AND id_cabang = ? ORDER BY urutan ASC, id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $hari, $id_kelompok, $id_cabang);
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
