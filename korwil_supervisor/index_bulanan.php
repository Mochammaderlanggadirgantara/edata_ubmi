<?php
include '../config/koneksi.php';
$result = $conn->query("SELECT * FROM index_bulanan ORDER BY tahun DESC, bulan DESC, klp ASC");

// Hitung total global
$sum = $conn->query("SELECT 
        SUM(program) as total_program,
        SUM(storting_valid) as total_storting
    FROM index_bulanan")->fetch_assoc();

$total_program = $sum['total_program'] ?? 0;
$total_storting = $sum['total_storting'] ?? 0;
$total_index = ($total_storting > 0) ? ($total_program / $total_storting * 100) : 0;

// Hitung total per KLP
$perKlp = $conn->query("SELECT 
        klp, 
        SUM(program) as total_program,
        SUM(storting_valid) as total_storting
    FROM index_bulanan
    GROUP BY klp
    ORDER BY klp ASC");
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
<style>
/* Tambahan untuk mempercantik tabel */
.table thead th {
    background: linear-gradient(90deg, #343a40, #495057);
    color: #fff;
    text-align: center;
    font-size: 14px;
}
.table td, .table th {
    vertical-align: middle;
    text-align: center;
}

/* Membuat tabel jadi responsive stacked di HP */
@media (max-width: 768px) {
    .responsive-table table,
    .responsive-table thead,
    .responsive-table tbody,
    .responsive-table th,
    .responsive-table td,
    .responsive-table tr {
        display: block;
        width: 100%;
    }
    .responsive-table thead {
        display: none;
    }
    .responsive-table tbody tr {
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 1rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        background: #fff;
    }
    .responsive-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border: none;
        border-bottom: 1px solid #f1f1f1;
        font-size: 14px;
    }
    .responsive-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6c757d;
    }
    .responsive-table td:last-child {
        border-bottom: none;
    }
}

/* Tombol */
.btn-sm {
    padding: 4px 8px;
    border-radius: 6px;
}
</style>

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
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">PERHITUNGAN INDEX AKHIR BULAN</h3>
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Kalkulasi KM</span>
                    </li>

                </ol>
            </nav>
        </div>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">

            <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
                    <button onclick="window.location.href='create_index_bulanan.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data</button>
                </div>
                <div class="container my-4">

    <!-- Data Utama -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            📊 Data Index Bulanan
        </div>
        <div class="card-body responsive-table">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Kelompok</th>
                        <th>Program</th>
                        <th>Storting Valid</th>
                        <th>Index (%)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="No"><?= $no++ ?></td>
                        <td data-label="Bulan"><?= $row['bulan'] ?></td>
                        <td data-label="Tahun"><?= $row['tahun'] ?></td>
                        <td data-label="Kelompok">Kelompok <?= $row['klp'] ?></td>
                        <td data-label="Program"><?= number_format($row['program'],0,',','.') ?></td>
                        <td data-label="Storting Valid"><?= number_format($row['storting_valid'],0,',','.') ?></td>
                        <td data-label="Index (%)"><?= number_format($row['idx_akhir'],2,',','.') ?>%</td>
                        <td data-label="Aksi">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-danger btn-sm">🗑️</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan Global -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            📌 Ringkasan Global
        </div>
        <div class="card-body responsive-table">
            <table class="table table-bordered mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Total Program</th>
                        <th>Total Storting Valid</th>
                        <th>Total Index (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Total Program"><?= number_format($total_program,0,',','.') ?></td>
                        <td data-label="Total Storting"><?= number_format($total_storting,0,',','.') ?></td>
                        <td data-label="Total Index"><strong><?= number_format($total_index,2,',','.') ?>%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan per Kelompok -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            📌 Ringkasan per Kelompok
        </div>
        <div class="card-body responsive-table">
            <table class="table table-bordered mb-0">
                <thead class="table-info">
                    <tr>
                        <th>Kelompok</th>
                        <th>Total Program</th>
                        <th>Total Storting Valid</th>
                        <th>Index (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = $perKlp->fetch_assoc()): 
                        $idx = ($r['total_storting'] > 0) ? ($r['total_program'] / $r['total_storting'] * 100) : 0;
                    ?>
                    <tr>
                        <td data-label="Kelompok">Kelompok <?= $r['klp'] ?></td>
                        <td data-label="Total Program"><?= number_format($r['total_program'],0,',','.') ?></td>
                        <td data-label="Total Storting"><?= number_format($r['total_storting'],0,',','.') ?></td>
                        <td data-label="Index"><?= number_format($idx,2,',','.') ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
    <!-- Bootstrap 5 JS (di akhir sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>