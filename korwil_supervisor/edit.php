<?php
include '../config/koneksi.php';
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM TUser WHERE id_user = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2>Edit User</h2>
    <form action="save.php" method="post">
        <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama_user" class="form-control" value="<?= $data['nama_user'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Jabatan</label>
            <select name="jabatan" class="form-control" required>
                <?php foreach (['korwil', 'manager', 'supervisor', 'kasir', 'staff', 'mantri'] as $j): ?>
                    <option value="<?= $j ?>" <?= $data['jabatan'] == $j ? 'selected' : '' ?>><?= ucfirst($j) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Tanggal Masuk</label>
            <input type="date" name="tgl_masuk" class="form-control" value="<?= $data['tgl_masuk'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Password Baru (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="tambah_user.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>

</html>