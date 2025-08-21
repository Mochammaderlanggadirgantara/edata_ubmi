<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data unik dari target_ubmi untuk form dropdown
$data_bulan    = mysqli_query($conn, "SELECT DISTINCT bulan FROM target_ubmi 
                                      ORDER BY FIELD(bulan,'Januari','Februari','Maret','April','Mei','Juni',
                                      'Juli','Agustus','September','Oktober','November','Desember')");
$data_tahun    = mysqli_query($conn, "SELECT DISTINCT tahun FROM target_ubmi ORDER BY tahun DESC");
$data_kelompok = mysqli_query($conn, "SELECT DISTINCT kelompok FROM target_ubmi ORDER BY kelompok ASC");
$data_hari     = mysqli_query($conn, "SELECT DISTINCT hari FROM target_ubmi 
                                      ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')");

// Proses simpan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulan    = mysqli_real_escape_string($conn, $_POST['bulan']);
    $tahun    = mysqli_real_escape_string($conn, $_POST['tahun']);
    $minggu   = (int) $_POST['minggu']; // hanya 2,3,4
    $kelompok = mysqli_real_escape_string($conn, $_POST['kelompok']);
    $hari     = mysqli_real_escape_string($conn, $_POST['hari']);

    // Cek apakah data minggu ini sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM target_ubmi 
                                WHERE bulan='$bulan' AND tahun='$tahun' 
                                AND minggu='$minggu' AND kelompok='$kelompok' AND hari='$hari'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = "❌ Data sudah ada untuk Minggu $minggu.";
        header("Location: tambah_target.php");
        exit();
    }

    // Ambil data dari minggu 1
    $query_m1 = mysqli_query($conn, "SELECT target, cm, mb 
                                     FROM target_ubmi 
                                     WHERE bulan='$bulan' AND tahun='$tahun' 
                                     AND minggu='1' AND kelompok='$kelompok' AND hari='$hari' 
                                     LIMIT 1");
    if (mysqli_num_rows($query_m1) == 0) {
        $_SESSION['error'] = "⚠️ Data Minggu 1 belum ada untuk kombinasi ini.";
        header("Location: tambah_target.php");
        exit();
    }

    $data_m1 = mysqli_fetch_assoc($query_m1);
    $target = $data_m1['target'];
    $cm     = $data_m1['cm'];
    $mb     = $data_m1['mb'];

    // Insert ke minggu yang dipilih
    $insert = mysqli_query($conn, "INSERT INTO target_ubmi 
        (bulan, tahun, minggu, kelompok, hari, target, cm, mb, drop_baru, t_masuk, t_keluar, t_jadi) 
        VALUES 
        ('$bulan', '$tahun', '$minggu', '$kelompok', '$hari', '$target', '$cm', '$mb', 0, 0, 0, 0)");

    if ($insert) {
        $_SESSION['success'] = "✅ Data berhasil ditambahkan untuk Minggu $minggu.";
    } else {
        $_SESSION['error'] = "❌ Gagal menambahkan data: " . mysqli_error($conn);
    }

    header("Location: tambah_target.php");
    exit();
}
