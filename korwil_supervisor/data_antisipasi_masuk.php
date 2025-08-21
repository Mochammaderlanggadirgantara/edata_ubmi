<?php
session_start();
include '../config/koneksi.php';

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
$id_user   = $_SESSION['id_user'];
$jabatan   = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
// Ambil filter bulan & tahun dari request
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$query = "SELECT * FROM antisipasi WHERE 1=1";
if ($bulan != '') $query .= " AND bulan='$bulan'";
if ($tahun != '') $query .= " AND tahun='$tahun'";
$result = mysqli_query($conn, $query);

// Mapping variabel ke header
$waktu_saldo = ['a' => 'Senin', 'b' => 'Selasa', 'c' => 'Rabu', 'd' => 'Kamis', 'e' => 'Jumat', 'f' => 'Sabtu'];
$minggu1 = ['g1' => 'Senin', 'h1' => 'Selasa', 'i1' => 'Rabu', 'j1' => 'Kamis', 'k1' => 'Jumat', 'l1' => 'Sabtu'];
$minggu2 = ['g2' => 'Senin', 'h2' => 'Selasa', 'i2' => 'Rabu', 'j2' => 'Kamis', 'k2' => 'Jumat', 'l2' => 'Sabtu'];
$minggu3 = ['g3' => 'Senin', 'h3' => 'Selasa', 'i3' => 'Rabu', 'j3' => 'Kamis', 'k3' => 'Jumat', 'l3' => 'Sabtu'];
$minggu4 = ['g4' => 'Senin', 'h4' => 'Selasa', 'i4' => 'Rabu', 'j4' => 'Kamis', 'k4' => 'Jumat', 'l4' => 'Sabtu'];
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
  <style>
    /* Desktop */
    .default-table-area table {
      table-layout: fixed;
      width: 100%;
      font-size: 12px;
    }

    .default-table-area th,
    .default-table-area td {
      padding: 0.4rem 0.6rem;
      word-wrap: break-word;
    }

    /* Hover effect */
    .default-table-area tbody tr:hover {
      background-color: #f8f9fa;
    }

    /* Mobile Responsive */
    @media(max-width:768px) {

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
        padding: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        background: #fff;
      }

      .default-table-area td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.3rem 0;
        border: none;
        border-bottom: 1px solid #f1f1f1;
        font-size: 12px;
      }

      .default-table-area td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6c757d;
        flex: 1;
        text-align: left;
      }

      .default-table-area td:last-child {
        border-bottom: none;
      }
    }
  </style>
</head>

<body class="boxed-size">


  <!-- End Preloader Area -->

  <!-- End Preloader Area -->
  <!-- Navbar and Header Area -->
  <?php include '../navbar/navbar_korwil.php'; ?>
  <!-- End Navbar and Header Area -->

  <div class="main-content-container overflow-hidden">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <h3 class="mb-0">Data Antisipasi Masuk</h3>

      <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb align-items-center mb-0 lh-1">
          <li class="breadcrumb-item">
            <a href="#" class="d-flex align-items-center text-decoration-none">
              <i class="ri-home-4-line fs-18 text-primary me-1"></i>
              <span class="text-secondary fw-medium hover">Dashboard</span>
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <span class="fw-medium">Data Antisipasi Masuk</span>
          </li>

        </ol>
      </nav>
    </div>
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
          <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
          <button onclick="window.location.href='create_data_antisipasi.php'" class="btn btn-outline-primary fs-16 py-2 px-4 ms-auto">Tambah Data</button>

        </div>
        <div class="container">
          <h3 class="mb-4">Data Antisipasi</h3>

          <!-- Filter Bulan & Tahun -->
          <div class="card mb-4">
            <div class="card-body">
              <form method="GET" class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Bulan</label>
                  <select name="bulan" class="form-control">
                    <option value="">-- Semua Bulan --</option>
                    <?php
                    $bulanList = [
                      "Januari",
                      "Februari",
                      "Maret",
                      "April",
                      "Mei",
                      "Juni",
                      "Juli",
                      "Agustus",
                      "September",
                      "Oktober",
                      "November",
                      "Desember"
                    ];
                    foreach ($bulanList as $b) {
                      $sel = ($bulan == $b) ? 'selected' : '';
                      echo "<option value='$b' $sel>$b</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tahun</label>
                  <input type="number" name="tahun" class="form-control" value="<?= htmlspecialchars($tahun) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary me-2">Filter</button>
                  <a href="index.php" class="btn btn-secondary">Reset</a>
                </div>
              </form>
            </div>
          </div>

          <!-- Tabel dengan Tab -->
          <div class="card">
            <div class="card-body">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#saldo">Waktu Saldo</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#m1">Minggu 1</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#m2">Minggu 2</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#m3">Minggu 3</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#m4">Minggu 4</a></li>
              </ul>

              <div class="tab-content mt-3">
                <!-- =================== SALDO =================== -->
                <div class="tab-pane fade show active" id="saldo">
                  <table class="table table-bordered text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Kelompok</th>
                        <?php foreach ($waktu_saldo as $h) echo "<th>$h</th>"; ?>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $totalSaldo = array_fill_keys(array_keys($waktu_saldo), 0);
                      $grandTotal = 0;

                      mysqli_data_seek($result, 0);
                      while ($row = mysqli_fetch_assoc($result)):
                        $rowTotal = 0;
                      ?>
                        <tr>
                          <td><?= $row['klp'] ?></td>
                          <?php foreach ($waktu_saldo as $k => $h):
                            $rowTotal += $row[$k];
                            $totalSaldo[$k] += $row[$k];
                          ?>
                            <td><?= $row[$k] ?></td>
                          <?php endforeach; ?>
                          <td class="fw-bold bg-light"><?= $rowTotal ?></td>
                        </tr>
                        <?php $grandTotal += $rowTotal; ?>
                      <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                      <tr>
                        <td>Total</td>
                        <?php foreach ($waktu_saldo as $k => $h): ?>
                          <td><?= $totalSaldo[$k] ?></td>
                        <?php endforeach; ?>
                        <td><?= $grandTotal ?></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <!-- =================== MINGGU 1 =================== -->
                <div class="tab-pane fade" id="m1">
                  <table class="table table-bordered text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Kelompok</th>
                        <?php foreach ($minggu1 as $h) echo "<th>$h</th>"; ?>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $totalM1 = array_fill_keys(array_keys($minggu1), 0);
                      $grandM1 = 0;

                      mysqli_data_seek($result, 0);
                      while ($row = mysqli_fetch_assoc($result)):
                        $rowTotal = 0;
                      ?>
                        <tr>
                          <td><?= $row['klp'] ?></td>
                          <?php foreach ($minggu1 as $k => $h):
                            $rowTotal += $row[$k];
                            $totalM1[$k] += $row[$k];
                          ?>
                            <td><?= $row[$k] ?></td>
                          <?php endforeach; ?>
                          <td class="fw-bold bg-light"><?= $rowTotal ?></td>
                        </tr>
                        <?php $grandM1 += $rowTotal; ?>
                      <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                      <tr>
                        <td>Total</td>
                        <?php foreach ($minggu1 as $k => $h): ?>
                          <td><?= $totalM1[$k] ?></td>
                        <?php endforeach; ?>
                        <td><?= $grandM1 ?></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <!-- =================== MINGGU 2 =================== -->
                <div class="tab-pane fade" id="m2">
                  <table class="table table-bordered text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Kelompok</th>
                        <?php foreach ($minggu2 as $h) echo "<th>$h</th>"; ?>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      mysqli_data_seek($result, 0);
                      $colTotals2 = array_fill_keys(array_keys($minggu2), 0);
                      while ($row = mysqli_fetch_assoc($result)):
                        $rowTotal = 0;
                      ?>
                        <tr>
                          <td><?= $row['klp'] ?></td>
                          <?php foreach ($minggu2 as $k => $h):
                            $val = (int)$row[$k];
                            $rowTotal += $val;
                            $colTotals2[$k] += $val;
                          ?>
                            <td><?= $val ?></td>
                          <?php endforeach; ?>
                          <td class="fw-bold bg-light"><?= $rowTotal ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                      <tr>
                        <th>Total</th>
                        <?php foreach ($minggu2 as $k => $h): ?>
                          <th><?= $colTotals2[$k] ?></th>
                        <?php endforeach; ?>
                        <th><?= array_sum($colTotals2) ?></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <!-- =================== MINGGU 3 =================== -->
                <div class="tab-pane fade" id="m3">
                  <table class="table table-bordered text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Kelompok</th>
                        <?php foreach ($minggu3 as $h) echo "<th>$h</th>"; ?>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      mysqli_data_seek($result, 0);
                      $colTotals3 = array_fill_keys(array_keys($minggu3), 0);
                      while ($row = mysqli_fetch_assoc($result)):
                        $rowTotal = 0;
                      ?>
                        <tr>
                          <td><?= $row['klp'] ?></td>
                          <?php foreach ($minggu3 as $k => $h):
                            $val = (int)$row[$k];
                            $rowTotal += $val;
                            $colTotals3[$k] += $val;
                          ?>
                            <td><?= $val ?></td>
                          <?php endforeach; ?>
                          <td class="fw-bold bg-light"><?= $rowTotal ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                      <tr>
                        <th>Total</th>
                        <?php foreach ($minggu3 as $k => $h): ?>
                          <th><?= $colTotals3[$k] ?></th>
                        <?php endforeach; ?>
                        <th><?= array_sum($colTotals3) ?></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <!-- =================== MINGGU 4 =================== -->
                <div class="tab-pane fade" id="m4">
                  <table class="table table-bordered text-center">
                    <thead class="table-dark">
                      <tr>
                        <th>Kelompok</th>
                        <?php foreach ($minggu4 as $h) echo "<th>$h</th>"; ?>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      mysqli_data_seek($result, 0);
                      $colTotals4 = array_fill_keys(array_keys($minggu4), 0);
                      while ($row = mysqli_fetch_assoc($result)):
                        $rowTotal = 0;
                      ?>
                        <tr>
                          <td><?= $row['klp'] ?></td>
                          <?php foreach ($minggu4 as $k => $h):
                            $val = (int)$row[$k];
                            $rowTotal += $val;
                            $colTotals4[$k] += $val;
                          ?>
                            <td><?= $val ?></td>
                          <?php endforeach; ?>
                          <td class="fw-bold bg-light"><?= $rowTotal ?></td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                      <tr>
                        <th>Total</th>
                        <?php foreach ($minggu4 as $k => $h): ?>
                          <th><?= $colTotals4[$k] ?></th>
                        <?php endforeach; ?>
                        <th><?= array_sum($colTotals4) ?></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap mt-3">
          <span class="fs-13 fw-medium">Items per pages: 10</span>

          <div class="d-flex align-items-center">
            <span class="fs-13 fw-medium me-2">1 - 10 of 12</span>
            <nav aria-label="Page navigation example">
              <ul class="pagination mb-0 justify-content-center">
                <li class="page-item">
                  <a class="page-link icon" href="#" aria-label="Previous">
                    <i class="material-symbols-outlined">keyboard_arrow_left</i>
                  </a>
                </li>
                <li class="page-item">
                  <a class="page-link icon" href="#" aria-label="Next">
                    <i class="material-symbols-outlined">keyboard_arrow_right</i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
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
</body>

</html>