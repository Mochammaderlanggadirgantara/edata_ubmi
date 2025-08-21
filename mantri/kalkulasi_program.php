<?php
include '../config/koneksi.php';
session_start();

// Pastikan variabel ada dan aman
$id_kelompok = isset($_SESSION['id_kelompok']) ? (int)$_SESSION['id_kelompok'] : 0;
$id_cabang   = isset($_SESSION['id_cabang']) ? (int)$_SESSION['id_cabang'] : 0;

// Ambil rencana_jadi dari tabel rekap_bulan_ini
$totalRencanaJadi = 0;
if ($id_kelompok > 0 && $id_cabang > 0) {
    $queryRencanaJadi = "
        SELECT rencana_jadi 
        FROM rekap_bulan_ini 
        WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang
    ";
    $resultRencanaJadi = mysqli_query($conn, $queryRencanaJadi);

    if ($resultRencanaJadi && $rowRencanaJadi = mysqli_fetch_assoc($resultRencanaJadi)) {
        $totalRencanaJadi = $rowRencanaJadi['rencana_jadi'];
    }
}

// Ambil data program + analisa
$query = "
    SELECT 
        p.*, 
        a.persen1, a.persen2, a.persen3, a.target_program
    FROM 
        program_mantri p
    INNER JOIN 
        analisa_storting a 
    ON 
        p.id = a.program_id
    WHERE 
        p.id_kelompok = $id_kelompok
    ORDER BY 
        p.id DESC
    LIMIT 1 
";

$result = mysqli_query($conn, $query);
$data = $result ? mysqli_fetch_assoc($result) : null;

$dataProgram = [];
$dataAnalisa = [];

if ($data) {
    $dataProgram = [
        'baru' => $data['baru'],
        'storting_jl' => $data['storting_jl'],
        'storting_jd' => $data['storting_jd'],
        'hari_kerja' => $data['hari_kerja'],
        'penagihan' => $data['penagihan'],
        'minggu' => $data['minggu']
    ];

    $dataAnalisa = [
        'persen1' => $data['persen1'],
        'persen2' => $data['persen2'],
        'persen3' => $data['persen3'],
        'target_program' => $data['target_program'],
    ];
}
?>

<style>
    body {
        background: #f5f7fa;
        font-family: "Segoe UI", Roboto, sans-serif;
        color: #333;
    }

    h5 {
        font-weight: 600;
        margin: 0;
    }

    /* Card */
    .card {
        border-radius: 1rem;
        overflow: hidden;
        border: none;
    }

    .card-header {
        font-weight: bold;
        font-size: 16px;
    }

    /* Table umum */
    .table {
        font-size: 14px;
        margin-bottom: 0;
    }

    .table th {
        background: #343a40 !important;
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table td input {
        font-size: 13px;
        padding: 6px;
    }

    /* Tabel Mobile Responsive */
    @media (max-width: 768px) {
        table thead {
            display: none;
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
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            padding: 0.5rem;
        }

        table tbody td,
        table tfoot td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem;
            border: none !important;
            border-bottom: 1px solid #f1f1f1 !important;
            font-size: 13px;
        }

        table tbody td:last-child,
        table tfoot td:last-child {
            border-bottom: none !important;
        }

        table tbody td::before,
        table tfoot td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #495057;
        }
    }

    /* Tombol simpan */
    .btn-success {
        padding: 0.75rem 1.8rem;
        font-size: 15px;
        font-weight: 600;
        border-radius: 0.75rem;
        transition: 0.3s ease;
    }

    .btn-success:hover {
        transform: scale(1.05);
    }

    /* Warna baris custom */
    .row-orange {
        background-color: #ffa94d !important;
        font-weight: bold;
    }

    .row-black {
        background-color: #343a40 !important;
        color: white !important;
        font-weight: bold;
    }

    .row-yellow {
        background-color: #fff3cd !important;
    }
</style>

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



    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white text-center">
            <h5 class="text-white mb-0">LIST VIEW DATA MANTRI</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-white mb-0">Hari</th>
                            <th class="text-white mb-0">Target</th>
                            <th class="text-white mb-0">CM</th>
                            <th class="text-white mb-0">MB</th>
                        </tr>
                    </thead>
                    <tbody class="row-orange">
                        <?php
                        $query = "SELECT a.hari, a.cm, a.mb, IFNULL(b.t_jadi, 0) AS target 
                              FROM targetmantri_babat1 a 
                              LEFT JOIN target_berjalan b 
                              ON a.hari = b.hari AND b.minggu = 4 
                              WHERE a.id_kelompok = $id_kelompok 
                              AND b.id_kelompok = $id_kelompok";
                        $result = mysqli_query($conn, $query);

                        $total_target = $total_cm = $total_mb = 0;

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td data-label='Hari'>" . htmlspecialchars($row['hari']) . "</td>";
                            echo "<td data-label='Target'>" . number_format($row['target'], 0, ',', '.') . "</td>";
                            echo "<td data-label='CM'>" . number_format($row['cm'], 0, ',', '.') . "</td>";
                            echo "<td data-label='MB'>" . number_format($row['mb'], 0, ',', '.') . "</td>";
                            echo "</tr>";

                            $total_target += $row['target'];
                            $total_cm += $row['cm'];
                            $total_mb += $row['mb'];
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="row-black">
                            <td>Jumlah</td>
                            <td class="row-orange"><?php echo number_format($total_target, 0, ',', '.'); ?></td>
                            <td class="row-orange"><?php echo number_format($total_cm, 0, ',', '.'); ?></td>
                            <td class="row-orange"><?php echo number_format($total_mb, 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Kalkulasi Program -->
        <div class="col-md-8 mb-3">
            <div class="card shadow h-100">
                <div class="card-header bg-secondary text-white text-center">
                    <h5 class="text-white mb-0">KALKULASI PROGRAM MANTRI</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <tbody>
                                <tr class="row-black">
                                    <td>Target</td>
                                    <td id="target-program-mantri"><?php echo number_format($total_target, 0, ',', '.'); ?></td>
                                    <td id="target-program-index">0</td>
                                </tr>
                                <tr class="row-black">
                                    <td>Pelunasan</td>
                                    <td id="pelunasan-program-mantri"><?php echo number_format($totalRencanaJadi, 0, ',', '.'); ?></td>
                                    <td id="pelunasan-program-index"><?php echo number_format(round($totalRencanaJadi * 0.13 * 2), 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td class="row-black">Baru</td>
                                    <td><input type="text" class="form-control text-center number-input" id="baru-program-mantri"></td>
                                    <td id="baru-program-index">0</td>
                                </tr>
                                <tr>
                                    <td class="row-black">Storting JL</td>
                                    <td colspan="2"><input type="text" class="form-control text-center number-input" id="storting-jl-mantri"></td>
                                </tr>
                                <tr>
                                    <td class="row-black">Storting JD</td>
                                    <td><input type="text" class="form-control text-center number-input" id="storting-jd-mantri" name="storting_jd_mantri"></td>
                                    <td id="storting-jd-index">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keterangan Tambahan -->
        <div class="col-md-4 mb-3">
            <div class="card shadow h-100">
                <div class="card-header bg-secondary text-white text-center">
                    <h5 class="text-white mb-0">KETERANGAN TAMBAHAN</h5>
                </div>
                <div class="card-body p-0">
                    <form id="form-keterangan">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <tbody>
                                    <tr class="row-black">
                                        <td>Kurang Hari Kerja</td>
                                    </tr>
                                    <tr class="row-yellow">
                                        <td><input type="text" class="form-control text-center number-input" id="input-hari-kerja"></td>
                                    </tr>
                                    <tr class="row-black">
                                        <td>Penagihan</td>
                                    </tr>
                                    <tr class="row-yellow">
                                        <td><input type="text" class="form-control text-center number-input" name="penagihan" id="input-penagihan"></td>
                                    </tr>
                                    <tr class="row-black">
                                        <td>1 Minggu</td>
                                    </tr>
                                    <tr class="row-yellow">
                                        <td><input type="text" class="form-control text-center number-input" id="input-minggu"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Kekuatan Program -->
    <div class="card shadow mt-4">
        <div class="card-header bg-secondary text-white text-center">
            <h5 class="text-white mb-0">KEKUATAN PROGRAM</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-white mb-0">Index</th>
                            <th class="text-white mb-0">Program Drop</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" id="persen-1" class="form-control text-center persen-input" value="115"></td>
                            <td id="storting-1"></td>
                        </tr>
                        <tr>
                            <td><input type="text" id="persen-2" class="form-control text-center persen-input" value="120"></td>
                            <td id="storting-2"></td>
                        </tr>
                        <tr>
                            <td><input type="text" id="persen-3" class="form-control text-center persen-input" value="123"></td>
                            <td id="storting-3"></td>
                        </tr>
                        <tr class="row-black">
                            <td><strong>Target Program</strong></td>
                            <td><input type="text" id="storting-target" class="form-control text-center number-input"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Analisa Storting -->
    <div class="card shadow mt-3 mb-5">
        <div class="card-header bg-secondary text-white text-center">
            <h5 class="text-white mb-0">ANALISA STORTING</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-white mb-0">Index</th>
                            <th class="text-white mb-0">T. Storting</th>
                            <th class="text-white mb-0">Plus / Minus</th>
                        </tr>
                    </thead>
                    <tbody id="analisa-table-body">
                        <tr>
                            <td class="persen-value">115%</td>
                            <td class="t-storting">0</td>
                            <td class="plus-minus">0</td>
                        </tr>
                        <tr>
                            <td class="persen-value">120%</td>
                            <td class="t-storting">0</td>
                            <td class="plus-minus">0</td>
                        </tr>
                        <tr>
                            <td class="persen-value">123%</td>
                            <td class="t-storting">0</td>
                            <td class="plus-minus">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center my-4">
        <button class="btn btn-success btn-lg" onclick="simpanData()">Simpan Data</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        function formatNumberWithDot(value) {
            return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        document.querySelectorAll('.number-input').forEach(input => {
            input.value = formatNumberWithDot(input.value);

            input.addEventListener('input', () => {
                const pos = input.selectionStart;
                const raw = input.value.replace(/\D/g, '');
                input.value = formatNumberWithDot(raw);
                const newPos = pos + (input.value.length - raw.length);
                input.setSelectionRange(newPos, newPos);
            });

            input.addEventListener('keypress', e => {
                if (!/\d/.test(e.key)) e.preventDefault();
            });
            input.addEventListener('paste', e => {
                if (/\D/.test((e.clipboardData || window.clipboardData).getData('text'))) e.preventDefault();
            });
        });

        document.querySelectorAll('.persen-input').forEach(input => {
            const format = () => {
                const angka = input.value.replace(/\D/g, '');
                input.value = angka ? angka + '%' : '';
            };

            format();
            input.addEventListener('input', format);
            input.addEventListener('keypress', e => {
                if (!/\d/.test(e.key)) e.preventDefault();
            });
            input.addEventListener('paste', e => {
                if (/\D/.test((e.clipboardData || window.clipboardData).getData('text'))) e.preventDefault();
            });
        });

        function hitungIndexProgram() {
            let t = parseInt(document.getElementById("target-program-mantri")?.innerText.replace(/\./g, '') || '0');
            let m = parseInt(document.getElementById("input-minggu")?.value.replace(/\./g, '') || '1');
            let h = parseInt(document.getElementById("input-hari-kerja")?.value.replace(/\./g, '') || '0');

            let hasil = m > 0 ? Math.round((t / m) * h) : 0;
            document.getElementById("target-program-index").innerText = formatNumberWithDot(hasil.toString());

            hitungStortingJDIndex();
        }

        ["input-minggu", "input-hari-kerja"].forEach(id => {
            document.getElementById(id)?.addEventListener('input', hitungIndexProgram);
        });

        window.addEventListener("DOMContentLoaded", hitungIndexProgram);

        function hitungBaruIndex() {
            let baru = parseInt(document.getElementById("baru-program-mantri")?.value.replace(/\./g, '') || '0');
            let penagihan = parseInt(document.querySelector('input[name="penagihan"]')?.value.replace(/\./g, '') || '0');
            let hasil = Math.round(baru * 0.13 * penagihan);
            document.getElementById("baru-program-index").innerText = formatNumberWithDot(hasil.toString());

            hitungStortingJDIndex();
        }

        document.getElementById("baru-program-mantri")?.addEventListener("input", hitungBaruIndex);
        document.querySelector('input[name="penagihan"]')?.addEventListener("input", hitungBaruIndex);

        window.addEventListener("DOMContentLoaded", () => {
            hitungPelunasanIndex();
            hitungBaruIndex();
        });

        function hitungStortingJDIndex() {
            let ids = [
                "target-program-index",
                "pelunasan-program-index",
                "baru-program-index",
                "storting-jl-mantri"
            ];

            let total = ids.reduce((sum, id) => {
                let el = document.getElementById(id);
                if (!el) return sum;

                // Gunakan .innerText jika <td>, .value jika <input>
                let val = el.tagName === "TD" ?
                    parseInt(el.innerText.replace(/\./g, '') || '0') :
                    parseInt(el.value.replace(/\./g, '') || '0');
                return sum + val;
            }, 0);

            let stortingJD = parseInt(document.querySelector('input[name="storting_jd_mantri"]')?.value.replace(/\./g, '') || '0');
            total += stortingJD;

            let el = document.getElementById("storting-jd-index");
            if (el) el.innerText = formatNumberWithDot(total.toString());
        }


        // Semua elemen pemicu
        [
            "target-program-index",
            "pelunasan-program-index",
            "baru-program-index",
            "storting-jl-mantri"
        ].forEach(id => {
            document.getElementById(id)?.addEventListener('input', hitungStortingJDIndex);
        });

        document.querySelector('input[name="storting_jd_mantri"]')?.addEventListener("input", hitungStortingJDIndex);
        window.addEventListener("DOMContentLoaded", hitungStortingJDIndex);

        function updateStortingByIndex() {
            const base = parseInt(document.getElementById('storting-jd-index')?.innerText.replace(/\./g, '') || '0');
            for (let i = 1; i <= 3; i++) {
                let persen = parseFloat((document.getElementById('persen-' + i)?.value || '0').replace('%', '')) || 0;
                let hasil = persen ? base / (persen / 100) : 0;
                document.getElementById('storting-' + i).innerText = Math.round(hasil).toLocaleString('id-ID');
            }
        }

        function updatePersenValue() {
            document.querySelectorAll('#analisa-table-body tr').forEach((row, i) => {
                const val = document.getElementById('persen-' + (i + 1))?.value || '';
                const cell = row.querySelector('.persen-value');
                if (cell) cell.innerText = val;
            });
        }

        function updateTStorting() {
            const base = parseInt(document.getElementById('storting-target')?.value.replace(/\./g, '') || '0');
            document.querySelectorAll('#analisa-table-body tr').forEach(row => {
                let persen = parseFloat(row.querySelector('.persen-value')?.innerText.replace('%', '') || '0') / 100;
                row.querySelector('.t-storting').innerText = Math.round(base * persen).toLocaleString('id-ID');
            });
        }

        function updatePlusMinus() {
            const base = parseInt(document.getElementById('storting-jd-index')?.innerText.replace(/\./g, '') || '0');
            document.querySelectorAll('#analisa-table-body tr').forEach(row => {
                const target = parseInt(row.querySelector('.t-storting')?.innerText.replace(/\./g, '') || '0');
                const selisih = base - target;
                const cell = row.querySelector('.plus-minus');
                if (cell) {
                    cell.innerText = selisih.toLocaleString('id-ID');
                    cell.style.color = selisih < 0 ? '#721c24' : '#155724';
                }
            });
        }

        for (let i = 1; i <= 3; i++) {
            document.getElementById('persen-' + i)?.addEventListener('input', () => {
                updatePersenValue();
                updateStortingByIndex();
                updateTStorting();
                updatePlusMinus();
            });
        }

        document.getElementById('storting-target')?.addEventListener('input', () => {
            updateTStorting();
            updatePlusMinus();
        });

        document.getElementById('storting-jd-index')?.addEventListener('input', () => {
            updateStortingByIndex();
            updatePlusMinus();
        });

        ["baru-program-mantri", "storting-jl-mantri", "storting-jd-mantri", "input-hari-kerja", "input-minggu", "input-penagihan"].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener("input", () => {
                    updateStortingByIndex();
                    updatePlusMinus(); // agar selisih ikut terupdate
                });
            }
        });

        [1, 2, 3].forEach(i => {
            document.getElementById(`persen-${i}`)?.addEventListener("input", () => {
                updatePersenValue();
                updateTStorting();
                updatePlusMinus();
            });
        });

        //simpan
        function simpanData() {
            const data = {
                // Grup 1: Program Mantri + Keterangan Tambahan
                baru: document.getElementById('baru-program-mantri').value.replace(/\./g, ''),
                storting_jl: document.getElementById('storting-jl-mantri').value.replace(/\./g, ''),
                storting_jd: document.getElementById('storting-jd-mantri').value.replace(/\./g, ''),
                hari_kerja: document.getElementById('input-hari-kerja').value.replace(/\./g, ''),
                penagihan: document.getElementById('input-penagihan').value.replace(/\./g, ''),
                minggu: document.getElementById('input-minggu').value.replace(/\./g, ''),

                // Grup 2: Analisa Storting
                persen1: document.getElementById('persen-1').value.replace('%', ''),
                persen2: document.getElementById('persen-2').value.replace('%', ''),
                persen3: document.getElementById('persen-3').value.replace('%', ''),
                storting_target: document.getElementById('storting-target').value.replace(/\./g, ''),
            };

            fetch('mantri/simpan_kalkulasi_mantri.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.text())
                .then(response => {
                    alert("Data berhasil disimpan!\n\n" + response);
                })
                .catch(error => {
                    alert("Gagal menyimpan data!");
                    console.error(error);
                });
        }
    </script>
    <script>
        fetch('mantri/get_kalkulasi.php')
            .then(res => res.json())
            .then(response => {
                console.log("DATA FETCHED:", response);
                if (response.success) {
                    const dataProgram = response.dataProgram;
                    const dataAnalisa = response.dataAnalisa;

                    // Isi input dari program_mantri
                    document.getElementById('baru-program-mantri').value = formatNumberWithDot(dataProgram.baru || '');
                    document.getElementById('storting-jl-mantri').value = formatNumberWithDot(dataProgram.storting_jl || '');
                    document.getElementById('storting-jd-mantri').value = formatNumberWithDot(dataProgram.storting_jd || '');
                    document.getElementById('input-hari-kerja').value = formatNumberWithDot(dataProgram.hari_kerja || '');
                    document.getElementById('input-penagihan').value = formatNumberWithDot(dataProgram.penagihan || '');
                    document.getElementById('input-minggu').value = formatNumberWithDot(dataProgram.minggu || '');

                    // Isi input dari analisa_storting
                    document.getElementById('persen-1').value = (dataAnalisa.persen1 || '') + '%';
                    document.getElementById('persen-2').value = (dataAnalisa.persen2 || '') + '%';
                    document.getElementById('persen-3').value = (dataAnalisa.persen3 || '') + '%';
                    document.getElementById('storting-target').value = formatNumberWithDot(dataAnalisa.target_program || '');

                    // Jalankan ulang kalkulasi jika perlu
                    //5 dibawah penting banget
                    hitungIndexProgram();
                    hitungBaruIndex();
                    hitungStortingJDIndex();
                    updateStortingByIndex();
                    updatePersenValue();

                    updateTStorting();
                    updatePlusMinus();
                } else {
                    //alert("Data kalkulasi belum tersedia.");
                }
            });
    </script>

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