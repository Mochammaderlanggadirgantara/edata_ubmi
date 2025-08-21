<?php
session_start();
include "../config/koneksi.php";

// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Filter bulan & tahun
$bulan_filter = $_GET['bulan'] ?? date('F');
$tahun_filter = $_GET['tahun'] ?? date('Y');

// Filter minggu & hari
$minggu = $_GET['minggu'] ?? '1';
$hari_filter = $_GET['hari'] ?? 'Senin';

// Karena di DB minggu tersimpan sebagai "Minggu 1", "Minggu 2", dst
$minggu_db = "Minggu " . $minggu;

// Pilih kolom target/t_jadi
$kolom_value = ($minggu == '1') ? "tu.target" : "tu.t_jadi";

// Daftar hari
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// Ambil data sesuai filter
$sql = "SELECT 
            tu.hari,
            tu.kelompok,
            tu.bulan,
            tu.tahun,
            tu.minggu,
            $kolom_value AS target,
            dsl.id,
            dsl.rencana,
            dsl.storting,
            dsl.storting_tg,
            dsl.min_plus
        FROM target_ubmi tu
        LEFT JOIN data_statistik_leader dsl 
          ON tu.hari = dsl.hari 
         AND tu.kelompok = dsl.kelompok 
         AND tu.minggu = dsl.minggu
         AND tu.bulan = dsl.bulan
         AND tu.tahun = dsl.tahun
        WHERE tu.hari = ?
          AND tu.bulan = ?
          AND tu.tahun = ?
          AND tu.minggu = ?
        ORDER BY tu.kelompok";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $hari_filter, $bulan_filter, $tahun_filter, $minggu_db);
$stmt->execute();
$result = $stmt->get_result();
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
    <!-- Styles -->
    <style>
        /* Table umum */
        .default-table-area table {
            width: 100%;
            table-layout: auto;
            white-space: nowrap;
        }

        .default-table-area td,
        .default-table-area th {
            white-space: normal;
            word-break: break-word;
            padding: 0.75rem;
            vertical-align: middle;
        }

        .default-table-area img {
            max-width: 40px;
            height: auto;
            border-radius: 8px;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {

            .default-table-area table,
            .default-table-area thead,
            .default-table-area tbody,
            .default-table-area th,
            .default-table-area td,
            .default-table-area tr {
                display: block;
                width: 100%;
            }

            .default-table-area thead {
                display: none;
            }

            .default-table-area tbody tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                padding: 1rem;
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .default-table-area td {
                display: block;
                width: 100%;
                padding: 0.5rem 0;
                border: none;
                border-bottom: 1px solid #f1f1f1;
                font-size: 14px;
            }

            .default-table-area td::before {
                content: attr(data-label);
                display: block;
                font-weight: 600;
                color: #6c757d;
                margin-bottom: 0.25rem;
                font-size: 13px;
            }

            .default-table-area td:last-child {
                border-bottom: none;
            }
        }

        /* Responsif untuk HP kecil */
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

        table.data-table {
            table-layout: fixed;
            width: 100%;
            /* Maksimalkan lebar */
            word-wrap: break-word;
            font-size: 14px;
        }

        table.data-table th,
        table.data-table td {
            text-align: center;
            /* rata tengah isi tabel */
            vertical-align: middle;
        }

        table.data-table tfoot td {
            font-weight: bold;
            background: #f8f9fa;
            /* warna abu lembut */
            text-align: center;
            /* rata tengah */
        }

        /* Lebar tiap kolom disesuaikan biar proporsional */
        .col-hari {
            width: 10%;
        }

        .col-kelompok {
            width: 12%;
        }

        .col-target,
        .col-rencana,
        .col-storting,
        .col-stortingtg,
        .col-minplus {
            width: 12%;
        }

        .col-aksi {
            width: 10%;
        }
    </style>
</head>

<body class="boxed-size">

    <!-- Start Preloader Area -->
    <!-- <div class="preloader" id="preloader">
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
    </div> -->
    <!-- End Preloader Area -->

    <!-- End Preloader Area -->
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <!-- Header + Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Inventaris</h3>
            <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
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

        <!-- Card Container -->
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">

                <!-- Button Action -->
                <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
                    <button onclick="window.location.href='target_ubmi.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">
                        Tambah Data
                    </button>
                </div>

                <!-- Statistik Section -->
                <div class="container my-4">
                    <div class="default-table-area all-products">
                        <h3 class="text-center title-section">Data Statistik</h3>

                        <!-- Filter Bulan & Tahun -->
                        <form method="get" class="mb-3 d-inline-flex gap-2 align-items-center">
                            <label class="form-label mb-0">Bulan:</label>
                            <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
                                <?php
                                $bulan_all = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                foreach ($bulan_all as $b) {
                                    $sel = ($b == $bulan_filter) ? "selected" : "";
                                    echo "<option value='$b' $sel>$b</option>";
                                }
                                ?>
                            </select>

                            <label class="form-label mb-0">Tahun:</label>
                            <input type="number" name="tahun" value="<?= $tahun_filter ?>" class="form-control w-auto" onchange="this.form.submit()">

                            <input type="hidden" name="hari" value="<?= $hari_filter ?>">
                        </form>

                        <!-- Tab Hari -->
                        <ul class="nav nav-tabs mb-3">
                            <?php foreach ($hari_list as $hari): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($hari_filter == $hari) ? 'active' : '' ?>"
                                        href="?bulan=<?= $bulan_filter ?>&tahun=<?= $tahun_filter ?>&minggu=<?= $minggu ?>&hari=<?= $hari ?>">
                                        <?= $hari ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Tombol Minggu -->
                        <div class="mb-3">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <a href="?bulan=<?= $bulan_filter ?>&tahun=<?= $tahun_filter ?>&minggu=<?= $i ?>&hari=<?= $hari_filter ?>"
                                    class="btn <?= ($minggu == $i) ? 'btn-primary' : 'btn-outline-primary' ?> me-2 mb-2">Minggu <?= $i ?></a>
                            <?php endfor; ?>
                            <a href="?bulan=<?= $bulan_filter ?>&tahun=<?= $tahun_filter ?>&minggu=all&hari=<?= $hari_filter ?>"
                                class="btn <?= ($minggu == 'all') ? 'btn-primary' : 'btn-outline-primary' ?> mb-2">Semua Minggu</a>
                        </div>

                        <!-- Tabel -->
                        <form method="post" action="update_data_statistik.php">
                            <div class="table-responsive">
                                <table id="statistikTable" class="table table-bordered table-hover align-middle text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Hari</th>
                                            <th>Kelompok</th>
                                            <th>Bulan</th>
                                            <th>Target / T.Jadi</th>
                                            <th>Rencana</th>
                                            <th>Storting</th>
                                            <th>Storting TG</th>
                                            <th>Min / Plus</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()):
                                            $key = $row['id'] ?: $row['hari'] . '_' . $row['kelompok'];
                                        ?>
                                            <tr>
                                                <td><?= $row['hari'] ?></td>
                                                <td><?= $row['kelompok'] ?></td>
                                                <td><?= $row['bulan'] ?></td>
                                                <td>
                                                    <input type="number" class="form-control text-center target"
                                                        value="<?= $row['target'] ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control text-center rencana" name="rencana[<?= $key ?>]" value="<?= $row['rencana'] ?? 0 ?>">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control text-center storting" name="storting[<?= $key ?>]" value="<?= $row['storting'] ?? 0 ?>">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control text-center storting_tg" value="<?= $row['storting_tg'] ?? 0 ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control text-center min_plus" value="<?= $row['min_plus'] ?? 0 ?>" readonly>
                                                </td>
                                                <td>
                                                    <button type="submit" name="update" value="<?= $key ?>" class="btn btn-success btn-sm">💾 Simpan</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot class="fw-bold">
                                        <tr>
                                            <td colspan="4">Total Global</td>
                                            <td id="total-rencana">0</td>
                                            <td id="total-storting">0</td>
                                            <td id="total-stortingtg">0</td>
                                            <td id="total-minplus">0</td>
                                            <td>-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- script File -->
    <!-- JS Realtime -->
    <script>
        function formatNumber(x) {
            return new Intl.NumberFormat().format(x);
        }

        function hitungTotal() {
            let totalRencana = 0,
                totalStorting = 0,
                totalStortingTG = 0,
                totalMinPlus = 0;
            document.querySelectorAll("#statistikTable tbody tr").forEach(tr => {
                let r = parseFloat(tr.querySelector(".rencana")?.value) || 0;
                let s = parseFloat(tr.querySelector(".storting")?.value) || 0;
                let stg = parseFloat(tr.querySelector(".storting_tg")?.value) || 0;
                let mp = parseFloat(tr.querySelector(".min_plus")?.value) || 0;
                totalRencana += r;
                totalStorting += s;
                totalStortingTG += stg;
                totalMinPlus += mp;
            });
            document.getElementById("total-rencana").innerText = formatNumber(totalRencana);
            document.getElementById("total-storting").innerText = formatNumber(totalStorting);
            document.getElementById("total-stortingtg").innerText = formatNumber(totalStortingTG);
            document.getElementById("total-minplus").innerText = formatNumber(totalMinPlus);
        }

        document.addEventListener("DOMContentLoaded", function() {
            hitungTotal();

            document.querySelectorAll(".rencana, .storting").forEach(input => {
                input.addEventListener("input", function() {
                    let tr = this.closest("tr");
                    let target = parseFloat(tr.querySelector(".target").value.replace(/,/g, '')) || 0;
                    let rencana = parseFloat(tr.querySelector(".rencana").value.replace(/,/g, '')) || 0;
                    let storting = parseFloat(tr.querySelector(".storting").value.replace(/,/g, '')) || 0;

                    let tg = target + (rencana * 0.26);
                    tr.querySelector(".storting_tg").value = formatNumber(tg.toFixed(2));
                    tr.querySelector(".min_plus").value = formatNumber((storting - tg).toFixed(2));

                    hitungTotal();
                });
            });
        });
    </script>

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