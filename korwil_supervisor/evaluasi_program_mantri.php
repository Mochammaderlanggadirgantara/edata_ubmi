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

// Ambil daftar tanggal unik untuk dropdown (dari evaluasi_program_mantri)
$dates_result = $conn->query("SELECT DISTINCT tanggal FROM evaluasi_program_mantri ORDER BY tanggal DESC");
$selected_date = $_GET['tanggal'] ?? '';

// Filter data berdasarkan tanggal
$where = '';
if ($selected_date) {
    $where = "WHERE tanggal = '".$conn->real_escape_string($selected_date)."'";
}

// Ambil data tabel evaluasi_program_mantri
$sql = "SELECT * FROM evaluasi_program_mantri $where ORDER BY klp ASC";
$result = $conn->query($sql);
if(!$result){
    die("Query error: ".$conn->error);
}

// Ambil total per kolom dari evaluasi_program_mantri (selain index)
$total_sql = "SELECT 
    SUM(kekuatan) AS total_kekuatan,
    SUM(program) AS total_program,
    SUM(storting) AS total_storting,
    SUM(rencana) AS total_rencana,
    SUM(baru) AS total_baru,
    SUM(gagalkan) AS total_gagalkan,
    SUM(drop_val) AS total_drop
    FROM evaluasi_program_mantri $where";

$total_result = $conn->query($total_sql);
$totals = $total_result->fetch_assoc();

// Ambil total storting_jadi dari index_program sesuai tanggal
$storting_index_sql = "SELECT SUM(storting_jadi) AS total_storting_jadi 
                       FROM index_program
                       WHERE tanggal = '".$conn->real_escape_string($selected_date)."'";
$storting_index_result = $conn->query($storting_index_sql);
$storting_index = $storting_index_result->fetch_assoc();
$total_storting_jadi = $storting_index['total_storting_jadi'] ?? 0;

// Hitung total_index: total_storting_jadi dari index_program - total_program dari evaluasi_program_mantri
$total_index = $total_storting_jadi - ($totals['total_program'] ?? 0);

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
                <h3 class="mb-0">Data Evaluasi Program Mantri</h3>

                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="#" class="d-flex align-items-center text-decoration-none">
                                <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                                <span class="text-secondary fw-medium hover">Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Data Evaluasi Program Mantri</span>
                        </li>

                    </ol>
                </nav>
            </div>
               <!-- Filter dan Tambah Data -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="create_evaluasi_program_mantri.php" class="btn btn-primary">Tambah Data</a>

        <form method="get" class="d-flex align-items-center">
            <label for="tanggal" class="me-2 mb-0">Filter Tanggal:</label>
            <select name="tanggal" id="tanggal" class="form-select me-2" onchange="this.form.submit()">
                <option value="">-- Semua Tanggal --</option>
                <?php while($date_row = $dates_result->fetch_assoc()): ?>
                    <option value="<?= $date_row['tanggal'] ?>" <?= ($selected_date == $date_row['tanggal']) ? 'selected' : '' ?>>
                        <?= $date_row['tanggal'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <noscript><button type="submit" class="btn btn-secondary">Filter</button></noscript>
        </form>
    </div>

    <!-- Card Summary -->
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="text-white">Total Kekuatan</h6>
                    <h5 class="text-white"><?= $totals['total_kekuatan'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="text-white">Total Program</h6>
                    <h5 class="text-white"><?= $totals['total_program'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="text-white">Total Storting</h6>
                    <h5 class="text-white"><?= $totals['total_storting'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6 class="text-white">Total Index %</h6>
                    <h5 class="text-white"><?= number_format($total_index,2) ?>%</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h6 class="text-white">Total Rencana</h6>
                    <h5 class="text-white"><?= $totals['total_rencana'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="text-white">Total Baru</h6>
                    <h5 class="text-white"><?= $totals['total_baru'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6 class="text-white">Total Gagalkan</h6>
                    <h5 class="text-white"><?= $totals['total_gagalkan'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="text-white">Total Drop</h6>
                    <h5 class="text-white"><?= $totals['total_drop'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel untuk desktop/tablet -->
<div class="table-responsive d-none d-md-block">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Kelompok</th>
            <th>Kekuatan</th>
            <th>Program</th>
            <th>Index %</th>
            <th>Drop</th>
            <th>Storting</th>
            <th>Rencana</th>
            <th>Baru</th>
            <th>Gagalkan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()):
        ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['klp'] ?></td>
                    <td><?= $row['kekuatan'] ?></td>
                    <td><?= $row['program'] ?></td>
                    <td><?= number_format($row['index'],2) ?>%</td>
                    <td><?= $row['drop_val'] ?></td>
                    <td><?= $row['storting'] ?></td>
                    <td><?= $row['rencana'] ?></td>
                    <td><?= $row['baru'] ?></td>
                    <td><?= $row['gagalkan'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
        <?php
            endwhile;
        } else {
            echo "<tr><td colspan='11' class='text-center'>Data tidak ditemukan</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Card ringkasan untuk mobile -->
<div class="d-block d-md-none">
    <?php
    $result->data_seek(0); // Reset pointer result
    $no = 1;
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()):
    ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Kelompok <?= $row['klp'] ?></h6>
                <p class="mb-1"><strong>Kekuatan:</strong> <?= $row['kekuatan'] ?></p>
                <p class="mb-1"><strong>Program:</strong> <?= $row['program'] ?></p>
                <p class="mb-1"><strong>Index %:</strong> <?= number_format($row['index'],2) ?>%</p>
                <p class="mb-1"><strong>Drop:</strong> <?= $row['drop_val'] ?></p>
                <p class="mb-1"><strong>Storting:</strong> <?= $row['storting'] ?></p>
                <p class="mb-1"><strong>Rencana:</strong> <?= $row['rencana'] ?></p>
                <p class="mb-1"><strong>Baru:</strong> <?= $row['baru'] ?></p>
                <p class="mb-1"><strong>Gagalkan:</strong> <?= $row['gagalkan'] ?></p>
                <div class="mt-2">
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                </div>
            </div>
        </div>
    <?php
        endwhile;
    } else {
        echo "<p class='text-center'>Data tidak ditemukan</p>";
    }
    ?>
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