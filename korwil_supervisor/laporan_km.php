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

// Ambil data user dari session (jika perlu)
$id_user = $_SESSION['id_user'] ?? null;
$jabatan = $_SESSION['jabatan'] ?? null;
$nama_user = $_SESSION['nama_user'] ?? null;

// Ambil filter bulan & tahun dari GET
$bulan = isset($_GET['bulan']) ? mysqli_real_escape_string($conn, $_GET['bulan']) : '';
$tahun = isset($_GET['tahun']) ? mysqli_real_escape_string($conn, $_GET['tahun']) : '';

// Buat kondisi filter untuk semua query
$where_filter = "";
if ($bulan != '') {
    $where_filter .= " AND bulan = '$bulan'";
}
if ($tahun != '') {
    $where_filter .= " AND tahun = '$tahun'";
}

/**
 * Query utama - ambil semua kolom hari per-row
 */
$query_all = "
    SELECT id, bulan, tahun, kelompok, katrol,
           senin_ml, senin_mb,
           selasa_ml, selasa_mb,
           rabu_ml, rabu_mb,
           kamis_ml, kamis_mb,
           jumat_ml, jumat_mb,
           sabtu_ml, sabtu_mb
    FROM pelunasan9
    WHERE 1=1 $where_filter
    ORDER BY tahun DESC, bulan DESC,
        FIELD(kelompok,
        'Kelompok 1','Kelompok 2','Kelompok 3','Kelompok 4','Kelompok 5',
        'Kelompok 6','Kelompok 7','Kelompok 8','Kelompok 9')
";
$result = mysqli_query($conn, $query_all);
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

/**
 * Total per kelompok
 */
$total_per_kelompok_q = "
    SELECT kelompok,
           SUM(katrol) AS total_katrol,
           SUM(
             COALESCE(senin_ml,0)+COALESCE(senin_mb,0) +
             COALESCE(selasa_ml,0)+COALESCE(selasa_mb,0) +
             COALESCE(rabu_ml,0)+COALESCE(rabu_mb,0) +
             COALESCE(kamis_ml,0)+COALESCE(kamis_mb,0) +
             COALESCE(jumat_ml,0)+COALESCE(jumat_mb,0) +
             COALESCE(sabtu_ml,0)+COALESCE(sabtu_mb,0)
           ) AS total_harian
    FROM pelunasan9
    WHERE 1=1 $where_filter
    GROUP BY kelompok
    ORDER BY FIELD(kelompok,
        'Kelompok 1','Kelompok 2','Kelompok 3','Kelompok 4','Kelompok 5',
        'Kelompok 6','Kelompok 7','Kelompok 8','Kelompok 9')
";
$total_per_kelompok = mysqli_query($conn, $total_per_kelompok_q);
if (!$total_per_kelompok) {
    die("Query total_per_kelompok gagal: " . mysqli_error($conn));
}

/**
 * Total per hari
 */
$total_per_hari_q = "
    SELECT 'Senin' AS hari,  SUM(COALESCE(senin_ml,0)+COALESCE(senin_mb,0))  AS total_mlmb FROM pelunasan9 WHERE 1=1 $where_filter
    UNION ALL
    SELECT 'Selasa', SUM(COALESCE(selasa_ml,0)+COALESCE(selasa_mb,0)) FROM pelunasan9 WHERE 1=1 $where_filter
    UNION ALL
    SELECT 'Rabu',   SUM(COALESCE(rabu_ml,0)+COALESCE(rabu_mb,0))     FROM pelunasan9 WHERE 1=1 $where_filter
    UNION ALL
    SELECT 'Kamis',  SUM(COALESCE(kamis_ml,0)+COALESCE(kamis_mb,0))   FROM pelunasan9 WHERE 1=1 $where_filter
    UNION ALL
    SELECT 'Jumat',  SUM(COALESCE(jumat_ml,0)+COALESCE(jumat_mb,0))   FROM pelunasan9 WHERE 1=1 $where_filter
    UNION ALL
    SELECT 'Sabtu',  SUM(COALESCE(sabtu_ml,0)+COALESCE(sabtu_mb,0))   FROM pelunasan9 WHERE 1=1 $where_filter
";
$total_per_hari = mysqli_query($conn, $total_per_hari_q);
if (!$total_per_hari) {
    die("Query total_per_hari gagal: " . mysqli_error($conn));
}
?>
<Style>
    @media (max-width: 768px) {
        table thead {
            display: none;
            /* Hilangkan header di layar kecil */
        }

        table tbody,
        table tfoot {
            display: block;
            width: 100%;
        }

        table tbody tr,
        table tfoot tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background: #fff;
            padding: 0.5rem;
        }

        table tbody td,
        table tfoot td {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem;
            border: none !important;
            border-bottom: 1px solid #f1f1f1 !important;
        }

        table tbody td:last-child,
        table tfoot td:last-child {
            border-bottom: none !important;
        }

        table tbody td::before,
        table tfoot td::before {
            content: attr(data-label);
            font-weight: bold;
            color: #495057;
        }
    }
</Style>
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
            case 'pengawas':
                include '../navbar/navbar_korwil.php';
                break;
                case 'pimpinan':
                include '../navbar/navbar_korwil.php';
                break;
                case 'kepala mantri':
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
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Pelunasan 9%</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Pelunasan 9%</span>
                    </li>

                </ol>
            </nav>
        </div>


        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
                    <button onclick="window.location.href='create_laporan.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data</button>
                </div>
                <!-- Form Filter -->
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    
                                     <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="kelompok" class="form-control">
                <option value="">-- Semua Kelompok --</option>
                <?php for($i=1;$i<=10;$i++): 
                    $selected = (isset($_GET['kelompok']) && $_GET['kelompok']=="Kelompok $i") ? "selected" : "";
                ?>
                    <option value="Kelompok <?= $i; ?>" <?= $selected; ?>>Kelompok <?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="cari" class="form-control" placeholder="Cari Nama Anggota..." value="<?= isset($_GET['cari']) ? $_GET['cari'] : '' ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>  
    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="container my-4">
                    <div class="default-table-area all-products">
                        <!-- TABEL DATA UTAMA -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kelompok</th>
                <th>No Anggota</th>
                <th>Hari & Tanggal</th>
                <th>Nama Anggota</th>
                <th>Pinjaman</th>
                <th>Sisa Saldo</th>
                <th>Keterangan</th>
                <th>Petugas Kontrol</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        $result = $conn->query("SELECT * FROM laporan_km ORDER BY id DESC");
        while($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['kelompok']; ?></td>
                <td><?= $row['no_anggota']; ?></td>
                <td><?= $row['hari_tanggal']; ?></td>
                <td><?= $row['nama_anggota']; ?></td>
                <td><?= number_format($row['pinjaman'],0,',','.'); ?></td>
                <td><?= number_format($row['sisa_saldo'],0,',','.'); ?></td>
                <td><?= $row['keterangan_pelanggan']; ?></td>
                <td><?= $row['petugas_kontrol']; ?></td>
                <td>
                    <a href="update_laporan.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_laporan.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
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