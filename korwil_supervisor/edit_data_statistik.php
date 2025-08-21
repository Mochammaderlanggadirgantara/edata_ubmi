<?php
include "../config/koneksi.php";

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: data_statistik.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM data_statistik_leader WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-4">
        <h2 class="mb-3">✏️ Edit Data Statistik</h2>
        <form method="post" action="update_data_statistik.php" class="row g-3">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <div class="col-md-3">
                <label class="form-label">Minggu</label>
                <select name="minggu" class="form-select" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= $data['minggu'] == $i ? 'selected' : '' ?>>Minggu <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Hari</label>
                <select name="hari" class="form-select" required>
                    <?php
                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    foreach ($hariList as $h): ?>
                        <option value="<?= $h ?>" <?= $data['hari'] == $h ? 'selected' : '' ?>><?= $h ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelompok</label>
                <select name="kelompok" class="form-select" required>
                    <?php for ($i = 1; $i <= 10; $i++):
                        $k = "Kelompok $i"; ?>
                        <option value="<?= $k ?>" <?= $data['kelompok'] == $k ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Target</label>
                <input type="number" name="target" class="form-control" value="<?= $data['target'] ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rencana</label>
                <input type="number" name="rencana" class="form-control" value="<?= $data['rencana'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Storting</label>
                <input type="number" name="storting" class="form-control" value="<?= $data['storting'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Storting TG</label>
                <input type="number" name="storting_tg" class="form-control" value="<?= $data['storting_tg'] ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Min/Plus</label>
                <input type="number" name="min_plus" class="form-control" value="<?= $data['min_plus'] ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="data_statistik.php?minggu=<?= $data['minggu'] ?>" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</body>

</html>