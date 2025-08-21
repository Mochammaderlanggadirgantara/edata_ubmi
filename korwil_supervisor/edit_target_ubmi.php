<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/edata_ubmi/config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);
$query = $conn->prepare("SELECT * FROM target_ubmi WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data target UBMI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-5">
    <h2>Edit Data target UBMI</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="update_target_ubmi.php">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="mb-3">
            <label>Kelompok</label>
            <input type="text" name="kelompok" class="form-control" value="<?= htmlspecialchars($data['kelompok']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Bulan</label>
            <input type="text" name="bulan" class="form-control" value="<?= htmlspecialchars($data['bulan']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?= $data['tahun'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Minggu</label>
            <input type="text" name="minggu" class="form-control" value="<?= htmlspecialchars($data['minggu']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Hari</label>
            <input type="text" name="hari" class="form-control" value="<?= htmlspecialchars($data['hari']) ?>" required>
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

        <div class="mb-3">
            <label>Drop Baru</label>
            <input type="number" name="drop_baru" class="form-control" value="<?= $data['drop_baru'] ?>">
        </div>

        <div class="mb-3">
            <label>T. Keluar</label>
            <input type="number" name="t_keluar" class="form-control" value="<?= $data['t_keluar'] ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="data_target_ubmi.php" class="btn btn-secondary">Batal</a>
    </form>
    <!-- Modal Bootstrap untuk error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-danger text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Peringatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>