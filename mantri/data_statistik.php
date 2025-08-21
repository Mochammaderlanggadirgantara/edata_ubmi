<?php
session_start();
$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang   = $_SESSION['id_cabang'];
include '../config/koneksi.php';

$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'];
$mingguList = [1, 2, 3, 4, 5];
$targetData = [];

// Ambil seluruh data dari target_berjalan
$data = [];
$sql = "SELECT minggu, hari, target, t_jadi 
        FROM target_berjalan 
        WHERE id_kelompok = '$id_kelompok' 
        AND id_cabang = '$id_cabang'
        ORDER BY minggu, FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu')";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $data[$row['minggu']][$row['hari']] = [
        'target' => $row['target'],
        't_jadi' => $row['t_jadi']
    ];
}
// Ambil data yang sudah ada di data_statistik
$savedData = [];
$sqlStatistik = "SELECT * FROM data_statistik 
                 WHERE id_kelompok = '$id_kelompok' 
                 AND id_cabang = '$id_cabang'";
$resStatistik = mysqli_query($conn, $sqlStatistik);

while ($row = mysqli_fetch_assoc($resStatistik)) {
    $minggu = $row['minggu'];
    $hari   = $row['hari'];
    $savedData[$minggu][$hari] = [
        'rencana'     => $row['rencana'],
        'storting'    => $row['storting'],
        'storting_tg' => $row['storting_tg'],
        'min_plus'    => $row['min_plus'],
    ];
}

// Siapkan target untuk tiap minggu
foreach ($mingguList as $minggu) {
    foreach ($hariList as $hari) {
        if ($minggu == 1) {
            $targetData[$minggu][$hari] = isset($data[1][$hari]['target']) ? $data[1][$hari]['target'] : 0;
        } else {
            $prev = $minggu - 1;
            $targetData[$minggu][$hari] = isset($data[$prev][$hari]['t_jadi']) ? $data[$prev][$hari]['t_jadi'] : 0;
        }
    }
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

    .minplus.positif {
        color: blue;
        font-weight: bold;
    }

    .minplus.negatif {
        color: red;
        font-weight: bold;
    }

    .minggu-tabel {
        transition: all 0.3s ease;
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

            <form id="form-statistik" method="post" action="mantri/simpan_data_statistik.php">
                <div class="container mt-3 mb-5">

                    <!-- Judul -->
                    <h5 class="text-center fw-bold">📊 DATA STATISTIK STORTING MURNI</h5>
                    <p class="text-center small text-muted mb-3">RUMUS: (DROP - 20%) - KASBON + TRANSPORT + SU + TF = STORTING</p>

                    <!-- Input LP -->
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-primary text-white fw-bold text-center">Input Storting LP</div>
                        <div class="card-body p-2">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Drop</label>
                                    <input type="number" class="form-control form-control-sm text-end" id="lp_drop" name="lp_drop">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Kasbon</label>
                                    <input type="number" class="form-control form-control-sm text-end" id="lp_kasbon" name="lp_kasbon">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Transport</label>
                                    <input type="number" class="form-control form-control-sm text-end" id="lp_transport" name="lp_transport">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">SU</label>
                                    <input type="number" class="form-control form-control-sm text-end" id="lp_su" name="lp_su">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">TF</label>
                                    <input type="number" class="form-control form-control-sm text-end" id="lp_tf" name="lp_tf">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Hasil</label>
                                    <div class="d-flex">
                                        <input type="text" class="form-control form-control-sm text-end fw-bold" id="lp_hasil" name="lp_hasil" readonly>
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="btn-clear-lp">🗑️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Minggu -->
                    <div class="mb-3">
                        <label for="filterMinggu" class="form-label fw-bold">Filter Minggu</label>
                        <select id="filterMinggu" class="form-select form-select-sm">
                            <option value="all">Tampilkan Semua</option>
                            <?php foreach ($mingguList as $minggu): ?>
                                <option value="minggu<?= $minggu ?>">Minggu ke-<?= $minggu ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Minggu List Accordion -->
                    <div class="accordion" id="accordionMinggu">
                        <?php foreach ($mingguList as $index => $minggu): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $minggu ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $minggu ?>" aria-expanded="false">
                                        📅 Minggu ke-<?= $minggu ?>
                                    </button>
                                </h2>
                                <div id="collapse<?= $minggu ?>" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionMinggu">
                                    <div class="accordion-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered text-center align-middle minggu-tabel"
                                                id="minggu<?= $minggu ?>">
                                                <thead class="table-dark">
                                                    <tr class="small">
                                                        <th>Hari</th>
                                                        <th>Target</th>
                                                        <th>Rencana</th>
                                                        <th>Storting</th>
                                                        <th>Storting TG</th>
                                                        <th>Min/Plus</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="small">
                                                    <?php
                                                    $totalTarget = 0;
                                                    $totalStortingTG = 0;
                                                    foreach ($hariList as $hari):
                                                        $target = isset($targetData[$minggu][$hari]) ? $targetData[$minggu][$hari] : 0;
                                                        $totalTarget += $target;
                                                        $totalStortingTG += $target;
                                                    ?>
                                                        <tr>
                                                            <td><?= $hari ?></td>
                                                            <td><?= number_format($target, 0, ',', '.') ?></td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm text-end"
                                                                    name="rencana[]"
                                                                    value="<?= isset($savedData[$minggu][$hari]['rencana']) ? number_format($savedData[$minggu][$hari]['rencana'], 0, ',', '.') : '' ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm text-end"
                                                                    name="storting[]"
                                                                    value="<?= isset($savedData[$minggu][$hari]['storting']) ? number_format($savedData[$minggu][$hari]['storting'], 0, ',', '.') : '' ?>">
                                                            </td>
                                                            <td><?= isset($savedData[$minggu][$hari]['storting_tg']) ? number_format($savedData[$minggu][$hari]['storting_tg'], 0, ',', '.') : '0' ?></td>
                                                            <td><?= isset($savedData[$minggu][$hari]['min_plus']) ? number_format($savedData[$minggu][$hari]['min_plus'], 0, ',', '.') : '0' ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot class="fw-bold small">
                                                    <tr class="table-secondary">
                                                        <td>Total</td>
                                                        <td><?= number_format($totalTarget, 0, ',', '.') ?></td>
                                                        <td class="totalRencana">0</td>
                                                        <td class="totalStorting">0</td>
                                                        <td><?= number_format($totalStortingTG / 1000, 3, ',', '.') ?></td>
                                                        <td class="totalMinPlus">0</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Total Bulanan -->
                    <div class="card shadow-sm rounded-3 mt-4">
                        <div class="card-header bg-dark text-white text-center fw-bold">TOTAL 1 BULAN</div>
                        <div class="card-body p-2">
                            <table class="table table-sm table-bordered text-center align-middle">
                                <thead class="table-dark small">
                                    <tr>
                                        <th>Total Target</th>
                                        <th>Total Rencana</th>
                                        <th>Total Storting</th>
                                        <th>Total Storting TG</th>
                                        <th>Total Min/Plus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-secondary fw-bold">
                                        <td id="bulanTarget">0</td>
                                        <td id="bulanRencana">0</td>
                                        <td id="bulanStorting">0</td>
                                        <td id="bulanStortingTG">0</td>
                                        <td id="bulanMinPlus">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="text-center mt-4">
                        <button id="btn-simpan" class="btn btn-success btn-lg w-45">💾 Simpan</button>
                        <button id="btn-bersihkan" class="btn btn-danger btn-lg w-45 ms-2">🧹 Bersihkan</button>
                        <p id="status-simpan" class="mt-2 fw-bold"></p>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        //filter minggu
        $('#filterMinggu').on('change', function() {
            const val = $(this).val();
            if (val === 'all') {
                $('.minggu-tabel').show();
                $('h5.text-primary').show();
            } else {
                $('.minggu-tabel').hide();
                $('h5.text-primary').hide();
                $(`#${val}`).show();
                $(`#${val}`).prev('h5.text-primary').show();
            }
        });

        function updateTotals() {
            let bulanTarget = 0;
            let bulanRencana = 0;
            let bulanStorting = 0;
            let bulanStortingTG = 0;
            let bulanMinPlus = 0;

            $('table').each(function() {
                let totalRencana = 0;
                let totalStorting = 0;
                let totalStortingTG = 0;
                let totalMP = 0;

                const mingguHeader = $(this).prev('h5').text();
                const isMingguan = mingguHeader.startsWith('MINGGU');

                $(this).find('tbody tr').each(function() {
                    const rencana = parseFloat($(this).find('.rencana').val()?.replace(/\./g, '')) || 0;
                    const storting = parseFloat($(this).find('.storting').val()?.replace(/\./g, '')) || 0;
                    const target = parseFloat($(this).find('.stortingTG').data('target')) || 0;

                    const stortingTG = (rencana * 0.26) + target;
                    $(this).find('.stortingTG').text(Math.round(stortingTG).toLocaleString('id-ID'));

                    const minplus = storting - stortingTG;
                    const minplusCell = $(this).find('.minplus');
                    minplusCell.text(Math.round(minplus).toLocaleString('id-ID'));
                    minplusCell.removeClass('positif negatif');
                    if (minplus < 0) {
                        minplusCell.addClass('negatif');
                    } else if (minplus > 0) {
                        minplusCell.addClass('positif');
                    }

                    totalRencana += rencana;
                    totalStorting += storting;
                    totalStortingTG += stortingTG;
                    totalMP += minplus;
                });

                $(this).find('.totalRencana').text(totalRencana.toLocaleString('id-ID'));
                $(this).find('.totalStorting').text(totalStorting.toLocaleString('id-ID'));
                $(this).find('tfoot td:nth-child(5)').text(
                    Math.round(totalStortingTG).toLocaleString('id-ID')
                );
                $(this).find('.totalMinPlus').text(totalMP.toLocaleString('id-ID'));

                if (isMingguan) {
                    const tds = $(this).find('tfoot tr td');
                    bulanTarget += parseFloat(tds.eq(1).text().replace(/\./g, '')) || 0;
                    bulanRencana += totalRencana;
                    bulanStorting += totalStorting;
                    bulanStortingTG += totalStortingTG;
                    bulanMinPlus += totalMP;
                }
            });

            $('#bulanTarget').text(bulanTarget.toLocaleString('id-ID'));
            $('#bulanRencana').text(bulanRencana.toLocaleString('id-ID'));
            $('#bulanStorting').text(bulanStorting.toLocaleString('id-ID'));
            $('#bulanStortingTG').text(
                Math.round(bulanStortingTG).toLocaleString('id-ID')
            );
            $('#bulanMinPlus').text(bulanMinPlus.toLocaleString('id-ID'));
        }

        function formatInputToRibuan(input) {
            let value = input.value.replace(/\./g, '').replace(/[^\d]/g, '');
            if (value === '') {
                input.value = '';
                return;
            }
            input.value = parseInt(value, 10).toLocaleString('id-ID');
        }

        // Tambahan untuk STORTING LP 1 baris
        function parseAngka(input) {
            return parseFloat(input.replace(/\./g, '').replace(',', '.')) || 0;
        }

        function updateHasilLP() {
            const drop = parseAngka($('#lp_drop').val());
            const kasbon = parseAngka($('#lp_kasbon').val());
            const transport = parseAngka($('#lp_transport').val());
            const su = parseAngka($('#lp_su').val());
            const tf = parseAngka($('#lp_tf').val());

            const hasil = (drop * 0.8) - kasbon + transport + su + tf;
            $('#lp_hasil').val(Math.round(hasil).toLocaleString('id-ID'));
        }

        $(document).ready(function() {
            updateTotals();
            updateHasilLP(); // inisialisasi hasil LP jika ada nilai default
        });

        $(document).on('input', '.rencana, .storting', function() {
            formatInputToRibuan(this);
            updateTotals();
        });

        // Event untuk input STORTING LP (1 baris)
        $(document).on('input', '#lp_drop, #lp_kasbon, #lp_transport, #lp_su, #lp_tf', function() {
            formatInputToRibuan(this);
            updateHasilLP();
        });

        $('#btn-simpan').on('click', function(e) {
            e.preventDefault();
            updateTotals();

            $('table').each(function() {
                $(this).find('tbody tr').each(function() {
                    const stortingTG = $(this).find('.stortingTG').text().replace(/\./g, '').replace(',', '.');
                    const minPlus = $(this).find('.minplus').text().replace(/\./g, '').replace(',', '.');
                    $(this).find('.stortingTG-input').val(stortingTG);
                    $(this).find('.minplus-input').val(minPlus);
                });
            });

            const formData = $('#form-statistik').serialize();

            $.ajax({
                url: 'mantri/simpan_data_statistik.php',
                type: 'POST',
                data: formData,
                success: function(res) {
                    $('#status-simpan').text('✅ Data berhasil disimpan!').css('color', 'green');
                },
                error: function() {
                    $('#status-simpan').text('❌ Gagal menyimpan data.').css('color', 'red');
                }
            });
        });

        $('#btn-bersihkan').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Apakah Anda yakin ingin menghapus semua data statistik?')) {
                return;
            }

            $.ajax({
                url: 'mantri/hapus_data_statistik.php',
                type: 'POST',
                success: function(res) {
                    $('#status-simpan').text('✅ Semua data berhasil dihapus.').css('color', 'green');

                    $('.rencana, .storting').val('');
                    $('.stortingTG').text('0');
                    $('.minplus').text('0').removeClass('positif negatif');
                    $('.totalRencana, .totalStorting, .totalMinPlus').text('0');
                    $('#bulanRencana, #bulanStorting, #bulanStortingTG, #bulanMinPlus').text('0');

                    // Kosongkan input STORTING LP
                    $('#lp_drop, #lp_kasbon, #lp_transport, #lp_su, #lp_tf, #lp_hasil').val('');
                },
                error: function() {
                    $('#status-simpan').text('❌ Gagal menghapus data.').css('color', 'red');
                }
            });
        });

        //menghapus tabel perhitungan storting lp
        $('#btn-clear-lp').on('click', function() {
            $('#lp_drop, #lp_kasbon, #lp_transport, #lp_su, #lp_tf, #lp_hasil').val('');
            updateHasilLP();
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