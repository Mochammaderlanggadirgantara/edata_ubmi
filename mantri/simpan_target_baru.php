<?php
include '../config/koneksi.php';

session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang = $_SESSION['id_cabang'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_target = $_POST['id_target'];
    $target_diinginkan = $_POST['target_diinginkan'];
    $plening = $_POST['plening'];

    foreach ($id_target as $i => $id) {
        $id = (int) $id;
        $diinginkan = str_replace('.', '', $target_diinginkan[$i]);
        $pleningVal = str_replace('.', '', $plening[$i]);

        $check = mysqli_query($conn, "SELECT * FROM target_baru WHERE id_target = '$id' AND id_kelompok = '$id_kelompok' and id_cabang = '$id_cabang'");

        if (mysqli_num_rows($check) > 0) {
            // Update
            $update = mysqli_query($conn, "UPDATE target_baru SET 
    target_diinginkan = '$diinginkan', 
    plening = '$pleningVal' 
    WHERE id_target = '$id' AND id_kelompok = '$id_kelompok' and id_cabang = '$id_cabang'");
        } else {
            // Insert
            $insert = mysqli_query($conn, "INSERT INTO target_baru (id_target, target_diinginkan, plening, id_kelompok, id_cabang) 
    VALUES ('$id', '$diinginkan', '$pleningVal', '$id_kelompok', '$id_cabang')");
        }
    }

    echo json_encode(["status" => "success"]);
}
