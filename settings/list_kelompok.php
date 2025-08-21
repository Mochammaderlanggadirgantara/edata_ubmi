<?php
include '../config/koneksi.php';

$query = "SELECT * FROM kelompok_mantri ORDER BY nama_kelompok ASC";
$result = mysqli_query($conn, $query);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<h4 class="text-center fw-bold mb-4">DAFTAR KELOMPOK MANTRI</h4>

<div class="row mb-3">
    <div class="col-md-6">
        <input type="text" id="search-input" class="form-control" placeholder="Cari nama kelompok...">
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahKelompokModal">+ Tambah Kelompok</button>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped text-center">
        <thead class="table-secondary">
            <tr>
                <th>No</th>
                <th>Nama Kelompok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="kelompok-table-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php $no = 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_kelompok']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit-kelompok"
                                data-id="<?= $row['id'] ?>"
                                data-nama="<?= htmlspecialchars($row['nama_kelompok']) ?>">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger btn-hapus-kelompok" data-id="<?= $row['id'] ?>">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Belum ada data kelompok.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahKelompokModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-tambah-kelompok">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Nama Kelompok</label>
                    <input type="text" name="nama_kelompok" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Tambah</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editKelompokModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-edit-kelompok">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelompok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id-kelompok">
                    <label>Nama Kelompok</label>
                    <input type="text" name="nama_kelompok" id="edit-nama-kelompok" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        // 🔍 Filter pencarian
        $('#search-input').on('input', function() {
            const keyword = $(this).val().toLowerCase();
            $('#kelompok-table-body tr').each(function() {
                const nama = $(this).find('td:eq(1)').text().toLowerCase();
                $(this).toggle(nama.includes(keyword));
            });
        });

        // ➕ Tambah kelompok AJAX
        $('#form-tambah-kelompok').submit(function(e) {
            e.preventDefault();
            $.post('settings/tambah_kelompok.php', $(this).serialize(), function(res) {
                const data = JSON.parse(res);
                if (data.status === 'success') {
                    loadKelompokTable();
                    $('#form-tambah-kelompok')[0].reset();
                    bootstrap.Modal.getInstance($('#tambahKelompokModal')[0]).hide();
                } else {
                    alert(data.msg);
                }
            });
        });

        // 📝 Edit - tampilkan modal
        $(document).on('click', '.btn-edit-kelompok', function() {
            $('#edit-id-kelompok').val($(this).data('id'));
            $('#edit-nama-kelompok').val($(this).data('nama'));
            new bootstrap.Modal(document.getElementById('editKelompokModal')).show();
        });

        // 📝 Edit - kirim data
        $('#form-edit-kelompok').submit(function(e) {
            e.preventDefault();
            $.post('settings/edit_kelompok.php', $(this).serialize(), function(res) {
                const data = JSON.parse(res);
                if (data.status === 'success') {
                    loadKelompokTable();
                    bootstrap.Modal.getInstance($('#editKelompokModal')[0]).hide();
                } else {
                    alert(data.msg);
                }
            });
        });

        // ❌ Hapus kelompok
        $(document).on('click', '.btn-hapus-kelompok', function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            if (confirm('Yakin ingin menghapus kelompok ini?')) {
                $.post('settings/hapus_kelompok.php', {
                    id
                }, function(res) {
                    const data = JSON.parse(res);
                    if (data.status === 'success') {
                        row.fadeOut(400, () => {
                            row.remove();
                            renumberTable();
                        });
                    } else {
                        alert(data.msg);
                    }
                });
            }
        });

        // 🔄 Muat ulang tabel kelompok
        function loadKelompokTable() {
            $.get('settings/get_kelompok_data.php', function(html) {
                $('#kelompok-table-body').html(html);
                renumberTable();
            });
        }

        // 🔢 Urutkan ulang nomor
        function renumberTable() {
            $('#kelompok-table-body tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
        }
    });
</script>