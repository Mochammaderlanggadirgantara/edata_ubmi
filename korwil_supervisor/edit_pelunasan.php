<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Role hanya untuk Pengawas
$allowed_roles = ['Pengawas'];
if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pelunasan.php");
    exit();
}
$id = (int)$_GET['id'];

// Ambil data lama
$sql = "SELECT * FROM pelunasan9 WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    echo "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
    exit();
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelompok = $_POST['kelompok'] ?? '';
    $katrol   = $_POST['katrol'] ?? 0;

    $senin_ml  = $_POST['senin_ml'] ?? 0;
    $senin_mb  = $_POST['senin_mb'] ?? 0;
    $selasa_ml = $_POST['selasa_ml'] ?? 0;
    $selasa_mb = $_POST['selasa_mb'] ?? 0;
    $rabu_ml   = $_POST['rabu_ml'] ?? 0;
    $rabu_mb   = $_POST['rabu_mb'] ?? 0;
    $kamis_ml  = $_POST['kamis_ml'] ?? 0;
    $kamis_mb  = $_POST['kamis_mb'] ?? 0;
    $jumat_ml  = $_POST['jumat_ml'] ?? 0;
    $jumat_mb  = $_POST['jumat_mb'] ?? 0;
    $sabtu_ml  = $_POST['sabtu_ml'] ?? 0;
    $sabtu_mb  = $_POST['sabtu_mb'] ?? 0;

    $update_sql = "UPDATE pelunasan9 SET
        kelompok = ?, katrol = ?,
        senin_ml = ?, senin_mb = ?,
        selasa_ml = ?, selasa_mb = ?,
        rabu_ml = ?, rabu_mb = ?,
        kamis_ml = ?, kamis_mb = ?,
        jumat_ml = ?, jumat_mb = ?,
        sabtu_ml = ?, sabtu_mb = ?
        WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param(
        $stmt,
        "siiiiiiiiiiiiiii",
        $kelompok,
        $katrol,
        $senin_ml,
        $senin_mb,
        $selasa_ml,
        $selasa_mb,
        $rabu_ml,
        $rabu_mb,
        $kamis_ml,
        $kamis_mb,
        $jumat_ml,
        $jumat_mb,
        $sabtu_ml,
        $sabtu_mb,
        $id
    );
    if (mysqli_stmt_execute($stmt)) {
        header("Location: pelunasan.php?msg=updated");
        exit();
    } else {
        $error = "Gagal mengupdate data: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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

    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Kelompok</label>
                    <select name="kelompok" class="form-select" required>
                        <?php for ($i = 1; $i <= 9; $i++):
                            $selected = ($data['kelompok'] == "Kelompok $i") ? 'selected' : '';
                        ?>
                            <option value="Kelompok <?= $i ?>" <?= $selected ?>>Kelompok <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Katrol</label>
                    <input type="number" name="katrol" class="form-control" value="<?= $data['katrol'] ?>" required>
                </div>

                <h5>Data Harian</h5>
                <div class="row">
                    <?php
                    $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                    foreach ($days as $day):
                    ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-capitalize"><?= $day ?> (ML / MB)</label>
                            <div class="input-group">
                                <input type="number" name="<?= $day ?>_ml" class="form-control" placeholder="ML" value="<?= $data[$day . '_ml'] ?>">
                                <input type="number" name="<?= $day ?>_mb" class="form-control" placeholder="MB" value="<?= $data[$day . '_mb'] ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
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
</body>



</html>