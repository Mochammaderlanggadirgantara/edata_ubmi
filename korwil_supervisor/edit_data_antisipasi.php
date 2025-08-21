<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'Pengawas'
$allowed_roles = ['pengawas', 'pimpinan', 'kepala mantri'];

if (!in_array(strtolower($_SESSION['jabatan']), $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<div class='alert alert-danger'>ID tidak ditemukan.</div>";
    exit();
}

// Ambil data berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM data_antisipasi_masuk WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
    exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelompok = $_POST['kelompok'];
    $wsenin = $_POST['wsenin'];
    $wselasa = $_POST['wselasa'];
    $wrabu = $_POST['wrabu'];
    $wkamis = $_POST['wkamis'];
    $wjumat = $_POST['wjumat'];
    $wsabtu = $_POST['wsabtu'];
    $msenin = $_POST['msenin'];
    $mselasa = $_POST['mselasa'];
    $mrabu = $_POST['mrabu'];
    $mkamis = $_POST['mkamis'];
    $mjumat = $_POST['mjumat'];
    $msabtu = $_POST['msabtu'];

    // Hitung jumlah total
    $jumlah_total = $wsenin + $wselasa + $wrabu + $wkamis + $wjumat + $wsabtu +
        $msenin + $mselasa + $mrabu + $mkamis + $mjumat + $msabtu;

    $update = $conn->prepare("
        UPDATE data_antisipasi_masuk SET 
        kelompok=?, wsenin=?, wselasa=?, wrabu=?, wkamis=?, wjumat=?, wsabtu=?, 
        msenin=?, mselasa=?, mrabu=?, mkamis=?, mjumat=?, msabtu=?, jumlah_total=?
        WHERE id=?
    ");
    $update->bind_param(
        "siiiiiiiiiiiiii",
        $kelompok,
        $wsenin,
        $wselasa,
        $wrabu,
        $wkamis,
        $wjumat,
        $wsabtu,
        $msenin,
        $mselasa,
        $mrabu,
        $mkamis,
        $mjumat,
        $msabtu,
        $jumlah_total,
        $id
    );

    if ($update->execute()) {
        header("Location: data_antisipasi.php?status=success");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data.</div>";
    }
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
    <!-- End Preloader Area -->
    <?php


    if (isset($_SESSION['jabatan'])) {
        switch ($_SESSION['jabatan']) {

            case 'Pengawas':
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

    <?php include '../navbar/navbar_korwil.php'; ?>
    <div class="card bg-white border-0 rounded-3 mb-4">



        <div class="container mt-4">
            <h3>Edit Data Antisipasi Masuk</h3>
            <form method="POST">
                <div class="mb-3">
                    <label>Kelompok</label>
                    <input type="text" name="kelompok" class="form-control" value="<?= htmlspecialchars($data['kelompok']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Waktu Saldo</h5>
                        <?php foreach (['wsenin' => 'Senin', 'wselasa' => 'Selasa', 'wrabu' => 'Rabu', 'wkamis' => 'Kamis', 'wjumat' => 'Jumat', 'wsabtu' => 'Sabtu'] as $field => $label): ?>
                            <div class="mb-3">
                                <label><?= $label ?></label>
                                <input type="number" name="<?= $field ?>" class="form-control" value="<?= $data[$field] ?>" required>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="col-md-6">
                        <h5>Minggu</h5>
                        <?php foreach (['msenin' => 'Senin', 'mselasa' => 'Selasa', 'mrabu' => 'Rabu', 'mkamis' => 'Kamis', 'mjumat' => 'Jumat', 'msabtu' => 'Sabtu'] as $field => $label): ?>
                            <div class="mb-3">
                                <label><?= $label ?></label>
                                <input type="number" name="<?= $field ?>" class="form-control" value="<?= $data[$field] ?>" required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="data_antisipasi_masuk.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>



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
    <script>
        function toInt(v) {
            v = parseInt(v);
            return isNaN(v) ? 0 : v;
        }

        function recalc() {
            const wfields = ['wsenin', 'wselasa', 'wrabu', 'wkamis', 'wjumat', 'wsabtu'];
            const mfields = ['msenin', 'mselasa', 'mrabu', 'mkamis', 'mjumat', 'msabtu'];
            let tw = 0,
                tm = 0;
            wfields.forEach(f => tw += toInt(document.getElementById(f).value));
            mfields.forEach(f => tm += toInt(document.getElementById(f).value));
            document.getElementById('total_waktu_saldo').value = tw;
            document.getElementById('total_waktu_saldo_hidden').value = tw;
            document.getElementById('total_minggu').value = tm;
            document.getElementById('total_minggu_hidden').value = tm;
            document.getElementById('jumlah_total').value = (tw + tm);
            document.getElementById('jumlah_total_hidden').value = (tw + tm);
        }
        // hitung saat load (untuk edit nanti)
        recalc();
    </script>
</body>



</html>