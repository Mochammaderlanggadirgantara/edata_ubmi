<?php
include '../config/koneksi.php';

// Ambil input kelompok
$kelompok = $_POST['kelompok'] ?? '';
$id_kelompok = (int) filter_var($kelompok, FILTER_SANITIZE_NUMBER_INT);
// misal "Kelompok 1" -> ambil "1"

$stmt = $conn->prepare("
    INSERT INTO database_tabel_utama_kalkulasi_baru
    (bulan, tahun, id_kelompok, kelompok, t_jadi, cm, mb)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "siisiii",
    $_POST['bulan'],
    $_POST['tahun'],
    $id_kelompok,
    $kelompok,
    $_POST['t_jadi'],
    $_POST['cm'],
    $_POST['mb']
);

if ($stmt->execute()) {
    header("Location: http://localhost/edata_ubmi/korwil_supervisor/create_kalkulasi.php?status=success");
    exit();
} else {
    echo "❌ Gagal menyimpan data utama: " . $conn->error;
}
