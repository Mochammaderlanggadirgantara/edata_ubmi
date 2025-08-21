<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2>Data User</h2>
    <a href="add.php" class="btn btn-success mb-3">+ Tambah User</a>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Tanggal Masuk</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $conn->prepare("SELECT * FROM TUser");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>$no</td>
                    <td>{$row['nama_user']}</td>
                    <td>{$row['jabatan']}</td>
                    <td>{$row['tgl_masuk']}</td>
                    <td>{$row['username']}</td>
                    <td>
                        <a href='edit.php?id={$row['id_user']}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='delete.php?id={$row['id_user']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin hapus?');\">Hapus</a>
                    </td>
                  </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</body>

</html>