<?php
session_start();
include '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];

// Ambil nama cabang berdasarkan user
$query_user = "SELECT t.*, c.nama_cabang FROM tuser t LEFT JOIN cabang c ON t.id_cabang = c.id_cabang WHERE t.id_user = '$id_user'";
$result_user = mysqli_query($conn, $query_user);
$data_user = mysqli_fetch_assoc($result_user);
$nama_cabang = $data_user['nama_cabang'] ?? '-';

$filter_bulan = $_GET['bulan'] ?? '';
$filter_minggu = $_GET['minggu'] ?? '';
$filter_kelompok = $_GET['kelompok'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

// Ambil daftar bulan unik dari database
$data_bulan = mysqli_query($conn, "SELECT DISTINCT bulan FROM target_ubmi ORDER BY FIELD(bulan, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')");
$data_tahun = mysqli_query($conn, "SELECT DISTINCT tahun FROM target_ubmi ORDER BY tahun DESC");

// Siapkan query utama
$sql = "SELECT * FROM target_ubmi WHERE 1=1";

// Jika hanya filter bulan
if (!empty($filter_bulan) && empty($filter_minggu) && empty($filter_kelompok)) {
    $bulan = mysqli_real_escape_string($conn, $filter_bulan);
    $sql .= " AND bulan = '$bulan'";
}
if (!empty($filter_tahun)) {
    $tahun = mysqli_real_escape_string($conn, $filter_tahun);
    $sql .= " AND tahun = '$tahun'";
}

// Jika hanya filter minggu
elseif (!empty($filter_minggu) && empty($filter_bulan) && empty($filter_kelompok)) {
    $minggu = mysqli_real_escape_string($conn, $filter_minggu);
    $sql .= " AND minggu = '$minggu'";
}

// Jika hanya filter kelompok
elseif (!empty($filter_kelompok) && empty($filter_bulan) && empty($filter_minggu)) {
    $kelompok = mysqli_real_escape_string($conn, $filter_kelompok);
    $sql .= " AND kelompok = '$kelompok'";
}

// Jika kombinasi filter
else {
    if (!empty($filter_bulan)) {
        $bulan = mysqli_real_escape_string($conn, $filter_bulan);
        $sql .= " AND bulan = '$bulan'";
    }
    if (!empty($filter_minggu)) {
        $minggu = mysqli_real_escape_string($conn, $filter_minggu);
        $sql .= " AND minggu = '$minggu'";
    }
    if (!empty($filter_kelompok)) {
        $kelompok = mysqli_real_escape_string($conn, $filter_kelompok);
        $sql .= " AND kelompok = '$kelompok'";
    }
}


if (!empty($filter_kelompok)) {
    $data_minggu = mysqli_query($conn, "SELECT DISTINCT minggu FROM target_ubmi WHERE kelompok = '$filter_kelompok' ORDER BY minggu");
} else {
    $data_minggu = mysqli_query($conn, "SELECT DISTINCT minggu FROM target_ubmi ORDER BY minggu");
}


$sql .= " ORDER BY bulan, minggu, kelompok, hari";

// Eksekusi query
$result = mysqli_query($conn, $sql);

// Data untuk dropdown filter
$data_kelompok = mysqli_query($conn, "SELECT DISTINCT kelompok FROM target_ubmi ORDER BY kelompok ASC");
$data_minggu = mysqli_query($conn, "SELECT DISTINCT minggu FROM target_ubmi ORDER BY minggu ASC");
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

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
                    <button onclick="window.location.href='target_ubmi.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data</button>
                    <button onclick="window.location.href='form_tambah_target.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data Minggu</button>
                </div>

                <form method="GET" class="row g-3 mb-4">
                    <!-- Filter Tahun -->
                    <div class="col-md-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            <?php while ($t = mysqli_fetch_assoc($data_tahun)): ?>
                                <option value="<?= htmlspecialchars($t['tahun']) ?>" <?= (isset($_GET['tahun']) && $_GET['tahun'] == $t['tahun']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['tahun']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Filter Bulan -->
                    <div class="col-md-4">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            <?php while ($b = mysqli_fetch_assoc($data_bulan)): ?>
                                <option value="<?= htmlspecialchars($b['bulan']) ?>" <?= (isset($_GET['bulan']) && $_GET['bulan'] == $b['bulan']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['bulan']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Filter Minggu -->
                    <div class="col-md-4">
                        <label for="minggu" class="form-label">Minggu</label>
                        <select name="minggu" id="minggu" class="form-select">
                            <option value="">Semua Minggu</option>
                            <?php while ($m = mysqli_fetch_assoc($data_minggu)): ?>
                                <option value="<?= htmlspecialchars($m['minggu']) ?>" <?= (isset($_GET['minggu']) && $_GET['minggu'] == $m['minggu']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['minggu']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Filter Kelompok -->
                    <div class="col-md-4">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <select name="kelompok" id="kelompok" class="form-select">
                            <option value="">Semua Kelompok</option>
                            <?php while ($k = mysqli_fetch_assoc($data_kelompok)): ?>
                                <option value="<?= htmlspecialchars($k['kelompok']) ?>" <?= (isset($_GET['kelompok']) && $_GET['kelompok'] == $k['kelompok']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['kelompok']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Tombol -->
                    <div class="col-md-12 d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Tampilkan</button>
                        <a href="data_target_ubmi.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
                <style>
                    /* Style umum table */
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
                        vertical-align: top;
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
                </style>


                <div class="container my-4">
                    <div class="default-table-area all-products">
                        <h3 class="text-center title-section">Rekap Data Target Kelompok</h3>
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered ">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Tahun</th> <!-- Tambahan kolom Tahun -->
                                            <th>Minggu</th>
                                            <th>Kelompok</th>
                                            <th>Hari</th>
                                            <th>Target</th>
                                            <th>CM</th>
                                            <th>MB</th>
                                            <th>Drop Baru</th>
                                            <th>T. Masuk (13%)</th>
                                            <th>T. Keluar</th>
                                            <th>T. Jadi</th>
                                            <th>Aksi</th> <!-- ✅ Tambahan kolom Aksi -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td data-label="Bulan"><?= htmlspecialchars($row['bulan']) ?></td>
                                                <td data-label="Tahun"><?= htmlspecialchars($row['tahun']) ?></td>
                                                <td data-label="Minggu"><?= htmlspecialchars($row['minggu']) ?></td>
                                                <td data-label="Kelompok"><?= htmlspecialchars($row['kelompok']) ?></td>
                                                <td data-label="Hari"><?= htmlspecialchars($row['hari']) ?></td>
                                                <td data-label="Target"><?= number_format($row['target']) ?></td>
                                                <td data-label="CM"><?= number_format($row['cm']) ?></td>
                                                <td data-label="MB"><?= number_format($row['mb']) ?></td>
                                                <td data-label="Drop Baru"><?= number_format($row['drop_baru']) ?></td>
                                                <td data-label="T. Masuk"><?= number_format($row['t_masuk'], 2) ?></td>
                                                <td data-label="T. Keluar"><?= number_format($row['t_keluar']) ?></td>
                                                <td data-label="T. Jadi"><?= number_format($row['t_jadi'], 2) ?></td>
                                                <td data-label="Aksi">
                                                    <a href="edit_target_ubmi.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <button type="button" class="btn btn-sm text-white" style="background-color: #dc3545;" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id'] ?>">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Modal Konfirmasi Hapus -->
                                            <div class="modal fade" id="modalHapus<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalHapusLabel<?= $row['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: #dc3545; color: white;">
                                                            <h5 class="modal-title text-white" id="modalHapusLabel<?= $row['id'] ?>">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Apakah kamu yakin ingin menghapus data ini?<br>
                                                            <strong>Kelompok: <?= htmlspecialchars($row['kelompok']) ?>, Hari: <?= htmlspecialchars($row['hari']) ?></strong>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <a href="hapus_target_ubmi.php?id=<?= $row['id'] ?>" class="btn btn-danger" style="background-color: #dc3545; color: white;">Ya, Hapus</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info text-center">Belum ada data yang tersedia.</div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap mt-3">
                                <span class="fs-13 fw-medium">Items per pages: 10</span>
                                <div class="d-flex align-items-center">
                                    <span class="fs-13 fw-medium me-2">1 - 10 of 12</span>
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination mb-0 justify-content-center">
                                            <li class="page-item">
                                                <a class="page-link icon" href="#" aria-label="Previous">
                                                    <i class="material-symbols-outlined">keyboard_arrow_left</i>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link icon" href="#" aria-label="Next">
                                                    <i class="material-symbols-outlined">keyboard_arrow_right</i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- script File -->
    <script>
        // Load libraries
        const loadLibraries = () => {
            const script3 = document.createElement('script');
            script3.src = "https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js";
            document.head.appendChild(script3);

            const script5 = document.createElement('script');
            script5.src = "https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js";
            document.head.appendChild(script5);
        };

        // Call to load libraries
        loadLibraries();



        // Export to PDF
        function exportToPDF() {
            let {
                jsPDF
            } = window.jspdf;
            let doc = new jsPDF();
            doc.autoTable({
                html: "#myTables"
            });
            doc.save("table_data.pdf");
        }
    </script>

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