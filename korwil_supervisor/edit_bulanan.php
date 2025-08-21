<?php include __DIR__ . '/partials/header.php';
include __DIR__ . '/functions.php'; // tambahkan ini

$id = (int)($_GET['id'] ?? 0);
$stmt = $mysqli->prepare("SELECT * FROM index_bulanan WHERE id=?");
$stmt->bind_param('i',$id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if(!$row){ echo '<div class="alert alert-warning">Data tidak ditemukan.</div>'; include __DIR__.'/partials/footer.php'; exit; }
?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Edit Data</h5>
    <form method="post" action="update.php" class="row g-2" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
      <div class="col-6">
        <label class="form-label mb-1">Bulan</label>
        <select name="bulan" class="form-select form-select-sm"><?= opsi_bulan($row['bulan']) ?></select>
      </div>
      <div class="col-6">
        <label class="form-label mb-1">Tahun</label>
        <input type="number" name="tahun" class="form-control form-control-sm" value="<?= (int)$row['tahun'] ?>" required>
      </div>
      <div class="col-12">
        <label class="form-label mb-1">KLP</label>
        <select name="klp" class="form-select form-select-sm" required><?= opsi_klp($row['klp']) ?></select>
      </div>
      <div class="col-12">
        <label class="form-label mb-1">Program</label>
        <input type="text" name="program" class="form-control form-control-sm format-angka" value="<?= fmt_int($row['program']) ?>" required>
      </div>
      <div class="col-12">
        <label class="form-label mb-1">Storting Valid</label>
        <input type="text" name="storting_valid" class="form-control form-control-sm format-angka" value="<?= fmt_int($row['storting_valid']) ?>" required>
      </div>
      <div class="col-12 d-flex gap-2 pt-2">
        <a href="index.php?bulan=<?= (int)$row['bulan'] ?>&tahun=<?= (int)$row['tahun'] ?>&klp=<?= (int)$row['klp'] ?>" class="btn btn-secondary btn-sm">Kembali</a>
        <button class="btn btn-primary btn-sm" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
