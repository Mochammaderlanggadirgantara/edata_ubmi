<?php
include '../config/koneksi.php';
$id = $_GET['id'];

$data = $conn->query("SELECT * FROM index_bulanan WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulan    = $_POST['bulan'];
    $tahun    = $_POST['tahun'];
    $klp      = $_POST['klp'];
    $program  = str_replace('.', '', $_POST['program']);
    $storting = str_replace('.', '', $_POST['storting_valid']);

    $idx = ($storting > 0) ? ($program / $storting * 100) : 0;

    $stmt = $conn->prepare("UPDATE index_bulanan SET bulan=?, tahun=?, klp=?, program=?, storting_valid=?, idx_akhir=? WHERE id=?");
    $stmt->bind_param("iiiddii", $bulan, $tahun, $klp, $program, $storting, $idx, $id);
    $stmt->execute();

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data</title>
  <link href="assets/bootstrap.min.css" rel="stylesheet">
  <script>
    function formatRibuan(input) {
      let value = input.value.replace(/\D/g,'');
      input.value = new Intl.NumberFormat('id-ID').format(value);
    }
  </script>
</head>
<body class="container py-4">
<h3>Edit Data Index Bulanan</h3>
<form method="POST">
  <div class="mb-3">
    <label>Bulan</label>
    <input type="number" name="bulan" class="form-control" value="<?= $data['bulan'] ?>" required>
  </div>
  <div class="mb-3">
    <label>Tahun</label>
    <input type="number" name="tahun" class="form-control" value="<?= $data['tahun'] ?>" required>
  </div>
  <div class="mb-3">
    <label>Kelompok</label>
    <select name="klp" class="form-control" required>
      <?php for($i=1;$i<=10;$i++): ?>
        <option value="<?= $i ?>" <?= ($data['klp']==$i?'selected':'') ?>>Kelompok <?= $i ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Program</label>
    <input type="text" name="program" value="<?= number_format($data['program'],0,',','.') ?>" oninput="formatRibuan(this)" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Storting Valid</label>
    <input type="text" name="storting_valid" value="<?= number_format($data['storting_valid'],0,',','.') ?>" oninput="formatRibuan(this)" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Update</button>
  <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
</body>
</html>
