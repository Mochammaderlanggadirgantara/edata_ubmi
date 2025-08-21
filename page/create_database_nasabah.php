<?php
session_start();
include '../config/koneksi.php';

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

$id_cabang  = $data_user['id_cabang'];
$nama_user  = $data_user['nama_user'];
$jabatan    = strtolower($data_user['jabatan']);
$nama_cabang= $data_user['nama_cabang'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik_nasabah  = $_POST['nik_nasabah'];

    // Cek apakah NIK sudah ada
    $cek = $conn->query("SELECT * FROM nasabah WHERE nik_nasabah = '$nik_nasabah'");
    if ($cek->num_rows > 0) {
        echo "NIK Nasabah sudah terdaftar!";
        exit;
    }

    // Ambil data lain
    $no_anggota     = $_POST['no_anggota'];
    $nama_nasabah   = $_POST['nama_nasabah'];
    $domisili       = $_POST['domisili'];
    $tanggal_drop   = $_POST['tanggal_drop'];
    $pinjaman       = $_POST['pinjaman'];
    $hari           = $_POST['hari'];
    $klp            = $_POST['klp'];
    $kl             = $_POST['kl'];

    $sql = "INSERT INTO nasabah (nik_nasabah, no_anggota, nama_nasabah, domisili, tanggal_drop, pinjaman, hari, klp, kl)
            VALUES ('$nik_nasabah', '$no_anggota', '$nama_nasabah', '$domisili', '$tanggal_drop', '$pinjaman', '$hari', '$klp', '$kl')";

    if ($conn->query($sql) === TRUE) {
    // Redirect ke database_nasabah.php setelah simpan
    header("Location: database_nasabah.php");
    exit();
} else {
    echo "Error: " . $conn->error;
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
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_staff.php'; ?>
    <!-- End Navbar and Header Area -->



    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="no anggota" class="form-label">No Anggota</label>
                        <input type="text" class="form-control" id="no_anggota" name="no_anggota" placeholder="Contoh: 1" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nik_nasabah" class="form-label">NIK Nasabah</label>
                        <input type="text" class="form-control" id="nik_nasabah" name="nik_nasabah" placeholder="Contoh: 350820250***" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nama_nasabah" class="form-label">Nama Nasabah</label>
                        <input type="text" class="form-control" id="nama_nasabah" name="nama_nasabah" placeholder="Contoh: surti" required>
                    </div>
                    <div class="col-md-6">
                        <label for="domisili" class="form-label">Domisili</label>
                        <input type="text" class="form-control" id="domisili" name="domisili" placeholder="Contoh: badu" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_drop" class="form-label">Tanggal Drop</label>
                        <input type="date" class="form-control" id="tanggal_drop" name="tanggal_drop" required>
                    </div>
                    <div class="col-md-6">
                        <label for="pinjaman" class="form-label">Pinjaman</label>
                        <input type="text" class="form-control" id="pinjaman" name="pinjaman" required>
                    </div>
                    <div class="col-md-6">
                        <label for="hari" class="form-label">Hari</label>
                        <input type="text" class="form-control" id="hari" name="hari" required>
                    </div>
                    <div class="col-md-6">
                        <label for="klp" class="form-label">klp</label>
                        <input type="number" class="form-control" id="klp" name="klp" required>

                    </div>
                    <div class="col-md-6">
                        <label for="kl" class="form-label">KL</label>
                        <input type="text" class="form-control" id="kl" name="kl" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button type="button" class="btn btn-secondary px-4" onclick="window.location.href='database_nasabah.php'">Kembali</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>

                <script>
                    document.querySelector('form').addEventListener('submit', function() {
                        const inputs = this.querySelectorAll('input[type="text"]');
                        inputs.forEach(function(input) {
                            input.value = input.value.toUpperCase();
                        });
                    });
                </script>

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