<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil id_detail dari URL
$id_detail = isset($_GET['id_detail']) ? intval($_GET['id_detail']) : 0;

$sql = "
    SELECT d.*, 
           m.gaji_bulan,
           m.pendapatan_bulan,
           u.nama_user,
           u.jabatan,
           u.tgl_masuk,
           c.nama_cabang
    FROM turpas_detail d
    JOIN turpas_master m ON d.id_master = m.id_master
    JOIN tuser u ON d.id_user = u.id_user
    JOIN cabang c ON d.id_cabang = c.id_cabang
    WHERE d.id_detail = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_detail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<div class='alert alert-danger'>❌ Data slip gaji tidak ditemukan.</div>";
    exit;
}

// ✅ Hitung setelah $row ada
$gaji_pokok = $row['gaji'];
$prestasi   = $row['cm'] + $row['mb'] + $row['ml'];
$bonus      = $row['goro'] + $row['um'];
$jumlah_pendapatan = $gaji_pokok + $prestasi + $bonus;

// Hitung potongan
$jumlah_potongan = $row['bon_prive'] + $row['beban'] + $row['wajib'] +
                   $row['sukarela'] + $row['absensi'] + $row['lain_lain'];


                 
function formatTanggalIndoLengkap($tanggal) {
    $bulanIndo = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    if (!empty($tanggal)) {
        $hari  = date('d', strtotime($tanggal)); // ambil tanggal (dd)
        $bulan = date('m', strtotime($tanggal)); // ambil bulan (mm)
        $tahun = date('Y', strtotime($tanggal)); // ambil tahun (yyyy)
        return $hari . ' ' . $bulanIndo[$bulan] . ' ' . $tahun;
    }
    return "-";
}
// Pendapatan bersih
$pendapatan_bersih = $jumlah_pendapatan - $jumlah_potongan;
$bulanIndo = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

$tanggal = $row['pendapatan_bulan']; // contoh: 2025-08-01

if (!empty($tanggal)) {
    $bulan = date('m', strtotime($tanggal)); // 08
    $tahun = date('Y', strtotime($tanggal)); // 2025
    echo $bulanIndo[$bulan] . ' ' . $tahun; // Agustus 2025
} else {
    echo "-";
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
         @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .page {
                width: 100%;
                height: 50%; /* setengah A4 */
                box-sizing: border-box;
                border-bottom: 1px dashed #000; /* garis pemisah */
                padding: 10px;
            }
        }
        .page {
            width: 100%;
            height: 50%;
            box-sizing: border-box;
            border-bottom: 1px dashed #000;
            padding: 10px;
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

    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_staff.php'; ?>
    <!-- End Navbar and Header Area -->


    <div class="container-fluid">
        <div class="main-content-container overflow-hidden">
            <!DOCTYPE html>
            <html lang="id">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Slip Gaji</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    .slip-container {
                        max-width: 600px;
                        margin: 30px auto;
                        border: 1px dashed #000;
                        padding: 20px;
                        background-color: #fff;
                    }

                    .kop img {
                        width: 60px;
                        height: 60px;
                    }

                    .kop-title {
                        font-weight: bold;
                        text-transform: uppercase;
                        font-size: 14px;
                    }

                    .kop-sub {
                        font-size: 12px;
                        margin-top: -5px;
                    }

                    table tr td {
                        font-size: 14px;
                    }

                    .section-title {
                        font-weight: bold;
                        text-decoration: underline;
                        margin-top: 10px;
                    }

                    .ttd td {
                        height: 80px;
                        vertical-align: bottom;
                    }
                </style>
            </head>

            <body>

                <div class="page slip-container border rounded shadow-sm p-4" style="max-width:700px; margin:auto; border:1px solid #000;">
                    <!-- Kop -->
                    <div class="text-center">
                        <div class="d-flex justify-content-start align-items-center mb-2">
                            <img src="../assets/images/logo ubmi.png" alt="Logo" style="height:85px; margin-right:10px;">
                            <div style="flex:1; text-align:center;">
                                <div style="font-weight:bold; font-size:16px;">KOPERASI SIMPAN PINJAM (KSP)</div>
                                <div style="font-weight:bold; font-size:17px;">"KSP USAHA BERSAMA MANDIRI INDONESIA"</div>
                                <div style="font-size:13px;">BADAN HUKUM : No. AHU-0005202.AH.01.26.TAHUN 2020</div>
                            </div>
                        </div>
                        <div style="font-weight:bold; margin-top:5px; font-size:15px;">
                            TANDA TERIMA PENDAPATAN BULAN : <span style="text-transform:capitalize;"><?= $bulanIndo[date('m', strtotime($row['pendapatan_bulan']))] . ' ' . date('Y', strtotime($row['pendapatan_bulan'])) ?>
</span>
                </div>
                    </div>

                    <!-- Informasi Karyawan -->
                    <table class="table table-borderless mt-3 mb-2" style="font-size:14px;">
                        <tr>
                            <td style="width:130px;">Nama</td>
                           <td>: <?= htmlspecialchars($row['nama_user']); ?><br></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>: <?= htmlspecialchars($row['jabatan']) ?></td>
                        </tr>
                        <tr>
                            <td>Tgl. Masuk</td>
                            <td>: <?= htmlspecialchars($row['tgl_masuk']) ?></td>
                        </tr>
                    </table>

                    <!-- Pendapatan -->
                    <div style="font-weight:bold; font-size:14px; margin-top:10px;">PENDAPATAN</div>
                    <table class="table table-borderless table-sm" style="font-size:14px;">
                        <tr>
                            <td style="width:130px;">Gaji Pokok</td>
                           <td>: Rp. <?= number_format($gaji_pokok,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>Prestasi</td>
                           <td>: Rp. <?= number_format($prestasi,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>Bonus</td>
                            <td>: Rp. <?= number_format($bonus,0,',','.') ?></td>
                        </tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="
        padding-left:150px;
        background: linear-gradient(to right, transparent 150px, #000 150px);
        background-size: 100% 2px;
        background-repeat: no-repeat;
        background-position: 0 0;
    ">
                               Jumlah Pendapatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($jumlah_pendapatan,0,',','.') ?>
            </td>
                        </tr>
                    </table>

                    <!-- Potongan -->
                    <div style="font-weight:bold; font-size:14px; margin-top:5px;">POTONGAN</div>
                    <table class="table table-borderless table-sm" style="font-size:14px;">
                        <tr><td style="width:130px;">Bon Prive</td><td>: Rp. <?= number_format($row['bon_prive'],0,',','.') ?></td></tr>
        <tr><td>Beban</td><td>: Rp. <?= number_format($row['beban'],0,',','.') ?></td></tr>
        <tr><td>Simp. Wajib</td><td>: Rp. <?= number_format($row['wajib'],0,',','.') ?></td></tr>
        <tr><td>Simp. Sukarela</td><td>: Rp. <?= number_format($row['sukarela'],0,',','.') ?></td></tr>
        <tr><td>Absensi</td><td>: Rp. <?= number_format($row['absensi'],0,',','.') ?></td></tr>
        <tr><td>Lain-lain</td><td>: Rp. <?= number_format($row['lain_lain'],0,',','.') ?></td></tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="
        padding-left:150px;
        background: linear-gradient(to right, transparent 150px, #000 150px);
        background-size: 100% 2px;
        background-repeat: no-repeat;
        background-position: 0 0;
    ">Jumlah Potongan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($jumlah_potongan,0,',','.') ?></td>
        </tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="padding-left:150px;">Pendapatan Bersih &nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($pendapatan_bersih,0,',','.') ?></td>
                        </tr>
                    </table>

                    <!-- Tanggal -->
                    <div class=" text-end mt-4 mb-2" style="font-size:14px;">Gresik,<?= formatTanggalIndoLengkap($row['gaji_bulan']) ?>

                    </div>

                    <!-- Mengetahui -->
                    <div class="text-center" style="font-size:14px;">Mengetahui</div>
                    <table class="table table-borderless text-center mt-2" style="font-size:14px; margin-bottom:40px;">
                        <tr>
                            <td>Pimpinan</td>
                            <td>Kasir</td>
                            <td>Penerima</td>
                        </tr>
                        <tr style="height:60px;"></tr>
                        <tr style="font-weight:bold;">
                            <td>Rizaldi Aditya H</td>
                            <td>Amelia Rahmadhani</td>
                            <td>RIZALDI ADITYA H</td>
                        </tr>
                    </table>
                </div>

                <div class="page slip-container border rounded shadow-sm p-4" style="max-width:700px; margin:auto; border:1px solid #000;">
                    <!-- Kop -->
                    <div class="text-center">
                        <div class="d-flex justify-content-start align-items-center mb-2">
                            <img src="../assets/images/logo ubmi.png" alt="Logo" style="height:85px; margin-right:10px;">
                            <div style="flex:1; text-align:center;">
                                <div style="font-weight:bold; font-size:16px;">KOPERASI SIMPAN PINJAM (KSP)</div>
                                <div style="font-weight:bold; font-size:17px;">"KSP USAHA BERSAMA MANDIRI INDONESIA"</div>
                                <div style="font-size:13px;">BADAN HUKUM : No. AHU-0005202.AH.01.26.TAHUN 2020</div>
                            </div>
                        </div>
                        <div style="font-weight:bold; margin-top:5px; font-size:15px;">
                            TANDA TERIMA PENDAPATAN BULAN : <span style="text-transform:capitalize;"><?= $bulanIndo[date('m', strtotime($row['pendapatan_bulan']))] . ' ' . date('Y', strtotime($row['pendapatan_bulan'])) ?>
</span>
                </div>
                    </div>

                    <!-- Informasi Karyawan -->
                    <table class="table table-borderless mt-3 mb-2" style="font-size:14px;">
                        <tr>
                            <td style="width:130px;">Nama</td>
                           <td>: <?= htmlspecialchars($row['nama_user']); ?><br></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td>: <?= htmlspecialchars($row['jabatan']) ?></td>
                        </tr>
                        <tr>
                            <td>Tgl. Masuk</td>
                            <td>: <?= htmlspecialchars($row['tgl_masuk']) ?></td>
                        </tr>
                    </table>

                    <!-- Pendapatan -->
                    <div style="font-weight:bold; font-size:14px; margin-top:10px;">PENDAPATAN</div>
                    <table class="table table-borderless table-sm" style="font-size:14px;">
                        <tr>
                            <td style="width:130px;">Gaji Pokok</td>
                           <td>: Rp. <?= number_format($gaji_pokok,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>Prestasi</td>
                           <td>: Rp. <?= number_format($prestasi,0,',','.') ?></td>
                        </tr>
                        <tr>
                            <td>Bonus</td>
                            <td>: Rp. <?= number_format($bonus,0,',','.') ?></td>
                        </tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="
        padding-left:150px;
        background: linear-gradient(to right, transparent 150px, #000 150px);
        background-size: 100% 2px;
        background-repeat: no-repeat;
        background-position: 0 0;
    ">
                               Jumlah Pendapatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($jumlah_pendapatan,0,',','.') ?>
            </td>
                        </tr>
                    </table>

                    <!-- Potongan -->
                    <div style="font-weight:bold; font-size:14px; margin-top:5px;">POTONGAN</div>
                    <table class="table table-borderless table-sm" style="font-size:14px;">
                        <tr><td style="width:130px;">Bon Prive</td><td>: Rp. <?= number_format($row['bon_prive'],0,',','.') ?></td></tr>
        <tr><td>Beban</td><td>: Rp. <?= number_format($row['beban'],0,',','.') ?></td></tr>
        <tr><td>Simp. Wajib</td><td>: Rp. <?= number_format($row['wajib'],0,',','.') ?></td></tr>
        <tr><td>Simp. Sukarela</td><td>: Rp. <?= number_format($row['sukarela'],0,',','.') ?></td></tr>
        <tr><td>Absensi</td><td>: Rp. <?= number_format($row['absensi'],0,',','.') ?></td></tr>
        <tr><td>Lain-lain</td><td>: Rp. <?= number_format($row['lain_lain'],0,',','.') ?></td></tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="
        padding-left:150px;
        background: linear-gradient(to right, transparent 150px, #000 150px);
        background-size: 100% 2px;
        background-repeat: no-repeat;
        background-position: 0 0;
    ">Jumlah Potongan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($jumlah_potongan,0,',','.') ?></td>
        </tr>
                        <tr style="font-weight:bold; white-space:nowrap;">
                            <td colspan="2" style="padding-left:150px;">Pendapatan Bersih &nbsp;&nbsp;&nbsp;&nbsp;: Rp. <?= number_format($pendapatan_bersih,0,',','.') ?></td>
                        </tr>
                    </table>

                    <!-- Tanggal -->
                    <div class=" text-end mt-4 mb-2" style="font-size:14px;">Gresik,<?= formatTanggalIndoLengkap($row['gaji_bulan']) ?>

                    </div>

                    <!-- Mengetahui -->
                    <div class="text-center" style="font-size:14px;">Mengetahui</div>
                    <table class="table table-borderless text-center mt-2" style="font-size:14px; margin-bottom:40px;">
                        <tr>
                            <td>Pimpinan</td>
                            <td>Kasir</td>
                            <td>Penerima</td>
                        </tr>
                        <tr style="height:60px;"></tr>
                        <tr style="font-weight:bold;">
                            <td>Rizaldi Aditya H</td>
                            <td>Amelia Rahmadhani</td>
                            <td>RIZALDI ADITYA H</td>
                        </tr>
                    </table>
                </div>
            </body>

            </html>

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




</body>

</html>