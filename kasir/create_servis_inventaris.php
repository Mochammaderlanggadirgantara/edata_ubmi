<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
$allowed_roles = ['kasir', 'korwil'];

if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

$success = false; // <- definisikan supaya tidak undefined
$error_message = ''; // <- ini juga untuk keperluan elseif di bagian bawah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_servis = $_POST['tanggal_servis'];
    $nama_part_jasa = $_POST['nama_part_jasa'];
    $sub_total = str_replace(',', '', $_POST['sub_total']); // hilangkan format ribuan
    $tanggal_servis_selanjutnya = $_POST['tanggal_servis_selanjutnya'];
    $jabatan = $_POST['jabatan'];

    $stmt = $conn->prepare("INSERT INTO riwayat_servis (tanggal_servis, nama_part_jasa, sub_total, jabatan, tanggal_servis_selanjutnya, id_cabang) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $tanggal_servis, $nama_part_jasa, $sub_total, $jabatan, $tanggal_servis_selanjutnya, $id_cabang);
    if ($stmt->execute()) {
        echo "Data berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Links Of CSS File -->
    <!-- Select2 CSS -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            case 'korwil':
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

    <!-- Start Main Content Area -->


    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form id="myForm" method="POST" action="create_servis_inventaris.php">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="jabatan" class="form-label">Pilih Jabatan</label>
                        <select class="form-select" id="jabatan" name="jabatan" required>
                            <option value="" disabled selected>-- Pilih Jabatan --</option>
                            <option value="Pimpinan">Pimpinan</option>
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <option value="KM<?= $i ?>">KM<?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_servis" class="form-label">Tanggal Servis</label>
                        <input type="date" class="form-control" id="tanggal_servis" name="tanggal_servis" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_part_jasa" class="form-label">Nama Part / Jasa Servis</label>
                        <input type="text" class="form-control" id="nama_part_jasa" name="nama_part_jasa" placeholder="Contoh: Ganti oli, rem, dll" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sub_total" class="form-label">Sub Total (Rp)</label>
                        <input type="number" class="form-control" id="sub_total" name="sub_total" value="0" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_servis_selanjutnya" class="form-label">Tanggal Servis Selanjutnya</label>
                        <input type="date" class="form-control" id="tanggal_servis_selanjutnya" name="tanggal_servis_selanjutnya">
                    </div>
                </div>


                <button type="submit" class="btn btn-primary" id="simpanBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='servis_inventaris.php'">Kembali</button>
            </form>

        </div>
    </div>



    <div class="flex-grow-1"></div>



    <!-- Start Main Content Area -->
    <?php if ($success): ?>
        <!-- Modal Berhasil -->
        <div class="modal show fade" id="successModal" tabindex="-1" style="display:block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Berhasil</h5>
                    </div>
                    <div class="modal-body">
                        ✅ Data berhasil disimpan.
                    </div>
                    <div class="modal-footer">
                        <a href="tunai_babat.php" class="btn btn-success">OK</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Redirect otomatis setelah 3 detik
            setTimeout(function() {
                window.location.href = "tunai_babat.php";
            }, 3000);
        </script>

    <?php elseif (!empty($error_message)): ?>
        <!-- Tampilkan pesan error -->
        <div class="container mt-4">
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
        </div>
    <?php endif; ?>

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 untuk KLP -->
    <script>
        $(document).ready(function() {
            $('#jabatan').select2({
                placeholder: "-- Pilih Jabatan --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>







</body>



</html>