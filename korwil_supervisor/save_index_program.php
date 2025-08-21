<?php
// simpan_index_program.php
include '../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $tanggal = $_POST['tanggal'];
    $klp = $_POST['klp'];
    $drop_rekap = (float)$_POST['drop_rekap'];
    $storting_rekap = (float)$_POST['storting_rekap'];
    $indeks = (float)$_POST['indeks'];
    $program = (float)$_POST['program'];

    // ===== LOGIKA PERHITUNGAN =====
    $storting_jadi = $program * ($indeks / 100);
    $min_drop = $drop_rekap - $program;
    $min_storting = $storting_rekap - $storting_jadi;
    // ==============================

    // Simpan ke database
    $sql = "INSERT INTO index_program 
            (tanggal, klp, drop_rekap, storting_rekap, indeks, program, storting_jadi, min_drop, min_storting)
            VALUES 
            ('$tanggal', '$klp', '$drop_rekap', '$storting_rekap', '$indeks', '$program', '$storting_jadi', '$min_drop', '$min_storting')";

    if (mysqli_query($conn, $sql)) {
        // Redirect ke halaman index setelah sukses
        header("Location: index_program.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    // Jika bukan POST, arahkan kembali ke form
    header("Location: index_program.php");
    exit();
}
