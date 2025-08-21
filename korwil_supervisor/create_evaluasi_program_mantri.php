<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal   = $_POST['tanggal']; // ambil tanggal dari form
    $klp       = $_POST['klp'];
   $kekuatan  = floatval($_POST['kekuatan']); // menggunakan float agar bisa nilai desimal

   $program = intval(str_replace('.', '', $_POST['program'])); // hapus titik ribuan

    $index_val = floatval($_POST['index_val']);
   $drop_val  = floatval(str_replace('.', '', $_POST['drop_val']));
$storting  = floatval(str_replace('.', '', $_POST['storting']));
$rencana   = floatval(str_replace('.', '', $_POST['rencana']));
$baru      = floatval(str_replace('.', '', $_POST['baru']));
$gagalkan  = floatval(str_replace('.', '', $_POST['gagalkan']));

    // Total per kolom
    $total_kekuatan = $kekuatan;
    $total_program  = $program;
    $total_drop     = $drop_val;
    $total_storting = $storting;
    $total_rencana  = $rencana;
    $total_baru     = $baru;
    $total_gagalkan = $gagalkan;

    // Hitung index %
    $total_index = ($storting != 0 && $program != 0) ? ($storting / $program) * 100 : 0;

    $stmt = $conn->prepare("INSERT INTO evaluasi_program_mantri 
        (tanggal, klp, kekuatan, program, `index`, drop_val, storting, rencana, baru, gagalkan, 
         total_kekuatan, total_program, total_index, total_drop, total_storting, total_rencana, total_baru, total_gagalkan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssiiidiiiiiiiiiiii",
        $tanggal, $klp, $kekuatan, $program, $index_val, $drop_val, $storting, $rencana, $baru, $gagalkan,
        $total_kekuatan, $total_program, $total_index, $total_drop, $total_storting, $total_rencana, $total_baru, $total_gagalkan);

    if($stmt->execute()){
        $stmt->close();
        $conn->close();
        header("Location: evaluasi_program_mantri.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: ".$stmt->error."</div>";
    }
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
    <!-- <div class="preloader" id="preloader">
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
    </div> -->
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

    <!-- Start Main Content Area -->


    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form action="" method="post" class="container mt-4">
    <div class="row g-3">

        <!-- Tanggal -->
        <div class="col-md-6">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
        </div>

        <!-- Kelompok -->
        <div class="col-md-6">
            <label for="klp" class="form-label">Kelompok</label>
            <select name="klp" id="klp" class="form-select" required>
                <?php for($i=1;$i<=10;$i++): ?>
                    <option value="Kelompok <?=$i?>">Kelompok <?=$i?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Kekuatan -->
<div class="col-md-6">
    <label for="kekuatan" class="form-label">Kekuatan</label>
    <input type="number" name="kekuatan" id="kekuatan" class="form-control" value="0" step="0.01">
</div>


        <div class="col-md-6">
    <label for="program" class="form-label">Program</label>
    <input type="text" name="program" id="program" class="form-control" value="0">
</div>

        <!-- Index % -->
        <div class="col-md-6">
            <label for="index_val" class="form-label">Index %</label>
            <input type="number" name="index_val" id="index_val" class="form-control" value="0" step="0.01">
        </div>

       <!-- Drop -->
<div class="col-md-6">
    <label for="drop_val" class="form-label">Drop</label>
    <input type="text" name="drop_val" id="drop_val" class="form-control" value="0">
</div>

<!-- Storting -->
<div class="col-md-6">
    <label for="storting" class="form-label">Storting</label>
    <input type="text" name="storting" id="storting" class="form-control" value="0">
</div>

<!-- Rencana -->
<div class="col-md-6">
    <label for="rencana" class="form-label">Rencana</label>
    <input type="text" name="rencana" id="rencana" class="form-control" value="0">
</div>

<!-- Baru -->
<div class="col-md-6">
    <label for="baru" class="form-label">Baru</label>
    <input type="text" name="baru" id="baru" class="form-control" value="0">
</div>

<!-- Gagalkan -->
<div class="col-md-6">
    <label for="gagalkan" class="form-label">Gagalkan</label>
    <input type="text" name="gagalkan" id="gagalkan" class="form-control" value="0">
</div>

        <!-- Tombol Submit -->
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-success w-100">Simpan Data</button>
        </div>

    </div>
</form>

<!-- Jangan lupa tambahkan Bootstrap CSS di head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


        </div>
    </div>



    <div class="flex-grow-1"></div>




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
<!-- JS Realtime untuk Index % -->
<script>
const ribuanFields = ['program','drop_val','storting','rencana','baru','gagalkan'];

ribuanFields.forEach(id => {
    const input = document.getElementById(id);
    input.addEventListener('input', function() {
        // Hapus semua karakter selain angka
        let value = this.value.replace(/\D/g, '');
        // Format ribuan
        this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
});
</script>







</body>



</html>