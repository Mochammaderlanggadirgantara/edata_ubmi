<?php include '../config/.php'; ?>
<?php
if(isset($_POST['submit'])){
    $kelompok = $_POST['kelompok'];
    $no_anggota = $_POST['no_anggota'];
    $hari_tanggal = $_POST['hari_tanggal'];
    $nama_anggota = $_POST['nama_anggota'];
    $pinjaman = $_POST['pinjaman'];
    $sisa_saldo = $_POST['sisa_saldo'];
    $keterangan = $_POST['keterangan'];
    $petugas = $_POST['petugas'];

    $conn->query("INSERT INTO laporan_km (kelompok, no_anggota, hari_tanggal, nama_anggota, pinjaman, sisa_saldo, keterangan_pelanggan, petugas_kontrol) 
    VALUES ('$kelompok','$no_anggota','$hari_tanggal','$nama_anggota','$pinjaman','$sisa_saldo','$keterangan','$petugas')");

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h2>Tambah Data Laporan KM</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php for($i=1;$i<=10;$i++): ?>
                    <option value="Kelompok <?= $i; ?>">Kelompok <?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>No Anggota</label>
            <input type="text" name="no_anggota" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Hari & Tanggal</label>
            <input type="date" name="hari_tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nama Anggota</label>
            <input type="text" name="nama_anggota" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Pinjaman</label>
            <input type="number" name="pinjaman" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Sisa Saldo</label>
            <input type="number" name="sisa_saldo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Keterangan Pelanggan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Petugas Kontrol</label>
            <input type="text" name="petugas" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-success">Simpan</button>
        <a href="laporan_km.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
