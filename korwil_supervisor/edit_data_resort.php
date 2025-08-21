<?php
include 'config.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM data_resort WHERE id=$id");
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<h3>Edit Data Resort</h3>
<form method="POST" action="update_data_resort.php">
  <input type="hidden" name="id" value="<?= $data['id'] ?>">
  <div class="mb-3">
    <label>Kelompok</label>
    <select name="kelompok" class="form-control" required>
      <?php for($i=1;$i<=10;$i++): ?>
        <option value="<?= $i ?>" <?= ($i==$data['kelompok'])?'selected':'' ?>>Kelompok <?= $i ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Hari</label>
    <select name="hari" class="form-control" required>
      <?php 
      $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
      foreach($hariList as $h): ?>
        <option value="<?= $h ?>" <?= ($h==$data['hari'])?'selected':'' ?>><?= $h ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Target</label>
    <input type="number" name="target" class="form-control" value="<?= $data['target'] ?>" required>
  </div>
  <div class="mb-3">
    <label>CM</label>
    <input type="number" name="cm" class="form-control" value="<?= $data['cm'] ?>" required>
  </div>
  <div class="mb-3">
    <label>MB</label>
    <input type="number" name="mb" class="form-control" value="<?= $data['mb'] ?>" required>
  </div>
  <button type="submit" class="btn btn-success">Update</button>
  <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>

</body>
</html>
