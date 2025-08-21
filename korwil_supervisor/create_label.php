<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kurang_hari_kerja = $_POST['kurang_hari_kerja'];
    $penagihan         = $_POST['penagihan'];
    $seminggu          = $_POST['seminggu'];

    $sql = "INSERT INTO label_setting (kurang_hari_kerja, penagihan, seminggu)
            VALUES ('$kurang_hari_kerja', '$penagihan', '$seminggu')";
    mysqli_query($conn, $sql);

    header("Location: create_kalkulasi.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Label Setting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Tambah Data</h3>
    <form method="post">
        <div class="mb-3">
            <label>Kurang Hari Kerja</label>
            <input type="number" name="kurang_hari_kerja" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Penagihan</label>
            <input type="number" name="penagihan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Seminggu</label>
            <input type="number" name="seminggu" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="create_kalkulasi.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
