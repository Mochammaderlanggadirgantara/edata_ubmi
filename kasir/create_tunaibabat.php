<?php
session_start();

// Koneksi ke database
include __DIR__ . '/../config/koneksi.php';
// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$id_cabang   = $data_user['id_cabang'];   // ini aman dipakai
$nama_user   = $data_user['nama_user'];
$jabatan     = strtolower($data_user['jabatan']);
$nama_cabang = $data_user['nama_cabang'];


$success = false;
$error_message = "";
$id_cabang = $_SESSION['id_cabang'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $klp         = isset($_POST['klp']) ? (int) $_POST['klp'] : 0;
    $tanggal     = isset($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $kasbon      = isset($_POST['kasbon']) ? (int) $_POST['kasbon'] : 0;
    $drop        = isset($_POST['drop']) ? (int) $_POST['drop'] : 0;
    $su_lapangan = isset($_POST['su_lapangan']) ? (int) $_POST['su_lapangan'] : 0;
    $transferan  = isset($_POST['transferan']) ? (int) $_POST['transferan'] : 0;
    $tunai       = isset($_POST['tunai']) ? (int) $_POST['tunai'] : 0;

    $persen_9  = round($drop * 0.09);
    $sisa_uang = $kasbon - $drop;
    $min_plus  = $sisa_uang - $tunai;

    // Tentukan hari dari tanggal
    $hari = '';
    if ($tanggal) {
        try {
            $tanggalObj = new DateTime($tanggal);
            $hariEnglish = $tanggalObj->format('l'); // Misal: Monday
            // Konversi ke bahasa Indonesia
            $namaHari = [
                'Sunday'    => 'Minggu',
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu'
            ];
            $hari = $namaHari[$hariEnglish] ?? '';
        } catch (Exception $e) {
            $error_message = "Format tanggal tidak valid.";
        }
    }

    if ($klp === 0) {
        $error_message = "KLP tidak boleh kosong atau 0.";
    } elseif (empty($tanggal)) {
        $error_message = "Tanggal wajib diisi.";
    } else {
        // Cek apakah data KLP sudah ada
        $cek_stmt = $conn->prepare("SELECT klp FROM tunai_babat1 WHERE klp = ? AND id_cabang=?");
        $cek_stmt->bind_param("i", $klp);
        $cek_stmt->execute();
        $cek_stmt->store_result();

        if ($cek_stmt->num_rows > 0) {
            $error_message = "Data dengan KLP ini sudah ada.";
        } else {
            $stmt = $conn->prepare("INSERT INTO tunai_babat1 
                (klp, kasbon, drop_uang, su_lapangan, transferan, persen_9, sisa_uang, tunai, min_plus, tanggal, hari, id_cabang)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param(
                    "iiiiiiiisss",
                    $klp,
                    $kasbon,
                    $drop,
                    $su_lapangan,
                    $transferan,
                    $persen_9,
                    $sisa_uang,
                    $tunai,
                    $min_plus,
                    $tanggal,
                    $hari,
                    $id_cabang
                );

                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error_message = "Gagal menyimpan data: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $error_message = "Query gagal disiapkan: " . $conn->error;
            }
        }

        $cek_stmt->close();
    }

    $conn->close();
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
            <form id="myForm" method="POST" action="create_tunaibabat.php">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="klp" class="form-label">KLP</label>
                        <select class="form-select" id="klp" name="klp" required>
                            <option value="" disabled selected>-- Pilih KLP --</option>
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <?php $klp = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                <option value="<?= (int) $klp ?>"><?= $klp ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="kasbon" class="form-label">Kasbon</label>
                        <input type="number" class="form-control" id="kasbon" name="kasbon" value="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="drop_uang" class="form-label">Drop</label>
                        <input type="number" class="form-control" id="drop_uang" name="drop" value="0" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="su_lapangan" class="form-label">SU Lapangan</label>
                        <input type="number" class="form-control" id="su_lapangan" name="su_lapangan" value="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="transferan" class="form-label">Transferan</label>
                        <input type="number" class="form-control" id="transferan" name="transferan" value="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="persen_9" class="form-label">9%</label>
                        <input type="number" class="form-control" id="persen_9" name="persen_9" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="sisa_uang" class="form-label">Sisa Uang</label>
                        <input type="number" class="form-control" id="sisa_uang" name="sisa_uang" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="tunai" class="form-label">Tunai</label>
                        <input type="number" class="form-control" id="tunai" name="tunai" value="0" required>
                    </div>
                    <div class="col-md-4">
                        <label for="min_plus" class="form-label">Min / Plus</label>
                        <input type="number" class="form-control" id="min_plus" name="min_plus" readonly>
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
                        <a href="tunai_babat.php" class="btn btn-success">OK</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Redirect otomatis setelah 3 detik
            setTimeout(function() {
                window.location.href = "tunai_babat.php";
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