<?php
session_start(); // ⬅ WAJIB dipanggil paling atas sebelum pakai $_SESSION
include '../config/koneksi.php';
// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Ambil semua data dengan join ke tabel kelompok
$sql = "
    SELECT t.*, k.kelompok, k.t_jadi, k.cm, k.mb 
    FROM database_total_penjumlahan_kalkulasi t
    LEFT JOIN database_tabel_utama_kalkulasi k 
        ON t.id_kelompok = k.id
    ORDER BY t.id ASC
";
$result = $conn->query($sql);

// Siapkan array global total
$global_total = [];
$columns = [
    't_jadi',
    'cm',
    'mb',
    'target_kalkulasi',
    'jumlah_target',
    'pelunasan',
    'jumlah_pelunasan',
    'baru',
    'jumlah_baru',
    'storting_jl',
    'jumlah_storting_jl',
    'storting_jd',
    'jumlah_storting_jd',
    'other',
    'jumlah_other',
    'total',
    'gagalkan',
    'rencana_jadi',
    'target_program',
    'program_murni',
    't_storting_100',
    'plus_minus_100',
    't_storting_115',
    'plus_minus_115',
    't_storting_120',
    'plus_minus_120',
    'kekuatan_115',
    'kekuatan_120',
    'kekuatan_125',
    'program',
    'nilai'
];

// Inisialisasi semua kolom global ke 0
foreach ($columns as $col) {
    $global_total[$col] = 0;
}

// Simpan data per kelompok & hitung total global
$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Tambahkan data ke array untuk ditampilkan per kelompok
        $data[] = $row;

        // Akumulasi global total
        foreach ($columns as $col) {
            $global_total[$col] += (float)($row[$col] ?? 0);
        }
    }
}
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
            <h3 class="mb-0">Kalkulasi KM</h3>

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

                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php
                        // Reset pointer ke awal
                        mysqli_data_seek($result, 0);

                        // Ambil daftar kelompok untuk membuat tab
                        $kelompok_list = [];
                        while ($row = $result->fetch_assoc()) {
                            $kelompok_list[] = $row['kelompok'] ?? ('Kelompok ' . $row['id_kelompok']);
                        }

                        // Reset lagi untuk render isi
                        mysqli_data_seek($result, 0);
                        ?>

                        <!-- NAV TABS -->
                        <ul class="nav nav-tabs mb-3" id="kelompokTab" role="tablist">
                            <?php foreach ($kelompok_list as $i => $nama_kelompok): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $i === 0 ? 'active' : '' ?>"
                                        id="tab-<?= $i ?>"
                                        data-bs-toggle="tab"
                                        data-bs-target="#kelompok-<?= $i ?>"
                                        type="button" role="tab">
                                        <?= htmlspecialchars($nama_kelompok) ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- TAB CONTENT -->
                        <div class="tab-content" id="kelompokTabContent">
                            <?php $i = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>"
                                    id="kelompok-<?= $i ?>" role="tabpanel">

                                    <div class="card shadow-sm mb-4">
                                        <div class="card-header bg-primary text-white fw-bold">
                                            Kelompok <?= htmlspecialchars($row['kelompok'] ?? ('Kelompok ' . $row['id_kelompok'])) ?>
                                        </div>

                                        <div class="card-body">
                                            <div class="row g-3">

                                                <!-- PRODUKSI -->
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <?php
                                                        $produksi = [
                                                            "Target" => $row['t_jadi'] ?? 0,
                                                            "Cm" => $row['cm'] ?? 0,
                                                            "Mb" => $row['mb'] ?? 0,
                                                        ];
                                                        foreach ($produksi as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- HASIL AKHIR -->
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <?php
                                                        $hasil = [
                                                            "Total" => $row['total'] ?? 0,
                                                            "Gagalkan" => $row['gagalkan'] ?? 0,
                                                            "Rencana jadi" => $row['rencana_jadi'] ?? 0,
                                                        ];
                                                        foreach ($hasil as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- TARGET & PELUNASAN -->
                                                <div class="col-12">
                                                    <div class="row g-2">
                                                        <?php
                                                        $target = [
                                                            "Target kalkulasi" => $row['target_kalkulasi'] ?? 0,
                                                            "Jumlah target" => $row['jumlah_target'] ?? 0,
                                                            "Pelunasan" => $row['pelunasan'] ?? 0,
                                                            "Jumlah pelunasan" => $row['jumlah_pelunasan'] ?? 0,
                                                            "Baru" => $row['baru'] ?? 0,
                                                            "Jumlah baru" => $row['jumlah_baru'] ?? 0,
                                                        ];
                                                        foreach ($target as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- PROGRAM -->
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <?php
                                                        $program = [
                                                            "Target program" => $row['target_program'] ?? 0,
                                                            "Program murni" => $row['program_murni'] ?? 0,
                                                        ];
                                                        foreach ($program as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- NILAI -->
                                                <div class="col-md-6">
                                                    <div class="row g-2">
                                                        <?php
                                                        $nilai = [
                                                            "Program" => $row['program'] ?? 0,
                                                            "Nilai" => $row['nilai'] ?? 0,
                                                        ];
                                                        foreach ($nilai as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- STORTING & KEKUATAN -->
                                                <div class="col-12">
                                                    <div class="row g-2">
                                                        <?php
                                                        $storting = [
                                                            "T storting 100" => $row['t_storting_100'] ?? 0,
                                                            "±100" => $row['plus_minus_100'] ?? 0,
                                                            "T storting 115" => $row['t_storting_115'] ?? 0,
                                                            "±115" => $row['plus_minus_115'] ?? 0,
                                                            "T storting 120" => $row['t_storting_120'] ?? 0,
                                                            "±120" => $row['plus_minus_120'] ?? 0,
                                                            "Kekuatan 115" => $row['kekuatan_115'] ?? 0,
                                                            "Kekuatan 120" => $row['kekuatan_120'] ?? 0,
                                                            "Kekuatan 125" => $row['kekuatan_125'] ?? 0,
                                                        ];
                                                        foreach ($storting as $label => $val): ?>
                                                            <div class="col text-center">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-2">
                                                                        <small><?= $label ?></small><br>
                                                                        <strong><?= number_format((float)$val, 0, ',', '.') ?></strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                            </div><!-- /.row grid -->
                                        </div><!-- /.card-body -->
                                    </div><!-- /.card kelompok -->
                                </div>
                                <?php $i++; ?>
                            <?php endwhile; ?>
                        </div><!-- /.tab-content -->


                    <?php else: ?>
                        <div class="alert alert-warning">Tidak ada data untuk ditampilkan.</div>
                    <?php endif; ?>
                </div>



                <!-- GLOBAL -->
                <div class="container my-5">
                    <h3 class="mb-3">GLOBAL</h3>
                    <div class="row g-3">

                        <!-- PRODUKSI -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-dark text-white">Produksi</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2">
                                                <small>Target</small><br>
                                                <strong><?= number_format($global_total['t_jadi'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2">
                                                <small>Cm</small><br>
                                                <strong><?= number_format($global_total['cm'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2">
                                                <small>Mb</small><br>
                                                <strong><?= number_format($global_total['mb'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HASIL AKHIR -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-dark text-white">Hasil Akhir</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2">
                                                <small>Total</small><br>
                                                <strong><?= number_format($global_total['total'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2">
                                                <small>Gagalkan</small><br>
                                                <strong><?= number_format($global_total['gagalkan'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <div class="border rounded p-2 text-nowrap">
                                                <small>Rencana jadi</small><br>
                                                <strong><?= number_format($global_total['rencana_jadi'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TARGET & PELUNASAN -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white">Target & Pelunasan</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <?php
                                        $fields = [
                                            'target_kalkulasi' => 'Target kalkulasi',
                                            'jumlah_target'    => 'Jumlah target',
                                            'pelunasan'        => 'Pelunasan',
                                            'jumlah_pelunasan' => 'Jumlah pelunasan',
                                            'baru'             => 'Baru',
                                            'jumlah_baru'      => 'Jumlah baru'
                                        ];
                                        foreach ($fields as $key => $label): ?>
                                            <div class="col-6 col-md-4 col-lg-2">
                                                <div class="border rounded p-2 text-nowrap">
                                                    <small><?= $label ?></small><br>
                                                    <strong><?= number_format($global_total[$key], 0, ',', '.') ?></strong>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PROGRAM -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-dark text-white">Program</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small>Target program</small><br>
                                                <strong><?= number_format($global_total['target_program'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small>Program murni</small><br>
                                                <strong><?= number_format($global_total['program_murni'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NILAI -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-dark text-white">Nilai</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small>Program</small><br>
                                                <strong><?= number_format($global_total['program'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small>Nilai</small><br>
                                                <strong><?= number_format($global_total['nilai'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STORTING & KEKUATAN -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white">Storting & Kekuatan</div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <?php
                                        $fields = [
                                            't_storting_100' => 'T storting 100',
                                            'plus_minus_100' => '±100',
                                            't_storting_115' => 'T storting 115',
                                            'plus_minus_115' => '±115',
                                            't_storting_120' => 'T storting 120',
                                            'plus_minus_120' => '±120',
                                            'kekuatan_115'   => 'Kekuatan 115',
                                            'kekuatan_120'   => 'Kekuatan 120',
                                            'kekuatan_125'   => 'Kekuatan 125'
                                        ];
                                        foreach ($fields as $key => $label): ?>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <div class="border rounded p-2 text-nowrap">
                                                    <small><?= $label ?></small><br>
                                                    <strong><?= number_format($global_total[$key], 0, ',', '.') ?></strong>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.row -->
                </div><!-- /.container -->


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