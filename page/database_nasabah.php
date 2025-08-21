<?php
session_start();
include '../config/koneksi.php';
$query = "SELECT * FROM nasabah";
$result = mysqli_query($conn, $query);

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
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
        #myTables td {
            text-transform: uppercase;
        }
    </style>

</head>

<body class="boxed-size">
    <!-- Start Preloader Area -->

    <!-- End Preloader Area -->
    <!-- End Preloader Area -->
    <?php


    if (isset($_SESSION['jabatan'])) {
        switch ($_SESSION['jabatan']) {
            case 'kasir':
                include '../navbar/navbar_kasir.php';
                break;
            case 'staff':
                include '../navbar/navbar_staff.php';
                break;
            case 'mantri':
                include '../navbar/navbar_mantri.php';
                break;
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
            <h3 class="mb-0">Data Nasabah</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Nasabah</span>
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

                    <?php if (isset($_SESSION['jabatan']) && strtolower($_SESSION['jabatan']) === 'kasir'): ?>
                        <button onclick="window.location.href='create_database_nasabah.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">
                            Tambah Data
                        </button>
                    <?php endif; ?>
                </div>


                <style>
                    .default-table-area table {
                        table-layout: auto;
                        width: 100%;
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

                    /* Responsive stacked table on small screens */
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
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                        }

                        .default-table-area td {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 0.5rem 0;
                            border: none;
                            border-bottom: 1px solid #f1f1f1;
                        }

                        .default-table-area td::before {
                            content: attr(data-label);
                            font-weight: 600;
                            color: #6c757d;
                        }

                        .default-table-area td:last-child {
                            border-bottom: none;
                        }
                    }
                </style>

                <div class="d-flex justify-content-center mb-3">
                    <form method="GET" class="d-flex flex-wrap gap-2 justify-content-center" style="max-width: 600px; width: 100%;">
                        <input type="text" class="form-control" name="search" placeholder="Cari Nama atau NIK" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">Cari</button>
                        <?php if (!empty($_GET['search'])): ?>
                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-danger">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>


                <?php if (!empty($search)): ?>
                    <p class="text-muted">Hasil pencarian untuk: <strong><?= htmlspecialchars($search) ?></strong></p>
                <?php endif; ?>

                <div class="container-fluid my-4">
                    <div class="default-table-area all-products">
                        <table class="table align-middle w-100" id="myTables">
                            <thead style="font-size: 10.4px; white-space: nowrap;">
                                <tr>
                                    <th>No Anggota</th>
                                    <th>Nik Nasabah</th>
                                    <th>Nama Nasabah</th>
                                    <th>Domisili</th>
                                    <th>Tanggal Drop</th>
                                    <th>Pinjaman</th>
                                    <th>Hari</th>
                                    <th>KLP</th>
                                    <th>KL</th>
                                    <th>Aksi</th> <!-- Tambahan -->
                                </tr>
                            </thead>
                            <tbody style="font-size: 10,4px;white-space: nowrap;">
                                <?php


                                $search = $_GET['search'] ?? '';
                                $search = $conn->real_escape_string($search);

                                if (!empty($search)) {
                                    $query = "SELECT * FROM nasabah 
              WHERE nama_nasabah LIKE '%$search%' 
              OR nik_nasabah LIKE '%$search%' 
              ORDER BY id DESC";
                                } else {
                                    $query = "SELECT * FROM nasabah ORDER BY id DESC";
                                }

                                $result = $conn->query($query);


                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['no_anggota']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nik_nasabah'] ?? '-') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_nasabah']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['domisili']) . "</td>";
                                        echo "<td>" . htmlspecialchars(date("d-m-Y", strtotime($row['tanggal_drop']))) . "</td>";
                                        echo "<td>" . number_format($row['pinjaman'], 0, ',', '.') . "</td>";
                                        echo "<td>" . $row['hari'] . "</td>";
                                        echo "<td>" . $row['klp'] . "</td>";
                                        echo "<td>" . $row['kl'] . "</td>";

                                        // Tombol Edit & Hapus
                                        $id = $row['id'];

                                        $btn_disabled = empty($nik) ? 'disabled' : '';
                                        echo "<td>
<a href='edit_database_nasabah.php?id={$id}' class='btn btn-sm btn-warning me-1'>Edit</a>
                    <button class='btn btn-sm btn-danger' onclick=\"confirmDelete('{$id}')\">Hapus</button>

                </td>";

                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Belum ada data nasabah.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

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

    <!-- script File -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: 'Data ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_nasabah.php?id=' + id;
                }
            });
        }
    </script>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data berhasil dihapus.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menghapus data.',
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        </script>
    <?php endif; ?>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>