<?php
include '../config/koneksi.php';
session_start();

$id_kelompok = $_SESSION['id_kelompok'];
$data = json_decode(file_get_contents("php://input"), true);
$id_cabang = $_SESSION['id_cabang'];
foreach ($data as $row) {
    $hari = mysqli_real_escape_string($conn, $row['hari']);
    $target = floatval(str_replace('.', '', $row['target']));
    $cm = floatval(str_replace('.', '', $row['cm']));
    $mb = floatval(str_replace('.', '', $row['mb']));

    $sql = "INSERT INTO data_resort_km (id_kelompok, hari, target, cm, mb)
            VALUES ('$id_kelompok', '$hari', $target, $cm, $mb, $id_cabang)
            ON DUPLICATE KEY UPDATE
                target = VALUES(target),
                cm = VALUES(cm),
                mb = VALUES(mb)";

    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
}

echo "Data berhasil disimpan atau diperbarui.";
