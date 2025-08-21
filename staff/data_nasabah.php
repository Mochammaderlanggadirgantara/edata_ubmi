<?php
session_start();
if (
    !isset($_SESSION['username']) ||
    !in_array($_SESSION['jabatan'], ['kasir', 'staff', 'korwil','mantri'])
) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
?>

<h3>Data Nasabah</h3>
<p>Selamat datang di dashboard, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</p>