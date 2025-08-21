<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
  echo "<div class='alert alert-danger'>Akses ditolak.</div>";
  exit();
}

// Simulasikan user login (replace dengan login session sebenarnya)
$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM TUser WHERE id_user = $id_user";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Ubah Username & Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h3 class="text-center">Ubah Username & Password</h3>
    <form id="formUbah" method="POST">
      <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

      <div class="mb-3">
        <label>Nama</label>
        <input type="text" class="form-control" value="<?= $user['nama_user'] ?>" readonly>
      </div>

      <div class="mb-3">
        <label>Jabatan</label>
        <input type="text" class="form-control" value="<?= $user['jabatan'] ?>" readonly>
      </div>

      <div class="mb-3">
        <label>Username Baru</label>
        <input type="text" class="form-control" name="username" value="<?= $user['username'] ?>" required>
      </div>

      <div class="mb-3">
        <label>Password Lama</label>
        <input type="password" class="form-control" name="password_lama" id="passwordLama" required>
      </div>

      <div class="mb-3">
        <label>Password Baru</label>
        <input type="password" class="form-control" name="password_baru" id="passwordBaru" required>
      </div>

      <div class="mb-3">
        <label>Ulangi Password Baru</label>
        <input type="password" class="form-control" name="ulangi_password_baru" id="ulangiPasswordBaru" required>
      </div>

      <!-- Checkbox untuk menampilkan semua password -->
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="showAllPasswords">
        <label class="form-check-label" for="showAllPasswords">Tampilkan Password</label>
      </div>

      <button type="button" class="btn btn-primary" onclick="previewData()">Simpan</button>
      <button type="reset" class="btn btn-secondary">Batal</button>
    </form>

    <!-- Modal Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Preview Perubahan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p><strong>Username Baru:</strong> <span id="previewUsername"></span></p>
            <p><strong>Password Baru:</strong> <span id="previewPassword"></span></p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-success" onclick="submitFormAjax()">Konfirmasi Simpan</button>

          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Kirim data ke PHP via AJAX setelah konfirmasi modal
    function submitFormAjax() {
      const form = document.getElementById('formUbah');
      const formData = new FormData(form);

      fetch('settings/proses_ubah_password.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            alert(data.message);
            window.location.href = data.redirect;
          } else {
            alert(data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert("Terjadi kesalahan saat menyimpan data.");
        });
    }
    // Fungsi untuk menampilkan atau menyembunyikan semua password
    function toggleAllPasswordsVisibility() {
      var isChecked = document.getElementById("showAllPasswords").checked;
      var passwordFields = ["passwordLama", "passwordBaru", "ulangiPasswordBaru"];

      passwordFields.forEach(function (id) {
        var passwordField = document.getElementById(id);
        passwordField.type = isChecked ? "text" : "password";
      });
    }

    // Event listener untuk checkbox
    document.getElementById("showAllPasswords").addEventListener("change", function () {
      toggleAllPasswordsVisibility();
    });

    // Fungsi untuk preview data
    function previewData() {
      let form = document.getElementById("formUbah");
      let username = form.username.value;
      let passwordBaru = form.password_baru.value;
      let ulangi = form.ulangi_password_baru.value;

      if (passwordBaru !== ulangi) {
        alert("Password baru dan konfirmasi tidak sama.");
        return;
      }

      document.getElementById("previewUsername").innerText = username;
      document.getElementById("previewPassword").innerText = passwordBaru;

      // Tampilkan modal
      let previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
      previewModal.show();
    }
  </script>

</body>

</html>