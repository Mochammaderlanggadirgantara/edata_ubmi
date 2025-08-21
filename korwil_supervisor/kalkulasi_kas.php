<?php
include '../config/koneksi.php';
session_start();

$id_cabang   = $_SESSION['id_cabang'] ?? 0;
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Ambil nama cabang
$nama_cabang = '';
if ($id_cabang) {
    $queryCabang = $conn->prepare("SELECT nama_cabang FROM cabang WHERE id_cabang = ?");
    $queryCabang->bind_param("i", $id_cabang);
    $queryCabang->execute();
    $resultCabang = $queryCabang->get_result()->fetch_assoc();
    if ($resultCabang) {
        $nama_cabang = $resultCabang['nama_cabang'];
    }
}

$data = [
    'target_murni' => '',
    'drop_baru' => '',
    'hk' => '',
    'penagihan' => '',
    'pelunasan' => '',
    'gaji' => '',
    'bps' => '',
    'setor' => '',
    'pengembalian' => '',
    'kasakhir' => '',
    'katrolan' => ''
];

// Ambil data terakhir dari database
$query = $conn->prepare("SELECT * FROM kalkulasi_kas WHERE id_cabang = ? ORDER BY id_kalkulasi DESC LIMIT 1");
$query->bind_param("i", $id_cabang);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    $data = $row; // isi $data dengan hasil query
}

$query->close();
?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <!-- Links Of CSS File -->
    <link rel="stylesheet" href="/edata_ubmi/assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/simplebar.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/apexcharts.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/prism.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/rangeslider.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/quill.snow.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/google-icon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/remixicon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/fullcalendar.main.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/jsvectormap.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/lightpick.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css" />


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
            <h3 class="mb-0">Kalkulasi KAS</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Kalkulasi KAS</span>
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
<section class="content">
    <div class="container-fluid">
        <div class="card card-danger">
            <div class="card-header text-center">
                <h3 class="card-title w-100">KALKULASI KAS <?= strtoupper(htmlspecialchars($nama_cabang)) ?></h3>
            </div>
            <div class="card-body">
                <form id="formKalkulasi" method="POST">
                    <table class="mx-auto text-center">
                        <!-- Target -->
                        <tr>
                            <td rowspan="2" style="width: 100px; text-align: center; vertical-align: middle;">TARGET</td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                TARGET MURNI <br>
                                <input type="text" id="target_murni" name="target_murni"
                                    value="<?= htmlspecialchars($data['target_murni']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td></td>
                            <td rowspan="1" style="width: 30px; text-align: center; vertical-align: middle;">X</td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                HK
                                <input type="text" id="hk" name="hk"
                                    value="<?= htmlspecialchars($data['hk']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td rowspan="2" style="width: 50px; text-align: center; vertical-align: middle;">=</td>
                            <td rowspan="2" style="width: 50px; text-align: center; vertical-align: middle;" id="hasil_target">0</td>
                        </tr>
                        <tr>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                DROP BARU <br>
                                <input type="text" id="drop_baru" name="drop_baru"
                                    value="<?= htmlspecialchars($data['drop_baru']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                PENAGIHAN <br>
                                <input type="text" id="penagihan" name="penagihan"
                                    value="<?= htmlspecialchars($data['penagihan']) ?>" style="width: 100%; text-align: center;" value="6">
                            </td>
                        </tr>
                        <tr style="height: 20px;"></tr>
                        <!-- Pelunasan -->
                        <tr>
                            <td style="width: 100px; text-align: center; vertical-align: middle;">PELUNASAN</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                <input type="text" id="pelunasan" name="pelunasan"
                                    value="<?= htmlspecialchars($data['pelunasan']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td></td>
                            <td style="width: 50px; text-align: center; vertical-align: middle;">-</td>
                            <td style="width: 50px; text-align: center; vertical-align: middle;">46%</td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">=
                            </td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_pelunasan">0</td>
                        </tr>
                        <!-- Total Target + Pelunasan -->
                        <tr style="height: 20px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">------------</td>
                        </tr>
                        <tr style="height: 20px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_tp">0</td>
                        </tr>
                        <!-- Kebutuhan -->
                        <tr>
                            <td rowspan="4" style="width: 100px; text-align: center; vertical-align: middle;">KEBUTUHAN</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                GAJI <br>
                                <input type="text" id="gaji" name="gaji"
                                    value="<?= htmlspecialchars($data['gaji']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td rowspan="4"></td>
                            <td rowspan="4"></td>
                            <td></td>
                            <td rowspan="4" style="width: 50px; text-align: center; vertical-align: middle;">=</td>
                            <td rowspan="4" style="width: 50px; text-align: center; vertical-align: middle;" id="hasil_kebutuhan">0</td>
                        </tr>
                        <tr>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                BPS / 9% / INV <br>
                                <input type="text" id="bps" name="bps"
                                    value="<?= htmlspecialchars($data['bps']) ?>" style="width: 100%; text-align: center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                SETOR <br>
                                <input type="text" id="setor" name="setor"
                                    value="<?= htmlspecialchars($data['setor']) ?>" style="width: 100%; text-align: center;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                PENGEMBALIAN <br>
                                <input type="text" id="pengembalian" name="pengembalian"
                                    value="<?= htmlspecialchars($data['pengembalian']) ?>" style="width: 100%; text-align: center;">
                            </td>
                        </tr>
                        <!-- (Total Target + Pelunasan) - Kebutuhan -->
                        <tr style="height: 20px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">------------</td>
                        </tr>
                        <tr style="height: 20px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_tpk">0</td>
                        </tr>
                        <!-- Kas Akhir -->
                        <tr>
                            <td style="width: 100px; text-align: center; vertical-align: middle;">KAS AKHIR</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                <input type="text" id="kasakhir" name="kasakhir"
                                    value="<?= htmlspecialchars($data['kasakhir']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">=</td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_kasakhir">0</td>
                        </tr>
                        <tr style="height: 20px;"></tr>
                        <!-- Katrolan -->
                        <tr>
                            <td style="width: 100px; text-align: center; vertical-align: middle;">KATROLAN</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;">
                                <input type="text" id="katrolan" name="katrolan"
                                    value="<?= htmlspecialchars($data['katrolan']) ?>" style="width: 100%; text-align: center;">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">=</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_katrolan">0</td>
                        </tr>
                        <!-- Kas Awal Bulan -->
                        <tr style="height: 20px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">------------</td>
                        </tr>
                        <tr style="height: 20px;"></tr>
                        <tr>
                            <td style="width: 150px; text-align: center; vertical-align: middle; white-space: nowrap;">
                                KAS AWAL BULAN
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="width: 150px; text-align: center; vertical-align: middle;">=</td>

                            <td style="width: 150px; text-align: center; vertical-align: middle;" id="hasil_kasawalbulan">0</td>
                        </tr>
                        <tr>
                            <td colspan="7" style="text-align: center; padding-top: 20px;">
                                <button type="submit" class="btn btn-danger">Simpan</button>
                                <button type="button" id="btnBersih" class="btn btn-danger">Bersih Data</button>

                            </td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>
    </div>
</section>
            </div>
        </div>

    </div>

    <!-- script File -->
    <script>
    function formatRibuan(angka) {
        if (angka === null || angka === undefined || angka === "") return "0";
        return String(angka).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function bersihRibuan(angka) {
        if (!angka) return "0";
        return String(angka).replace(/\./g, "");
    }

    // Simpan variabel global dalam satu object
    window.kalkulasi = {
        nilaiTarget: 0,
        nilaiPelunasan: 0,
        nilaiTP: 0,
        nilaiKebutuhan: 0,
        nilaiTPK: 0
    };

    // Hasil Target
    function hitungTarget() {
        let targetMurni = parseInt(bersihRibuan(document.getElementById("target_murni").value)) || 0;
        let dropBaru = parseInt(bersihRibuan(document.getElementById("drop_baru").value)) || 0;
        let hk = parseInt(bersihRibuan(document.getElementById("hk").value)) || 0;
        let penagihan = parseInt(bersihRibuan(document.getElementById("penagihan").value)) || 0;

        window.kalkulasi.nilaiTarget = ((targetMurni / 6) * hk) + ((dropBaru * 0.13) * penagihan);

        document.getElementById("hasil_target").innerText = formatRibuan(Math.round(window.kalkulasi.nilaiTarget));
        hitungTP();
    }

    // Hasil Pelunasan
    function hitungPelunasan() {
        let pelunasan = parseInt(bersihRibuan(document.getElementById("pelunasan").value)) || 0;

        window.kalkulasi.nilaiPelunasan = pelunasan * 0.54; // 54% dari pelunasan

        document.getElementById("hasil_pelunasan").innerText = formatRibuan(Math.round(window.kalkulasi.nilaiPelunasan));
        hitungTP();
    }

    // Hasil TP = Target - Pelunasan
    function hitungTP() {
        window.kalkulasi.nilaiTP = window.kalkulasi.nilaiTarget - window.kalkulasi.nilaiPelunasan;
        document.getElementById("hasil_tp").innerText = formatRibuan(Math.round(window.kalkulasi.nilaiTP));
        hitungTPK();
    }

    // Hasil Kebutuhan
    function hitungKebutuhan() {
        let gaji = parseInt(bersihRibuan(document.getElementById("gaji").value)) || 0;
        let bps = parseInt(bersihRibuan(document.getElementById("bps").value)) || 0;
        let setor = parseInt(bersihRibuan(document.getElementById("setor").value)) || 0;
        let pengembalian = parseInt(bersihRibuan(document.getElementById("pengembalian").value)) || 0;

        window.kalkulasi.nilaiKebutuhan = gaji + bps + setor + pengembalian;

        document.getElementById("hasil_kebutuhan").innerText = formatRibuan(Math.round(window.kalkulasi.nilaiKebutuhan));
        hitungTPK();
    }

    // Hasil TPK = TP - Kebutuhan
    function hitungTPK() {
        window.kalkulasi.nilaiTPK = window.kalkulasi.nilaiTP - window.kalkulasi.nilaiKebutuhan;
        document.getElementById("hasil_tpk").innerText = formatRibuan(Math.round(window.kalkulasi.nilaiTPK));

        hitungKasAwalBulan();
    }

    // Hasil Kas Akhir
    function hitungKasAkhir() {
        let kasakhir = parseInt(bersihRibuan(document.getElementById("kasakhir").value)) || 0;

        document.getElementById("hasil_kasakhir").innerText = formatRibuan(kasakhir);

        hitungKasAwalBulan();
    }

    // Hasil Katrolan
    function hitungKatrolan() {
        let katrolan = parseInt(bersihRibuan(document.getElementById("katrolan").value)) || 0;

        document.getElementById("hasil_katrolan").innerText = formatRibuan(katrolan);

        hitungKasAwalBulan();
    }

    // Hasil Kas Awal Bulan
    function hitungKasAwalBulan() {
        let hasil_tpk = parseInt(bersihRibuan(document.getElementById("hasil_tpk").innerText)) || 0;
        let hasil_kasakhir = parseInt(bersihRibuan(document.getElementById("hasil_kasakhir").innerText)) || 0;
        let hasil_katrolan = parseInt(bersihRibuan(document.getElementById("hasil_katrolan").innerText)) || 0;

        let hasil_kasawalbulan = hasil_tpk + hasil_kasakhir + hasil_katrolan;

        document.getElementById("hasil_kasawalbulan").innerText = formatRibuan(hasil_kasawalbulan);
    }

    // Event listener
    document.querySelectorAll("#target_murni, #drop_baru, #hk, #penagihan").forEach(function(input) {
        input.addEventListener("input", hitungTarget);
    });
    document.getElementById("pelunasan").addEventListener("input", hitungPelunasan);
    document.querySelectorAll("#gaji, #bps, #setor, #pengembalian").forEach(function(input) {
        input.addEventListener("input", hitungKebutuhan);
    });
    document.getElementById("kasakhir").addEventListener("input", hitungKasAkhir);
    document.getElementById("katrolan").addEventListener("input", hitungKatrolan);

    function refreshSemua() {
        // pastikan semua input angka diformat
        formatInputAngka("#target_murni, #drop_baru, #hk, #penagihan, #pelunasan, #gaji, #bps, #setor, #pengembalian, #kasakhir, #katrolan");

        hitungTarget();
        hitungPelunasan();
        hitungKebutuhan();
        hitungKasAkhir();
        hitungKatrolan();
        hitungKasAwalBulan();
    }

    $(function() {
        refreshSemua(); // saat load awal
    });

    //reset 0 setelah bersihkan data
    function resetFormKalkulasi() {
        $("#formKalkulasi input").val("");
        refreshSemua(); // setelah reset, baru format + hitung ulang
    }

    // Format ribuan saat user mengetik
    function formatInputAngka(selector) {
        document.querySelectorAll(selector).forEach(function(input) {
            // Format sekali saat halaman load
            if (input.value) {
                input.value = formatRibuan(bersihRibuan(input.value));
            }

            // Auto format setiap kali ketik
            input.addEventListener("input", function(e) {
                let angka = bersihRibuan(e.target.value); // hapus titik
                e.target.value = formatRibuan(angka); // tulis ulang dengan titik
            });
        });
    }
</script>
<script>
    // Fungsi Simpan
    function bersihRibuan(str) {
        return str ? str.replace(/\./g, "") : "0";
    }

    $(document).ready(function() {
        $("#formKalkulasi").on("submit", function(e) {
            e.preventDefault(); // biar tidak reload

            $.ajax({
                url: "manager/simpan_kalkulasi_kas.php",
                type: "POST",
                dataType: "json",
                data: {
                    target_murni: bersihRibuan($("#target_murni").val()),
                    drop_baru: bersihRibuan($("#drop_baru").val()),
                    hk: bersihRibuan($("#hk").val()),
                    penagihan: bersihRibuan($("#penagihan").val()),
                    pelunasan: bersihRibuan($("#pelunasan").val()),
                    gaji: bersihRibuan($("#gaji").val()),
                    bps: bersihRibuan($("#bps").val()),
                    setor: bersihRibuan($("#setor").val()),
                    pengembalian: bersihRibuan($("#pengembalian").val()),
                    kasakhir: bersihRibuan($("#kasakhir").val()),
                    katrolan: bersihRibuan($("#katrolan").val())
                },
                success: function(res) {
                    if (res.status === "success") {
                        alert(res.message);
                        console.log(res.data); // debug
                        refreshSemua();
                    } else {
                        alert("Gagal menyimpan data!");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    alert("Terjadi kesalahan koneksi ke server.");
                }
            });
        });
    });
</script>
<script>
    $("#btnBersih").on("click", function() {
        if (!confirm("Yakin ingin menghapus semua data untuk cabang ini?")) {
            return;
        }

        $.ajax({
            url: "manager/hapus_kalkulasi_kas.php",
            type: "POST",
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    alert(res.message);
                    resetFormKalkulasi();
                } else {
                    alert("Gagal menghapus data!");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("Terjadi kesalahan koneksi ke server.");
            }
        });
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
    <!-- Bootstrap 5 JS (di akhir sebelum </body>) -->
</body>

</html>