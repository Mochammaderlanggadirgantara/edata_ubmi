<?php
session_start();
include '../config/koneksi.php';
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

$id_cabang   = $data_user['id_cabang'];   // ini aman dipakai
$nama_user   = $data_user['nama_user'];
$jabatan     = strtolower($data_user['jabatan']);
$nama_cabang = $data_user['nama_cabang'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi ID
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        die("ID tidak valid.");
    }

    // Ambil data dari POST
    $id = (int)$_POST['id'];
    $tanggal_servis = $_POST['tanggal_servis'] ?? null;
    $nama_part_jasa = $_POST['nama_part_jasa'] ?? null;
    $sub_total = isset($_POST['sub_total']) ? str_replace(',', '', $_POST['sub_total']) : 0;
    $tanggal_selanjutnya = $_POST['tanggal_servis_selanjutnya'] ?? null;
    $jabatan = $_POST['jabatan'] ?? null;

    // Validasi minimal
    if (!$tanggal_servis || !$nama_part_jasa || !$sub_total) {
        die("Semua field wajib diisi.");
    }

    // Siapkan query update
    $stmt = $conn->prepare("UPDATE riwayat_servis 
                        SET tanggal_servis = ?, 
                            nama_part_jasa = ?, 
                            sub_total = ?, 
                            jabatan = ?, 
                            tanggal_servis_selanjutnya = ? 
                        WHERE id = ? AND id_cabang=?");
   $stmt->bind_param(
    "ssdsiii", 
    $tanggal_servis, 
    $nama_part_jasa, 
    $sub_total, 
    $jabatan, 
    $tanggal_selanjutnya, 
    $id,
    $id_cabang
);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data servis berhasil diperbarui!');
                window.location.href = 'servis_inventaris.php';
              </script>";
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Jika GET, ambil data untuk ditampilkan di form
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

  
$stmt = $conn->prepare("SELECT * FROM riwayat_servis WHERE id = ? AND id_cabang = ?");
$stmt->bind_param("ii", $id, $id_cabang);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

    if (!$data) {
        die("Data tidak ditemukan.");
    }

    $stmt->close();
} else {
    die("ID tidak ditemukan.");
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
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_kasir.php'; ?>
    <!-- End Navbar and Header Area -->
    <!-- Start Main Content Area -->


    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <!-- FORM EDIT DATA SERVIS -->
            <form method="POST" action="edit_servis_inventaris.php">
                <!-- Kirim ID tersembunyi -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="jabatan" class="form-label">KATEGORI</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan"
                            value="<?= htmlspecialchars($data['jabatan']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_servis" class="form-label">Tanggal Servis</label>
                        <input type="date" class="form-control" id="tanggal_servis" name="tanggal_servis"
                            value="<?= htmlspecialchars($data['tanggal_servis']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_part_jasa" class="form-label">Nama Part / Jasa Servis</label>
                        <input type="text" class="form-control" id="nama_part_jasa" name="nama_part_jasa"
                            value="<?= htmlspecialchars($data['nama_part_jasa']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sub_total" class="form-label">Sub Total (Rp)</label>
                        <input type="number" class="form-control" id="sub_total" name="sub_total"
                            value="<?= htmlspecialchars($data['sub_total']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_servis_selanjutnya" class="form-label">Tanggal Servis Selanjutnya</label>
                        <input type="date" class="form-control" id="tanggal_servis_selanjutnya" name="tanggal_servis_selanjutnya"
                            value="<?= htmlspecialchars($data['tanggal_servis_selanjutnya']) ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="servis_inventaris.php" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>


        </div>
    </div>



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