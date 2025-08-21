<?php
include '../config/koneksi.php';
session_start();
$id_kelompok = $_SESSION['id_kelompok']; // Ambil id_kelompok dari session
$id_cabang = $_SESSION['id_cabang'];
// Gunakan 'Jum\'at' secara konsisten
define('HARI_ARRAY', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu']);

function formatAngka($angka)
{
    if ($angka === null || $angka === '') {
        $angka = 0;
    }
    return number_format(floatval($angka), 0, ',', '.');
}


function renderInputRow($hari, $target = '', $cm = '', $mb = '')
{
    echo "<tr>
        <td>$hari</td>
        <td><input type='text' class='form-control text-center target-input' name='target[]' value='" . formatAngka($target) . "'></td>
        <td><input type='text' class='form-control text-center cm-input' name='cm[]' value='" . formatAngka($cm) . "'></td>
        <td><input type='text' class='form-control text-center mb-input' name='mb[]' value='" . formatAngka($mb) . "'></td>
    </tr>";
}

function renderMingguan($judul, $minggu, $targetPerHari, $dataBerjalan)
{
    echo "<h5 class='fw-bold'>$judul</h5>
    <div class='table-responsive mb-4'>
        <table class='table table-bordered text-center minggu' data-minggu='$minggu'>
        <thead class='table-light'>
            <tr>
                <th>HARI</th>
                <th>TARGET</th>
                <th>DROP BARU</th>
                <th>T. MASUK (13%)</th>
                <th>T. KELUAR</th>
                <th>T. JADI</th>
            </tr>
        </thead>
        <tbody>";

    foreach (HARI_ARRAY as $i => $namaHari) {
        $target = $targetPerHari[$i] ?? 0;
        $drop = floatval($dataBerjalan[$minggu][$namaHari]['drop'] ?? 0);
        $keluar = floatval($dataBerjalan[$minggu][$namaHari]['keluar'] ?? 0);

        $masuk = round($drop * 0.13);
        $jadi = $target + $masuk - $keluar;

        echo "<tr>
            <td>$namaHari</td>
            <td><input type='text' class='form-control target' data-index='$i' data-minggu='$minggu' value='" . formatAngka($target) . "' readonly></td>
            <td><input type='text' class='form-control drop' data-index='$i' data-minggu='$minggu' value='" . formatAngka($drop) . "'></td>
            <td><input type='text' class='form-control masuk' data-index='$i' data-minggu='$minggu' value='" . formatAngka($masuk) . "' readonly></td>
            <td><input type='text' class='form-control keluar' data-index='$i' data-minggu='$minggu' value='" . formatAngka($keluar) . "'></td>
            <td><input type='text' class='form-control jadi' data-index='$i' data-minggu='$minggu' value='" . formatAngka($jadi) . "' readonly></td>
        </tr>";
    }

    echo "<tr class='fw-bold table-warning sum-row'>
        <td>JUMLAH</td>
        <td class='sum-target'>0</td>
        <td class='sum-drop'>0</td>
        <td class='sum-masuk'>0</td>
        <td class='sum-keluar'>0</td>
        <td class='sum-jadi'>0</td>
    </tr>
    </tbody></table></div>";
}

// Ambil data berjalan
$dataBerjalan = [];
$sql = "SELECT * FROM target_berjalan WHERE id_kelompok = '$id_kelompok' and id_cabang = '$id_cabang'";
$resultBerjalan = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($resultBerjalan)) {
    $minggu = $row['minggu'];
    $hari = ucfirst(strtolower(trim($row['hari'])));
    if ($hari === 'Jumat')
        $hari = "Jum'at"; // Normalisasi ke 'Jum\'at'
    $dataBerjalan[$minggu][$hari] = [
        'drop' => $row['drop_baru'] ?? 0,
        'keluar' => $row['t_keluar'] ?? 0
    ];
}

// Ambil data resort mantri
$dataResort = [];
$sqlResort = "SELECT * FROM data_resort_km WHERE id_kelompok = '$id_kelompok' and id_cabang = '$id_cabang'";
$resultResort = mysqli_query($conn, $sqlResort);

while ($row = mysqli_fetch_assoc($resultResort)) {
    $hari = ucfirst(strtolower(trim($row['hari'])));
    if ($hari === 'Jumat')
        $hari = "Jum'at"; // Normalisasi ke 'Jum\'at'
    $dataResort[$hari] = [
        'target' => $row['target'],
        'cm' => $row['cm'],
        'mb' => $row['mb']
    ];
}

// Ambil target tetap mingguan dari database
$query = "SELECT * FROM targetmantri_babat1 
          WHERE id_kelompok = '$id_kelompok'
          ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu')";
$result = mysqli_query($conn, $query);
$targetPerHariFromDB = [];
$total_target = $total_cm = $total_mb = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $targetPerHariFromDB[] = $row['target'];
    $total_target += $row['target'];
    $total_cm += $row['cm'];
    $total_mb += $row['mb'];
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
            <h3 class="mb-0">Target Berjalan</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Target Berjalan</span>
                    </li>

                </ol>
            </nav>
        </div>


        <div class="card bg-light border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3 p-md-4">

                <!-- Judul -->
                <h4 class="text-center fw-bold text-primary mb-4">📊 DATA RESORT MANTRI</h4>

                <!-- Tabel Input Resort Mantri -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>HARI</th>
                                <th>TARGET</th>
                                <th>CM</th>
                                <th>MB</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach (HARI_ARRAY as $h) {
                                $targetVal = $dataResort[$h]['target'] ?? '';
                                $cmVal = $dataResort[$h]['cm'] ?? '';
                                $mbVal = $dataResort[$h]['mb'] ?? '';
                                renderInputRow($h, $targetVal, $cmVal, $mbVal);
                            }
                            ?>
                            <tr class="table-dark fw-bold">
                                <td>JUMLAH</td>
                                <td class="bg-warning" id="total-target">0</td>
                                <td class="bg-warning" id="total-cm">0</td>
                                <td class="bg-warning" id="total-mb">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Simpan -->
                <div class="text-center mb-5">
                    <button id="simpanResortKm" class="btn btn-primary fw-bold px-4 py-2 shadow-sm w-100 w-md-auto">
                        💾 SIMPAN DATA RESORT
                    </button>
                </div>

                <!-- Data Resort Valid -->
                <h4 class="text-center fw-bold text-success mb-4">✅ DATA RESORT VALID</h4>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-success">
                            <tr>
                                <th>HARI</th>
                                <th>TARGET</th>
                                <th>CM</th>
                                <th>MB</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                            <td>" . htmlspecialchars($row['hari']) . "</td>
                            <td>" . formatAngka($row['target']) . "</td>
                            <td>" . formatAngka($row['cm']) . "</td>
                            <td>" . formatAngka($row['mb']) . "</td>
                        </tr>";
                            }
                            ?>
                            <tr class="table-dark fw-bold">
                                <td>JUMLAH</td>
                                <td class="bg-warning"><?= formatAngka($total_target) ?></td>
                                <td class="bg-warning"><?= formatAngka($total_cm) ?></td>
                                <td class="bg-warning"><?= formatAngka($total_mb) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mingguan -->
                <?php
                renderMingguan("Minggu 1", 1, $targetPerHariFromDB, $dataBerjalan);
                for ($i = 2; $i <= 4; $i++) {
                    renderMingguan("Minggu $i", $i, array_fill(0, 6, 0), $dataBerjalan);
                }
                ?>

                <!-- Bulanan -->
                <div class="table-responsive mb-5">
                    <h5 class="fw-bold text-center text-secondary">📅 1 BULAN</h5>
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-warning">
                            <tr>
                                <th>DROP BARU</th>
                                <th>T. MASUK</th>
                                <th>T. KELUAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fw-bold">
                                <td id="total-drop-bulan">0</td>
                                <td id="total-masuk-bulan">0</td>
                                <td id="total-keluar-bulan">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Simpan Akhir -->
                <div class="text-center">
                    <button id="btnSimpan" class="btn btn-success fw-bold px-4 py-2 shadow-sm w-100 w-md-auto">
                        ✅ SIMPAN SEMUA DATA
                    </button>
                </div>

            </div>
        </div>


    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!-- script File -->

    <script>
        // === FUNGSI FORMAT & BERSIH ===
        function formatRibuan(nilai) {
            return nilai.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function bersihkanFormat(nilai) {
            const isNegative = nilai.trim().startsWith('-');
            let cleaned = nilai.replace(/\./g, '').replace(/[^0-9]/g, '');
            return isNegative ? '-' + cleaned : cleaned;
        }

        function applyFormatInput(input) {
            const posisiAwal = input.selectionStart;
            const nilaiSebelum = input.value;
            const cleaned = bersihkanFormat(nilaiSebelum);
            const formatted = formatRibuan(cleaned);
            input.value = formatted;

            let posisiBaru = posisiAwal + (formatted.length - nilaiSebelum.length);
            input.setSelectionRange(
                Math.max(0, Math.min(posisiBaru, formatted.length)),
                Math.max(0, Math.min(posisiBaru, formatted.length))
            );
        }

        function loopRows(table, callback) {
            const rows = table.querySelectorAll('tbody tr:not(.sum-row)');
            rows.forEach((row, index) => callback(row, index));
        }

        function kirimDataKeServer(url, data) {
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.text())
                .then(alert)
                .catch(err => {
                    console.error('Gagal:', err);
                    alert('Terjadi kesalahan saat menyimpan data.');
                });
        }

        function hitungJumlahInput(className, outputId) {
            let total = 0;
            document.querySelectorAll('.' + className).forEach(input => {
                total += parseFloat(bersihkanFormat(input.value)) || 0;
            });
            document.getElementById(outputId).textContent = total.toLocaleString('id-ID');
        }

        // === FUNGSI UTAMA ===
        function updateMinggu(minggu) {
            const table = document.querySelector(`.minggu[data-minggu="${minggu}"]`);
            if (!table) return;

            let sumTarget = 0,
                sumDrop = 0,
                sumMasuk = 0,
                sumKeluar = 0,
                sumJadi = 0;

            loopRows(table, (row) => {
                const targetInput = row.querySelector('.target');
                const dropInput = row.querySelector('.drop');
                const keluarInput = row.querySelector('.keluar');
                const masukInput = row.querySelector('.masuk');
                const jadiInput = row.querySelector('.jadi');

                const drop = parseFloat(bersihkanFormat(dropInput.value)) || 0;
                const keluar = parseFloat(bersihkanFormat(keluarInput.value)) || 0;
                const target = parseFloat(bersihkanFormat(targetInput.value)) || 0;
                const masuk = Math.round(drop * 0.13);
                const jadi = target + masuk - keluar;

                masukInput.value = formatRibuan(masuk);
                jadiInput.value = formatRibuan(jadi);

                jadiInput.classList.toggle('text-danger', jadi < 0);
                jadiInput.classList.toggle('text-primary', jadi >= 0);
                jadiInput.classList.add('fw-bold');

                sumTarget += target;
                sumDrop += drop;
                sumMasuk += masuk;
                sumKeluar += keluar;
                sumJadi += jadi;
            });

            const sumRow = table.querySelector('.sum-row');
            if (sumRow) {
                sumRow.querySelector('.sum-target').textContent = sumTarget.toLocaleString('id-ID');
                sumRow.querySelector('.sum-drop').textContent = sumDrop.toLocaleString('id-ID');
                sumRow.querySelector('.sum-masuk').textContent = sumMasuk.toLocaleString('id-ID');
                sumRow.querySelector('.sum-keluar').textContent = sumKeluar.toLocaleString('id-ID');
                sumRow.querySelector('.sum-jadi').textContent = sumJadi.toLocaleString('id-ID');
            }

            // Auto-update minggu berikutnya
            const nextMinggu = parseInt(minggu) + 1;
            const nextTable = document.querySelector(`.minggu[data-minggu="${nextMinggu}"]`);
            if (nextTable) {
                const nextTargets = nextTable.querySelectorAll('.target');
                const currentJadis = table.querySelectorAll('.jadi');

                currentJadis.forEach((jadiInput, index) => {
                    const value = parseFloat(bersihkanFormat(jadiInput.value)) || 0;
                    if (nextTargets[index]) {
                        nextTargets[index].value = formatRibuan(value);
                    }
                });

                updateMinggu(nextMinggu);
            }

            updateTotalBulanan();
        }

        function updateTotalBulanan() {
            let totalDrop = 0,
                totalMasuk = 0,
                totalKeluar = 0;

            document.querySelectorAll('.minggu').forEach(table => {
                loopRows(table, (row) => {
                    totalDrop += parseFloat(bersihkanFormat(row.querySelector('.drop')?.value)) || 0;
                    totalMasuk += parseFloat(bersihkanFormat(row.querySelector('.masuk')?.value)) || 0;
                    totalKeluar += parseFloat(bersihkanFormat(row.querySelector('.keluar')?.value)) || 0;
                });
            });

            document.getElementById('total-drop-bulan').textContent = totalDrop.toLocaleString('id-ID');
            document.getElementById('total-masuk-bulan').textContent = totalMasuk.toLocaleString('id-ID');
            document.getElementById('total-keluar-bulan').textContent = totalKeluar.toLocaleString('id-ID');
        }

        // === EVENT LISTENER: Input .drop dan .keluar ===
        document.querySelectorAll('.drop, .keluar').forEach(input => {
            input.addEventListener('input', () => {
                applyFormatInput(input);
                updateMinggu(input.dataset.minggu);
            });
        });

        // === EVENT LISTENER: Input Resort Mantri ===
        ['target-input', 'cm-input', 'mb-input'].forEach(cls => {
            document.querySelectorAll('.' + cls).forEach(input => {
                applyFormatInput(input);
                input.addEventListener('input', () => {
                    applyFormatInput(input);
                    hitungJumlahInput(cls, 'total-' + cls.split('-')[0]);
                });
            });
            hitungJumlahInput(cls, 'total-' + cls.split('-')[0]);
        });

        // === INISIALISASI: Update Semua Minggu ===
        document.querySelectorAll('.minggu').forEach(table => {
            updateMinggu(table.dataset.minggu);
        });

        // === TOMBOL SIMPAN Target Berjalan ===
        document.getElementById('btnSimpan').addEventListener('click', () => {
            const semuaData = [];

            document.querySelectorAll('.minggu').forEach(table => {
                const minggu = table.dataset.minggu;
                loopRows(table, row => {
                    const hari = row.children[0].textContent.trim();
                    const target = bersihkanFormat(row.querySelector('.target').value) || "0";
                    const drop = bersihkanFormat(row.querySelector('.drop').value) || "0";
                    const masuk = bersihkanFormat(row.querySelector('.masuk').value) || "0";

                    let keluarValue = row.querySelector('.keluar').value.trim();
                    let keluar = parseFloat(keluarValue.replace(/\./g, '').replace(',', '.')) || 0;
                    if (keluarValue.startsWith('-')) keluar = -Math.abs(keluar);

                    const jadi = bersihkanFormat(row.querySelector('.jadi').value) || "0";

                    semuaData.push({
                        minggu,
                        hari,
                        target,
                        drop,
                        masuk,
                        keluar,
                        jadi
                    });
                });
            });

            kirimDataKeServer('mantri/simpan_target_berjalan.php', {
                data: semuaData
            });
        });

        // === TOMBOL SIMPAN Resort KM ===
        document.getElementById('simpanResortKm').addEventListener('click', () => {
            const dataResort = [];

            document.querySelectorAll('.table tbody tr').forEach(row => {
                const hari = row.children[0]?.textContent?.trim();
                if (hari && row.querySelector('.target-input')) {
                    dataResort.push({
                        hari: hari,
                        target: bersihkanFormat(row.querySelector('.target-input').value),
                        cm: bersihkanFormat(row.querySelector('.cm-input').value),
                        mb: bersihkanFormat(row.querySelector('.mb-input').value)
                    });
                }
            });

            kirimDataKeServer('mantri/simpan_resort_km.php', dataResort);
        });
    </script>

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