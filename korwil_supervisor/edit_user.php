<?php
session_start();
include '../config/koneksi.php';

// Proses update data saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['id'];
    $nama      = $_POST['nama_user'];
    $jabatan   = $_POST['jabatan'];
    $tgl       = $_POST['tgl_masuk'];
    $username  = $_POST['username'];
    $password  = $_POST['password'];
    $id_cabang = $_POST['id_cabang'];
     $status    = $_POST['status'];

    $errors = [];

    // Validasi input
    if (empty($nama)) $errors[] = "Nama tidak boleh kosong.";
    if (empty($username)) $errors[] = "Username tidak boleh kosong.";
    if (!empty($password) && strlen($password) < 6) $errors[] = "Password minimal 6 karakter.";
    if (empty($jabatan)) $errors[] = "Jabatan harus diisi.";
    if (empty($tgl)) $errors[] = "Tanggal masuk harus diisi.";
    if (empty($id_cabang) || !is_numeric($id_cabang)) $errors[] = "Cabang harus dipilih.";

    // Eksekusi update jika tidak ada error
    if (empty($errors)) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE TUser SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, password=?, id_cabang=? WHERE id_user=?");
            $stmt->bind_param("ssssssi", $nama, $jabatan, $tgl, $username, $hashed, $id_cabang, $id);
        } else {
            $stmt = $conn->prepare("UPDATE TUser SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, id_cabang=? WHERE id_user=?");
            $stmt->bind_param("ssssii", $nama, $jabatan, $tgl, $username, $id_cabang, $id);
        }

        if ($stmt->execute()) {
            header("Location: ../korwil_supervisor/data_karyawan.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data: " . $stmt->error;
        }
    }
}

// Ambil data user untuk form
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM TUser WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<script>alert('ID user tidak ditemukan.'); window.location.href='user_list.php';</script>";
    exit;
}

// Ambil data cabang untuk dropdown
$queryCabang = mysqli_query($conn, "SELECT id_cabang, nama_cabang FROM cabang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Edit Data Karyawan</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $data['id_user'] ?>">

        <div class="mb-3">
            <label for="nama_user" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?= htmlspecialchars($data['nama_user']) ?>" required>
        </div>

        <div class="mb-3">
          
           <select name="jabatan" class="form-control" required>
                                <option value="">Pilih Jabatan</option>
                                <?php foreach ( ['Pengawas', 'Pimpinan', 'kepala_mantri', 'kasir', 'staff', 'mantri'] as $j): ?>
                                    <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                                <?php endforeach; ?>
                            </select>
        </div>
        
        <div class="mb-3">
            <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?= $data['tgl_masuk'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru <small>(kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="mb-3">
            <label for="id_cabang" class="form-label">Cabang</label>
            <select class="form-control" name="id_cabang" required>
                <option value="">-- Pilih Cabang --</option>
                <?php while ($cabang = mysqli_fetch_assoc($queryCabang)): ?>
                    <option value="<?= $cabang['id_cabang'] ?>" <?= ($data['id_cabang'] == $cabang['id_cabang']) ? 'selected' : '' ?>>
                        <?= $cabang['nama_cabang'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
 <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <?php foreach (['aktif', 'tidak aktif'] as $j): ?>
                                    <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>       
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="../korwil_supervisor/data_karyawan.php" class="btn btn-secondary ms-2">Batal</a>
        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form');
  form.addEventListener('submit', function (e) {
    const jabatan = document.querySelector('[name="jabatan"]').value.trim();
    const modalMessage = document.getElementById('modalMessage');

    if (jabatan === "") {
      e.preventDefault();
      modalMessage.textContent = "Field Jabatan tidak boleh kosong.";
      const modal = new bootstrap.Modal(document.getElementById('errorModal'));
      modal.show();
    }
  });
});
</script>

</body>
</html>