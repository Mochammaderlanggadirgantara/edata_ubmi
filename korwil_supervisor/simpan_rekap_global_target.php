<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan = $_POST['bulan'];
    $hari = $_POST['hari'];
    $total_target = $_POST['total_target'];
    $total_cm = $_POST['total_cm'];
    $total_mb = $_POST['total_mb'];
    $total_dropbaru = $_POST['total_dropbaru'];
    $total_tmasuk = $_POST['total_tmasuk'];
    $total_tkeluar = $_POST['total_tkeluar'];
    $total_tjadi = $_POST['total_tjadi'];

    $stmt = $conn->prepare("INSERT INTO rekap_global_target_ubmi 
        (bulan, hari, total_target, total_cm, total_mb, total_dropbaru, total_tmasuk, total_tkeluar, total_tjadi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($hari); $i++) {
        $stmt->bind_param(
            "ssiiiiidd",
            $bulan,
            $hari[$i],
            $total_target[$i],
            $total_cm[$i],
            $total_mb[$i],
            $total_dropbaru[$i],
            $total_tmasuk[$i],
            $total_tkeluar[$i],
            $total_tjadi[$i]
        );
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo "<script>alert('Data rekap berhasil disimpan!'); window.location.href='data_target_ubmi.php';</script>";
}
