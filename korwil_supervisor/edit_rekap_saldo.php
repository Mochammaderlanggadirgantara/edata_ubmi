<?php 
include '../config/koneksi.php'; 
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM laporan WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Edit Data</h2>
    <form method="post">
        <div class="mb-3"><label>Tahun</label><input type="number" name="tahun" value="<?= $data['tahun'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>Bulan</label><input type="text" name="bulan" value="<?= $data['bulan'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>Kelompok</label><input type="text" name="kelompok" value="<?= $data['kelompok'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>DB</label><input type="text" name="db" value="<?= $data['db'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>Saldo</label><input type="number" name="saldo" value="<?= $data['saldo'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Senin</label><input type="number" name="senin" value="<?= $data['senin'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Selasa</label><input type="number" name="selasa" value="<?= $data['selasa'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Rabu</label><input type="number" name="rabu" value="<?= $data['rabu'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Kamis</label><input type="number" name="kamis" value="<?= $data['kamis'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Jumat</label><input type="number" name="jumat" value="<?= $data['jumat'] ?>" class="form-control"></div>
        <div class="mb-3"><label>Sabtu</label><input type="number" name="sabtu" value="<?= $data['sabtu'] ?>" class="form-control"></div>
        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="rekap_saldo.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>

<?php
if(isset($_POST['update'])){
    $stmt = $conn->prepare("UPDATE rekap_saldo SET tahun=?, bulan=?, kelompok=?, db=?, saldo=?, senin=?, selasa=?, rabu=?, kamis=?, jumat=?, sabtu=? WHERE id=?");
    $stmt->bind_param("isssiiiiiiii", 
        $_POST['tahun'], $_POST['bulan'], $_POST['kelompok'], $_POST['db'],
        $_POST['saldo'], $_POST['senin'], $_POST['selasa'], $_POST['rabu'], $_POST['kamis'], $_POST['jumat'], $_POST['sabtu'],
        $id
    );
    $stmt->execute();
    header("Location: index.php");
}
?>
