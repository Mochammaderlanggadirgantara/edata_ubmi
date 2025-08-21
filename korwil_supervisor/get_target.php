<?php
include '../config/koneksi.php';

$kelompok = $_POST['kelompok'];
$bulan    = $_POST['bulan'];
$tahun    = $_POST['tahun'];
$hari     = $_POST['hari'];

$query = mysqli_query($conn, "SELECT target, cm, mb FROM target_ubmi 
    WHERE kelompok='$kelompok' AND bulan='$bulan' AND tahun='$tahun' AND hari='$hari' LIMIT 1");

if (mysqli_num_rows($query) > 0) {
    echo json_encode(mysqli_fetch_assoc($query));
} else {
    echo json_encode(null);
}
