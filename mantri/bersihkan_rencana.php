<?php
include '../config/koneksi.php';

// Hapus semua data dari rencana dan rekap
mysqli_query($conn, "DELETE FROM rencana_bulan_ini");
mysqli_query($conn, "DELETE FROM rencana_bulan_depan");
mysqli_query($conn, "DELETE FROM rekap_rencana WHERE jenis IN ('bulan_ini', 'bulan_depan') and id_cabang = ?");

echo "berhasil";
