<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil id_kelompok dari form (1,2,3 dst)
    $id_kelompok = (int) ($_POST['id_kelompok'] ?? 0);
    if ($id_kelompok <= 0) {
        die("❌ ID Kelompok tidak boleh kosong");
    }

    $bulan = $_POST['bulan'] ?? '';
    $tahun = $_POST['tahun'] ?? '';

    // --- CEK / INSERT DI TABEL UTAMA ---
    $stmt_check = $conn->prepare("
        SELECT id FROM database_tabel_utama_kalkulasi_baru WHERE id_kelompok = ?
    ");
    $stmt_check->bind_param("i", $id_kelompok);
    $stmt_check->execute();
    $stmt_check->bind_result($id_utama); // ambil kolom id (auto_increment)
    $stmt_check->fetch();
    if (!$id_utama) {
        // Insert default ke tabel utama
        $kelompok_text = "Kelompok " . $id_kelompok;
        $stmt_insert_utama = $conn->prepare("
            INSERT INTO database_tabel_utama_kalkulasi_baru
            (bulan, tahun, id_kelompok, kelompok, t_jadi, cm, mb)
            VALUES (?, ?, ?, ?, 0, 0, 0)
        ");
        $stmt_insert_utama->bind_param("siis", $bulan, $tahun, $id_kelompok, $kelompok_text);
        $stmt_insert_utama->execute();

        // Ambil id terakhir yg baru diinsert
        $id_utama = $conn->insert_id;
        $stmt_insert_utama->close();
    }
    $stmt_check->close();
    // --- INSERT KE TABEL TOTAL ---
    $fields = [
        'id_kelompok', // FK mengacu ke id utama
        'total',
        'gagalkan',
        'rencana_jadi',
        'target_program',
        'program_murni',
        'target_kalkulasi',
        'jumlah_target',
        'pelunasan',
        'jumlah_pelunasan',
        'baru',
        'jumlah_baru',
        'storting_jl',
        'jumlah_storting_jl',
        'storting_jd',
        'jumlah_storting_jd',
        'other',
        'jumlah_other',
        't_storting_100',
        'plus_minus_100',
        't_storting_115',
        'plus_minus_115',
        't_storting_120',
        'plus_minus_120',
        'kekuatan_115',
        'kekuatan_120',
        'kekuatan_125',
        'program',
        'nilai'
    ];

    $values = [];
    foreach ($fields as $f) {
        $values[$f] = ($f === 'id_kelompok') ? $id_utama : ($_POST[$f] ?? 0);
        // id_kelompok sekarang = id dari tabel utama (FK)
    }

    $sql = "INSERT INTO database_total_penjumlahan_kalkulasi_baru (" . implode(",", $fields) . ")
            VALUES (" . rtrim(str_repeat("?,", count($fields)), ",") . ")";
    $stmt = $conn->prepare($sql);

    $types = str_repeat("i", count($fields));
    $stmt->bind_param($types, ...array_values($values));

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan'); window.location.href='form_semua.php';</script>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
