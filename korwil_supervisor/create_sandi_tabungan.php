<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
// Ambil data cabang
$cabang = $conn->query("SELECT * FROM cabang");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kata_sandi = $_POST['kata_sandi'];
    $id_cabang = $_POST['id_cabang'];

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO sandi (kata_sandi, id_cabang) VALUES (?, ?)");
    $stmt->bind_param("si", $kata_sandi, $id_cabang);
    $stmt->execute();

    header("Location: sandi_tabungan.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="/edata_ubmi/assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/simplebar.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/apexcharts.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/prism.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/rangeslider.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/quill.snow.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/google-icon.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/remixicon.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/fullcalendar.main.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/jsvectormap.min.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/lightpick.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png">
    <!-- Title -->
    <title>Aplikasi TaskSight</title>
    <style>
        #myTables td {
            text-transform: uppercase;
        }
    </style>

</head>

<body class="container mt-5">
    <!-- End Preloader Area -->
    <?php


    if (isset($_SESSION['jabatan'])) {
        switch ($_SESSION['jabatan']) {
           
            case 'pengawas':
                include '../navbar/navbar_korwil.php';
                break;
            default:
                echo "<p class='text-danger'>Jabatan tidak dikenali.</p>";
                break;
        }
    } else {
        echo "<p class='text-danger'>Session jabatan belum diset. Silakan login terlebih dahulu.</p>";
    }
    ?>
    <h3>Tambah Kata Sandi</h3>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="kata_sandi" class="form-label">Kata Sandi</label>
            <input type="password" name="kata_sandi" id="kata_sandi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="id_cabang" class="form-label">Pilih Cabang</label>
            <select name="id_cabang" id="id_cabang" class="form-select" required>
                <option value="">-- Pilih Cabang --</option>
                <?php while ($row = $cabang->fetch_assoc()): ?>
                    <option value="<?= $row['id_cabang'] ?>"><?= $row['nama_cabang'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
    <script src="/edata_ubmi/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>