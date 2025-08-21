<?php
include '../config/koneksi.php'; // sesuaikan path

$kelompok = $_GET['kelompok'] ?? '';

if ($kelompok !== '') {
    // Ambil total target berdasarkan kelompok
    $query = $conn->prepare("SELECT SUM(target) as total_target FROM target_ubmi WHERE kelompok = ?");
    $query->bind_param("s", $kelompok);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    $target_kalkulasi = (float)$result['total_target'];

    // Ambil data setting (anggap hanya ada 1 baris di label_setting)
    $setting = $conn->query("SELECT seminggu, kurang_hari_kerja FROM label_setting LIMIT 1")->fetch_assoc();
    $seminggu = (int)$setting['seminggu'];
    $kurang_hari_kerja = (int)$setting['kurang_hari_kerja'];

    // Hitung jumlah target pakai nilai dari tabel label_setting
    if ($seminggu > 0) {
        $jumlah_target = ($target_kalkulasi / $seminggu) * $kurang_hari_kerja;
    } else {
        $jumlah_target = 0; // supaya tidak division by zero
    }

    echo json_encode([
        'target_kalkulasi' => $target_kalkulasi,
        'jumlah_target' => $jumlah_target,
        'seminggu' => $seminggu,
        'kurang_hari_kerja' => $kurang_hari_kerja
    ]);
}
