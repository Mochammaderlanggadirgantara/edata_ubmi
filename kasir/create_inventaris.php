<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
$allowed_roles = ['kasir', 'korwil'];

if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Ambil data user + cabang dari DB
$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT t.id_user, t.jabatan, t.nama_user, t.id_cabang, c.nama_cabang
                              FROM tuser t
                              JOIN cabang c ON t.id_cabang = c.id_cabang
                              WHERE t.id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query);

if (!$data_user) {
    die("Data user tidak ditemukan. Silakan login ulang.");
}

$id_cabang   = $data_user['id_cabang'];   // ini aman dipakai
$nama_user   = $data_user['nama_user'];
$jabatan     = strtolower($data_user['jabatan']);
$nama_cabang = $data_user['nama_cabang'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama_anggota   = $_POST['nama_anggota'];
    $jabatan        = $_POST['jabatan'];
    $jenis_kendaraan= $_POST['jenis_kendaraan'];
    $nomor_polisi   = $_POST['nomor_polisi'];
    $masa_berlaku   = $_POST['masa_berlaku'];

    $sql = "INSERT INTO inventaris (nama_anggota, jabatan, jenis_kendaraan, nomor_polisi, masa_berlaku, id_cabang)
            VALUES ('$nama_anggota', '$jabatan', '$jenis_kendaraan', '$nomor_polisi', '$masa_berlaku', '$id_cabang')";

    if (mysqli_query($conn, $sql)) {
        header("Location: inventaris.php");
        exit; // pastikan stop setelah redirect
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="zxx">

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
</head>

<body class="boxed-size">
    <!-- Start Preloader Area -->
    <div class="preloader" id="preloader">
        <div class="preloader">
            <div class="waviy position-relative">
                <span class="d-inline-block">T</span>
                <span class="d-inline-block">A</span>
                <span class="d-inline-block">S</span>
                <span class="d-inline-block">K</span>
                <span class="d-inline-block">S</span>
                <span class="d-inline-block">I</span>
                <span class="d-inline-block">G</span>
                <span class="d-inline-block">H</span>
                <span class="d-inline-block">T</span>
            </div>
        </div>
    </div>
    <!-- End Preloader Area -->
    <!-- End Preloader Area -->
    <?php


    if (isset($_SESSION['jabatan'])) {
        switch ($_SESSION['jabatan']) {
            case 'kasir':
                include '../navbar/navbar_kasir.php';
                break;
            case 'staff':
                include '../navbar/navbar_staff.php';
                break;
            case 'mantri':
                include '../navbar/navbar_mantri.php';
                break;
            case 'korwil':
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

    <div class="card bg-white border-0 rounded-3 mb-4">
        <form method="POST" action="../page/import_excel_inventaris.php" enctype="multipart/form-data">
            <label for="file_excel">Upload file Excel:</label>
            <input type="file" name="file_excel" accept=".xls,.xlsx" required>
            <button type="submit" class="btn btn-success mt-2">Import</button>
        </form>

        <div class="card-body p-4">
            <form method="POST" action="">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="nama_anggota" class="form-label">Nama Anggota</label>
                        <input type="text" class="form-control" id="nama_anggota" name="nama_anggota" placeholder="Contoh: MOH ILFAN AFIF" required>
                    </div>
                    <div class="col-md-6">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Contoh: KM 1" required>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
                        <input type="text" class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" placeholder="Contoh: REVO 110" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nomor_polisi" class="form-label">Nomor Polisi</label>
                        <input type="text" class="form-control" id="nomor_polisi" name="nomor_polisi" placeholder="Contoh: AG 6340 AAX" required>
                    </div>
                    <div class="col-md-6">
                        <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
                        <input type="date" class="form-control" id="masa_berlaku" name="masa_berlaku" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>



    <div class="flex-grow-1"></div>


    </div>
    </div>
    <!-- Start Main Content Area -->

    <!-- Link Of JS File -->
    <script src="/edata_ubmi/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/edata_ubmi/assets/js/sidebar-menu.js"></script>
    <script src="/edata_ubmi/assets/js/dragdrop.js"></script>
    <script src="/edata_ubmi/assets/js/rangeslider.min.js"></script>
    <script src="/edata_ubmi/assets/js/quill.min.js"></script>
    <script src="/edata_ubmi/assets/js/data-table.js"></script>
    <script src="/edata_ubmi/assets/js/prism.js"></script>
    <script src="/edata_ubmi/assets/js/clipboard.min.js"></script>
    <script src="/edata_ubmi/assets/js/feather.min.js"></script>
    <script src="/edata_ubmi/assets/js/simplebar.min.js"></script>
    <script src="/edata_ubmi/assets/js/apexcharts.min.js"></script>
    <script src="/edata_ubmi/assets/js/echarts.js"></script>
    <script src="/edata_ubmi/assets/js/swiper-bundle.min.js"></script>
    <script src="/edata_ubmi/assets/js/fullcalendar.main.js"></script>
    <script src="/edata_ubmi/assets/js/jsvectormap.min.js"></script>
    <script src="/edata_ubmi/assets/js/world-merc.js"></script>
    <script src="/edata_ubmi/assets/js/moment.min.js"></script>
    <script src="/edata_ubmi/assets/js/lightpick.js"></script>
    <script src="/edata_ubmi/assets/js/custom/apexcharts.js"></script>
    <script src="/edata_ubmi/assets/js/custom/echarts.js"></script>
    <script src="/edata_ubmi/assets/js/custom/custom.js"></script>
</body>



</html>