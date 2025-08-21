<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Tambah Data</h2>
    <form method="post">
        <div class="mb-3">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Bulan</label>
            <select name="bulan" class="form-control" required>
                <?php 
                $bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                          'Juli','Agustus','September','Oktober','November','Desember'];
                foreach($bulan as $b) echo "<option>$b</option>";
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
                <?php for($i=1;$i<=10;$i++) echo "<option>Kelompok $i</option>"; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>DB</label>
            <input type="text" name="db" class="form-control" required placeholder="ml / mb / cm / bulan">
        </div>
        <div class="mb-3"><label>Saldo</label><input type="number" name="saldo" class="form-control"></div>
        <div class="mb-3"><label>Senin</label><input type="number" name="senin" class="form-control"></div>
        <div class="mb-3"><label>Selasa</label><input type="number" name="selasa" class="form-control"></div>
        <div class="mb-3"><label>Rabu</label><input type="number" name="rabu" class="form-control"></div>
        <div class="mb-3"><label>Kamis</label><input type="number" name="kamis" class="form-control"></div>
        <div class="mb-3"><label>Jumat</label><input type="number" name="jumat" class="form-control"></div>
        <div class="mb-3"><label>Sabtu</label><input type="number" name="sabtu" class="form-control"></div>
        <button type="submit" name="submit" class="btn btn-success">Simpan</button>
        <a href="rekap_saldo.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>

<?php
if(isset($_POST['submit'])){
    $stmt = $conn->prepare("INSERT INTO rekap_saldo 
        (tahun,bulan,kelompok,db,saldo,senin,selasa,rabu,kamis,jumat,sabtu) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssiiiiiii", 
        $_POST['tahun'], $_POST['bulan'], $_POST['kelompok'], $_POST['db'],
        $_POST['saldo'], $_POST['senin'], $_POST['selasa'], $_POST['rabu'], $_POST['kamis'], $_POST['jumat'], $_POST['sabtu']
    );
    $stmt->execute();
    header("Location: rekap_saldo.php");
}
?>
