<?php
include '../config/koneksi.php';

$tabel = $_GET['tabel'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelompok = $_POST['kelompok'];

    $days = ['senin','selasa','rabu','kamis','jumat','sabtu'];
    $columns = "kelompok";
    $values = "'$kelompok'";

    foreach ($days as $day) {
        $start = $_POST[$day.'_start'];
        $finish = $_POST[$day.'_finish'];
        $color = $_POST[$day.'_color'];

        $columns .= ", {$day}_start, {$day}_finish, {$day}_color";
        $values  .= ", '$start', '$finish', '$color'";
    }

       // langsung ke tabel pemetaan_resort
    $sql = "INSERT INTO pemetaan_resort ($columns) VALUES ($values)";
    $conn->query($sql);

    header("Location: pemetaan_resort.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tambah Data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4">
  <h3 class="mb-4">Tambah Data <?= ucfirst(str_replace("_"," ",$tabel)) ?></h3>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Kelompok</label>
      <select name="kelompok" class="form-select" required>
        <?php for($i=1;$i<=10;$i++): ?>
        <option value="Kelompok <?= $i ?>">Kelompok <?= $i ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <?php $days=['senin','selasa','rabu','kamis','jumat','sabtu']; ?>
    <?php foreach($days as $day): ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-body">
        <h5 class="card-title text-capitalize"><?= $day ?></h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Start</label>
            <input type="text" name="<?= $day ?>_start" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Finish</label>
            <input type="text" name="<?= $day ?>_finish" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Warna</label>
            <input type="color" name="<?= $day ?>_color" class="form-control form-control-color">
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="d-flex justify-content-between">
      <button type="submit" class="btn btn-success">Simpan</button>
      <a href="pemetaan_resort.php?tabel=<?= $tabel ?>" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</body>
</html>
