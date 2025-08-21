<?php
session_start();
include '../config/koneksi.php';

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
$totalRows = 30;
$rowsPerTable = 6;

// Ambil data user + cabang dari DB
$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT t.id_user, t.jabatan, t.nama_user, t.id_cabang, c.nama_cabang
                              FROM tuser t
                              JOIN cabang c ON t.id_cabang = c.id_cabang
                              WHERE t.id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query);

if (!$data_user) {
    die("Data user tidak ditemukan. Silakan login ulang.");
}

$id_cabang   = $data_user['id_cabang'];
$nama_user   = $data_user['nama_user'];
$jabatan     = strtolower($data_user['jabatan']);
$nama_cabang = $data_user['nama_cabang'];

// ✅ Ambil id_kelompok dari sesi login
$id_kelompok = isset($_SESSION['id_kelompok']) ? (int) $_SESSION['id_kelompok'] : 0;

if ($id_kelompok === 0) {
    die("ID Kelompok tidak ditemukan di sesi. Silakan login ulang.");
}

// Ambil data bulan ini
$data_ini = [];
$q_ini = mysqli_query($conn, "SELECT * FROM rencana_bulan_ini WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang");
while ($row = mysqli_fetch_assoc($q_ini)) {
  $data_ini[$row['nomor']] = $row;
}

// Ambil data bulan depan
$data_depan = [];
$q_depan = mysqli_query($conn, "SELECT * FROM rencana_bulan_depan WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang");
while ($row = mysqli_fetch_assoc($q_depan)) {
  $data_depan[$row['nomor']] = $row;
}

// Rekap
$rekap_ini   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rekap_bulan_ini WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang"));
$rekap_depan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rekap_bulan_depan WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang"));
?>

<style>
  /* --- Layout dasar --- */
  body {
    background: #f5f7fa;
    font-family: "Segoe UI", Roboto, sans-serif;
    color: #333;
  }

  h4 {
    font-weight: 600;
    color: #2c3e50;
  }

  /* --- Section Box Modern --- */
  .section-box {
    background: #fff;
    border-radius: 1rem;
    padding: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    transition: 0.3s ease;
  }

  .section-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
  }

  .judul-bulan-ini,
  .judul-bulan-depan {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 1rem;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    text-align: center;
  }

  .judul-bulan-ini {
    background: #eaf3ff;
    color: #0d6efd;
  }

  .judul-bulan-depan {
    background: #fff4e6;
    color: #fd7e14;
  }

  /* --- Tabel --- */
  .table {
    font-size: 14px;
    border-radius: 0.75rem;
    overflow: hidden;
  }

  .table th {
    font-weight: 600;
    color: #495057;
    background: #f8f9fa !important;
  }

  .table td input {
    font-size: 13px;
    padding: 3px 6px;
  }

  /* Hover row */
  .table tbody tr:hover {
    background: #f1f3f5;
  }

  /* --- Responsive Table untuk Mobile --- */
  @media (max-width: 768px) {
    table thead {
      display: none;
    }

    table tbody,
    table tfoot {
      display: block;
      width: 100%;
    }

    table tbody tr,
    table tfoot tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 0.75rem;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      padding: 0.5rem;
    }

    table tbody td,
    table tfoot td {
      display: flex;
      justify-content: space-between;
      padding: 0.6rem;
      border: none !important;
      border-bottom: 1px solid #f1f1f1 !important;
      font-size: 13px;
    }

    table tbody td:last-child,
    table tfoot td:last-child {
      border-bottom: none !important;
    }

    table tbody td::before,
    table tfoot td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #495057;
    }
  }

  /* --- Ringkasan --- */
  .bg-ringkasan th {
    width: 40%;
    text-align: left;
  }

  .bg-ringkasan td input {
    font-size: 13px;
    text-align: center;
    font-weight: 600;
    background: #f8f9fa;
    border-radius: 0.5rem;
  }

  /* --- Tombol Simpan --- */
  #btn-simpan {
    padding: 0.6rem 1.5rem;
    font-size: 15px;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: 0.3s ease;
  }

  #btn-simpan:hover {
    transform: scale(1.05);
  }
</style>

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
      case 'mantri':
        include '../navbar/navbar_mantri.php';
        break;
      default:
        echo "<p class='text-danger'>Jabatan tidak dikenali.</p>";
        break;
    }
  } else {
    echo "<p class='text-danger'>Session jabatan belum diset. Silakan login terlebih dahulu.</p>";
  }
  ?>


  <div class="container py-4">
    <h4 class="text-center mb-4">📅 Form Rencana Bulanan</h4>

    <div class="row g-4">
      <!-- Rencana Bulan Ini -->
      <div class="col-md-6">
        <div class="section-box">
          <div class="judul-bulan-ini">Rencana Bulan Ini</div>
          <?php for ($i = 0; $i < $totalRows / $rowsPerTable; $i++): ?>
            <div class="table-responsive mb-3">
              <table class="table table-borderless table-sm align-middle text-center mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="nomor-kolom">Nomor</th>
                    <th>Ke</th>
                    <th>Hari</th>
                    <th>Rencana</th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($j = 0; $j < $rowsPerTable; $j++):
                    $nomor = $i * $rowsPerTable + $j + 1;
                    $day = $days[($nomor - 1) % count($days)];
                    $data = $data_ini[$nomor] ?? null;
                  ?>
                    <tr>
                      <td class="nomor-kolom" data-label="Nomor"><?= $nomor ?></td>
                      <td data-label="Ke">
                        <input type="text" class="form-control form-control-sm text-center" name="ke_ini_<?= $nomor ?>"
                          value="<?= (isset($data['ke']) && $data['ke'] != 0) ? $data['ke'] : '' ?>">
                      </td>
                      <td data-label="Hari"><?= $day ?></td>
                      <td data-label="Rencana">
                        <input type="text" class="form-control form-control-sm text-center rencana-ini"
                          name="rencana_ini_<?= $nomor ?>"
                          value="<?= (isset($data['rencana']) && $data['rencana'] != 0) ? number_format($data['rencana'], 0, ',', '.') : '' ?>">
                      </td>
                    </tr>
                  <?php endfor; ?>
                </tbody>
              </table>
            </div>
          <?php endfor; ?>

          <!-- Ringkasan -->
          <table class="table table-borderless table-sm mt-3 bg-ringkasan">
            <tbody>
              <tr>
                <th>TOTAL</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="total_ini"
                    value="<?= number_format($rekap_ini['total'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
              </tr>
              <tr>
                <th>GAGALKAN</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="gagalkan_ini"
                    value="<?= number_format($rekap_ini['gagalkan'] ?? 0, 0, ',', '.') ?>">
                </td>
              </tr>
              <tr>
                <th>RENCANA JADI</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="jadi_ini"
                    value="<?= number_format($rekap_ini['rencana_jadi'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Rencana Bulan Depan -->
      <div class="col-md-6">
        <div class="section-box">
          <div class="judul-bulan-depan">Rencana Bulan Depan</div>
          <?php for ($i = 0; $i < $totalRows / $rowsPerTable; $i++): ?>
            <div class="table-responsive mb-3">
              <table class="table table-borderless table-sm align-middle text-center mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="nomor-kolom">Nomor</th>
                    <th>Ke</th>
                    <th>Hari</th>
                    <th>Rencana</th>
                    <th>Potongan 1</th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($j = 0; $j < $rowsPerTable; $j++):
                    $nomor = $i * $rowsPerTable + $j + 1;
                    $day = $days[($nomor - 1) % count($days)];
                    $data = $data_depan[$nomor] ?? null;
                  ?>
                    <tr>
                      <td class="nomor-kolom" data-label="Nomor"><?= $nomor ?></td>
                      <td data-label="Ke">
                        <input type="text" class="form-control form-control-sm text-center" name="ke_depan_<?= $nomor ?>"
                          value="<?= (isset($data['ke']) && $data['ke'] != 0) ? $data['ke'] : '' ?>">
                      </td>
                      <td data-label="Hari"><?= $day ?></td>
                      <td data-label="Rencana">
                        <input type="text" class="form-control form-control-sm text-center rencana-depan"
                          name="rencana_depan_<?= $nomor ?>"
                          value="<?= (isset($data['rencana']) && $data['rencana'] != 0) ? number_format($data['rencana'], 0, ',', '.') : '' ?>">
                      </td>
                      <td data-label="Potongan 1">
                        <input type="text" class="form-control form-control-sm text-center potongan-depan"
                          name="potongan_<?= $nomor ?>"
                          value="<?= (isset($data['potongan']) && $data['potongan'] != 0) ? number_format($data['potongan'], 0, ',', '.') : '' ?>">
                      </td>
                    </tr>
                  <?php endfor; ?>
                </tbody>
              </table>
            </div>
          <?php endfor; ?>

          <!-- Ringkasan -->
          <table class="table table-borderless table-sm mt-3 bg-ringkasan">
            <tbody>
              <tr>
                <th>TOTAL</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="total_depan"
                    value="<?= number_format($rekap_depan['total_rencana'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="total_potongan"
                    value="<?= number_format($rekap_depan['total_potongan'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
              </tr>
              <tr>
                <th>GAGALKAN</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="gagalkan_rencana_depan"
                    value="<?= number_format($rekap_depan['gagalkan_rencana'] ?? 0, 0, ',', '.') ?>">
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="gagalkan_potongan_depan"
                    value="<?= number_format($rekap_depan['gagalkan_potongan'] ?? 0, 0, ',', '.') ?>">
                </td>
              </tr>
              <tr>
                <th>RENCANA JADI</th>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="rencana_jadi_depan"
                    value="<?= number_format($rekap_depan['rencana_jadi'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm text-center" id="potongan_jadi_depan"
                    value="<?= number_format($rekap_depan['rencana_jadi_potongan'] ?? 0, 0, ',', '.') ?>" readonly>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <button id="btn-simpan" class="btn btn-primary shadow">💾 Simpan</button>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    function parseNumber(val) {
      return parseInt(val.replace(/\./g, '').replace(/[^0-9]/g, '')) || 0;
    }

    function formatNumber(num) {
      return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function hitungTotal(selector, outputId) {
      let total = 0;
      document.querySelectorAll(selector).forEach(input => {
        total += parseNumber(input.value);
      });
      document.getElementById(outputId).value = formatNumber(total);
      return total;
    }

    function hitungJadi(totalId, gagalkanId, outputId) {
      const total = parseNumber(document.getElementById(totalId).value);
      const gagal = parseNumber(document.getElementById(gagalkanId).value);
      document.getElementById(outputId).value = formatNumber(total - gagal);
    }

    function updateSemuaTotal() {
      hitungTotal('.rencana-ini', 'total_ini');
      hitungTotal('.rencana-depan', 'total_depan');
      hitungTotal('.potongan-depan', 'total_potongan');

      hitungJadi('total_ini', 'gagalkan_ini', 'jadi_ini');
      hitungJadi('total_depan', 'gagalkan_rencana_depan', 'rencana_jadi_depan');
      hitungJadi('total_potongan', 'gagalkan_potongan_depan', 'potongan_jadi_depan');
    }

    function formatInputRibuan(input) {
      const val = input.value.replace(/\./g, '').replace(/[^0-9]/g, '');
      input.value = val ? formatNumber(parseInt(val)) : '';
    }
    document.addEventListener('input', function(e) {
      const el = e.target;
      if (
        el.classList.contains('rencana-ini') ||
        el.classList.contains('rencana-depan') ||
        el.classList.contains('potongan-depan') ||
        el.id.startsWith('gagalkan_')
      ) {
        formatInputRibuan(el);
        updateSemuaTotal();
      }
    });
    window.addEventListener('DOMContentLoaded', updateSemuaTotal);

    document.getElementById('btn-simpan').addEventListener('click', function() {
      const formData = new FormData();
      for (let i = 1; i <= 30; i++) {
        formData.append(`ke_ini_${i}`, document.querySelector(`[name="ke_ini_${i}"]`).value);
        formData.append(`rencana_ini_${i}`, document.querySelector(`[name="rencana_ini_${i}"]`).value);
        formData.append(`ke_depan_${i}`, document.querySelector(`[name="ke_depan_${i}"]`).value);
        formData.append(`rencana_depan_${i}`, document.querySelector(`[name="rencana_depan_${i}"]`).value);
        formData.append(`potongan_${i}`, document.querySelector(`[name="potongan_${i}"]`).value);
      }

      ['total_ini', 'gagalkan_ini', 'jadi_ini', 'total_depan', 'total_potongan', 'gagalkan_rencana_depan', 'gagalkan_potongan_depan', 'rencana_jadi_depan', 'potongan_jadi_depan'].forEach(id => {
        formData.append(id, document.getElementById(id).value);
      });

      fetch('mantri/simpan_rencana.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(msg => alert(msg))
        .catch(err => alert('Gagal simpan: ' + err));
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