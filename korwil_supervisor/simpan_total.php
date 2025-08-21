<?php
include '../config/koneksi.php';

$stmt = $conn->prepare("
    INSERT INTO database_total_penjumlahan_kalkulasi
    (id_kelompok, target_kalkulasi, jumlah_target, pelunasan, jumlah_pelunasan,
     baru, jumlah_baru, storting_jl, jumlah_storting_jl, storting_jd, jumlah_storting_jd,
     other, jumlah_other)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiiiiiiiiii",
    $_POST['id_kelompok'],
    $_POST['target_kalkulasi'],
    $_POST['jumlah_target'],
    $_POST['pelunasan'],
    $_POST['jumlah_pelunasan'],
    $_POST['baru'],
    $_POST['jumlah_baru'],
    $_POST['storting_jl'],
    $_POST['jumlah_storting_jl'],
    $_POST['storting_jd'],
    $_POST['jumlah_storting_jd'],
    $_POST['other'],
    $_POST['jumlah_other']
);

if ($stmt->execute()) {
    echo "✅ Data total penjumlahan berhasil disimpan";
} else {
    echo "❌ Gagal menyimpan data total penjumlahan: " . $conn->error;
}
