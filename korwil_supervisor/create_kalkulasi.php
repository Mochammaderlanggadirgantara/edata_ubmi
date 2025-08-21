<?php
session_start();
include '../config/koneksi.php';
// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Ambil data user dari session
$id_user = $_SESSION['id_user'];
$jabatan = $_SESSION['jabatan'];
$nama_user = $_SESSION['nama_user'];
// Role hanya untuk Pengawas
$allowed_roles = ['pengawas'];
if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
// Ambil 1 baris saja
$result = mysqli_query($conn, "SELECT * FROM label_setting LIMIT 1");
$row = mysqli_fetch_assoc($result);
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
    <!-- Tambahkan Bootstrap & Font Awesome -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/edata_ubmi/assets/images/favicon.png">
    <!-- Title -->
    <title>Aplikasi TaskSight</title>
    <style>
        .section-title {
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .badge-indicator {
            font-size: 1rem;
            min-width: 40px;
            text-align: center;
        }

        .badge-custom {
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            display: inline-block;
            min-width: 30px;
            text-align: center;
        }

        .badge-black {
            background-color: black;
            border: 1px solid black;
        }

        .badge-red {
            background-color: red;
            border: 1px solid red;
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

    <!-- End Preloader Area -->
    <!-- Navbar and Header Area -->
    <?php include '../navbar/navbar_korwil.php'; ?>
    <!-- End Navbar and Header Area -->

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Data Kalkulasi KM</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Data Kalkulasi KM</span>
                    </li>

                </ol>
            </nav>
        </div>

        <style>
            @media (max-width: 576px) {

                #myTables th,
                #myTables td {
                    font-size: 12px;
                    padding: 6px 4px !important;
                }

                #myTables .btn i {
                    font-size: 14px;
                }

                #myTables img.wh-40 {
                    width: 28px;
                    height: 28px;
                }

                #myTables h6.fs-14 {
                    font-size: 12px !important;
                }
            }
        </style>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="container my-4">
                    <h2>Kalkulasi KM</h2>
                    <h4 class="mb-4">Keterangan Tambahan</h4>
                    <div class="container">


                        <!-- Badge tampilan data -->
                        <div class="mb-3">
                            <span class="badge bg-primary">Kurang Hari Kerja: <?= $row['kurang_hari_kerja'] ?? 0 ?></span>
                            <span class="badge bg-success">Penagihan: <?= $row['penagihan'] ?? 0 ?></span>
                            <span class="badge bg-warning text-dark">Seminggu: <?= $row['seminggu'] ?? 0 ?></span>
                        </div>
                        <!-- Tombol Aksi -->
                        <?php if ($row): ?>
                            <a href="edit_label.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">Edit</a>
                            <a href="delete_label.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger"
                                onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        <?php else: ?>
                            <a href="create_label.php" class="btn btn-success">Tambah Baru</a>
                        <?php endif; ?>




                        <form action="simpan_utama.php" method="post" class="mb-4 border p-3">
                            <!-- Data Utama -->
                            <div class="section-title">Data Utama</div>
                            <!-- Pilihan Bulan, Tahun, Kelompok, Hari -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label>Bulan</label>
                                    <select id="bulan" name="bulan" class="form-control">
                                        <option value="">Pilih Bulan</option>
                                        <option value="Januari">Januari</option>
                                        <option value="Februari">Februari</option>
                                        <option value="Maret">Maret</option>
                                        <option value="April">April</option>
                                        <option value="Mei">Mei</option>
                                        <option value="Juni">Juni</option>
                                        <option value="Juli">Juli</option>
                                        <option value="Agustus">Agustus</option>
                                        <option value="September">September</option>
                                        <option value="Oktober">Oktober</option>
                                        <option value="November">November</option>
                                        <option value="Desember">Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Tahun</label>
                                    <select id="tahun" name="tahun" class="form-control">
                                        <option value="">Pilih Tahun</option>
                                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                            <option value="<?= $y ?>"><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Kelompok</label>
                                    <select id="kelompok" name="kelompok" class="form-control">
                                        <option value="">Pilih Kelompok</option>
                                        <?php
                                        // Ambil list kelompok dari database
                                        $result = $conn->query("SELECT DISTINCT kelompok FROM target_ubmi ORDER BY kelompok ASC");
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['kelompok']}'>{$row['kelompok']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h5>Hasil Total Kalkulasi</h5>
                                <table class="table table-bordered text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Total Target</th>
                                            <th>Total CM</th>
                                            <th>Total MB</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="total_t_jadi">0</td>
                                            <td id="total_cm">0</td>
                                            <td id="total_mb">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Hidden input supaya ikut terkirim -->
                            <input type="hidden" name="t_jadi" id="input_t_jadi" value="0">
                            <input type="hidden" name="cm" id="input_cm" value="0">
                            <input type="hidden" name="mb" id="input_mb" value="0">

                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4 py-2">💾 Simpan Data</button>
                            </div>

                        </form>
                        <form action="simpan_semua.php" method="post" class="mb-4 border p-3">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label>ID Kelompok</label>
                                    <select id="id_kelompok" name="id_kelompok" class="form-control" required>
                                        <option value="">Pilih Kelompok</option>
                                        <?php
                                        $result = $conn->query("SELECT DISTINCT id_kelompok FROM rekap_bulan_ini ORDER BY id_kelompok ASC");
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id_kelompok']}'>{$row['id_kelompok']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Bagian Rencana -->
                            <h5 class="fw-bold">Rencana</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label>Rencana</label>
                                    <input type="text" id="total_field_display" class="form-control" readonly>
                                    <input type="hidden" id="total_field" name="total">
                                </div>
                                <div class="col-md-6">
                                    <label>Gagalkan</label>
                                    <input type="number" id="gagalkan" name="gagalkan" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Rencana Jadi</label>
                                    <input type="text" id="rencana_jadi_display" class="form-control" readonly>
                                    <input type="hidden" id="rencana_jadi" name="rencana_jadi">
                                </div>
                                <div class="col-md-3">
                                    <label>Target Program</label>
                                    <input type="number" id="target_program" name="target_program" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Program Murni</label>
                                    <input type="number" id="program_murni" name="program_murni" class="form-control">
                                </div>
                            </div>

                            <!-- Bagian Total Penjumlahan -->
                            <h5 class="fw-bold">Total Penjumlahan</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label>Target Kalkulasi</label>
                                    <input type="number" id="target_kalkulasi" name="target_kalkulasi" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Target</label>
                                    <input type="number" id="jumlah_target" name="jumlah_target" class="form-control" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label>Pelunasan</label>
                                    <input type="text" id="pelunasan_display" class="form-control" readonly>
                                    <input type="hidden" id="pelunasan" name="pelunasan">
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Pelunasan</label>
                                    <input type="text" id="jumlah_pelunasan_display" class="form-control" readonly>
                                    <input type="hidden" id="jumlah_pelunasan" name="jumlah_pelunasan">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label>Baru</label>
                                    <input type="number" id="baru_input" name="baru" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Baru</label>
                                    <input type="text" id="jumlah_baru_display" class="form-control" readonly>
                                    <input type="hidden" id="jumlah_baru" name="jumlah_baru">
                                </div>
                                <div class="col-md-3">
                                    <label>Storting JL</label>
                                    <input type="number" id="storting_jl" name="storting_jl" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Storting JL</label>
                                    <input type="text" id="jumlah_storting_jl_display" class="form-control" readonly>
                                    <input type="hidden" id="jumlah_storting_jl" name="jumlah_storting_jl">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label>Storting JD</label>
                                    <input type="number" id="storting_jd" name="storting_jd" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Storting JD</label>
                                    <input type="text" id="jumlah_storting_jd_display" class="form-control" readonly>
                                    <input type="hidden" id="jumlah_storting_jd" name="jumlah_storting_jd">
                                </div>
                                <div class="col-md-3">
                                    <label>Other</label>
                                    <input type="number" id="other" name="other" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Jumlah Other</label>
                                    <input type="text" id="jumlah_other_display" class="form-control" readonly>
                                    <input type="hidden" id="jumlah_other" name="jumlah_other">
                                </div>
                            </div>

                            <!-- Bagian Index -->
                            <h5 class="fw-bold">Index</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-2"><label>T. Storting 100%</label><input type="number" name="t_storting_100" id="t_storting_100" class="form-control" readonly></div>
                                <div class="col-md-2"><label>± 100%</label><input type="number" name="plus_minus_100" id="plus_minus_100" class="form-control" readonly></div>
                                <div class="col-md-2"><label>T. Storting 115%</label><input type="number" name="t_storting_115" id="t_storting_115" class="form-control" readonly></div>
                                <div class="col-md-2"><label>± 115%</label><input type="number" name="plus_minus_115" id="plus_minus_115" class="form-control" readonly></div>
                                <div class="col-md-2"><label>T. Storting 120%</label><input type="number" name="t_storting_120" id="t_storting_120" class="form-control" readonly></div>
                                <div class="col-md-2"><label>± 120%</label><input type="number" name="plus_minus_120" id="plus_minus_120" class="form-control" readonly></div>
                            </div>

                            <!-- Bagian Kekuatan -->
                            <h5 class="fw-bold">Kekuatan</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4"><label>Kekuatan 115%</label><input type="number" name="kekuatan_115" id="kekuatan_115" class="form-control" readonly></div>
                                <div class="col-md-4"><label>Kekuatan 120%</label><input type="number" name="kekuatan_120" id="kekuatan_120" class="form-control" readonly></div>
                                <div class="col-md-4"><label>Kekuatan 125%</label><input type="number" name="kekuatan_125" id="kekuatan_125" class="form-control" readonly></div>
                            </div>

                            <!-- Bagian Program Nilai -->
                            <h5 class="fw-bold">Program Nilai</h5>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label>Program</label>
                                    <input type="number" name="program" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Nilai</label>
                                    <input type="number" name="nilai" class="form-control">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4 py-2">💾 Simpan Semua Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- script File -->

        <script>
            function hitungStorting() {
                let target = parseFloat(document.getElementById("target_program").value) || 0;
                let jumlahJD = parseFloat(document.getElementById("jumlah_storting_jd").value) || 0;
                let jumlahOther = parseFloat(document.getElementById("jumlah_other").value) || 0;

                // Hitung T. Storting
                let t100 = target * 100;
                let t115 = target * 115;
                let t120 = target * 120;

                // Update nilai T. Storting
                document.getElementById("t_storting_100").value = t100;
                document.getElementById("t_storting_115").value = t115;
                document.getElementById("t_storting_120").value = t120;

                // Hitung Plus/Minus
                document.getElementById("plus_minus_100").value = jumlahJD - t100;
                document.getElementById("plus_minus_115").value = jumlahOther - t115;
                document.getElementById("plus_minus_120").value = jumlahOther - t120;
            }

            // Jalankan saat input berubah
            document.querySelectorAll("#target_program, #jumlah_storting_jd, #jumlah_other")
                .forEach(el => el.addEventListener("input", hitungStorting));
        </script>
        <script>
            document.getElementById("storting_jl").addEventListener("keyup", function() {
                let val = this.value || 0;
                document.getElementById("jumlah_storting_jl_display").value = val;
                document.getElementById("jumlah_storting_jl").value = val;
            });

            document.getElementById("storting_jl").addEventListener("change", function() {
                let val = this.value || 0;
                document.getElementById("jumlah_storting_jl_display").value = val;
                document.getElementById("jumlah_storting_jl").value = val;
            });
        </script>
        <script>
            function hitungJumlahStortingJD() {
                // Ambil nilai dari semua field (kalau kosong = 0)
                let jumlah_target = parseFloat(document.getElementById("jumlah_target").value) || 0;
                let jumlah_pelunasan = parseFloat(document.getElementById("jumlah_pelunasan").value) || 0;
                let jumlah_baru = parseFloat(document.getElementById("jumlah_baru").value) || 0;
                let jumlah_storting_jl = parseFloat(document.getElementById("jumlah_storting_jl").value) || 0;
                let storting_jd = parseFloat(document.getElementById("storting_jd")?.value) || 0;
                let other = parseFloat(document.getElementById("other")?.value) || 0;

                // Rumus penjumlahan
                let hasil = jumlah_target + jumlah_pelunasan + jumlah_baru + jumlah_storting_jl + storting_jd + other;

                // Tampilkan ke field readonly dan hidden
                document.getElementById("jumlah_storting_jd_display").value = hasil;
                document.getElementById("jumlah_storting_jd").value = hasil;
                document.getElementById("jumlah_other_display").value = hasil;
                document.getElementById("jumlah_other").value = hasil;

                // --- Kekuatan ---
                document.getElementById("kekuatan_115").value = (hasil * 1.15).toFixed(0);
                document.getElementById("kekuatan_120").value = (hasil * 1.20).toFixed(0);
                document.getElementById("kekuatan_125").value = (hasil * 1.25).toFixed(0);
            }

            // Jalankan otomatis kalau ada perubahan input
            document.addEventListener("input", function(e) {
                if (["baru_input", "storting_jl", "storting_jd", "other"].includes(e.target.id)) {
                    hitungJumlahStortingJD();
                }
            });

            // Jalankan juga saat halaman load (biar nilai awal langsung muncul kalau ada readonly yg sudah terisi)
            window.onload = hitungJumlahStortingJD;
        </script>


        <script>
            // Helper format ribuan pakai titik
            function formatRibuan(angka) {
                angka = Math.floor(Number(angka) || 0);
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            document.getElementById('baru_input').addEventListener('input', function() {
                const baru = parseFloat(this.value) || 0;

                // Ambil penagihan via AJAX setiap kali input berubah
                fetch("get_penagihan.php")
                    .then(res => res.text())
                    .then(penagihan => {
                        penagihan = parseFloat(penagihan) || 0;
                        const jumlahBaru = baru * 0.13 * penagihan;
                        document.getElementById('jumlah_baru_display').value = formatRibuan(jumlahBaru);
                        document.getElementById('jumlah_baru').value = Math.floor(jumlahBaru);
                    })
                    .catch(err => console.error("Error fetch penagihan:", err));

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
        <script>
            document.getElementById('id_kelompok').addEventListener('change', function() {
                const id_kelompok = this.value;

                if (id_kelompok) {
                    fetch(`get_total.php?id_kelompok=${id_kelompok}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById('total_field').value = data.total;
                            } else {
                                document.getElementById('total_field').value = '';
                            }
                        });
                } else {
                    document.getElementById('total_field').value = '';
                }
            });
        </script>
        <script>
            document.getElementById('kelompok').addEventListener('change', function() {
                let kelompok = this.value;
                if (kelompok) {
                    fetch('get_target_kalkulasi.php?kelompok=' + kelompok)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('target_kalkulasi').value = data.target_kalkulasi || 0;
                            document.getElementById('jumlah_target').value = data.jumlah_target || 0;
                        });
                } else {
                    document.getElementById('target_kalkulasi').value = '';
                    document.getElementById('jumlah_target').value = '';
                }
            });
        </script>

        <script>
            function formatAngka(angka) {
                let tanpaKoma = Math.floor(angka);
                return tanpaKoma.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function loadTotalKalkulasi() {
                const bulan = document.getElementById('bulan').value;
                const tahun = document.getElementById('tahun').value;
                const kelompok = document.getElementById('kelompok').value;

                if (bulan && tahun && kelompok) {
                    fetch(`get_total_kalkulasi.php?bulan=${bulan}&tahun=${tahun}&kelompok=${encodeURIComponent(kelompok)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                // update tampilan tabel
                                document.getElementById('total_t_jadi').textContent = formatAngka(data.total_t_jadi);
                                document.getElementById('total_cm').textContent = formatAngka(data.total_cm);
                                document.getElementById('total_mb').textContent = formatAngka(data.total_mb);

                                // update hidden input supaya ikut terkirim saat submit
                                document.getElementById('input_t_jadi').value = data.total_t_jadi;
                                document.getElementById('input_cm').value = data.total_cm;
                                document.getElementById('input_mb').value = data.total_mb;
                            } else {
                                document.getElementById('total_t_jadi').textContent = "0";
                                document.getElementById('total_cm').textContent = "0";
                                document.getElementById('total_mb').textContent = "0";

                                document.getElementById('input_t_jadi').value = 0;
                                document.getElementById('input_cm').value = 0;
                                document.getElementById('input_mb').value = 0;
                            }
                        })
                        .catch(err => {
                            console.error('Error ambil data total:', err);
                        });
                } else {
                    document.getElementById('total_t_jadi').textContent = "0";
                    document.getElementById('total_cm').textContent = "0";
                    document.getElementById('total_mb').textContent = "0";

                    document.getElementById('input_t_jadi').value = 0;
                    document.getElementById('input_cm').value = 0;
                    document.getElementById('input_mb').value = 0;
                }
            }

            // Jalankan loadTotalKalkulasi setiap dropdown berubah
            document.querySelectorAll('#bulan, #tahun, #kelompok').forEach(el => {
                el.addEventListener('change', loadTotalKalkulasi);
            });
        </script>


        <script>
            // Hilangkan koma
            function floor0(n) {
                return Math.floor(Number(n) || 0);
            }

            // Format ribuan pakai titik
            function formatRibuan(angka) {
                angka = floor0(angka);
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Set nilai display + hidden
            function setValueFormatted(displayId, hiddenId, val) {
                document.getElementById(displayId).value = formatRibuan(val);
                document.getElementById(hiddenId).value = floor0(val);
            }

            function hitungRencanaJadi() {
                const total = parseFloat(document.getElementById('total_field').value) || 0;
                const gagalkan = parseFloat(document.getElementById('gagalkan').value) || 0;
                const hasil = total - gagalkan;
                const rencanaJadi = hasil >= 0 ? floor0(hasil) : 0;

                setValueFormatted('rencana_jadi_display', 'rencana_jadi', rencanaJadi);
            }

            document.getElementById('id_kelompok').addEventListener('change', function() {
                const id_kelompok = this.value;
                if (id_kelompok) {
                    fetch(`get_total.php?id_kelompok=${id_kelompok}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                setValueFormatted('total_field_display', 'total_field', data.total ?? 0);
                            } else {
                                setValueFormatted('total_field_display', 'total_field', 0);
                            }
                            hitungRencanaJadi();
                        });
                } else {
                    setValueFormatted('total_field_display', 'total_field', 0);
                    hitungRencanaJadi();
                }
            });

            document.getElementById('gagalkan').addEventListener('input', hitungRencanaJadi);
        </script>

        <script>
            // Helper: floor ke bilangan bulat (hilangkan angka di belakang koma)
            function floor0(n) {
                return Math.floor(Number(n) || 0);
            }

            // Helper: format ribuan pakai titik
            function formatRibuan(angka) {
                angka = floor0(angka);
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Helper: set nilai di display & hidden input
            function setValueFormatted(displayId, hiddenId, val) {
                const displayEl = document.getElementById(displayId);
                const hiddenEl = document.getElementById(hiddenId) || document.querySelector(`[name="${hiddenId}"]`);

                if (displayEl) displayEl.value = formatRibuan(val);
                if (hiddenEl) hiddenEl.value = floor0(val);
            }

            function hitungRencanaJadi() {
                const total = parseFloat(document.getElementById('total_field').value) || 0;
                const gagalkan = parseFloat(document.getElementById('gagalkan').value) || 0;

                const hasil = total - gagalkan;
                const rencanaJadi = hasil >= 0 ? floor0(hasil) : 0;

                // Rencana Jadi
                setValueFormatted('rencana_jadi_display', 'rencana_jadi', rencanaJadi);

                // Pelunasan mengikuti Rencana Jadi
                setValueFormatted('pelunasan_display', 'pelunasan', rencanaJadi);

                // Jumlah Pelunasan = pelunasan * 13% * 2
                const jumlahPelunasan = rencanaJadi * 0.13 * 2;
                setValueFormatted('jumlah_pelunasan_display', 'jumlah_pelunasan', jumlahPelunasan);
            }

            document.getElementById('id_kelompok').addEventListener('change', function() {
                const id_kelompok = this.value;
                if (id_kelompok) {
                    fetch(`get_total.php?id_kelompok=${id_kelompok}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById('total_field').value = data.total ?? '';
                            } else {
                                document.getElementById('total_field').value = '';
                            }
                            hitungRencanaJadi();
                        });
                } else {
                    document.getElementById('total_field').value = '';
                    hitungRencanaJadi();
                }
            });

            document.getElementById('gagalkan').addEventListener('input', hitungRencanaJadi);
        </script>



        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>