<?php
include '../config/koneksi.php';

$stmt = $conn->prepare("
    INSERT INTO database_kekuatan_kalkulasi
    (id_kelompok, kekuatan_115, kekuatan_120, kekuatan_125, program, nilai)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiis",
    $_POST['id_kelompok'],
    $_POST['kekuatan_115'],
    $_POST['kekuatan_120'],
    $_POST['kekuatan_125'],
    $_POST['program'],
    $_POST['nilai']
);

if ($stmt->execute()) {
    echo "✅ Data kekuatan kalkulasi berhasil disimpan";
} else {
    echo "❌ Gagal menyimpan data kekuatan kalkulasi: " . $conn->error;
}
