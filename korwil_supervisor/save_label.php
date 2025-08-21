<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kurang_hari_kerja = intval($_POST['kurang_hari_kerja']);
    $penagihan = intval($_POST['penagihan']);
    $seminggu = intval($_POST['seminggu']);

    // cek apakah data sudah ada
    $cek = $conn->query("SELECT id FROM label_setting LIMIT 1");

    if ($cek->num_rows > 0) {
        // update baris pertama
        $row = $cek->fetch_assoc();
        $id = $row['id'];
        $sql = "UPDATE label_setting 
                SET kurang_hari_kerja=$kurang_hari_kerja, 
                    penagihan=$penagihan, 
                    seminggu=$seminggu 
                WHERE id=$id";
    } else {
        // insert kalau belum ada
        $sql = "INSERT INTO label_setting (kurang_hari_kerja, penagihan, seminggu) 
                VALUES ($kurang_hari_kerja, $penagihan, $seminggu)";
    }

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}
