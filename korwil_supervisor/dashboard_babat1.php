<?php
session_start();

// Cek apakah user sudah login
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
// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="/edata_ubmi/assets/css/sidebar-menu.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/simplebar.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/apexcharts.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/prism.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/rangeslider.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/quill.snow.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/google-icon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/remixicon.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/fullcalendar.main.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/jsvectormap.min.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/lightpick.css" />
    <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png" />
    <!-- Title -->
    <title>APP TASKSIGHTT</title>
    <style>
      /* Judul card */
.card h6 {
  font-size: clamp(0.75rem, 2vw, 0.95rem);
  line-height: 1.2;
  white-space: normal; /* pastikan bisa multi-line */
  word-break: break-word; /* pecah kata panjang */
}

/* Deskripsi card */
.card p {
  font-size: clamp(0.65rem, 1.8vw, 0.85rem);
  line-height: 1.2;
  white-space: normal;
  word-break: break-word;
}

/* Ikon responsif */
.card i {
  font-size: clamp(1.2rem, 4vw, 1.8rem) !important;
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
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->
    <div class="main-content-container overflow-hidden">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11 col-md-12"> <!-- Lebarkan container -->
                <div class="card bg-primary border-0 rounded-4 welcome-box mb-5">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <div class="border-bottom pb-3 mb-3">
                                    <h2 class="text-white fw-bold mb-2 fs-1"> <!-- Ukuran besar -->
                                        Selamat Datang,

                                        <span class="text-warning"><?= htmlspecialchars($_SESSION['nama_user']) ?></span>
                                    </h2>
                                    <p class="text-light fs-5">Babat 1</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 col-sm-12 text-center mt-4 mt-md-0">
                                <img src="/edata_ubmi/assets/images/welcome.png" alt="welcome" class="img-fluid" style="max-height: 180px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border-0 rounded-4 mb-5">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <h2 class="mb-0 fw-bold fs-3">Dashboard</h2>
                        </div>
                        <div class="row g-3 text-center">

  <!-- Card 1 -->
  <div class="col-4">
    <a href="/edata_ubmi/korwil_supervisor/data_inventaris_korwil.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-primary d-block mb-2">inventory_2</i>
         
          <p class="fw-semibold small mb-0">Inventaris</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 2 -->
  <div class="col-4">
    <a href="/edata_ubmi/kasir/tunai_babat.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-danger d-block mb-2">payments</i>
        
          <p class="fw-semibold small mb-0">Tunai Babat</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 3 -->
  <div class="col-4">
    <a href="/edata_ubmi/kasir/servis_inventaris.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-success d-block mb-2">build</i>
      
          <p class="fw-semibold small mb-0">Servis Inventaris</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 4 -->
  <div class="col-4">
    <a href="/edata_ubmi/page/database_nasabah.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-info d-block mb-2">database</i>
         
          <p class="fw-semibold small mb-0">Data Nasabah</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 5 -->
  <div class="col-4">
    <a href="/edata_ubmi/page/sisa_saldo.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-warning d-block mb-2">account_balance_wallet</i>
          
          <p class="fw-semibold small mb-0">Sisa Saldo</p>
        </div>
      </div>
    </a>
  </div>

  <!-- Card 6 -->
  <div class="col-4">
    <a href="/edata_ubmi/page/tabungan_kita.php" class="text-decoration-none">
      <div class="card shadow-sm border-0 rounded-4 h-100 hover-shadow">
        <div class="card-body p-3">
          <i class="material-symbols-outlined fs-1 text-secondary d-block mb-2">wallet</i>
         
          <p class="fw-semibold small mb-0">Tabungan Kita</p>
        </div>
      </div>
    </a>
  </div>

</div>

                    </div>
                </div>
            </div>
        </div>
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