<?php
include '../config/koneksi.php';
$result = $conn->query("SELECT * FROM kalkulasi_km");
?>
<a href="create_kalkulasi.php">Tambah Data</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Kelompok</th>
        <th>Target Program</th>
        <th>Program Murni</th>
        <th>Jumlah Pelunasan</th>
        <th>Global Jumlah Storting</th>
        <th>Jumlah Other Global</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['kelompok'] ?></td>
            <td><?= intval($row['target_program']) ?></td>
            <td><?= intval($row['program_murni']) ?></td>
            <td><?= intval($row['jumlah_pelunasan']) ?></td>
            <td><?= intval($row['global_jumlah_storting']) ?></td>
            <td><?= intval($row['jumlah_other_global']) ?></td>
            <td>
                <a href="update_kalkulasi.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="delete_kalkulasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>