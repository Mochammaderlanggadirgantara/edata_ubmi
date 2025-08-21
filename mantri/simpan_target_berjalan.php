<?php
include '../config/koneksi.php';
session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang = $_SESSION['id_cabang'];

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['data'])) {
    http_response_code(400);
    echo "Data tidak valid!";
    exit;
}

$sukses = true;

foreach ($data['data'] as $item) {
    $minggu = intval($item['minggu']);
    $hari = mysqli_real_escape_string($conn, $item['hari']);
    $target = intval($item['target']);
    $drop = intval($item['drop']);
    $masuk = intval($item['masuk']);
    $keluar = intval($item['keluar']);
    $jadi = intval($item['jadi']);

    // Cek apakah data sudah ada di database
    $cek_query = "SELECT id FROM target_berjalan 
              WHERE id_kelompok = '$id_kelompok' AND minggu = '$minggu' AND hari = '$hari' and id_cabang = '$id_cabang'";
    $cek_result = mysqli_query($conn, $cek_query);

    if (mysqli_num_rows($cek_result) > 0) {
        // Data ada -> update
        $update_query = "UPDATE target_berjalan 
                 SET target = '$target',
                     drop_baru = '$drop',
                     t_masuk = '$masuk',
                     t_keluar = '$keluar',
                     t_jadi = '$jadi'
                 WHERE id_kelompok = '$id_kelompok' AND minggu = '$minggu' AND hari = '$hari' and id_cabang = '$id_cabang'";
        if (!mysqli_query($conn, $update_query)) {
            $sukses = false;
            break;
        }
    } else {
        // Data tidak ada -> insert
        $insert_query = "INSERT INTO target_berjalan 
                 (id_kelompok, minggu, hari, target, drop_baru, t_masuk, t_keluar, t_jadi, id_cabang)
                 VALUES ('$id_kelompok', '$minggu', '$hari', '$target', '$drop', '$masuk', '$keluar', '$jadi', '$id_cabang')";
        if (!mysqli_query($conn, $insert_query)) {
            $sukses = false;
            break;
        }
    }
}

if ($sukses) {
    echo "Data berhasil disimpan atau diperbarui.";
} else {
    http_response_code(500);
    echo "Gagal menyimpan atau memperbarui data.";
}
