<?php
session_start();
include "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $key      = $_POST['update'];
    $minggu   = $_GET['minggu'] ?? 1;

    $hari     = $_POST['hari'][$key];
    $kelompok = $_POST['kelompok'][$key];
    $bulan    = $_POST['bulan'][$key]; // ⬅️ tambahin bulan
    $target   = $_POST['target'][$key] ?? 0;
    $rencana  = $_POST['rencana'][$key] ?? 0;
    $storting = $_POST['storting'][$key] ?? 0;

    // Hitung ulang
    $storting_tg = ($rencana * 0.26) + $target;
    $min_plus    = $storting - $storting_tg;

    // Cek apakah $key itu id numeric (update) atau string gabungan (insert baru)
    if (is_numeric($key)) {
        // UPDATE
        $sql = "UPDATE data_statistik_leader 
                   SET rencana=?, storting=?, storting_tg=?, min_plus=?, bulan=? 
                 WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiii", $rencana, $storting, $storting_tg, $min_plus, $bulan, $key);
    } else {
        // INSERT
        $sql = "INSERT INTO data_statistik_leader
                   (hari, kelompok, bulan, minggu, target, rencana, storting, storting_tg, min_plus)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiiiii", $hari, $kelompok, $bulan, $minggu, $target, $rencana, $storting, $storting_tg, $min_plus);
    }

    if ($stmt->execute()) {
        header("Location: data_statistik.php?minggu=$minggu&status=success");
        exit;
    } else {
        echo "❌ Gagal update data: " . $conn->error;
    }
}
