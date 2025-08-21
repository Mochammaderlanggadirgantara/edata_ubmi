<?php
include '../config/koneksi.php';

$stmt = $conn->prepare("
    INSERT INTO database_index_kalkulasi
    (id_kelompok, t_storting_100, plus_minus_100, t_storting_115, plus_minus_115,
     t_storting_120, plus_minus_120)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiiii",
    $_POST['id_kelompok'],
    $_POST['t_storting_100'],
    $_POST['plus_minus_100'],
    $_POST['t_storting_115'],
    $_POST['plus_minus_115'],
    $_POST['t_storting_120'],
    $_POST['plus_minus_120']
);

if ($stmt->execute()) {
    echo "✅ Data index kalkulasi berhasil disimpan";
} else {
    echo "❌ Gagal menyimpan data index kalkulasi: " . $conn->error;
}
