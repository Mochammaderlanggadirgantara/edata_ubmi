<?php
include '../config/koneksi.php';
include '../page/function_tabungan_kita.php';

$data = [];

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan.");
}

// Ambil data yang akan diedit
$stmt = $conn->prepare("SELECT * FROM tabungan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Data tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $uraian = $_POST['uraian'];
    $debit = $_POST['debit'] ?: 0;
    $kredit = $_POST['kredit'] ?: 0;

    // Update data
    $stmt = $conn->prepare("UPDATE tabungan SET tanggal = ?, uraian = ?, debit = ?, kredit = ? WHERE id = ?");
    $stmt->bind_param("ssddi", $tanggal, $uraian, $debit, $kredit, $id);
    $stmt->execute();
    $stmt->close();

    // Recalculate semua saldo ulang
    recalculateSaldo($conn);

    header("Location: tabungan_kita.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Links Of CSS File -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

    <!-- Start Main Content Area -->

    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <form id="formUpdate" method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= $data['tanggal'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="uraian" class="form-label">Uraian</label>
                        <input type="text" class="form-control" id="uraian" name="uraian" required value="<?= $data['uraian'] ?>">
                    </div>
                    <div class=" col-md-4">
                        <label for="debit" class="form-label">Debit</label>
                        <input type="text" class="form-control" id="debit" name="debit" required value="<?= $data['debit'] ?>">
                    </div>
                    <div class=" col-md-4">
                        <label for="kredit" class="form-label">Kredit</label>
                        <input type="text" class="form-control" id="kredit" name="kredit" required value="<?= $data['kredit'] ?>">
                    </div>
                </div>


                <button type="submit" name="submit" class="btn btn-primary">Perbarui</button>
                <a href="tabungan_kita.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>


    <div class="flex-grow-1"></div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            const kasbonInput = document.getElementById('kasbon');
            const dropInput = document.getElementById('drop_uang');
            const suInput = document.getElementById('su_lapangan');
            const transferanInput = document.getElementById('transferan');
            const persen9Input = document.getElementById('persen_9');
            const sisaInput = document.getElementById('sisa_uang');
            const tunaiInput = document.getElementById('tunai');
            const minPlusInput = document.getElementById('min_plus');

            function hitung() {
                const kasbon = parseInt(kasbonInput.value) || 0;
                const drop = parseInt(dropInput.value) || 0;
                const su = parseInt(suInput.value) || 0;
                const transferan = parseInt(transferanInput.value) || 0;
                const tunai = parseInt(tunaiInput.value) || 0;

                const persen9 = Math.round(drop * 0.09);
                const sisa = su + transferan - persen9;
                const minPlus = sisa - tunai;

                persen9Input.value = persen9;
                sisaInput.value = sisa;
                minPlusInput.value = minPlus;
            }

            function getNamaHari(dateString) {
                const hariMap = {
                    0: 'Minggu',
                    1: 'Senin',
                    2: 'Selasa',
                    3: 'Rabu',
                    4: 'Kamis',
                    5: 'Jumat',
                    6: 'Sabtu'
                };
                const date = new Date(dateString);
                const hari = date.getDay();
                return hariMap[hari] || '';
            }

            const tanggalInput = document.getElementById('tanggal');
            const hariInput = document.getElementById('hari');

            tanggalInput.addEventListener('change', function() {
                hariInput.value = getNamaHari(this.value);
            });

            // Isi awal jika tanggal sudah ada
            if (tanggalInput.value) {
                hariInput.value = getNamaHari(tanggalInput.value);
            }

            kasbonInput.addEventListener('input', hitung);
            dropInput.addEventListener('input', hitung);
            suInput.addEventListener('input', hitung);
            transferanInput.addEventListener('input', hitung);
            tunaiInput.addEventListener('input', hitung);

            hitung(); // hitung saat halaman dimuat
        });
    </script>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="responseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Notifikasi</h5>
                </div>
                <div class="modal-body" id="modalBody">...</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="modalCloseBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnUpdate').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('formUpdate'));

            fetch('edit_tunaibabat_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    const modal = new bootstrap.Modal(document.getElementById('responseModal'));
                    document.getElementById('modalTitle').textContent = res.success ? 'Berhasil!' : 'Gagal!';
                    document.getElementById('modalBody').textContent = res.message;
                    document.getElementById('modalCloseBtn').className = res.success ? 'btn btn-success' : 'btn btn-danger';

                    modal.show();

                    if (res.success) {
                        document.getElementById('modalCloseBtn').addEventListener('click', () => {
                            window.location.href = 'tunai_babat.php';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim data.');
                });
        });
    </script>


</body>



</html>