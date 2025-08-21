<?php
include '../config/koneksi.php';

$errors = [];
$success = false;

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama         = $_POST['nama_user'];
    $jabatan      = $_POST['jabatan'];
    $tgl          = $_POST['tgl_masuk'];
    $username     = $_POST['username'];
    $password     = $_POST['password'];
    $id_cabang    = $_POST['id_cabang'];
     $status    = $_POST['status'];
    $id_kelompok  = $_POST['id_kelompok'] ?? null;

    // Validasi nama
    if (empty($nama)) {
        $errors[] = "Nama tidak boleh kosong.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $errors[] = "Nama hanya boleh berisi huruf dan spasi.";
    }

    // Validasi username
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    } elseif (strlen($username) < 5) {
        $errors[] = "Username minimal 5 karakter.";
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM TUser WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errors[] = "Username sudah digunakan.";
        }
    }

    // Validasi password
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    // Validasi tanggal masuk
    if (empty($tgl) || !strtotime($tgl)) {
        $errors[] = "Tanggal masuk tidak valid.";
    }

    // Validasi jabatan
    $valid_jabatan = ['pengawas', 'pimpinan', 'kasir', 'staff','kepala_mantri', 'mantri'];
    if (!in_array($jabatan, $valid_jabatan)) {
        $errors[] = "Jabatan tidak valid.";
    }

    // Validasi cabang
    if (empty($id_cabang) || !is_numeric($id_cabang)) {
        $errors[] = "Cabang harus dipilih.";
    }

    // Validasi kelompok jika jabatan adalah mantri
    if ($jabatan === 'mantri' && empty($id_kelompok)) {
        $errors[] = "Kelompok harus dipilih jika jabatan adalah Mantri.";
    }

    // Simpan ke database jika tidak ada error
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO TUser (nama_user, jabatan, tgl_masuk, username, password, id_cabang, status, id_kelompok) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $nama, $jabatan, $tgl, $username, $hashed, $id_cabang, $status, $id_kelompok);

        if ($stmt->execute()) {
            $success = true;
            header("Location: ../korwil_supervisor/data_karyawan.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data: " . $stmt->error;
        }
    }
}

// Ambil data untuk select
$queryKelompok = mysqli_query($conn, "SELECT id, nama_kelompok FROM kelompok_mantri");
$queryCabang   = mysqli_query($conn, "SELECT id_cabang, nama_cabang FROM cabang");
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
            <h3 class="mb-0">Data Inventaris</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Inventaris</span>
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
                    <h2>Tambah User</h2>

                    <form action="save.php" method="post">
                        <input type="hidden" name="id_user" value="">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama_user" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="id_cabang">Nama Cabang</label>
                            <select class="form-control" name="id_cabang" id="id_cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                <?php while ($row = mysqli_fetch_assoc($queryCabang)) : ?>
                                    <option value="<?= $row['id_cabang'] ?>"><?= $row['nama_cabang'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jabatan</label>
                            <select name="jabatan" class="form-control" required>
                                <option value="">Pilih Jabatan</option>
                                <?php foreach (['pengawas', 'pimpinan', 'kepala_mantri', 'kasir', 'staff', 'mantri'] as $j): ?>
                                    <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3" id="kelompokContainer" style="display: none;">
                            <label for="id_kelompok">Kelompok Mantri</label>
                            <select name="id_kelompok" class="form-control">
                                <option value="">-- Pilih Kelompok --</option>
                                <?php while ($row = mysqli_fetch_assoc($queryKelompok)) : ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['nama_kelompok'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <?php foreach (['aktif', 'tidak aktif'] as $j): ?>
                                    <option value="<?= $j ?>"><?= ucfirst($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>           
                        <div class="mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="../korwil_supervisor/data_karyawan.php" class="btn btn-secondary">Batal</a>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const jabatanSelect = document.querySelector("select[name='jabatan']");
                                const kelompokContainer = document.getElementById("kelompokContainer");

                                function toggleKelompok() {
                                    if (jabatanSelect.value.toLowerCase() === "mantri") {
                                        kelompokContainer.style.display = "block";
                                    } else {
                                        kelompokContainer.style.display = "none";
                                    }
                                }

                                jabatanSelect.addEventListener("change", toggleKelompok);
                                toggleKelompok(); // run on load
                            });
                        </script>

                    </form>

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
    <!-- Bootstrap 5 JS (di akhir sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>