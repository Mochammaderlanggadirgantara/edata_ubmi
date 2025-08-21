<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulan    = $_POST['bulan'];
    $tahun    = $_POST['tahun'];
    $klp      = $_POST['klp'];
    $program  = str_replace('.', '', $_POST['program']);
    $storting = str_replace('.', '', $_POST['storting_valid']);

    $idx = ($storting > 0) ? ($program / $storting * 100) : 0;

    $stmt = $conn->prepare("INSERT INTO index_bulanan (bulan, tahun, klp, program, storting_valid, idx_akhir) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiddi", $bulan, $tahun, $klp, $program, $storting, $idx);
    $stmt->execute();

    header("Location: index_bulanan.php");
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
  <script>
    function formatRibuan(input) {
      let value = input.value.replace(/\D/g,'');
      input.value = new Intl.NumberFormat('id-ID').format(value);
    }
  </script>
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
      <?php if (!empty($errors)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <h3>Tambah Data Index Bulanan</h3>
      <form method="POST">
<div class="mb-3">
  <label>Bulan</label>
  <select name="bulan" class="form-control" required>
    <option value="">-- Pilih Bulan --</option>
    <option value="1">Januari</option>
    <option value="2">Februari</option>
    <option value="3">Maret</option>
    <option value="4">April</option>
    <option value="5">Mei</option>
    <option value="6">Juni</option>
    <option value="7">Juli</option>
    <option value="8">Agustus</option>
    <option value="9">September</option>
    <option value="10">Oktober</option>
    <option value="11">November</option>
    <option value="12">Desember</option>
  </select>
</div>

  <div class="mb-3">
    <label>Tahun</label>
    <input type="number" name="tahun" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Kelompok</label>
    <select name="klp" class="form-control" required>
      <?php for($i=1;$i<=10;$i++): ?>
        <option value="<?= $i ?>">Kelompok <?= $i ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="mb-3">
    <label>Program</label>
    <input type="text" name="program" oninput="formatRibuan(this)" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Storting Valid</label>
    <input type="text" name="storting_valid" oninput="formatRibuan(this)" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
  <a href="index_bulanan.php" class="btn btn-secondary">Kembali</a>
</form>
      
    </div>
  </div>


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