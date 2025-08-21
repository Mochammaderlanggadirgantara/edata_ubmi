<?php
include '../config/koneksi.php';
session_start();

$id_kelompok = $_SESSION['id_kelompok'];
$id_cabang   = $_SESSION['id_cabang']; // Pastikan session id_cabang ada

$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];

// Hitung total mingguan
$totalQuery = $conn->prepare("SELECT 
    SUM(pinjaman) AS total_pinjaman, 
    SUM(sisa_saldo) AS total_sisa_saldo, 
    SUM(selesai) AS total_selesai 
FROM catatan_mantri 
WHERE id_kelompok = ? AND id_cabang = ?");
$totalQuery->bind_param("ii", $id_kelompok, $id_cabang); // 2 parameter
$totalQuery->execute();
$total = $totalQuery->get_result()->fetch_assoc();
?>

<style>
    /* --- Responsive Table untuk Mobile --- */
    @media (max-width: 768px) {
        table thead {
            display: none;
            /* Hilangkan header di layar kecil */
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
            font-size: 14px;
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

    /* Highlight untuk angka positif & negatif */
    .minplus.positif {
        color: #0d6efd;
        font-weight: bold;
    }

    .minplus.negatif {
        color: #dc3545;
        font-weight: bold;
    }

    /* Smooth transition */
    .minggu-tabel {
        transition: all 0.3s ease;
    }

    /* Card styling biar modern */
    .card {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-header {
        font-size: 15px;
        font-weight: 600;
        padding: 0.75rem 1rem;
    }

    .table {
        margin-bottom: 0;
    }

    /* Hover effect pada baris tabel */
    .table tbody tr:hover {
        background: #f8f9fa;
        transition: 0.2s;
    }

    /* Modal lebih elegan */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: none;
        padding-bottom: 0.5rem;
    }

    .modal-footer {
        border-top: none;
        padding-top: 0.5rem;
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


    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Data berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="main-content-container container-fluid px-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h3 class="fw-bold">📒 Catatan Mantri</h3>
        </div>

        <div class="row g-3">
            <?php foreach ($hariList as $hari): ?>
                <div class="col-12 col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span><?= $hari ?></span>
                            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah" data-hari="<?= $hari ?>">
                                ➕ Tambah
                            </button>
                        </div>
                        <div class="card-body table-responsive p-2">
                            <table class="table table-bordered table-striped table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Urutan</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Pinjaman</th>
                                        <th>Sisa Saldo</th>
                                        <th>Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("SELECT id, urutan, nama, alamat, pinjaman, sisa_saldo, selesai 
                                                        FROM catatan_mantri 
                                                        WHERE hari = ? AND id_kelompok = ? 
                                                        ORDER BY urutan ASC");
                                    $stmt->bind_param("si", $hari, $id_kelompok);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                    ?>
                                            <tr>
                                                <td data-label="Urutan"><?= $row['urutan'] ?></td>
                                                <td data-label="Nama"><?= htmlspecialchars($row['nama']) ?></td>
                                                <td data-label="Alamat"><?= htmlspecialchars($row['alamat']) ?></td>
                                                <td data-label="Pinjaman"><?= number_format($row['pinjaman'], 0, ',', '.') ?></td>
                                                <td data-label="Sisa Saldo"><?= number_format($row['sisa_saldo'], 0, ',', '.') ?></td>
                                                <td data-label="Selesai"><?= number_format($row['selesai'], 0, ',', '.') ?></td>
                                                <td data-label="Aksi">
                                                    <button class="btn btn-warning btn-sm btn-edit mb-1"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-urutan="<?= $row['urutan'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                                        data-alamat="<?= htmlspecialchars($row['alamat']) ?>"
                                                        data-pinjaman="<?= $row['pinjaman'] ?>"
                                                        data-sisa_saldo="<?= $row['sisa_saldo'] ?>"
                                                        data-selesai="<?= $row['selesai'] ?>"
                                                        data-hari="<?= $hari ?>">
                                                        ✏️ Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-hapus">🗑 Hapus</button>
                                                </td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Data tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Card Total Mingguan -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span>Total Mingguan</span>
                        <button class="btn btn-light btn-sm text-danger" id="btnBersihkan">🧹 Bersihkan Catatan</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Total Pinjaman</th>
                                    <th>Total Sisa Saldo</th>
                                    <th>Total Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= number_format($total['total_pinjaman'], 0, ',', '.') ?></td>
                                    <td><?= number_format($total['total_sisa_saldo'], 0, ',', '.') ?></td>
                                    <td><?= number_format($total['total_selesai'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        function inisialisasiEventCatatan() {
            // === FITUR EDIT ===
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', () => {
                    const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));
                    document.getElementById('editId').value = button.dataset.id;
                    document.getElementById('editHari').value = button.dataset.hari;
                    document.getElementById('editUrutan').value = button.dataset.urutan;
                    document.getElementById('editNama').value = button.dataset.nama;
                    document.getElementById('editAlamat').value = button.dataset.alamat;
                    document.getElementById('editPinjaman').value = button.dataset.pinjaman;
                    document.getElementById('editSisaSaldo').value = button.dataset.sisa_saldo;
                    document.getElementById('editSelesai').value = button.dataset.selesai;
                    modalEdit.show();
                });
            });

            document.querySelectorAll('.btn-hapus').forEach(button => {
                button.addEventListener('click', () => {
                    if (confirm('Yakin ingin menghapus data ini?')) {
                        fetch('mantri/hapus_catatan.php?id=' + button.dataset.id, {
                            method: 'GET'
                        }).then(res => res.text()).then(res => {
                            if (res.trim() === 'sukses') {
                                fetch('mantri/catatan.php?success=1')
                                    .then(r => r.text())
                                    .then(html => {
                                        document.getElementById('content-container').innerHTML = html;
                                        inisialisasiEventCatatan(); // aktifkan ulang
                                    });
                            } else {
                                alert('Gagal hapus: ' + res);
                            }
                        });
                    }
                });
            });

            const modalTambah = document.getElementById('modalTambah');
            if (modalTambah) {
                modalTambah.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const hari = button.getAttribute('data-hari') || '';
                    modalTambah.querySelector('#inputHari').value = hari;
                    modalTambah.querySelector('#modalTambahLabel').textContent = 'Tambah Catatan - ' + hari;
                    modalTambah.querySelector('#keteranganHari strong').textContent = hari;

                    // Ambil urutan otomatis
                    fetch('mantri/get_urutan_catatan.php?hari=' + encodeURIComponent(hari))
                        .then(res => res.text())
                        .then(data => {
                            modalTambah.querySelector('[name="urutan"]').value = data;
                        });
                });

                modalTambah.addEventListener('hidden.bs.modal', function() {
                    this.querySelector('form').reset();
                });

                document.getElementById('formTambah').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = e.target;
                    const formData = new FormData(form);

                    fetch('mantri/tambah_catatan.php', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.text()).then(response => {
                        const modal = bootstrap.Modal.getInstance(modalTambah);
                        modal.hide();

                        fetch('mantri/catatan.php?success=1')
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('content-container').innerHTML = html;
                                inisialisasiEventCatatan(); // Aktifkan ulang semua event
                            });
                    }).catch(err => {
                        alert('Gagal menyimpan data.');
                        console.error(err);
                    });
                });
            }

            document.getElementById('formEdit').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);

                fetch('mantri/edit_catatan.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEdit'));
                        modal.hide();

                        // Reload konten catatan.php setelah edit
                        fetch('mantri/catatan.php?success=1')
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('content-container').innerHTML = html;
                                inisialisasiEventCatatan(); // Aktifkan ulang event
                            });
                    })
                    .catch(err => {
                        alert('Gagal menyimpan perubahan.');
                        console.error(err);
                    });
            });

            document.getElementById('btnBersihkan').addEventListener('click', function() {
                if (confirm('Yakin ingin menghapus semua data catatan mantri?')) {
                    fetch('mantri/bersihkan_catatan.php', {
                            method: 'GET'
                        })
                        .then(res => res.text())
                        .then(response => {
                            if (response.trim() === 'sukses') {
                                // Reload ulang halaman catatan
                                fetch('mantri/catatan.php?success=1')
                                    .then(res => res.text())
                                    .then(html => {
                                        document.getElementById('content-container').innerHTML = html;
                                        inisialisasiEventCatatan(); // Aktifkan ulang event
                                    });
                            } else {
                                alert('Gagal menghapus semua data:\n' + response);
                            }
                        })
                        .catch(err => {
                            alert('Terjadi kesalahan koneksi.');
                            console.error(err);
                        });
                }
            });

        }

        // Aktifkan event awal saat pertama kali dimuat
        inisialisasiEventCatatan();
    </script>
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