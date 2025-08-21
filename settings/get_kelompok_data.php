<?php
include '../config/koneksi.php';

$query = "SELECT id, nama_kelompok FROM kelompok_mantri ORDER BY nama_kelompok ASC";
$result = mysqli_query($conn, $query);
$no = 1;

if (mysqli_num_rows($result) > 0):
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
                <button class="btn btn-sm btn-danger btn-hapus-kelompok"
                    data-id="<?= $row['id'] ?>">
                    Hapus
                </button>
            </td>
        </tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="3">Belum ada data kelompok.</td>
    </tr>
<?php endif; ?>