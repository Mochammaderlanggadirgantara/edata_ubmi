<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/edata_ubmi/config/koneksi.php";

// Cek login
if (empty($_SESSION['id_user'])) {
    echo "Akses ditolak. Silakan login.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil id_cabang user yang login
$query_user = mysqli_query($conn, "SELECT id_cabang FROM tuser WHERE id_user = '$id_user' LIMIT 1");
$data_user = mysqli_fetch_assoc($query_user);
$id_cabang_user = $data_user['id_cabang'] ?? null;

if (!$id_cabang_user) {
    echo "ID cabang tidak ditemukan. Akses ditolak.";
    exit;
}

// Verifikasi sekali per sesi
if (!isset($_SESSION['verifikasi_cabang']) || $_SESSION['verifikasi_cabang'] !== $id_cabang_user) {
    if (isset($_POST['verifikasi'])) {
        $input_sandi = $_POST['kata_sandi'] ?? '';

        $query_sandi = mysqli_query($conn, "SELECT kata_sandi FROM sandi WHERE id_cabang = '$id_cabang_user' LIMIT 1");
        $data_sandi = mysqli_fetch_assoc($query_sandi);

        if ($data_sandi) {
            // Jika sandi plain text
            if ($input_sandi === $data_sandi['kata_sandi']) {
                $_SESSION['verifikasi_cabang'] = $id_cabang_user;
            }
            // Jika sandi sudah di-hash, pakai ini:
            // if (password_verify($input_sandi, $data_sandi['kata_sandi'])) { ... }
            else {
                echo "<div class='alert alert-danger text-center'>Kata sandi salah. Akses ditolak.</div>";
            }
        } else {
            echo "<div class='alert alert-danger text-center'>Data sandi cabang tidak ditemukan.</div>";
        }
    }

    // Kalau belum berhasil verifikasi → tampilkan form
    if (!isset($_SESSION['verifikasi_cabang']) || $_SESSION['verifikasi_cabang'] !== $id_cabang_user) {
?>
        <!DOCTYPE html>
        <html lang="id">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verifikasi Sisa Saldo</title>
            <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        </head>

        <body>
            <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
                    <div class="text-center mb-4">
                        <img src="../assets/images/login-icon.PNG" alt="Logo" style="width: 120px;">
                        <h5 class="mt-3">Verifikasi Akses Sisa Saldo</h5>
                    </div>
                    <form method="post">
                        <div class="mb-3">
                            <label for="kata_sandi" class="form-label">Kata Sandi Cabang</label>
                            <input type="password" name="kata_sandi" id="kata_sandi" class="form-control" required autofocus>
                        </div>
                        <button type="submit" name="verifikasi" class="btn btn-primary w-100">Verifikasi</button>
                    </form>
                </div>
            </div>
        </body>

        </html>
<?php
        exit;
    }
}
?>