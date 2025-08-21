<?php
session_start();
include '../config/koneksi.php';
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

      <h3>Tambah / Update Data Saldo</h3>
      <form method="POST" action="">
        <div class="row mb-2">
          <div class="col">
            <label>Bulan</label>
            <select name="bulan" class="form-control">
              <option>Januari</option>
              <option>Februari</option>
              <option>Maret</option>
              <option>April</option>
              <option>Mei</option>
              <option>Juni</option>
              <option>Juli</option>
              <option>Agustus</option>
              <option>September</option>
              <option>Oktober</option>
              <option>November</option>
              <option>Desember</option>
            </select>
          </div>
          <div class="col">
            <label>Tahun</label>
            <input type="number" name="tahun" class="form-control" value="2025">
          </div>
          <div class="col">
            <label>KLP</label>
            <select name="klp" class="form-control">
              <?php for ($i = 1; $i <= 10; $i++): ?>
                <option>Kelompok <?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <h5>Waktu Saldo</h5>
        <?php foreach (['a' => 'Senin', 'b' => 'Selasa', 'c' => 'Rabu', 'd' => 'Kamis', 'e' => 'Jumat', 'f' => 'Sabtu'] as $k => $v): ?>
          <input type="number" name="<?= $k ?>" placeholder="<?= $v ?>" class="form-control mb-1">
        <?php endforeach; ?>

        <div class="mb-3">
          <label>Pilih Minggu</label>
          <select id="selectMinggu" class="form-control">
            <option value="1">Minggu 1</option>
            <option value="2">Minggu 2</option>
            <option value="3">Minggu 3</option>
            <option value="4">Minggu 4</option>
          </select>
        </div>

        <!-- Minggu Fields -->
        <div class="minggu-group" id="minggu1">
          <h5>Minggu 1</h5>
          <?php foreach (['g1' => 'Senin', 'h1' => 'Selasa', 'i1' => 'Rabu', 'j1' => 'Kamis', 'k1' => 'Jumat', 'l1' => 'Sabtu'] as $k => $v): ?>
            <input type="number" name="<?= $k ?>" placeholder="<?= $v ?>" class="form-control mb-1">
          <?php endforeach; ?>
        </div>

        <div class="minggu-group d-none" id="minggu2">
          <h5>Minggu 2</h5>
          <?php foreach (['g2' => 'Senin', 'h2' => 'Selasa', 'i2' => 'Rabu', 'j2' => 'Kamis', 'k2' => 'Jumat', 'l2' => 'Sabtu'] as $k => $v): ?>
            <input type="number" name="<?= $k ?>" placeholder="<?= $v ?>" class="form-control mb-1">
          <?php endforeach; ?>
        </div>

        <div class="minggu-group d-none" id="minggu3">
          <h5>Minggu 3</h5>
          <?php foreach (['g3' => 'Senin', 'h3' => 'Selasa', 'i3' => 'Rabu', 'j3' => 'Kamis', 'k3' => 'Jumat', 'l3' => 'Sabtu'] as $k => $v): ?>
            <input type="number" name="<?= $k ?>" placeholder="<?= $v ?>" class="form-control mb-1">
          <?php endforeach; ?>
        </div>

        <div class="minggu-group d-none" id="minggu4">
          <h5>Minggu 4</h5>
          <?php foreach (['g4' => 'Senin', 'h4' => 'Selasa', 'i4' => 'Rabu', 'j4' => 'Kamis', 'k4' => 'Jumat', 'l4' => 'Sabtu'] as $k => $v): ?>
            <input type="number" name="<?= $k ?>" placeholder="<?= $v ?>" class="form-control mb-1">
          <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="data_antisipasi_masuk.php" class="btn btn-secondary mt-3">Kembali</a>
      </form>
      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $bulan = $_POST['bulan'];
        $tahun = $_POST['tahun'];
        $klp   = $_POST['klp'];

        $cek = mysqli_query($conn, "SELECT * FROM antisipasi WHERE bulan='$bulan' AND tahun='$tahun' AND klp='$klp'");
        $fields = [];
        foreach ($_POST as $key => $val) {
          if ($key != 'bulan' && $key != 'tahun' && $key != 'klp' && $val !== '') {
            $fields[$key] = (int)$val;
          }
        }

        if (mysqli_num_rows($cek) > 0) {
          $set = [];
          foreach ($fields as $k => $v) {
            $set[] = "$k=$v";
          }
          $sql = "UPDATE antisipasi SET " . implode(",", $set) . " WHERE bulan='$bulan' AND tahun='$tahun' AND klp='$klp'";
        } else {
          $cols = "bulan,tahun,klp," . implode(",", array_keys($fields));
          $vals = "'$bulan',$tahun,'$klp'," . implode(",", array_values($fields));
          $sql = "INSERT INTO antisipasi ($cols) VALUES ($vals)";
        }

        if (mysqli_query($conn, $sql)) {
          echo "<div class='alert alert-success mt-3'>Data berhasil disimpan</div>";
        } else {
          echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
        }
      }
      ?>
    </div>
  </div>
  <div class="flex-grow-1"></div>
  </div>
  </div>
  <!-- Start Main Content Area -->
  <script>
    const selectMinggu = document.getElementById('selectMinggu');
    const mingguGroups = document.querySelectorAll('.minggu-group');

    selectMinggu.addEventListener('change', function() {
      mingguGroups.forEach(group => group.classList.add('d-none'));
      const selected = document.getElementById('minggu' + this.value);
      if (selected) selected.classList.remove('d-none');
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