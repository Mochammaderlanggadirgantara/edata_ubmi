<?php
include '../config/koneksi.php';
require '../vendor/autoload.php'; // Sesuaikan path jika perlu

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['file_excel']['name'])) {
    $file_tmp = $_FILES['file_excel']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file_tmp);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Bersihkan header
        $header = array_map(function ($item) {
            $item = strtolower(trim($item));
            $item = preg_replace('/\s+/', '_', $item);
            $item = str_replace(['"', "'"], '', $item);
            return $item;
        }, $rows[0]);

        // Ubah data menjadi array asosiatif
        $data = [];
        for ($i = 1; $i < count($rows); $i++) {
            if (array_filter($rows[$i])) {
                $data[] = array_combine($header, $rows[$i]);
            }
        }

        // Fungsi parsing angka
        if (!function_exists('parseNumber')) {
            function parseNumber($value)
            {
                $string_value = (string) $value;
                $clean = str_replace('.', '', trim($string_value));
                return is_numeric($clean) ? (float)$clean : 0;
            }
        }

        $error_count = 0;

        foreach ($data as $row) {
            $kelompok = mysqli_real_escape_string($conn, trim($row['kelompok'] ?? ''));
            $bulan    = mysqli_real_escape_string($conn, trim($row['bulan'] ?? ''));
            $tahun    = mysqli_real_escape_string($conn, trim($row['tahun'] ?? ''));
            $minggu   = mysqli_real_escape_string($conn, trim($row['minggu'] ?? ''));

            // Normalisasi hari
            $raw_hari = strtolower(trim($row['hari'] ?? ''));
            $map_hari = [
                "senin" => "Senin",
                "selasa" => "Selasa",
                "rabu" => "Rabu",
                "kamis" => "Kamis",
                "jum'at" => "Jumat",
                "jumat" => "Jumat",
                "sabtu" => "Sabtu",
                "minggu" => "Minggu"
            ];
            $hari = mysqli_real_escape_string($conn, $map_hari[$raw_hari] ?? ucfirst($raw_hari));

            // Ambil dan hitung nilai numerik
            $target     = parseNumber($row['target'] ?? 0);
            $cm         = parseNumber($row['cm'] ?? 0);
            $mb         = parseNumber($row['mb'] ?? 0);
            $drop_baru  = parseNumber($row['drop_baru'] ?? 0);
            $t_keluar   = parseNumber($row['t_keluar'] ?? 0);

            $t_masuk = round($drop_baru * 0.13, 2);
            $t_jadi  = round($target + $t_masuk - $t_keluar, 2);

            // Masukkan jika data utama tersedia
            if ($kelompok && $bulan && $tahun && $minggu && $hari) {
                $stmt = $conn->prepare("INSERT INTO target_ubmi 
                    (kelompok, bulan, tahun, minggu, hari, target, cm, mb, drop_baru, t_masuk, t_keluar, t_jadi) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "sssssiiiiddd",
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
                    $t_jadi
                );
                $stmt->execute();
            } else {
                $error_count++;
            }
        }

        if ($error_count > 0) {
            echo "<script>alert('Import sebagian berhasil. Ada $error_count baris yang gagal.'); 
                  window.location.href='../korwil_supervisor/data_target_ubmi.php';</script>";
        } else {
            echo "<script>alert('Import berhasil!'); 
                  window.location.href='../korwil_supervisor/data_target_ubmi.php';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Gagal membaca file: " . $e->getMessage() . "'); history.back();</script>";
    }
} else {
    echo "<script>alert('File tidak ditemukan'); history.back();</script>";
}
