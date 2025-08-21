<?php
session_start();
include '../config/koneksi.php';


$tabel = isset($_GET['tabel']) ? $_GET['tabel'] : 'planning_resort';
$result = $conn->query("SELECT * FROM $tabel");
// Ambil warna dari DB

// Helper untuk tentukan warna teks agar kontras dengan background
function getTextColor($hexColor) {
    if (!$hexColor) return '#000000'; // default hitam kalau kosong

    $hexColor = ltrim($hexColor, '#');

    if (strlen($hexColor) == 3) {
        $r = hexdec(str_repeat(substr($hexColor, 0, 1), 2));
        $g = hexdec(str_repeat(substr($hexColor, 1, 1), 2));
        $b = hexdec(str_repeat(substr($hexColor, 2, 1), 2));
    } else {
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
    }

    // luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return ($luminance > 0.5) ? '#000000' : '#FFFFFF';
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png">
    <!-- Title -->
    <title>Aplikasi TaskSight</title>

</head>

<body class="boxed-size">
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Planning Resort</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Planning Reosrt</span>
                    </li>

                </ol>
            </nav>
        </div>
        <div class="container mt-4">

            <!-- HTML -->
            <div class="container mt-4">
                <a href="create_planning_resort.php?tabel=<?= $tabel ?>" class="btn btn-primary mb-3">+ Tambah Data</a>
<div class="card-body table-responsive">
         <table class="table table-bordered table-hover align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th rowspan="2">ID</th>
            <th rowspan="2">Kelompok</th>
            <th colspan="6">Jadwal</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th>Senin</th>
            <th>Selasa</th>
            <th>Rabu</th>
            <th>Kamis</th>
            <th>Jumat</th>
            <th>Sabtu</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['kelompok']) ?></td>

            <?php 
            $days = ['senin','selasa','rabu','kamis','jumat','sabtu'];
            foreach ($days as $day): 
                $bgColor = $row[$day.'_color'] ?: '#FFFFFF';
                $textColor = getTextColor($bgColor);
            ?>
                <td style="background-color: <?= htmlspecialchars($bgColor) ?>; color: <?= $textColor ?>;">
                    <?= htmlspecialchars($row[$day.'_start']) ?>  <?= htmlspecialchars($row[$day.'_finish']) ?>
                </td>
            <?php endforeach; ?>
             <!-- Kolom Status -->
            <td>
                <span class="badge bg-success">START</span><br>
                <span class="badge bg-danger">FINISH</span>
            </td>
            <td>
                <a href="edit.php?tabel=<?= $tabel ?>&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="delete_planning.php?tabel=<?= $tabel ?>&id=<?= $row['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Yakin hapus data ini?')">
                    <i class="bi bi-trash"></i> Hapus
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
        </div>
<style>
/* Mobile friendly card view */
@media (max-width: 768px) {
  .custom-table thead {
    display: none; /* hide header di mobile */
  }

  .custom-table, 
  .custom-table tbody, 
  .custom-table tr, 
  .custom-table td {
    display: block;
    width: 100%;
  }

  .custom-table tr {
    margin-bottom: 1rem;
    background: #fff;
    border-radius: .75rem;
    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    padding: .5rem;
  }

  .custom-table td {
    text-align: left !important;
    padding: .5rem .75rem;
    border: none;
    border-bottom: 1px solid #f1f1f1;
  }

  .custom-table td:last-child {
    border-bottom: none;
  }

  .custom-table td::before {
    content: attr(data-label);
    font-weight: 600;
    color: #0d6efd;
    display: block;
    margin-bottom: 2px;
    font-size: 0.85rem;
  }

  .custom-table td[rowspan] {
    background: #f8f9fa;
    font-weight: bold;
    text-align: center !important;
    border-radius: .5rem;
    margin-bottom: .5rem;
    border: none !important;
  }
}
</style>

            </div>
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
</body>