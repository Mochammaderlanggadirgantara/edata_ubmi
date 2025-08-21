<?php
session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang = $_SESSION['id_cabang'];
include '../config/koneksi.php';
// dari tabel users
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $minggu = $_POST['minggu'];
    $hari = $_POST['hari'];
    $target = $_POST['target'];
    $rencana = $_POST['rencana'];
    $storting = $_POST['storting'];
    $storting_tg = $_POST['storting_tg'];
    $min_plus = $_POST['min_plus'];

    for ($i = 0; $i < count($minggu); $i++) {
        $mingguVal = intval($minggu[$i]);
        $hariVal = mysqli_real_escape_string($conn, $hari[$i]);
        $targetVal = intval(str_replace('.', '', $target[$i]));
        $rencanaVal = intval(str_replace('.', '', $rencana[$i]));
        $stortingVal = intval(str_replace('.', '', $storting[$i]));
        $stortingTGVal = intval(str_replace('.', '', $storting_tg[$i]));
        $minPlusVal = intval(str_replace('.', '', $min_plus[$i]));

        $cek = mysqli_query($conn, "SELECT id FROM data_statistik WHERE minggu='$mingguVal' AND hari='$hariVal' AND id_kelompok='$id_kelompok' and id_cabang = '$id_cabang'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE data_statistik SET 
        target='$targetVal',
        rencana='$rencanaVal',
        storting='$stortingVal',
        storting_tg='$stortingTGVal',
        min_plus='$minPlusVal'
        WHERE minggu='$mingguVal' AND hari='$hariVal' AND id_kelompok='$id_kelompok' and id_cabang='$id_cabang'");
        } else {
            mysqli_query($conn, "INSERT INTO data_statistik 
        (id_kelompok, minggu, hari, target, rencana, storting, storting_tg, min_plus, id_cabang) VALUES 
        ('$id_kelompok', '$mingguVal', '$hariVal', '$targetVal', '$rencanaVal', '$stortingVal', '$stortingTGVal', '$minPlusVal', '$id_cabang')");
        }
    }

    http_response_code(200); // optional
    echo "OK"; // untuk menandakan berhasil
    exit;
}
