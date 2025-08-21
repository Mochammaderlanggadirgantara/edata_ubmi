<?php
include '../config/koneksi.php';

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM label_setting WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kurang_hari_kerja = $_POST['kurang_hari_kerja'];
    $penagihan         = $_POST['penagihan'];
    $seminggu          = $_POST['seminggu'];

    $sql = "UPDATE label_setting 
            SET kurang_hari_kerja='$kurang_hari_kerja', penagihan='$penagihan', seminggu='$seminggu' 
            WHERE id='$id'";
    mysqli_query($conn, $sql);

    header("Location: create_kalkulasi.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Label Setting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Edit Data</h3>
    <form method="post">
        <div class="mb-3">
            <label>Kurang Hari Kerja</label>
            <input type="number" name="kurang_hari_kerja" class="form-control" value="<?= $row['kurang_hari_kerja'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Penagihan</label>
            <input type="number" name="penagihan" class="form-control" value="<?= $row['penagihan'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Seminggu</label>
            <input type="number" name="seminggu" class="form-control" value="<?= $row['seminggu'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="create_kalkulasi.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
