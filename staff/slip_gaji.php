<?php
session_start();
include '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan yang diizinkan
$allowed_roles = ['pengawas', 'staff']; // daftar jabatan yang boleh mengakses
if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
// Ambil data dengan JOIN ke cabang
$sql = "
    SELECT 
        d.id_detail,
        u.nama_user,
        c.nama_cabang,
        u.jabatan,
        u.tgl_masuk,
        m.gaji_bulan
    FROM turpas_detail d
    JOIN turpas_master m ON d.id_master = m.id_master
    JOIN tuser u ON d.id_user = u.id_user
    JOIN cabang c ON d.id_cabang = c.id_cabang
    ORDER BY d.id_detail DESC
";
$result = $conn->query($sql);
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
            <h3 class="mb-0">Data Slip Gaji</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Slip Gaji</span>
                    </li>

                </ol>
            </nav>
        </div>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4"
                        onclick="window.location.href='slip_all.php'">
                        Download as PDF
                    </button>

                    <button onclick="window.location.href='add_user.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data</button>
                </div>

                <div class="container mt-4">
                    <h3 class="mb-3">Daftar Data Turpas</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama User</th>
                                    <th>Cabang</th>
                                    <th>Jabatan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Gaji Bulan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $no = 1;
                                    while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                            <td><?= htmlspecialchars($row['tgl_masuk']) ?></td>
                                            <td><?= htmlspecialchars($row['gaji_bulan']) ?></td>
                                            <td>
                                                <a href="slip_gaji_detail.php?id_detail=<?= $row['id_detail'] ?>" class="btn btn-sm btn-info">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Data tidak tersedia</td>
                                    </tr>
                                <?php endif; ?>
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
</body>

</html>