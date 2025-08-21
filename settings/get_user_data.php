<?php
include '../config/koneksi.php';

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
$no = 1;

if (mysqli_num_rows($result) > 0):
    while ($row = mysqli_fetch_assoc($result)): ?>
        <tr class='data-row'>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_user']) ?></td>
            <td><?= ucwords($row['jabatan']) ?></td>
            <td><?= $row['tgl_masuk'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['jabatan'] === 'mantri' ? htmlspecialchars($row['nama_kelompok']) : '-' ?></td>
            <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning btn-edit"
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