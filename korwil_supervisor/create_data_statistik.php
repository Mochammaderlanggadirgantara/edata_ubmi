<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hari      = $_POST['hari'];
    $kelompok  = $_POST['kelompok'];
    $target    = $_POST['target'];
    $rencana   = $_POST['rencana'];
    $storting  = $_POST['storting'];

    // Hitung otomatis
    $storting_tg = ($rencana * 0.26) + $target;
    $min_plus    = $storting - $storting_tg;

    // Simpan ke database
    $query = "INSERT INTO data_statistik_leader 
              (hari, kelompok, target, rencana, storting, storting_tg, min_plus) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiiii", $hari, $kelompok, $target, $rencana, $storting, $storting_tg, $min_plus);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan'); window.location='data_statistik.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">

    <h2 class="mb-4">Tambah Data Statistik Leader</h2>

    <form method="post">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Hari</label>
                <select name="hari" class="form-select" required>
                    <option value="">-- Pilih Hari --</option>
                    <option>Senin</option>
                    <option>Selasa</option>
                    <option>Rabu</option>
                    <option>Kamis</option>
                    <option>Jumat</option>
                    <option>Sabtu</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelompok</label>
                <select name="kelompok" class="form-select" required>
                    <option value="">-- Pilih Kelompok --</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="Kelompok <?= $i ?>">Kelompok <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Target</label>
                <input type="number" name="target" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Rencana</label>
                <input type="number" name="rencana" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Storting</label>
                <input type="number" name="storting" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="data_statistik.php" class="btn btn-secondary">Kembali</a>
    </form>

</body>

</html>