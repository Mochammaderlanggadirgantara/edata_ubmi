<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan = $_POST['bulan'];
    $klp = $_POST['klp'];
    $total_target = $_POST['total_target'];
    $total_cm = $_POST['total_cm'];
    $total_mb = $_POST['total_mb'];
    $total_dropbaru = $_POST['total_dropbaru'];
    $total_tmasuk = $_POST['total_tmasuk'];
    $total_tkeluar = $_POST['total_tkeluar'];
    $total_tjadi = $_POST['total_tjadi'];

    $stmt = $conn->prepare("INSERT INTO total_rekap_target 
        (bulan, klp, total_target, total_cm, total_mb, total_dropbaru, total_tmasuk, total_tkeluar, total_tjadi) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssiiiiiii", $bulan, $klp, $total_target, $total_cm, $total_mb, $total_dropbaru, $total_tmasuk, $total_tkeluar, $total_tjadi);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='data_target_ubmi.php';</script>";
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
