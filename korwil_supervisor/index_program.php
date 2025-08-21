<?php
session_start();
include '../config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
// Cek apakah user memiliki jabatan 'kasir'
$allowed_roles = ['pengawas'];

if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}


// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];


$sql = "SELECT * FROM index_program ORDER BY tanggal DESC, klp ASC";
$result = mysqli_query($conn, $sql);
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
    table-layout: auto; /* biar kolom menyesuaikan konten */
}

.default-table-area th,
.default-table-area td {
    padding: 0.5rem 0.75rem;
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
    text-align: center;
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
        padding: 0.8rem;
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
        white-space: normal; /* biar teks bisa wrap di mobile */
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
        display: none; /* hide total on mobile */
    }
}

/* Body & Card */
body {
    background: #f5f6fa;
}

.card {
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

/* Text Alignment */
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

/* Responsive adjustments for desktop */
@media (min-width: 992px) {
    .default-table-area th,
    .default-table-area td {
        font-size: 13px;
        padding: 6px 8px;
        white-space: nowrap;
    }
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
                <h3 class="mb-0">Data Index Program</h3>

                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="#" class="d-flex align-items-center text-decoration-none">
                                <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                                <span class="text-secondary fw-medium hover">Dashboard</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Data Index Program</span>
                        </li>

                    </ol>
                </nav>
            </div>
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                        <button onclick="window.location.href='create_index_program.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">
                            Tambah Data
                        </button>
                    </div>
                    <div class="container my-4">
                        <div class="default-table-area all-products" style="overflow-x:auto;">
                            <?php
                            // Inisialisasi nilai default badge
                            $nbadge = 50;
                            ?>

                            <div class="mb-3 row align-items-center">
                                <label for="nbadge" class="col-sm-2 col-form-label">Nilai Badge</label>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" id="nbadge" value="<?php echo $nbadge; ?>" min="1">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="indexTable">
                                    <thead class="table-dark text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tanggal</th>
                                            <th>KLP</th>
                                            <th>DROP REKAP</th>
                                            <th>STORTING REKAP</th>
                                            <th>INDEX</th>
                                            <th>PROGRAM</th>
                                            <th>STORTING JADI</th>
                                            <th>MIN DROP</th>
                                            <th>MIN STORTING</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_drop = 0;
                                        $total_storting_rekap = 0;
                                        $total_program = 0;
                                        $total_storting_jadi = 0;
                                        $total_min_drop = 0;
                                        $total_min_storting = 0;
                                        ?>

                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <?php
                                                $total_drop += $row['drop_rekap'];
                                                $total_storting_rekap += $row['storting_rekap'];
                                                $total_program += $row['program'];
                                                $total_storting_jadi += $row['storting_jadi'];
                                                $total_min_drop += $row['min_drop'];
                                                $total_min_storting += $row['min_storting'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                                                    <td><?php echo $row['klp']; ?></td>
                                                    <td><?php echo $row['drop_rekap']; ?></td>
                                                    <td><?php echo $row['storting_rekap']; ?></td>
                                                    <td><?php echo $row['indeks']; ?></td>
                                                    <td><?php echo $row['program']; ?></td>
                                                    <td><?php echo $row['storting_jadi']; ?></td>
                                                    <td><?php echo $row['min_drop']; ?></td>
                                                    <td><?php echo $row['min_storting']; ?></td>
                                                    <td class="text-center">
                                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary fw-bold text-center">
                                            <td colspan="3">TOTAL</td>
                                            <td id="total_drop"><?php echo $total_drop; ?></td>
                                            <td id="total_storting_rekap"><?php echo $total_storting_rekap; ?></td>
                                            <td id="total_index">
                                                <?php
                                                $total_index = ($total_program != 0) ? ($total_storting_jadi / $total_program) * 100 : 0;
                                                echo number_format($total_index, 2) . '%';
                                                ?>
                                            </td>
                                            <td id="total_program"><?php echo $total_program; ?></td>
                                            <td id="total_storting_jadi"><?php echo $total_storting_jadi; ?></td>
                                            <td id="total_min_drop"><?php echo $total_min_drop; ?></td>
                                            <td id="total_min_storting"><?php echo $total_min_storting; ?></td>
                                            <td>-</td>
                                        </tr>
                                        <tr class="table-info fw-bold text-center">
                                            <td colspan="10">TOTAL RINGKASAN</td>
                                            <td id="total_ringkasan">
                                                <?php
                                                $total_ringkasan = ($nbadge != 0) ? $total_min_storting / $nbadge : 0;
                                                echo number_format($total_ringkasan, 2);
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <script>
                                const nbadgeInput = document.getElementById('nbadge');
                                const totalMinStorting = <?php echo $total_min_storting; ?>;
                                const totalRingkasanTd = document.getElementById('total_ringkasan');

                                nbadgeInput.addEventListener('input', function() {
                                    let nbadge = parseFloat(this.value) || 1;
                                    let totalRingkasan = totalMinStorting / nbadge;
                                    totalRingkasanTd.textContent = totalRingkasan.toFixed(2);
                                });
                            </script>
                        </div>
                        <!-- MODE MOBILE (Ringkasan) -->
                        <div class="d-block d-md-none">
                            <?php if (!empty($rows)): foreach ($rows as $row): ?>
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
                                                        <div class="col-4 small mb-1">
                                                            <strong>B<?= $i ?>:</strong> <?= number_format($row["bulan$i"], 0, ',', '.') ?>
                                                        </div>
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
                            <?php endforeach;
                            endif; ?>
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