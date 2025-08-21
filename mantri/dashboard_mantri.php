<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
if ($_SESSION['jabatan'] !== 'mantri') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
$id_cabang = $_SESSION['id_cabang'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="/edata_ubmi/assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/simplebar.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/apexcharts.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/prism.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/rangeslider.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/quill.snow.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/google-icon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/remixicon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/fullcalendar.main.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/jsvectormap.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/lightpick.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png" />
    <!-- Title -->
    <title>APP TASKSIGHTT</title>
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
    <?php include '../navbar/navbar_mantri.php'; ?>
    <!-- End Navbar and Header Area -->
    <div class="main-content-container overflow-hidden">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11 col-md-12"> <!-- Lebarkan container -->
                <div class="card bg-primary border-0 rounded-4 welcome-box mb-5">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <div class="border-bottom pb-3 mb-3">
                                    <h2 class="text-white fw-bold mb-2 fs-1"> <!-- Ukuran besar -->
                                        Selamat Pagi,

                                        <span class="text-warning"><?= htmlspecialchars($_SESSION['nama_user']) ?></span>
                                    </h2>
                                    <p class="text-light fs-5">Semoga Harimu menyenangkan</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 col-sm-12 text-center mt-4 mt-md-0">
                                <img src="/edata_ubmi/assets/images/welcome.png" alt="welcome" class="img-fluid" style="max-height: 180px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border-0 rounded-4 mb-5">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <h2 class="mb-0 fw-bold fs-3">Dashboard</h2>
                        </div>
                        <div class="row g-4">
                            <!-- Setiap kolom -->

                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/mantri/target_baru.php" style="text-decoration: none;">
                                    <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-primary">inventory_2</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Target baru</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Target Baru</span>
                                        </div>
                                    </div>
                                </a>
                            </div>



                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/kasir/tunai_babat.php" style="text-decoration: none;">
                                    <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-danger">payments</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Target Berjalan</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Target Berjalan</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/kasir/servis_inventaris.php" style="text-decoration: none;">
                                    <div class="card bg-success bg-opacity-10 border-success border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-success">build</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Data Statistik</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Data Statistik</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/page/database_nasabah.php" style="text-decoration: none;">
                                    <div class="card bg-info bg-opacity-10 border-info border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-info">database</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Catatan</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Catatan</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/page/sisa_saldo.php" style="text-decoration: none;">
                                    <div class="card bg-warning bg-opacity-10 border-warning border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-warning">account_balance_wallet</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Rencana</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Rencana</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xl-4 col-md-6">
                                <a href="/edata_ubmi/page/tabungan_kita.php" style="text-decoration: none;">
                                    <div class="card bg-secondary bg-opacity-10 border-secondary border-opacity-25 rounded-4 stats-box h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="material-symbols-outlined fs-40 text-secondary">wallet</i>
                                                <h4 class="fs-5 ms-3 mt-1 mb-0">Kalkulasi Program</h4>
                                            </div>
                                            <span class="fs-6 text-muted">Kalkulasi Program</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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