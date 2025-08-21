<?php
session_start();
require '../config/koneksi.php';
require '../vendor/autoload.php'; // sesuaikan path autoload Composer

use PhpOffice\PhpSpreadsheet\IOFactory;

// Validasi akses
$allowed_roles = ['kasir', 'korwil'];
if (!isset($_SESSION['username']) || !in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Fungsi generate user_id otomatis
function generateUserId($conn)
{
    $query = "SELECT user_id FROM inventaris ORDER BY user_id DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $lastId = mysqli_fetch_assoc($result)['user_id'] ?? 'USR0000';

    // Ambil angka terakhir, tambah 1
    $number = intval(substr($lastId, 3)) + 1;
    return 'USR' . str_pad($number, 4, '0', STR_PAD_LEFT);
}

if (isset($_FILES['file_excel']['tmp_name'])) {
    $file = $_FILES['file_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $inserted = 0;
    $skipped = 0;

    foreach ($rows as $index => $row) {
        if ($index === 0) continue; // skip header

        [$nama_anggota, $jabatan, $jenis_kendaraan, $nomor_polisi, $masa_berlaku] = $row;

        // Validasi minimal
        if (empty($nama_anggota) || empty($jabatan)) {
            $skipped++;
            continue;
        }

        $user_id = generateUserId($conn); // Buat user_id baru

        $stmt = $conn->prepare("INSERT INTO inventaris (user_id, nama_anggota, jabatan, jenis_kendaraan, nomor_polisi, masa_berlaku) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user_id, $nama_anggota, $jabatan, $jenis_kendaraan, $nomor_polisi, $masa_berlaku);

        if ($stmt->execute()) {
            $inserted++;
        } else {
            $skipped++;
        }
    }

    echo "<script>alert('Import selesai. Berhasil: $inserted, Gagal: $skipped'); window.location.href='../kasir/inventaris.php';</script>";
} else {
    echo "Tidak ada file yang diunggah.";
}
