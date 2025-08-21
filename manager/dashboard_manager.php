<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'manager') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
?>

<h3>Dashboard Manager</h3>
<p>Selamat datang di dashboard, <?= htmlspecialchars($_SESSION['nama_user']) ?>!</p>
<div class="card">
    <div class="card-body">
        <p>Total Pendapatan Hari Ini: Rp 12.000.000</p>
        <p>Total Pengeluaran: Rp 5.000.000</p>
    </div>
</div>