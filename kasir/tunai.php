<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
?>

<h3>Tunai</h3>
<p>Selamat datang di dashboard, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</p>