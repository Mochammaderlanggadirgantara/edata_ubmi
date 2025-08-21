
<?php
session_start();
include '../config/koneksi.php';


// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah user memiliki jabatan 'kasir'
if ($_SESSION['jabatan'] !== 'pengawas') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];


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
 <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png">
    <!-- Title -->
    <title>Aplikasi TaskSight</title>
<style> .default-table-area table { table-layout: auto; width: 100%; white-space: nowrap; } .default-table-area td, .default-table-area th { white-space: normal; word-break: break-word; padding: 0.75rem; vertical-align: top; } .default-table-area img { max-width: 40px; height: auto; border-radius: 8px; } /* Responsive stacked table on small screens */ @media (max-width: 768px) { .default-table-area table, .default-table-area thead, .default-table-area tbody, .default-table-area th, .default-table-area td, .default-table-area tr { display: block; width: 100%; } .default-table-area thead { display: none; } .default-table-area tbody tr { margin-bottom: 1rem; border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); } .default-table-area td { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border: none; border-bottom: 1px solid #f1f1f1; } .default-table-area td::before { content: attr(data-label); font-weight: 600; color: #6c757d; } .default-table-area td:last-child { border-bottom: none; } } </style> <style> @media (max-width: 576px) { #myTables th, #myTables td { font-size: 12px; padding: 6px 4px !important; } #myTables .btn i { font-size: 14px; } #myTables img.wh-40 { width: 28px; height: 28px; } #myTables h6.fs-14 { font-size: 12px !important; } } 

    .card {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  }

  .card-header {
    background: linear-gradient(135deg, #4e73df, #224abe);
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 1rem;
    border-bottom: none;
  }

  .default-table-area table {
    border-collapse: separate;
    border-spacing: 0 8px;
  }

  .default-table-area thead th {
    background: #f8f9fc;
    color: #4e73df;
    font-weight: 600;
    border: none;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: .05em;
  }

  .default-table-area tbody tr {
    background: #fff;
    transition: 0.3s;
  }

  .default-table-area tbody tr:hover {
    transform: scale(1.01);
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }

  .default-table-area td {
    vertical-align: middle;
    border: none !important;
  }

  /* Responsif stacked */
  @media (max-width: 768px) {
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

    .default-table-area tr {
      margin-bottom: 1rem;
      background: #fff;
      padding: 0.75rem;
      border-radius: 0.75rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .default-table-area td {
      padding: 0.5rem 0;
      text-align: right;
      position: relative;
    }

    .default-table-area td::before {
      content: attr(data-label);
      position: absolute;
      left: 0;
      font-weight: 600;
      color: #4e73df;
      text-align: left;
    }
  }

</style>
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

    <!-- End Preloader Area -->
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Inventaris</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Inventaris</span>
                    </li>

                </ol>
            </nav>
        </div>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="button-container d-flex justify-content-between flex-wrap gap-3 mb-4">
                    <button class="btn btn-outline-primary fs-16 py-2 px-4" onclick="exportToPDF()">Download as PDF</button>
                  <!-- Tombol Tambah Data -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
  + Tambah Data
</button>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="store_data_resort.php">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Data Resort</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
              <?php for($i=1;$i<=10;$i++): ?>
                <option value="<?= $i ?>">Kelompok <?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Hari</label>
            <select name="hari" class="form-control" required>
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
              <option value="Sabtu">Sabtu</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Target</label>
            <input type="number" name="target" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>CM</label>
            <input type="number" name="cm" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>MB</label>
            <input type="number" name="mb" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
                </div>
<!-- Filter Kelompok (Realtime) -->
<div class="card my-4">
  <div class="card-header">🔍 Filter Kelompok</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Pilih Kelompok</label>
        <select id="filterKelompok" class="form-select">
          <option value="">-- Semua Kelompok --</option>
          <?php for($i=1; $i<=10; $i++): ?>
            <option value="<?= $i ?>">Kelompok <?= $i ?></option>
          <?php endfor; ?>
        </select>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Data Resort -->
<div id="tabelResort" class="card my-4">
  <div class="card-header">📊 Data Resort</div>
  <div class="card-body default-table-area">
    <table class="table align-middle table-bordered">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Kelompok</th>
          <th>Hari</th>
          <th>Target</th>
          <th>CM</th>
          <th>MB</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $where = "";
        if (!empty($_GET['kelompok'])) {
          $kelompok = intval($_GET['kelompok']);
          $where = "WHERE kelompok = $kelompok";
        }

        $result = $conn->query("SELECT * FROM data_resort $where ORDER BY id DESC");

        $total_target = $total_cm = $total_mb = 0;
        while($row = $result->fetch_assoc()):
          $total_target += $row['target'];
          $total_cm     += $row['cm'];
          $total_mb     += $row['mb'];
        ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><span class="badge bg-primary">Kelompok <?= $row['kelompok'] ?></span></td>
          <td><span class="badge bg-info text-dark"><?= $row['hari'] ?></span></td>
          <td><?= number_format($row['target'],0,',','.') ?></td>
          <td><?= number_format($row['cm'],0,',','.') ?></td>
          <td><?= number_format($row['mb'],0,',','.') ?></td>
          <td>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">✏️ Edit</button>
              <a href="delete_data_resort.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">🗑️ Hapus</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot class="table-primary fw-bold text-center">
        <tr>
          <th colspan="3" class="text-center">TOTAL</th>
          <th><?= number_format($total_target,0,',','.') ?></th>
          <th><?= number_format($total_cm,0,',','.') ?></th>
          <th><?= number_format($total_mb,0,',','.') ?></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Tabel Data Target Mantri Babat 1 -->
<div id="tabelMantri" class="card my-4">
  <div class="card-header">📊 Data Target Mantri Babat 1</div>
  <div class="card-body default-table-area">
    <table class="table align-middle table-bordered">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Kelompok</th>
          <th>Hari</th>
          <th>Target</th>
          <th>CM</th>
          <th>MB</th>
          <th>AKSI</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $where2 = "";
        if (!empty($_GET['kelompok'])) {
          $kelompok = intval($_GET['kelompok']);
          $where2 = "WHERE id_kelompok = $kelompok";
        }

        $result = $conn->query("SELECT id, id_kelompok, hari, target, cm, mb 
                                FROM targetmantri_babat1 $where2 
                                ORDER BY id DESC");

        $totalTarget = $totalCm = $totalMb = 0;
        while($row = $result->fetch_assoc()):
          $totalTarget += $row['target'];
          $totalCm     += $row['cm'];
          $totalMb     += $row['mb'];
        ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><span class="badge bg-primary">Kelompok <?= $row['id_kelompok'] ?></span></td>
          <td><span class="badge bg-info text-dark"><?= $row['hari'] ?></span></td>
          <td><?= number_format($row['target'],0,',','.') ?></td>
          <td><?= number_format($row['cm'],0,',','.') ?></td>
          <td><?= number_format($row['mb'],0,',','.') ?></td>
          <td>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">✏️ Edit</button>
              <a href="delete_dataresortmantri.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">🗑️ Hapus</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot class="table-primary fw-bold">
        <tr>
          <td colspan="3" class="text-end">TOTAL</td>
          <td><?= number_format($totalTarget,0,',','.') ?></td>
          <td><?= number_format($totalCm,0,',','.') ?></td>
          <td><?= number_format($totalMb,0,',','.') ?></td>
          <td>-</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>


            </div>
        </div>

    </div>

    <!-- script File -->
     <!-- Script AJAX untuk Filter Realtime -->
<script>
document.getElementById('filterKelompok').addEventListener('change', function() {
  let kelompok = this.value;

  fetch(`?kelompok=${kelompok}`)
    .then(res => res.text())
    .then(html => {
      let parser = new DOMParser();
      let doc = parser.parseFromString(html, 'text/html');

      document.querySelector('#tabelResort').innerHTML =
        doc.querySelector('#tabelResort').innerHTML;

      document.querySelector('#tabelMantri').innerHTML =
        doc.querySelector('#tabelMantri').innerHTML;
    });
});
</script>
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