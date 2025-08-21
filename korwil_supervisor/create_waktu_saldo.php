<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/edata_ubmi/config/koneksi.php";

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bulan    = $_POST['bulan'];
    $tahun    = intval($_POST['tahun']);
    $klp      = intval($_POST['klp']);
    $ws_senin = intval($_POST['ws_senin']);
    $ws_selasa= intval($_POST['ws_selasa']);
    $ws_rabu  = intval($_POST['ws_rabu']);
    $ws_kamis = intval($_POST['ws_kamis']);
    $ws_jumat = intval($_POST['ws_jumat']);
    $ws_sabtu = intval($_POST['ws_sabtu']);

    $sql = "INSERT INTO antisipasi_ws (bulan, tahun, klp, ws_senin, ws_selasa, ws_rabu, ws_kamis, ws_jumat, ws_sabtu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiiiiii", $bulan, $tahun, $klp, $ws_senin, $ws_selasa, $ws_rabu, $ws_kamis, $ws_jumat, $ws_sabtu);

    if ($stmt->execute()) {
        echo "<p>Data berhasil disimpan.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
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

    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
           
            <form method="POST">
        <div class="row mb-3">
            <div class="col-md-4">
    <label>Bulan</label>
    <input type="text" name="bulan" id="input-bulan" class="form-control" required>
</div>
            <div class="col-md-4">
                <label>Tahun</label>
                <input type="number" name="tahun" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>KLP</label>
                <select name="klp" class="form-select" required>
                    <option value="">Pilih</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
                <label>Senin</label>
                <input type="number" name="ws_senin" class="form-control">
            </div>
        <div class="col-md-4">
                <label>Selasa</label>
                <input type="number" name="ws_selasa" class="form-control" >
            </div>
        <div class="col-md-4">
                <label>Rabu</label>
                <input type="number" name="ws_rabu" class="form-control" >
            </div>
        <div class="col-md-4">
                <label>Kamis</label>
                <input type="number" name="ws_kamis" class="form-control" >
            </div>
        <div class="col-md-4">
                <label>Jumat</label>
                <input type="number" name="ws_jumat" class="form-control" >
            </div>
        <div class="col-md-4">
                <label>Sabtu</label>
                <input type="number" name="ws_sabtu" class="form-control">
            </div>
        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="data_antisipasi.php" class="btn btn-secondary">Lihat Data</a>
        </div>
    </form>
        </div>
    </div>



    <div class="flex-grow-1"></div>


    </div>
    </div>
    <!-- Start Main Content Area -->
    <script>
$(function() {
    var bulan = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    $("#input-bulan").autocomplete({
        source: bulan
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
</body>



</html>