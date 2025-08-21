<?php
session_start();
include '../config/koneksi.php';

// cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data user + cabang dari DB
$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT t.id_user, t.jabatan, t.nama_user, t.id_cabang, c.nama_cabang
                              FROM tuser t
                              JOIN cabang c ON t.id_cabang = c.id_cabang
                              WHERE t.id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query);

if (!$data_user) {
    die("Data user tidak ditemukan. Silakan login ulang.");
}

$id_cabang  = $data_user['id_cabang'];
$nama_user  = $data_user['nama_user'];
$jabatan    = strtolower($data_user['jabatan']);
$nama_cabang= $data_user['nama_cabang'];

// query data tunai_babat1 sesuai cabang
$result = $conn->query("SELECT * FROM tunai_babat1 WHERE id_cabang = '$id_cabang' ORDER BY klp ASC");

// cek role
$allowed_roles = ['kasir', 'pengawas', 'mantri', 'staff'];
if (!in_array($jabatan, $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
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
        /* Animasi ringan untuk modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: scale(0.9);
        }

        .modal.fade.show .modal-dialog {
            transform: scale(1);
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
                <h3 class="mb-0">Data Tunai Babat</h3>

                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="#" class="d-flex align-items-center text-decoration-none">
                                <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                                <span class="text-secondary fw-medium hover">Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Data Tunai Babat</span>
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
                            <button onclick="window.location.href='create_tunaibabat.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">
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

                    <div class="container my-4">
                        <div class="default-table-area all-products">
                            <form method="GET" class="row g-3 align-items-end mb-4">
                                <div class="col-md-3">
                                    <label for="filter_tanggal" class="form-label">Filter Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" id="filter_tanggal" value="<?= $_GET['tanggal'] ?? '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_klp" class="form-label">Filter KLP</label>
                                    <select name="klp" id="filter_klp" class="form-select">
                                        <option value="">-- Semua KLP --</option>
                                        <?php for ($i = 1; $i <= 10; $i++) :
                                            $val = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                            <option value="<?= (int)$val ?>" <?= (isset($_GET['klp']) && $_GET['klp'] == (int)$val) ? 'selected' : '' ?>><?= $val ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    <a href="tunai_babat.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>

                            <table class="table table-sm align-middle w-100 small" id="myTables">
                                <thead style="font-size: 10px;">
                                    <tr>
                                        <th>KLP</th>
                                        <th>TANGGAL</th>
                                        <th>HARI</th>
                                        <th>KASBON</th>
                                        <th>DROP</th>
                                        <th>SU LAPANGAN</th>
                                        <th>TRANSFERAN</th>
                                        <th>9%</th>
                                        <th>SISA UANG</th>
                                        <th>TUNAI</th>
                                        <th>MIN/PLUS</th>
                                        <th>AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $sql = "SELECT * FROM tunai_babat1 WHERE 1=1 AND id_cabang = '$id_cabang'";
                                    $params = [];
                                    $types = '';

                                    if (!empty($_GET['tanggal'])) {
                                        $sql .= " AND tanggal = ?";
                                        $params[] = $_GET['tanggal'];
                                        $types .= 's';
                                    }

                                    if (!empty($_GET['klp'])) {
                                        $sql .= " AND klp = ?";
                                        $params[] = (int)$_GET['klp'];
                                        $types .= 'i';
                                    }

                                    $stmt = $conn->prepare($sql);

                                    if (!empty($params)) {
                                        $stmt->bind_param($types, ...$params);
                                    }

                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) :
                                        while ($row = mysqli_fetch_assoc($result)) : ?>
                                            <tr>
                                                <td data-label="KLP"><?= htmlspecialchars($row['klp']) ?></td>
                                                <td data-label="TANGGAL"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                                <td data-label="HARI"><?= htmlspecialchars($row['hari']) ?></td>
                                                <td data-label="KASBON"><?= number_format($row['kasbon'], 0, ',', '.') ?></td>
                                                <td data-label="DROP"><?= number_format($row['drop_uang'], 0, ',', '.') ?></td>
                                                <td data-label="SU LAPANGAN"><?= number_format($row['su_lapangan'], 0, ',', '.') ?></td>
                                                <td data-label="TRANSFERAN"><?= number_format($row['transferan'], 0, ',', '.') ?></td>
                                                <td data-label="9%"><?= number_format($row['persen_9'], 0, ',', '.') ?></td>
                                                <td data-label="SISA UANG"><?= number_format($row['sisa_uang'], 0, ',', '.') ?></td>
                                                <td data-label="TUNAI"><?= number_format($row['tunai'], 0, ',', '.') ?></td>
                                                <td data-label="MIN/PLUS"><?= number_format($row['min_plus'], 0, ',', '.') ?></td>
                                                <td data-label="Aksi">
                                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                                        <a href="edit_tunaibabat.php?klp=<?= $row['klp'] ?>" class="ps-0 border-0 bg-transparent lh-1" title="Edit">
                                                            <i class="material-symbols-outlined fs-16 text-body">edit</i>
                                                        </a>
                                                        <a href="#" class="ps-0 border-0 bg-transparent lh-1 delete-btn"
                                                            data-klp="<?= $row['klp'] ?>" title="Delete">
                                                            <i class="material-symbols-outlined fs-16 text-danger">delete</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="12" class="text-center text-muted">Data tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>

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