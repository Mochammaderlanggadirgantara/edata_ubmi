<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/edata_ubmi/config/koneksi.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/edata_ubmi/page/verifikasi_sisa_saldo.php";

// Cek apakah user sudah login (session username)
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

// ✅ Ambil saldo terakhir
$result = $conn->query("SELECT saldo FROM tabungan ORDER BY id DESC LIMIT 1");
$row = $result->fetch_assoc();
$sisa_saldo = $row ? $row['saldo'] : 0;

// ✅ Ambil total debit (pemasukan)
$resultDebit = $conn->query("SELECT SUM(debit) AS total_debit FROM tabungan");
$total_debit = $resultDebit->fetch_assoc()['total_debit'] ?? 0;

// ✅ Ambil total kredit (pengeluaran)
$resultKredit = $conn->query("SELECT SUM(kredit) AS total_kredit FROM tabungan");
$total_kredit = $resultKredit->fetch_assoc()['total_kredit'] ?? 0;

// ✅ Tampilkan halaman
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
    <style>
        .card-container {
            max-width: 100%;
            width: 100%;
            max-width: 400px;
            background: linear-gradient(to top right,
                    #975af4,
                    #2f7cf8 40%,
                    #78aafa 65%,
                    #934cff 100%);
            padding: 4px;
            border-radius: 32px;
            display: flex;
            flex-direction: column;
            margin: auto;
            box-sizing: border-box;
        }

        .title-card {
            display: flex;
            align-items: center;
            padding: 16px 18px;
            justify-content: space-between;
            color: #fff;
        }

        .title-card p {
            font-size: 1.5rem;
            font-weight: 600;
            font-style: italic;
            text-shadow: 2px 2px 6px #2975ee;
            margin: 0;
        }

        .card-content {
            width: 100%;
            background-color: #161a20;
            border-radius: 30px;
            color: #838383;
            font-size: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-sizing: border-box;
        }

        .card-content .title {
            font-weight: 600;
            color: #bab9b9;
            font-size: 0.9rem;
        }

        .card-content .plain span {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            /* Responsive font size */
            color: #fff;
            word-break: break-word;
            /* Prevent overflow */
        }

        .card-content .card-btn {
            background: linear-gradient(4deg,
                    #975af4,
                    #2f7cf8 40%,
                    #78aafa 65%,
                    #934cff 100%);
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 8px;
            color: white;
            font-size: 0.85rem;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.6);
        }

        .card-content .card-btn:hover {
            color: #ffffff;
            text-shadow: 0 0 8px #fff;
            transform: scale(1.03);
        }

        .card-content .card-btn:active {
            transform: scale(1);
        }

        .center-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
        }

        /* Responsive padding on small screens */
        @media (max-width: 500px) {
            .card-container {
                border-radius: 20px;
            }

            .card-content {
                padding: 12px;
            }

            .title-card {
                padding: 12px;
            }

            .card-content .plain span {
                font-size: clamp(1.5rem, 5vw, 2.2rem);
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
    <!-- Navbar and Header Area -->
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
    <!-- End Navbar and Header Area -->


    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Sisa Saldo</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Sisa Saldo</span>
                    </li>

                </ol>
            </nav>
        </div>



        <div class="card bg-white border-0 rounded-3 mb-4">

            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body p-4">

                    <h3 class="mb-3 fs-16 fw-semibold">Total Pemasukan</h3>
                    <p class="mb-20">Rp. <?= number_format($total_debit, 0, ',', '.') ?></p>

                </div>
                <div class="card-body p-4">

                    <h3 class="mb-3 fs-16 fw-semibold">Total Pengeluaran</h3>
                    <p class="mb-20">Rp. <?= number_format($total_kredit, 0, ',', '.') ?></p>

                </div>
            </div>
            <div class="card-body center-wrapper p-4">
                <!-- From Uiverse.io by Cobp -->
                <div class="card-container">
                    <div class="title-card">
                        <p>SISA SALDO</p>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24">
                            <path
                                fill="currentColor"
                                d="M10.277 16.515c.005-.11.187-.154.24-.058c.254.45.686 1.111 1.177 1.412c.49.3 1.275.386 1.791.408c.11.005.154.186.058.24c-.45.254-1.111.686-1.412 1.176s-.386 1.276-.408 1.792c-.005.11-.187.153-.24.057c-.254-.45-.686-1.11-1.176-1.411s-1.276-.386-1.792-.408c-.11-.005-.153-.187-.057-.24c.45-.254 1.11-.686 1.411-1.177c.301-.49.386-1.276.408-1.791m8.215-1c-.008-.11-.2-.156-.257-.062c-.172.283-.421.623-.697.793s-.693.236-1.023.262c-.11.008-.155.2-.062.257c.283.172.624.42.793.697s.237.693.262 1.023c.009.11.2.155.258.061c.172-.282.42-.623.697-.792s.692-.237 1.022-.262c.11-.009.156-.2.062-.258c-.283-.172-.624-.42-.793-.697s-.236-.692-.262-1.022M14.704 4.002l-.242-.306c-.937-1.183-1.405-1.775-1.95-1.688c-.545.088-.806.796-1.327 2.213l-.134.366c-.149.403-.223.604-.364.752c-.143.148-.336.225-.724.38l-.353.141l-.248.1c-1.2.48-1.804.753-1.881 1.283c-.082.565.49 1.049 1.634 2.016l.296.25c.325.275.488.413.58.6c.094.187.107.403.134.835l.024.393c.093 1.52.14 2.28.634 2.542s1.108-.147 2.336-.966l.318-.212c.35-.233.524-.35.723-.381c.2-.032.402.024.806.136l.368.102c1.422.394 2.133.591 2.52.188c.388-.403.196-1.14-.19-2.613l-.099-.381c-.11-.419-.164-.628-.134-.835s.142-.389.365-.752l.203-.33c.786-1.276 1.179-1.914.924-2.426c-.254-.51-.987-.557-2.454-.648l-.379-.024c-.417-.026-.625-.039-.806-.135c-.18-.096-.314-.264-.58-.6m-5.869 9.324C6.698 14.37 4.919 16.024 4.248 18c-.752-4.707.292-7.747 1.965-9.637c.144.295.332.539.5.73c.35.396.852.82 1.362 1.251l.367.31l.17.145c.005.064.01.14.015.237l.03.485c.04.655.08 1.294.178 1.805"></path>
                        </svg>
                    </div>
                    <div class="card-content">
                        <p class="title">Tersisa</p>
                        <p class="plain">
                            <span>Rp. <?= number_format($sisa_saldo, 0, ',', '.') ?></span>

                        </p>

                    </div>
                </div>


            </div>
        </div>

    </div>

    <!-- script File -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(nik) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: 'Data ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Arahkan ke file PHP untuk hapus
                    window.location.href = 'delete_nasabah.php?nik=' + nik;
                }
            });
        }
    </script>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>