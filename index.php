<?php
session_start();
include 'config/koneksi.php';

// Fungsi ubah format tanggal ke Indonesia penuh
function formatTanggalIndonesia($tanggal)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $tgl = date('j', strtotime($tanggal));
    $bln = $bulan[(int)date('n', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return "$tgl $bln $thn";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM tuser WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'aktif') {
            // 🔒 regenerate session id
            session_regenerate_id(true);

            // simpan session
            $_SESSION['id_user']     = $user['id_user'];
            $_SESSION['username']    = $user['username'];
            $_SESSION['nama_user']   = $user['nama_user'];
            $_SESSION['jabatan']     = strtolower($user['jabatan']); // diseragamkan
            $_SESSION['id_kelompok'] = $user['id_kelompok'];
            $_SESSION['id_cabang']   = $user['id_cabang'];

            // redirect sesuai jabatan
            switch ($_SESSION['jabatan']) {
                case 'pengawas':
                    header("Location: korwil_supervisor/dashboard_korwil.php");
                    break;
                    case 'pimpinan':
                    header("Location: korwil_supervisor/dashboard_babat1.php");
                    break;
                    case 'kepala mantri':
                    header("Location: korwil_supervisor/dashboard_babat1.php");
                    break;
                case 'kasir':
                    header("Location: kasir/dashboard_kasir.php");
                    break;
                case 'mantri':
                    header("Location: mantri/dashboard_mantri.php");
                    break;
                case 'staff':
                    header("Location: staff/dashboard_staff.php");
                    break;
                default:
                    // fallback kalau jabatan tidak dikenali
                    $_SESSION['notif'] = ['type' => 'warning', 'message' => 'Jabatan tidak dikenali.'];
                    header("Location: index.php");
            }
            exit;
        } else {
            // ❌ Status tidak aktif → tampilkan pesan error dengan tgl_nonaktif format Indonesia
            $tanggalNonaktif = formatTanggalIndonesia($user['tgl_nonaktif']);
            $_SESSION['notif'] = [
                'type' => 'danger',
                'message' => "Anda tidak memiliki akses lagi sejak tanggal $tanggalNonaktif"
            ];
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['notif'] = ['type' => 'danger', 'message' => 'Username atau password salah!'];
        header("Location: index.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            margin-top: 100px;
        }

        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 200px;
            /* atau pakai max-width untuk fleksibilitas */
            max-width: 100%;
            margin: 0 auto 20px;
            display: block;
        }

        body::before {
            content: "";
            background: url('picts/logo.png') no-repeat center center;
            background-size: 700px;
            /* ukuran watermark */
            opacity: 0.05;
            /* transparansi watermark */
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
        }

        .login-container,
        .card {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['notif'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $_SESSION['notif']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['notif']['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['notif']); ?>
    <?php endif; ?>

    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="card-body">
                        <!-- Logo -->
                        <img src="assets/images/logo ubmi.png" alt="Logo" class="logo">
                        <h4 class="card-title text-center mb-4">Login</h4>
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-3 text-muted">&copy; <?= date('Y') ?> Sistem Login</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>