<?php
include '../config/koneksi.php';
session_start();
// dari tabel users
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
if ($_SESSION['jabatan'] !== 'mantri') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
$id_cabang = $_SESSION['id_cabang'];
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
            case 'mantri':
                include '../navbar/navbar_mantri.php';
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
            <h3 class="mb-0">Target baru</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Target Baru</span>
                    </li>

                </ol>
            </nav>
        </div>


        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="card">
                    <div class="card-header bg-dark text-white text-center">
                        <strong>PERHITUNGAN CARI DROP BARU MELALUI TARGET</strong>
                    </div>
                    <form id="form-target" method="POST">
                        <div class="card-body p-0">
                            <div class="table-responsive d-none d-md-block"><!-- Desktop View -->
                                <table class="table table-hover table-bordered text-center align-middle mb-0" id="targetTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>HARI KERJA</th>
                                            <th class="bg-info">TARGET MURNI</th>
                                            <th class="bg-warning">TARGET YANG DIINGINKAN</th>
                                            <th class="bg-info">KEKURANGAN TARGET</th>
                                            <th class="bg-info">DROP BARU</th>
                                            <th class="bg-warning">PLENING</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $id_kelompok = $_SESSION['id_kelompok'];
                                        $targetQuery = mysqli_query($conn, "SELECT `id`, `hari`, `target` FROM `targetmantri_babat1` WHERE id_kelompok = '$id_kelompok'");

                                        $targetData = [];
                                        while ($row = mysqli_fetch_assoc($targetQuery)) {
                                            $targetData[$row['id']] = [
                                                'hari' => strtoupper($row['hari']),
                                                'target' => $row['target']
                                            ];
                                        }

                                        foreach ($targetData as $id => $data) {
                                            $id_kelompok = $_SESSION['id_kelompok'];
                                            $saved = mysqli_query($conn, "SELECT * FROM target_baru WHERE id_target = '$id' AND id_kelompok = '$id_kelompok'");
                                            $savedData = mysqli_fetch_assoc($saved);
                                            $target_diinginkan = $savedData['target_diinginkan'] ?? 0;
                                            $plening = $savedData['plening'] ?? 0;

                                            $hari = $data['hari'];
                                            $targetMurni = (int) $data['target'];

                                            echo "<tr data-id='$id'>";
                                            echo "<td><span class='badge bg-primary px-3 py-2'>$hari</span><input type='hidden' name='id_target[]' value='$id'></td>";
                                            echo "<td class='target-murni fw-bold text-info' data-value='$targetMurni'>" . number_format($targetMurni, 0, ',', '.') . "</td>";
                                            echo "<td><input type='text' name='target_diinginkan[]' class='form-control text-center target-input' data-value='$target_diinginkan' value='" . number_format($target_diinginkan, 0, ',', '.') . "'></td>";
                                            echo "<td class='kekurangan text-danger fw-bold'>0</td>";
                                            echo "<td class='drop-baru text-success fw-bold'>0</td>";
                                            echo "<td><input type='text' name='plening[]' class='form-control text-center plening-input' data-value='$plening' value='" . number_format($plening, 0, ',', '.') . "'></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot class="table-secondary fw-bold">
                                        <tr>
                                            <td>JUMLAH</td>
                                            <td id="total-murni">0</td>
                                            <td id="total-diinginkan">0</td>
                                            <td id="total-kekurangan">0</td>
                                            <td id="total-drop">0</td>
                                            <td id="total-plening">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Mobile Friendly Cards -->
                            <div class="d-md-none p-2">
                                <?php
                                foreach ($targetData as $id => $data) {
                                    $id_kelompok = $_SESSION['id_kelompok'];
                                    $saved = mysqli_query($conn, "SELECT * FROM target_baru WHERE id_target = '$id' AND id_kelompok = '$id_kelompok'");
                                    $savedData = mysqli_fetch_assoc($saved);
                                    $target_diinginkan = $savedData['target_diinginkan'] ?? 0;
                                    $plening = $savedData['plening'] ?? 0;

                                    $hari = $data['hari'];
                                    $targetMurni = (int) $data['target'];
                                ?>
                                    <div class="card mb-3 shadow-sm" data-id="<?= $id ?>">
                                        <div class="card-header bg-primary text-white fw-bold">
                                            <?= $hari ?>
                                        </div>
                                        <div class="card-body">
                                            <p><strong class="text-info">Target Murni:</strong> <?= number_format($targetMurni, 0, ',', '.') ?></p>
                                            <div class="mb-2">
                                                <label class="form-label fw-bold">Target Diinginkan</label>
                                                <input type="text" name="target_diinginkan[]" class="form-control text-center target-input" value="<?= number_format($target_diinginkan, 0, ',', '.') ?>">
                                            </div>
                                            <p><strong class="text-danger">Kekurangan:</strong> <span class="kekurangan">0</span></p>
                                            <p><strong class="text-success">Drop Baru:</strong> <span class="drop-baru">0</span></p>
                                            <div>
                                                <label class="form-label fw-bold">Plening</label>
                                                <input type="text" name="plening[]" class="form-control text-center plening-input" value="<?= number_format($plening, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
                </form>

            </div>


        </div>
    </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        function formatNumber(num) {
            return num.toLocaleString('id-ID');
        }

        function parseFormattedNumber(str) {
            return parseInt(str.replace(/\./g, '')) || 0;
        }

        function handleFormattedInput($input) {
            let rawValue = parseFormattedNumber($input.val());
            $input.val(formatNumber(rawValue));
            $input.data('value', rawValue);
        }

        function updateTable() {
            let totalMurni = 0;
            let totalDiinginkan = 0;
            let totalKekurangan = 0;
            let totalDrop = 0;
            let totalPlening = 0;

            $('#targetTable tbody tr').each(function() {
                const $row = $(this);
                const targetMurni = parseInt($row.find('.target-murni').data('value')) || 0;
                const targetInput = parseInt($row.find('.target-input').data('value')) || 0;
                const pleningInput = parseInt($row.find('.plening-input').data('value')) || 0;

                const kekurangan = targetInput - targetMurni;
                const dropBaru = kekurangan / 0.13;

                totalMurni += targetMurni;
                totalDiinginkan += targetInput;
                totalKekurangan += kekurangan;
                totalDrop += dropBaru;
                totalPlening += pleningInput;

                $row.find('.kekurangan').text(formatNumber(kekurangan));
                // $row.find('.drop-baru').text(formatNumber(Math.round(dropBaru)));

                // muncul (-) jika nilai positif
                let dropFormatted = Math.round(dropBaru);
                $row.find('.drop-baru').text((dropFormatted > 0 ? '-' : '') + formatNumber(dropFormatted));

            });

            $('#total-murni').text(formatNumber(totalMurni));
            $('#total-diinginkan').text(formatNumber(totalDiinginkan));
            $('#total-kekurangan').text(formatNumber(totalKekurangan));
            // $('#total-drop').text(formatNumber(Math.round(totalDrop)));
            let totalDropFormatted = Math.round(totalDrop);
            $('#total-drop').text((totalDropFormatted > 0 ? '-' : '') + formatNumber(totalDropFormatted));

            $('#total-plening').text(formatNumber(totalPlening));
        }

        $(document).ready(function() {

            updateTable();

            $('.target-input, .plening-input').each(function() {
                handleFormattedInput($(this));
            });

            $('.target-input, .plening-input').on('input', function() {
                handleFormattedInput($(this));
                updateTable();
            });
        });

        $('#form-target').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: 'mantri/simpan_target_baru.php',
                method: 'POST',
                data: formData,
                success: function(res) {
                    const response = JSON.parse(res);
                    if (response.status === 'success') {
                        alert('Data berhasil disimpan!');
                    } else {
                        alert('Gagal menyimpan data.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan AJAX.');
                }
            });
        });
    </script>
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