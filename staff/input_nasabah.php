<?php
session_start();
if (
    !isset($_SESSION['username']) ||
    !in_array($_SESSION['jabatan'], ['staff'])
) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
?>

<h3>Input Data Nasabah</h3>
<p>Selamat datang di dashboard, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</p>