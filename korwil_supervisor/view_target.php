<!-- view_target.php -->
<?php
session_start();
include '../config/koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM target_ubmi ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Target UBMI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Target UBMI</h4>
    <a href="create_target.php" class="btn btn-primary">+ Tambah Data</a>
  </div>
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Kelompok</th>
        <th>Hari</th>
        <th>Minggu</th>
        <th>Target</th>
        <th>CM</th>
        <th>MB</th>
        <th>Drop Baru</th>
        <th>T. Masuk</th>
        <th>T. Keluar</th>
        <th>T. Jadi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['bulan']}</td>
                  <td>{$row['tahun']}</td>
                  <td>{$row['kelompok']}</td>
                  <td>{$row['hari']}</td>
                  <td>{$row['minggu_ke']}</td>
                  <td>{$row['target']}</td>
                  <td>{$row['cm']}</td>
                  <td>{$row['mb']}</td>
                  <td>{$row['drop_baru']}</td>
                  <td>{$row['t_masuk']}</td>
                  <td>{$row['t_keluar']}</td>
                  <td>{$row['t_jadi']}</td>
                  <td>
                    <a href='edit_target.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                    <a href='delete_target.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin?')\">Delete</a>
                  </td>
                </tr>";
          $no++;
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
