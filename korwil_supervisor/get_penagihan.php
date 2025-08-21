<?php
include '../config/koneksi.php';

// Ambil data penagihan dari tabel label_setting
$result = mysqli_query($conn, "SELECT penagihan FROM label_setting LIMIT 1");
$row = mysqli_fetch_assoc($result);

echo $row ? $row['penagihan'] : 0;
