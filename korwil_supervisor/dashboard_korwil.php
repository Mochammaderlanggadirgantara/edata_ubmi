<?php
session_start();

include '../config/koneksi.php'; // pastikan koneksi dipan
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang = $_SESSION['id_cabang'];

// Ambil nama cabang
$nama_cabang = '';
if ($id_cabang) {
    $queryCabang = $conn->prepare("SELECT nama_cabang FROM cabang WHERE id_cabang = ?");
    $queryCabang->bind_param("i", $id_cabang);
    $queryCabang->execute();
    $resultCabang = $queryCabang->get_result()->fetch_assoc();
    if ($resultCabang) {
        $nama_cabang = $resultCabang['nama_cabang'];
    }
}

// Ambil nama kelompok
$nama_kelompok = '';
if ($id_kelompok) {
    $queryKelompok = $conn->prepare("SELECT nama_kelompok FROM kelompok_mantri WHERE id = ?");
    $queryKelompok->bind_param("i", $id_kelompok);
    $queryKelompok->execute();
    $resultKelompok = $queryKelompok->get_result()->fetch_assoc();
    if ($resultKelompok) {
        $nama_kelompok = $resultKelompok['nama_kelompok'];
    }
}
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
    <?php include '../navbar/navbar_korwil.php'; ?>
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

                                        <span class="text-danger-div">Rizal!</span>
                                        </h3>
                                        <p class="text-light">
                                            Here's what's happening with your store today.
                                        </p>
                                </div>


                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div
                                    class="welcome-img text-center text-sm-end mt-4 mt-sm-0">
                                    <img src="/edata_ubmi/assets/images/welcome.png" alt="welcome" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-4" style="padding-bottom: 0 !important">
                        <div class="mb-3 mb-lg-4">
                            <h3 class="mb-0">Team Overview</h3>
                        </div>
                        <div class="row">
                            <div class="col-xxl-6 col-xl-6 col-sm-6">
                                <a href="dashboard_babat1.php" style="text-decoration: none; color: inherit;">
                                    <div class="card bg-primary bg-opacity-10 border-primary border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-19">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined fs-40 text-primary">folder_open</i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h3 class="fs-20 mt-1 mb-0">Babat 1</h3>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                                <span class="fs-12">Data Babat 1</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-6 col-xl-6 col-sm-6">
                                <div
                                    class="card bg-danger bg-opacity-10 border-danger border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-19">
                                            <div class="flex-shrink-0">
                                                <i
                                                    class="material-symbols-outlined fs-40 text-danger">folder_open</i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <!-- <span>Staff admin</span> -->
                                                <h3 class="fs-20 mt-1 mb-0">Babat 2</h3>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                            <span class="fs-12">Data Babat 2</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-xl-6 col-sm-6">
                                <div
                                    class="card bg-success bg-opacity-10 border-success border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-19">
                                            <div class="flex-shrink-0">
                                                <i
                                                    class="material-symbols-outlined fs-40 text-success">folder_open</i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">

                                                <h3 class="fs-20 mt-1 mb-0">Babat 3</h3>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                            <span class="fs-12">Data Babat 3</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-6 col-xl-6 col-sm-6">
                                <div
                                    class="card bg-primary-div bg-opacity-10 border-primary-div border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-19">
                                            <div class="flex-shrink-0">
                                                <i
                                                    class="material-symbols-outlined fs-40 text-primary-div">folder_open</i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">

                                                <h3 class="fs-20 mt-1 mb-0">Babat 4</h3>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                            <span class="fs-12">Data Babat 4</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-grow-1"></div>
        <!-- Start Footer Area -->


    </div>
    <!-- Start Main Content Area -->

    <!-- Start Theme Setting Area -->
    <div
        class="offcanvas offcanvas-end bg-white"
        data-bs-scroll="true"
        data-bs-backdrop="true"
        tabindex="-1"
        id="offcanvasScrolling"
        aria-labelledby="offcanvasScrollingLabel"
        style="box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px">
        <div class="offcanvas-header bg-body-bg py-3 px-4">
            <h5 class="offcanvas-title fs-18" id="offcanvasScrollingLabel">
                Theme Settings
            </h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">RTL / LTR</h4>
                <div class="settings-btn rtl-btn">
                    <label id="switch" class="switch">
                        <input type="checkbox" onchange="toggleTheme()" id="slider" />
                        <span class="sliders round"></span>
                    </label>
                </div>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Container Style Fluid / Boxed
                </h4>
                <button
                    class="boxed-style settings-btn fluid-boxed-btn"
                    id="boxed-style">
                    Click To <span class="fluid">Fluid</span>
                    <span class="boxed">Boxed</span>
                </button>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Only Sidebar Light / Dark
                </h4>
                <button
                    class="sidebar-light-dark settings-btn sidebar-dark-btn"
                    id="sidebar-light-dark">
                    Click To <span class="dark1">Dark</span>
                    <span class="light1">Light</span>
                </button>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Only Header Light / Dark
                </h4>
                <button
                    class="header-light-dark settings-btn header-dark-btn"
                    id="header-light-dark">
                    Click To <span class="dark2">Dark</span>
                    <span class="light2">Light</span>
                </button>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Only Footer Light / Dark
                </h4>
                <button
                    class="footer-light-dark settings-btn footer-dark-btn"
                    id="footer-light-dark">
                    Click To <span class="dark3">Dark</span>
                    <span class="light3">Light</span>
                </button>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Card Style Radius / Square
                </h4>
                <button
                    class="card-radius-square settings-btn card-style-btn"
                    id="card-radius-square">
                    Click To <span class="square">Square</span>
                    <span class="radius">Radius</span>
                </button>
            </div>
            <div class="mb-4 pb-2">
                <h4 class="fs-15 fw-semibold border-bottom pb-2 mb-3">
                    Card Style BG White / Gray
                </h4>
                <button class="card-bg settings-btn card-bg-style-btn" id="card-bg">
                    Click To <span class="white">White</span>
                    <span class="gray">Gray</span>
                </button>
            </div>
        </div>
    </div>
    <!-- End Theme Setting Area -->

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