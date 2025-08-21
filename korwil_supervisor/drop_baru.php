<?php
session_start();
include '../config/koneksi.php';

// Cek login dan hak akses
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
$allowed_roles = ['pengawas'];
if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];

// Array bulan
$daftarBulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

// Bulan sekarang dan bulan kemarin
$bulanSekarang = $daftarBulan[date('n')];
$bulanKemarin = $daftarBulan[date('n') - 1 > 0 ? date('n') - 1 : 12];

// Ambil data bulan sekarang
$sqlNow = "SELECT * FROM drop_baru WHERE bulan = '$bulanSekarang' ORDER BY kelompok ASC";
$resultNow = mysqli_query($conn, $sqlNow);
$rowsNow = mysqli_fetch_all($resultNow, MYSQLI_ASSOC);

// Ambil data bulan kemarin
$sqlPrev = "SELECT * FROM drop_baru WHERE bulan = '$bulanKemarin' ORDER BY kelompok ASC";
$resultPrev = mysqli_query($conn, $sqlPrev);
$rowsPrev = mysqli_fetch_all($resultPrev, MYSQLI_ASSOC);

// Fungsi hitung total
function hitungTotals($rows)
{
    $totalDrop = $totalJumlah = $totalSisa = 0;
    $totalsPerBulan = array_fill(1, 12, 0);
    foreach ($rows as $row) {
        $totalDrop += $row['drop_baru'];
        $totalJumlah += $row['jumlah'];
        $totalSisa += $row['sisa_baru'];
        for ($i = 1; $i <= 12; $i++) {
            $totalsPerBulan[$i] += $row["data$i"];
        }
    }
    return compact('totalDrop', 'totalJumlah', 'totalSisa', 'totalsPerBulan');
}

$totalsNow = hitungTotals($rowsNow);
$totalsPrev = hitungTotals($rowsPrev);
?>



<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Tambahkan FontAwesome CDN di <head> jika belum -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
        /* Global Table */
        .default-table-area table {
            width: 100%;
            border-collapse: collapse;
        }

        .default-table-area th,
        .default-table-area td {
            padding: 0.75rem;
            font-size: 14px;
            vertical-align: middle;
        }

        .default-table-area thead th {
            background: #343a40;
            color: #fff;
            text-align: center;
            font-size: 13px;
        }

        .default-table-area tbody tr:hover {
            background-color: #f8f9fa;
            transition: 0.2s ease;
        }

        tfoot td {
            background: #f1f3f5;
        }

        /* --- Mobile View --- */
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
                background: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .default-table-area td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.4rem 0;
                border: none;
                border-bottom: 1px solid #f1f1f1;
                font-size: 13px;
            }

            .default-table-area td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #6c757d;
                flex: 1;
            }

            .default-table-area td:last-child {
                border-bottom: none;
            }

            tfoot {
                display: none;
                /* hide total on mobile biar ringkas */
            }
        }

        body {
            background: #f5f6fa;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        table {
            font-size: 14px;
        }

        th {
            background: #304861;
            color: white;
            text-align: center;
        }

        td {
            text-align: right;
        }

        td:first-child,
        td:nth-child(2),
        td:last-child {
            text-align: center;
        }

        /* Untuk desktop - kecilkan font tabel */
        @media (min-width: 992px) {

            #myTables th,
            #myTables td {
                font-size: 12px;
                /* lebih kecil biar muat */
                padding: 4px 6px;
                /* rapatkan padding */
            }

            #myTables th {
                font-weight: 600;
            }
        }

        #myTables th,
        #myTables td {
            padding: 2px 4px !important;
        }

        #myTables th,
        #myTables td {
            padding: 2px 4px;
            /* lebih rapat */
            font-size: 11px;
            /* kecil lagi */
            white-space: nowrap;
            /* biar ga turun ke bawah */
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
    <?php


    if (isset($_SESSION['jabatan'])) {
        switch ($_SESSION['jabatan']) {

            case 'pengawas':
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
    <div class="container-fluid">
        <div class="main-content-container overflow-hidden">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h3 class="mb-0">Data Drop Baru</h3>

                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="#" class="d-flex align-items-center text-decoration-none">
                                <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                                <span class="text-secondary fw-medium hover">Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Data Drop Baru</span>
                        </li>

                    </ol>
                </nav>
            </div>
            <!-- Fungsi tampil tabel -->
            <?php function tampilTabel($rows, $totals, $bulanLabel)
            { ?>
                <div class="container my-4">
                    <h3>Data Bulan <?= $bulanLabel ?></h3>
                    <div class="mb-3 d-flex gap-3">
                        <div class="card shadow-sm text-center flex-fill">
                            <div class="card-body p-2">
                                <h6 class="mb-1">Grand Total Jumlah</h6>
                                <h5 class="fw-bold text-primary mb-0"><?= number_format($totals['totalJumlah'], 0, ',', '.') ?></h5>
                            </div>
                        </div>
                        <div class="card shadow-sm text-center flex-fill">
                            <div class="card-body p-2">
                                <h6 class="mb-1">Grand Total Sisa Baru</h6>
                                <h5 class="fw-bold text-danger mb-0"><?= number_format($totals['totalSisa'], 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($rows)): ?>
                        <div class="default-table-area all-products" style="overflow-x:auto;">
                            <table class="table table-bordered align-middle table-sm text-nowrap" style="font-size:11px; padding:2px;">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Klp</th>
                                        <th rowspan="2">Drop Baru</th>
                                        <th colspan="6" class="text-center">data 1 - 6</th>
                                        <th colspan="6" class="text-center">data 7 - 12</th>
                                        <th rowspan="2">Jumlah</th>
                                        <th rowspan="2">Sisa Baru</th>
                                        <th rowspan="2">Aksi</th>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <th><?= $i ?></th>
                                        <?php endfor; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandJumlah = 0;
                                    $grandSisa = 0;
                                    foreach ($rows as $row):
                                        $jumlahInputan = 0;
                                        for ($i = 1; $i <= 12; $i++) $jumlahInputan += $row["data$i"];
                                        $sisaBaru = $row['drop_baru'] - $jumlahInputan;
                                        $grandJumlah += $jumlahInputan;
                                        $grandSisa += $sisaBaru;
                                    ?>
                                        <tr>
                                            <td data-label="Kelompok"><?= $row['kelompok'] ?></td>
                                            <td data-label="Drop Baru" class="text-end"><?= number_format($row['drop_baru'], 0, ',', '.') ?></td>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <td data-label="Bulan <?= $i ?>" class="text-end"><?= number_format($row["data$i"], 0, ',', '.') ?></td>
                                            <?php endfor; ?>
                                            <td data-label="Jumlah" class="text-end fw-bold"><?= number_format($jumlahInputan, 0, ',', '.') ?></td>
                                            <td data-label="Sisa Baru" class="text-end fw-bold text-danger"><?= number_format($sisaBaru, 0, ',', '.') ?></td>
                                            <td data-label="Aksi" class="text-center">
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td>Global Total</td>
                                        <td class="text-end"><?= number_format($totals['totalDrop'], 0, ',', '.') ?></td>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <td class="text-end"><?= number_format($totals['totalsPerBulan'][$i], 0, ',', '.') ?></td>
                                        <?php endfor; ?>
                                        <td class="text-end"><?= number_format($grandJumlah, 0, ',', '.') ?></td>
                                        <td class="text-end"><?= number_format($grandSisa, 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- MODE MOBILE -->
                        <div class="d-block d-md-none">
                            <?php foreach ($rows as $row): ?>
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2">Kelompok: <?= $row['kelompok'] ?></h6>
                                        <p class="mb-1"><strong>Drop Baru:</strong> <?= number_format($row['drop_baru'], 0, ',', '.') ?></p>
                                        <p class="mb-1"><strong>Jumlah:</strong> <?= number_format($row['jumlah'], 0, ',', '.') ?></p>
                                        <p class="mb-1 text-danger"><strong>Sisa Baru:</strong> <?= number_format($row['sisa_baru'], 0, ',', '.') ?></p>
                                        <details class="mt-2">
                                            <summary class="text-primary">Detail Bulanan</summary>
                                            <div class="row mt-2">
                                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                                    <div class="col-4 small mb-1"><strong>B<?= $i ?>:</strong> <?= number_format($row["data$i"], 0, ',', '.') ?></div>
                                                <?php endfor; ?>
                                            </div>
                                        </details>
                                        <p class="mt-2"><strong>Catatan:</strong> <?= $row['catatan'] ?></p>
                                        <div class="mt-2">
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                                            <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php else: ?>
                        <p>Tidak ada data untuk bulan <?= $bulanLabel ?></p>
                    <?php endif; ?>
                </div>
            <?php } ?>

            <!-- Tampilkan kedua section -->
            <?php tampilTabel($rowsNow, $totalsNow, $bulanSekarang); ?>
            <?php tampilTabel($rowsPrev, $totalsPrev, $bulanKemarin); ?>
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
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="confirmDeleteModalLabel">
                        <i class="fas fa-question-circle"></i> Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5">
                        <i class="fas fa-trash-alt text-danger me-2"></i>
                        Apakah kamu yakin ingin <strong class="text-danger">menghapus</strong> data ini?
                    </p>
                </div>
                <div class="modal-footer justify-content-between px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="button" class="btn text-white" style="background-color: #dc3545;" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let klpToDelete = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                klpToDelete = this.dataset.klp;
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (klpToDelete) {
                fetch('hapus_tunaibabat.php?klp=' + klpToDelete)
                    .then(response => response.json())
                    .then(data => {
                        deleteModal.hide();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Redirect ke tuna_babat.php setelah 2 detik
                            setTimeout(() => {
                                window.location.href = 'tunai_babat.php';
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal menghapus data.'
                            });
                        }
                    })
                    .catch(error => {
                        deleteModal.hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan!',
                            text: 'Terjadi kesalahan: ' + error
                        });
                    });
            }
        });
    </script>





</body>

</html>