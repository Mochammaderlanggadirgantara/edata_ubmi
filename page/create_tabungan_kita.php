<?php
// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi ke database
include __DIR__ . '/../config/koneksi.php';

$success = false;
$error_message = "";

// Proses Create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $uraian = $_POST['uraian'];
    $debit = $_POST['debit'] ?: 0;
    $kredit = $_POST['kredit'] ?: 0;

    // Ambil saldo terakhir
    $result = $conn->query("SELECT saldo FROM tabungan ORDER BY id DESC LIMIT 1");
    $last_saldo = ($result->num_rows > 0) ? $result->fetch_assoc()['saldo'] : 0;

    // Hitung saldo baru
    $saldo_baru = $last_saldo + $debit - $kredit;

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO tabungan (tanggal, uraian, debit, kredit, saldo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddd", $tanggal, $uraian, $debit, $kredit, $saldo_baru);
    $stmt->execute();
    $stmt->close();

    header("Location: tabungan_kita.php");
    exit;
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
    <?php include '../navbar/navbar_kasir.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form id="myForm" method="POST" action="create_tabungan_kita.php">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="uraian" class="form-label">Uraian</label>
                        <input type="text" class="form-control" id="uraian" name="uraian" required>
                    </div>
                    <div class="col-md-4">
                        <label for="debit" class="form-label">Debit</label>
                        <input type="number" step="0.01" class="form-control" id="debit" name="debit" value="0" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="kredit" class="form-label">Kredit</label>
                        <input type="number" step="0.01" class="form-control" id="kredit" name="kredit" value="0" required>
                    </div>

                </div>



                <button type="submit" class="btn btn-primary" id="simpanBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='tunai_babat.php'">Kembali</button>
            </form>

        </div>
    </div>



    <div class="flex-grow-1"></div>


    </div>
    </div>
    <!-- Start Main Content Area -->
    <?php if ($success): ?>
        <!-- Modal Berhasil -->
        <div class="modal show fade" id="successModal" tabindex="-1" style="display:block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Berhasil</h5>
                    </div>
                    <div class="modal-body">
                        ✅ Data berhasil disimpan.
                    </div>
                    <div class="modal-footer">
                        <a href="tabungan_kita.php" class="btn btn-success">OK</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Redirect otomatis setelah 3 detik
            setTimeout(function() {
                window.location.href = "tabungan_kita.php";
            }, 3000);
        </script>

    <?php elseif (!empty($error_message)): ?>
        <!-- Tampilkan pesan error -->
        <div class="container mt-4">
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
        </div>
    <?php endif; ?>

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

    <!-- Select2 untuk KLP -->
    <script>
        $(document).ready(function() {
            $('#klp').select2({
                placeholder: "-- Pilih KLP --",
                allowClear: true,
                minimumResultsForSearch: 0
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropInput = document.getElementById('drop_uang');
            const persenInput = document.getElementById('persen_9');
            const suLapanganInput = document.getElementById('su_lapangan');
            const transferanInput = document.getElementById('transferan');
            const sisaUangInput = document.getElementById('sisa_uang');
            const tunaiInput = document.getElementById('tunai');
            const minPlusInput = document.getElementById('min_plus');

            function parseNumber(value) {
                // Hilangkan titik ribuan dan konversi ke float
                return parseFloat(value.replace(/\./g, '').replace(/,/g, '.')) || 0;
            }

            function update9Persen() {
                const dropValue = parseNumber(dropInput.value);
                persenInput.value = Math.round(dropValue * 0.09);
                updateSisaUang();
            }

            function updateSisaUang() {
                const suLapangan = parseNumber(suLapanganInput.value);
                const transferan = parseNumber(transferanInput.value);
                const persen9 = parseNumber(persenInput.value);
                const sisa = suLapangan + transferan - persen9;
                sisaUangInput.value = Math.round(sisa);
                updateMinPlus();
            }

            function updateMinPlus() {
                const tunai = parseNumber(tunaiInput.value);
                const sisaUang = parseNumber(sisaUangInput.value);
                minPlusInput.value = sisaUang - tunai;
            }

            // Event listeners
            dropInput.addEventListener('input', update9Persen);
            suLapanganInput.addEventListener('input', updateSisaUang);
            transferanInput.addEventListener('input', updateSisaUang);
            tunaiInput.addEventListener('input', updateMinPlus);

            // Hitung saat pertama kali load
            update9Persen();
        });
    </script>





</body>



</html>