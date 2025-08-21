<?php
include "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $minggu = $_POST['minggu'];
    $hari = $_POST['hari'];
    $kelompok = $_POST['kelompok'];
    $target = $_POST['target'];
    $rencana = $_POST['rencana'];
    $storting = $_POST['storting'];
    $storting_tg = $_POST['storting_tg'];
    $min_plus = $_POST['min_plus'];

    $sql = "INSERT INTO data_statistik_leader (minggu,hari,kelompok,target,rencana,storting,storting_tg,min_plus)
            VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiiiii", $minggu, $hari, $kelompok, $target, $rencana, $storting, $storting_tg, $min_plus);

    if ($stmt->execute()) {
        header("Location: data_statistik.php?minggu=$minggu");
    } else {
        echo "Error: " . $conn->error;
    }
}
