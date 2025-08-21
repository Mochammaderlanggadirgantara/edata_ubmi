<?php include '../config/koneksi.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM laporan_km WHERE id=$id");
$data = $result->fetch_assoc();

if(isset($_POST['submit'])){
    $kelompok = $_POST['kelompok'];
    $no_anggota = $_POST['no_anggota'];
    $hari_tanggal = $_POST['hari_tanggal'];
    $nama_anggota = $_POST['nama_anggota'];
    $pinjaman = $_POST['pinjaman'];
    $sisa_saldo = $_POST['sisa_saldo'];
    $keterangan = $_POST['keterangan'];
    $petugas = $_POST['petugas'];

    $conn->query("UPDATE laporan_km SET 
        kelompok='$kelompok',
        no_anggota='$no_anggota',
        hari_tanggal='$hari_tanggal',
        nama_anggota='$nama_anggota',
        pinjaman='$pinjaman',
        sisa_saldo='$sisa_saldo',
        keterangan_pelanggan='$keterangan',
        petugas_kontrol='$petugas'
        WHERE id=$id");

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h2>Edit Data Laporan KM</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
                <?php for($i=1;$i<=10;$i++): 
                    $selected = ($data['kelompok']=="Kelompok $i") ? "selected" : "";
                ?>
                    <option value="Kelompok <?= $i; ?>" <?= $selected; ?>>Kelompok <?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>No Anggota</label>
            <input type="text" name="no_anggota" class="form-control" value="<?= $data['no_anggota']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Hari & Tanggal</label>
            <input type="date" name="hari_tanggal" class="form-control" value="<?= $data['hari_tanggal']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Nama Anggota</label>
            <input type="text" name="nama_anggota" class="form-control" value="<?= $data['nama_anggota']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Pinjaman</label>
            <input type="number" name="pinjaman" class="form-control" value="<?= $data['pinjaman']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Sisa Saldo</label>
            <input type="number" name="sisa_saldo" class="form-control" value="<?= $data['sisa_saldo']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Keterangan Pelanggan</label>
            <textarea name="keterangan" class="form-control"><?= $data['keterangan_pelanggan']; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Petugas Kontrol</label>
            <input type="text" name="petugas" class="form-control" value="<?= $data['petugas_kontrol']; ?>" required>
        </div>
        <button type="submit" name="submit" class="btn btn-success">Update</button>
        <a href="laporan_km.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
