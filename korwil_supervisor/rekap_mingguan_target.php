<?php
session_start();
include '../config/koneksi.php';

// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

$filter_kelp = $filter_minggu = $filter_bulan = '';
$total_result = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter_kelp = $_POST['kelompok'];
    $filter_minggu = $_POST['minggu'];
    $filter_bulan = $_POST['bulan'];

    $stmt_total = $conn->prepare("SELECT 
        SUM(target) AS total_target,
        SUM(cm) AS total_cm,
        SUM(mb) AS total_mb,
        SUM(drop_baru) AS total_dropbaru,
        SUM(t_masuk) AS total_tmasuk,
        SUM(t_keluar) AS total_tkeluar,
        SUM(t_jadi) AS total_tjadi
      FROM target_ubmi WHERE kelompok = ? AND minggu = ? AND bulan = ?");
    $stmt_total->bind_param("sss", $filter_kelp, $filter_minggu, $filter_bulan);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result()->fetch_assoc();
    $stmt_total->close();
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
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Target Ubmi</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Target Ubmi</span>
                    </li>

                </ol>
            </nav>
        </div>
        <style>
            @media (max-width: 576px) {

                #myTables th,
                #myTables td {
                    font-size: 12px;
                    padding: 6px 4px !important;
                }

                #myTables .btn i {
                    font-size: 14px;
                }

                #myTables img.wh-40 {
                    width: 28px;
                    height: 28px;
                }

                #myTables h6.fs-14 {
                    font-size: 12px !important;
                }
            }
        </style>

        <div class="container mt-4">

            <!-- HTML -->
            <div class="container mt-4">
                <h4>Rekap Total per Kelompok & Minggu</h4>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Bulan:</label>
                            <select class="form-control" name="bulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                <?php
                                $bulan_list = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                foreach ($bulan_list as $b) {
                                    $selected = ($filter_bulan == $b) ? 'selected' : '';
                                    echo "<option value='$b' $selected>$b</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Kelompok:</label>
                            <select class="form-control" name="kelompok" required>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="Kelompok <?= $i ?>" <?= ($filter_kelp == "Kelompok $i") ? 'selected' : '' ?>>Kelompok <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Minggu:</label>
                            <select class="form-control" name="minggu" required>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <option value="Minggu <?= $i ?>" <?= ($filter_minggu == "Minggu $i") ? 'selected' : '' ?>>Minggu <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Hitung Total</button>
                        </div>
                    </div>
                </form>
                <?php if (!empty($total_result)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header text-white" style="background-color: #5b8db8;">
                            <h5 class="mt-4 text-white">Rekap: <?= $filter_kelp ?> - <?= $filter_minggu ?> - <?= $filter_bulan ?></h5>


                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="card-title">Hasil Total</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Target: <?= number_format($total_result['total_target']) ?></li>
                                        <li class="list-group-item">CM: <?= number_format($total_result['total_cm']) ?></li>
                                        <li class="list-group-item">MB: <?= number_format($total_result['total_mb']) ?></li>
                                        <li class="list-group-item">Drop Baru: <?= number_format($total_result['total_dropbaru']) ?></li>
                                        <li class="list-group-item">T. Masuk: <?= number_format($total_result['total_tmasuk']) ?></li>
                                        <li class="list-group-item">T. Keluar: <?= number_format($total_result['total_tkeluar']) ?></li>
                                        <li class="list-group-item">T. Jadi: <?= number_format($total_result['total_tjadi']) ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>


    </div>
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
    <!-- Bootstrap 5 JS (di akhir sebelum </body>) -->
</body>