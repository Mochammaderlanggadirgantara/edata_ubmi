<?php
include '../config/koneksi.php';
require '../vendor/autoload.php'; // pastikan kamu sudah install phpoffice/phpspreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Import Data Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title text-center mb-3">Import Data Nasabah dari Excel</h4>

                <?php
                if (isset($_POST['import']) && isset($_FILES['excel_file'])) {
                    $file = $_FILES['excel_file']['tmp_name'];

                    try {
                        $spreadsheet = IOFactory::load($file);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray();

                        $imported = 0;
                        $skipped = 0;

                        for ($i = 1; $i < count($rows); $i++) {
                            $row = $rows[$i];

                            // Validasi minimal NIK tidak kosong
                            if (empty($row[2])) {
                                $skipped++;
                                continue;
                            }

                            $no_anggota    = $conn->real_escape_string(trim($row[1]));
                            $nik_nasabah   = $conn->real_escape_string(trim($row[2]));
                            $nama_nasabah  = strtoupper($conn->real_escape_string(trim($row[3])));
                            $domisili      = strtoupper($conn->real_escape_string(trim($row[4])));
                            $tanggal_drop  = date('Y-m-d', strtotime($row[5]));
                            $pinjaman      = str_replace(',', '', trim($row[6]));
                            $hari          = strtoupper($conn->real_escape_string(trim($row[7])));
                            $klp           = $conn->real_escape_string(trim($row[8]));
                            $kl            = strtoupper($conn->real_escape_string(trim($row[9])));

                            $cek = $conn->query("SELECT id FROM nasabah WHERE nik_nasabah = '$nik_nasabah'");
                            if ($cek->num_rows == 0) {
                                $sql = "INSERT INTO nasabah (no_anggota, nik_nasabah, nama_nasabah, domisili, tanggal_drop, pinjaman, hari, klp, kl)
                                    VALUES ('$no_anggota', '$nik_nasabah', '$nama_nasabah', '$domisili', '$tanggal_drop', '$pinjaman', '$hari', '$klp', '$kl')";
                                $conn->query($sql);
                                $imported++;
                            } else {
                                $skipped++;
                            }
                        }

                        echo "<div class='alert alert-success'>Import berhasil! $imported data berhasil diimport, $skipped data dilewati karena duplikat atau kosong.</div>";
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>Gagal mengimpor data: " . $e->getMessage() . "</div>";
                    }
                }
                ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Upload file Excel (.xlsx)</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx" required>
                    </div>
                    <button type="submit" name="import" class="btn btn-primary w-100">Import Data</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>