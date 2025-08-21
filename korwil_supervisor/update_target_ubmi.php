<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/edata_ubmi/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = intval($_POST['id']);
    $kelompok   = $_POST['kelompok'];
    $bulan      = $_POST['bulan'];
    $tahun      = intval($_POST['tahun']);
    $minggu     = $_POST['minggu'];
    $hari       = $_POST['hari'];
    $target     = intval($_POST['target']);
    $cm         = intval($_POST['cm']);
    $mb         = intval($_POST['mb']);
    $drop_baru  = floatval($_POST['drop_baru']);
    $t_keluar   = floatval($_POST['t_keluar']);

    $t_masuk    = $drop_baru * 0.13;
    $t_jadi     = $target + $t_masuk - $t_keluar;

    $query = $conn->prepare("UPDATE target_ubmi SET 
        kelompok = ?, bulan = ?, tahun = ?, minggu = ?, hari = ?, 
        target = ?, cm = ?, mb = ?, drop_baru = ?, t_masuk = ?, t_keluar = ?, t_jadi = ?
        WHERE id = ?");

    $query->bind_param(
        "ssissiiiddddi",
        $kelompok,
        $bulan,
        $tahun,
        $minggu,
        $hari,
        $target,
        $cm,
        $mb,
        $drop_baru,
        $t_masuk,
        $t_keluar,
        $t_jadi,
        $id
    );

    if ($query->execute()) {
        header("Location: data_target_ubmi.php?pesan=update_berhasil");
        exit;
    } else {
        echo "Gagal memperbarui data: " . $query->error;
    }
} else {
    echo "Akses tidak diizinkan.";
}
