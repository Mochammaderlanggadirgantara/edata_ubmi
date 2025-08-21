<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

$bulan_global = $_POST['bulan'] ?? '';
$minggu_global = $_POST['minggu'] ?? '';
$data_per_hari = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt_global = $conn->prepare("SELECT hari,
        SUM(target) AS total_target,
        SUM(cm) AS total_cm,
        SUM(mb) AS total_mb,
        SUM(drop_baru) AS total_dropbaru,
        SUM(t_masuk) AS total_tmasuk,
        SUM(t_keluar) AS total_tkeluar,
        SUM(t_jadi) AS total_tjadi
      FROM target_ubmi WHERE bulan = ? AND minggu = ? GROUP BY hari");
    $stmt_global->bind_param("ss", $bulan_global, $minggu_global);
    $stmt_global->execute();
    $result_global = $stmt_global->get_result();

    while ($row = $result_global->fetch_assoc()) {
        $data_per_hari[] = $row;
    }
    $stmt_global->close();
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
            <h3 class="mb-0">Data Inventaris</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Inventaris</span>
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


        <!-- HTML -->
        <div class="container mt-4">
            <h4>Global Rekap Hari</h4>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Bulan:</label>
                        <select name="bulan" class="form-control" required>
                            <option value="">-- Pilih Bulan --</option>
                            <?php
                            $bulan_list = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            foreach ($bulan_list as $b) {
                                $selected = ($b == $bulan_global) ? 'selected' : '';
                                echo "<option value='$b' $selected>$b</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Minggu:</label>
                        <select name="minggu" class="form-control" required>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="Minggu <?= $i ?>" <?= ($minggu_global == "Minggu $i") ? 'selected' : '' ?>>Minggu <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </div>
            </form>
            <?php if (!empty($data_per_hari)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header text-white" style="background-color: #5b8db8;">
                        <h5 class="mb-0 text-white">Rekap Global: <?= htmlspecialchars($minggu_global) ?> - <?= htmlspecialchars($bulan_global) ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="simpan_rekap_global_target.php">
                            <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan_global) ?>">
                            <input type="hidden" name="minggu" value="<?= htmlspecialchars($minggu_global) ?>">

                            <?php foreach ($data_per_hari as $row): ?>
                                <div class="mb-4 p-3 border rounded bg-light">
                                    <h6 class="fw-bold mb-3"><?= htmlspecialchars($row['hari']) ?></h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>Target: <?= number_format($row['total_target']) ?></li>
                                        <li>CM: <?= number_format($row['total_cm']) ?></li>
                                        <li>MB: <?= number_format($row['total_mb']) ?></li>
                                        <li>Drop Baru: <?= number_format($row['total_dropbaru']) ?></li>
                                        <li>T. Masuk: <?= number_format($row['total_tmasuk'], 2) ?></li>
                                        <li>T. Keluar: <?= number_format($row['total_tkeluar']) ?></li>
                                        <li>T. Jadi: <?= number_format($row['total_tjadi'], 2) ?></li>
                                    </ul>

                                    <!-- Input hidden untuk menyimpan nilai -->
                                    <input type="hidden" name="hari[]" value="<?= $row['hari'] ?>">
                                    <input type="hidden" name="total_target[]" value="<?= $row['total_target'] ?>">
                                    <input type="hidden" name="total_cm[]" value="<?= $row['total_cm'] ?>">
                                    <input type="hidden" name="total_mb[]" value="<?= $row['total_mb'] ?>">
                                    <input type="hidden" name="total_dropbaru[]" value="<?= $row['total_dropbaru'] ?>">
                                    <input type="hidden" name="total_tmasuk[]" value="<?= $row['total_tmasuk'] ?>">
                                    <input type="hidden" name="total_tkeluar[]" value="<?= $row['total_tkeluar'] ?>">
                                    <input type="hidden" name="total_tjadi[]" value="<?= $row['total_tjadi'] ?>">
                                </div>
                            <?php endforeach; ?>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i> Simpan Rekap Global
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>


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