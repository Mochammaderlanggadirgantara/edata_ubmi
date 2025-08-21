<?php
include '../config/koneksi.php';

// Ambil semua kelompok mantri
$kelompokQuery = "SELECT id, nama_kelompok FROM kelompok_mantri ORDER BY nama_kelompok ASC";
$kelompokResult = mysqli_query($conn, $kelompokQuery);
$kelompok = [];
while ($row = mysqli_fetch_assoc($kelompokResult)) {
    $kelompok[] = $row;
}

$cabangQuery = "SELECT id_cabang, nama_cabang FROM cabang ORDER BY nama_cabang ASC";
$cabangResult = mysqli_query($conn, $cabangQuery);
$cabang = [];
while ($row = mysqli_fetch_assoc($cabangResult)) {
    $cabang[] = $row;
}

// Ambil data user + kelompok (jika ada)
$query = "
    SELECT 
        u.id_user,
        u.nama_user,
        u.jabatan,
        u.tgl_masuk,
        u.username,
        u.id_kelompok,
        k.nama_kelompok,
        u.id_cabang,
        c.nama_cabang,
        u.status,
        u.tgl_nonaktif
    FROM tuser u
    LEFT JOIN kelompok_mantri k ON u.id_kelompok = k.id
    LEFT JOIN cabang c ON u.id_cabang = c.id_cabang
    ORDER BY u.nama_user ASC
";



$result = mysqli_query($conn, $query);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<h4 class="text-center fw-bold mb-4">DAFTAR USER</h4>

<div class="row mb-3">
    <div class="col-md-3">
        <select id="filter-kelompok" class="form-select">
            <option value="">Semua Kelompok Mantri</option>
            <?php foreach ($kelompok as $k): ?>
                <option value="<?= htmlspecialchars($k['nama_kelompok']) ?>">
                    <?= htmlspecialchars($k['nama_kelompok']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select id="filter-cabang" class="form-select">
            <option value="">Semua Cabang</option>
            <?php foreach ($cabang as $c): ?>
                <option value="<?= htmlspecialchars($c['nama_cabang']) ?>">
                    <?= htmlspecialchars($c['nama_cabang']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select id="filter-jabatan" class="form-select">
            <option value="">Semua Jabatan</option>
            <option value="pengawas">Pengawas</option>
            <option value="pimpinan">Pimpinan</option>
            <option value="kepala mantri">Kepala Mantri</option>
            <option value="kasir">Kasir</option>
            <option value="staff">Staff</option>
            <option value="mantri">Mantri</option>
        </select>
    </div>
    <div class="col-md-2">
        <select id="filter-status" class="form-select">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="tidak aktif">Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-2 mb-3">
        <input type="text" id="search-input" class="form-control" placeholder="Nama atau Username...">
    </div>



    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahModal">+ Tambah User</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-secondary">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Tanggal Masuk</th>
                    <th>Username</th>
                    <th>Kelompok</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr class="data-row">
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= ucwords($row['jabatan']) ?></td>
                            <td><?= $row['tgl_masuk'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= $row['jabatan'] === 'mantri' ? htmlspecialchars($row['nama_kelompok']) : '-' ?></td>
                            <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit me-2"
                                    data-id="<?= $row['id_user'] ?>"
                                    data-nama="<?= htmlspecialchars($row['nama_user']) ?>"
                                    data-jabatan="<?= $row['jabatan'] ?>"
                                    data-tgl="<?= $row['tgl_masuk'] ?>"
                                    data-username="<?= htmlspecialchars($row['username']) ?>"
                                    data-idkelompok="<?= $row['id_kelompok'] ?>"
                                    data-idcabang="<?= $row['id_cabang'] ?>"
                                    data-status="<?= $row['status'] ?>">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_user'] ?>">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                <tr id="no-data-row" style="<?= mysqli_num_rows($result) > 0 ? 'display:none;' : '' ?>">
                    <td colspan="9" class="text-center text-muted">Belum ada data user.</td>
                </tr>

            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-edit-user">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_user" id="edit-id">

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" class="form-control" name="nama_user" id="edit-nama" required>
                        </div>
                        <div class="mb-2">
                            <label>Jabatan</label>
                            <select class="form-control" name="jabatan" id="edit-jabatan">
                                <option value="pengawas">Pengawas</option>
                                <option value="pimpinan">Pimpinan</option>
                                <option value="kepala mantri">Kepala Mantri</option>
                                <option value="kasir">Kasir</option>
                                <option value="staff">Staff</option>
                                <option value="mantri">Mantri</option>
                            </select>
                        </div>
                        <div class="mb-2" id="kelompok-wrapper">
                            <label>Kelompok</label>
                            <select class="form-control" name="id_kelompok" id="edit-kelompok">
                                <option value="">-- Pilih Kelompok --</option>
                                <?php foreach ($kelompok as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= $k['nama_kelompok'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Cabang</label>
                            <select class="form-control" name="id_cabang" id="edit-cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                <?php foreach ($cabang as $c): ?>
                                    <option value="<?= $c['id_cabang'] ?>"><?= $c['nama_cabang'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" id="edit-tgl" required
                                max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-2">
                            <label>Status</label>
                            <select class="form-control" name="status" id="edit-status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class=" mb-2">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" id="edit-username" required>
                        </div>
                        <div class="mb-2">
                            <label>Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" name="password" id="edit-password">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-tambah-user">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" class="form-control" name="nama_user" required>
                        </div>
                        <div class="mb-2">
                            <label>Jabatan</label>
                            <select class="form-control" name="jabatan" id="tambah-jabatan">
                                <option value="pengawas">Pengawas</option>
                                <option value="pimpinan">Pimpinan</option>
                                <option value="kepala mantri">Kepala Mantri</option>
                                <option value="kasir">Kasir</option>
                                <option value="staff">Staff</option>
                                <option value="mantri">Mantri</option>
                            </select>
                        </div>
                        <div class="mb-2" id="tambah-kelompok-wrapper" style="display: none;">
                            <label>Kelompok</label>
                            <select class="form-control" name="id_kelompok" id="tambah-kelompok">
                                <option value="">-- Pilih Kelompok --</option>
                                <?php foreach ($kelompok as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= $k['nama_kelompok'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Cabang</label>
                            <select class="form-control" name="id_cabang" id="tambah-cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                <?php foreach ($cabang as $c): ?>
                                    <option value="<?= $c['id_cabang'] ?>"><?= $c['nama_cabang'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Tanggal Masuk</label>
                            <input type="date" class="form-control" name="tgl_masuk" required max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Tambah</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function filterTable() {
                const kelompok = $('#filter-kelompok').val().toLowerCase();
                const cabang = $('#filter-cabang').val().toLowerCase();
                const status = $('#filter-status').val().toLowerCase();
                const jabatanFilter = $('#filter-jabatan').val().toLowerCase();
                const keyword = $('#search-input').val().toLowerCase();

                let visibleCount = 0;

                $('.data-row').each(function() {
                    const nama = $(this).find('td:eq(1)').text().toLowerCase();
                    const jabatan = $(this).find('td:eq(2)').text().toLowerCase();
                    const username = $(this).find('td:eq(4)').text().toLowerCase();
                    const kolomKelompok = $(this).find('td:eq(5)').text().toLowerCase();
                    const kolomCabang = $(this).find('td:eq(6)').text().toLowerCase();
                    const stat = $(this).find('td:eq(7)').text().toLowerCase();

                    const cocokKelompok = !kelompok || kolomKelompok.includes(kelompok);
                    const cocokCabang = !cabang || kolomCabang.includes(cabang);
                    const cocokStatus = !status || stat === status;
                    const cocokJabatan = !jabatanFilter || jabatan === jabatanFilter;
                    const cocokCari = !keyword || nama.includes(keyword) || username.includes(keyword) || jabatan.includes(keyword);

                    if (cocokKelompok && cocokCabang && cocokStatus && cocokJabatan && cocokCari) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0) {
                    $('#no-data-row').show();
                } else {
                    $('#no-data-row').hide();
                }
            }

            // jalankan filter setiap ada perubahan
            $('#filter-kelompok, #filter-cabang, #filter-status, #filter-jabatan, #search-input')
                .on('input change', filterTable);
        });
    </script>

    <script>
        //Tambah User
        $(document).ready(function() {
            // Tampilkan/ sembunyikan kelompok
            $('#tambah-jabatan').on('change', function() {
                if ($(this).val() === 'mantri') {
                    $('#tambah-kelompok-wrapper').show();
                } else {
                    $('#tambah-kelompok-wrapper').hide();
                    $('#tambah-kelompok').val('');
                }
            });

            // Validasi username tidak boleh mengandung spasi
            $('input[name="username"]').on('input', function() {
                const val = $(this).val();
                if (val.includes(' ')) {
                    alert('Username tidak boleh mengandung spasi!');
                    $(this).val(val.replace(/\s/g, ''));
                }
            });

            // Saat klik submit form
            $('#form-tambah-user').submit(function(e) {
                e.preventDefault();

                const formData = $(this).serializeArray();
                const dataObj = {};
                formData.forEach(field => dataObj[field.name] = field.value);

                if (dataObj.jabatan === 'mantri' && (!dataObj.id_kelompok || dataObj.id_kelompok === '')) {
                    alert('Kelompok wajib dipilih jika jabatan Mantri.');
                    return;
                }

                if (dataObj.username.includes(' ')) {
                    alert('Username tidak boleh mengandung spasi!');
                    return;
                }

                // Validasi cabang
                if (!dataObj.id_cabang || dataObj.id_cabang === '') {
                    alert('Cabang wajib dipilih!');
                    return;
                }


                // AJAX tambah user
                $.ajax({
                    url: 'settings/tambah_user.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        const data = JSON.parse(res);
                        if (data.status === 'success') {
                            // Tambahkan langsung ke tabel tanpa reload
                            loadUserTable();
                            $('#form-tambah-user')[0].reset();
                            $('#tambah-kelompok-wrapper').hide();
                            $('#tambahModal').modal('hide');

                            alert('User berhasil ditambahkan!');
                        } else {
                            alert('Gagal menambahkan user: ' + data.msg);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghubungi server.');
                    }
                });
            });
        });

        function loadUserTable() {
            $.ajax({
                url: 'settings/get_user_data.php', // Buat file ini untuk generate <tbody> tabel
                type: 'GET',
                success: function(html) {
                    $('#user-table-body .data-row').remove();
                    $('#no-data-row').before(html);
                    //$('#user-table-body').html(html); // Ganti seluruh tbody
                    bindEditAndDeleteButtons(); // Re-bind tombol edit & hapus ke data baru

                    refilterTable();
                },
                error: function() {
                    alert('Gagal memuat data user terbaru.');
                }
            });
        }

        //edit dan hapus
        function bindEditAndDeleteButtons() {
            const modal = new bootstrap.Modal(document.getElementById('editModal'));

            $('.btn-edit').click(function() {
                $('#edit-id').val($(this).data('id'));
                $('#edit-nama').val($(this).data('nama'));
                $('#edit-jabatan').val($(this).data('jabatan'));
                $('#edit-tgl').val($(this).data('tgl'));
                $('#edit-username').val($(this).data('username'));
                $('#edit-kelompok').val($(this).data('idkelompok'));
                $('#edit-cabang').val($(this).data('idcabang'));
                $('#edit-password').val('');
                $('#edit-status').val($(this).data('status'));


                if ($(this).data('jabatan') === 'mantri') {
                    $('#kelompok-wrapper').show();
                } else {
                    $('#kelompok-wrapper').hide();
                }

                modal.show();
            });

            $('#edit-jabatan').on('change', function() {
                if ($(this).val() === 'mantri') {
                    $('#kelompok-wrapper').show();
                } else {
                    $('#kelompok-wrapper').hide();
                    $('#edit-kelompok').val('');
                }
            });

            $('#form-edit-user').submit(function(e) {
                e.preventDefault();

                // Validasi klien: jika mantri maka kelompok harus dipilih
                const jabatan = $('#edit-jabatan').val();
                const kelompok = $('#edit-kelompok').val();

                if (jabatan === 'mantri' && (!kelompok || kelompok === '')) {
                    alert('Kelompok wajib dipilih jika jabatan Mantri.');
                    return;
                }

                $.ajax({
                    url: 'settings/edit_user.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        let data = JSON.parse(res);
                        if (data.status === 'success') {

                            // Ambil nilai baru
                            const idUser = $('#edit-id').val();
                            const namaUser = $('#edit-nama').val();
                            const jabatan = $('#edit-jabatan').val();
                            const tglMasuk = $('#edit-tgl').val();
                            const username = $('#edit-username').val();
                            const idKelompok = $('#edit-kelompok').val();
                            const kelompokText = jabatan === 'mantri' ? $('#edit-kelompok option:selected').text() : '-';
                            const idCabang = $('#edit-cabang').val();
                            const cabangText = $('#edit-cabang option:selected').text();
                            const status = $('#edit-status').val();

                            // Temukan baris dan tombol edit yang sesuai
                            const btnEdit = $('.btn-edit[data-id="' + idUser + '"]');
                            const row = btnEdit.closest('tr');

                            // Update isi tabel langsung
                            row.find('td:eq(1)').text(namaUser);
                            row.find('td:eq(2)').text(jabatan.charAt(0).toUpperCase() + jabatan.slice(1));
                            row.find('td:eq(3)').text(tglMasuk);
                            row.find('td:eq(4)').text(username);
                            row.find('td:eq(5)').text(kelompokText);
                            row.find('td:eq(6)').text(cabangText);
                            row.find('td:eq(7)').text(status.charAt(0).toUpperCase() + status.slice(1));


                            // Update atribut tombol edit
                            btnEdit.data('nama', namaUser);
                            btnEdit.data('jabatan', jabatan);
                            btnEdit.data('tgl', tglMasuk);
                            btnEdit.data('username', username);
                            btnEdit.data('idkelompok', idKelompok);
                            btnEdit.data('idcabang', idCabang);
                            btnEdit.data('status', status);


                            // Tutup modal
                            refilterTable();
                            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                        } else {
                            alert('Gagal mengubah data: ' + data.msg);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghubungi server.');
                    }
                });
            });

            $('.btn-hapus').click(function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');

                if (confirm('Yakin ingin menghapus user ini?')) {
                    $.ajax({
                        url: 'settings/hapus_user.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        success: function(res) {
                            const response = JSON.parse(res);
                            if (response.status === 'success') {
                                row.fadeOut(500, function() {
                                    $(this).remove();
                                    renumberTable();
                                    refilterTable();
                                });

                            } else {
                                alert('Gagal menghapus: ' + response.msg);
                            }
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat menghubungi server.');
                        }
                    });
                }
            });
        }

        // mengurutkan angka tabel
        function renumberTable() {
            $('#user-table-body tr').each(function(index) {
                $(this).find('td:eq(0)').text(index + 1);
            });
        }

        //filter ulang
        function refilterTable() {
            if (typeof filterTable === 'function') filterTable();
        }

        $(document).ready(function() {
            bindEditAndDeleteButtons(); // Panggil saat load awal
        });
    </script>