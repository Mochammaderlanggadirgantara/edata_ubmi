<?php
include '../config/koneksi.php';
session_start();
// dari tabel users
function parseAngka($val)
{
    return (int) str_replace('.', '', $val);
}

$id_kelompok = $_SESSION['id_kelompok'] ?? 0;
$id_cabang = $_SESSION['id_cabang'];
if ($id_kelompok == 0) {
    die("Kelompok tidak valid. Silakan login ulang.");
}

// ============================
// RENCANA BULAN INI
// ============================
for ($i = 1; $i <= 30; $i++) {
    $nomor = $i;
    $ke = mysqli_real_escape_string($conn, $_POST["ke_ini_$i"] ?? '');
    $rencana = parseAngka($_POST["rencana_ini_$i"] ?? '0');
    $hari = mysqli_real_escape_string($conn, ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'][($i - 1) % 6]);

    $sql = "INSERT INTO rencana_bulan_ini (id_kelompok, nomor, ke, hari, rencana, id_cabang)
            VALUES ($id_kelompok, '$nomor', '$ke', '$hari', '$rencana', '$id_cabang')
            ON DUPLICATE KEY UPDATE ke=VALUES(ke), hari=VALUES(hari), rencana=VALUES(rencana)";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

// ============================
// RENCANA BULAN DEPAN
// ============================
for ($i = 1; $i <= 30; $i++) {
    $nomor = $i;
    $ke = mysqli_real_escape_string($conn, $_POST["ke_depan_$i"] ?? '');
    $rencana = parseAngka($_POST["rencana_depan_$i"] ?? '0');
    $potongan = parseAngka($_POST["potongan_$i"] ?? '0');
    $hari = mysqli_real_escape_string($conn, ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'][($i - 1) % 6]);

    $sql = "INSERT INTO rencana_bulan_depan (id_kelompok, nomor, ke, hari, rencana, potongan, id_cabang)
            VALUES ($id_kelompok, '$nomor', '$ke', '$hari', '$rencana', '$potongan', '$id_cabang')
            ON DUPLICATE KEY UPDATE ke=VALUES(ke), hari=VALUES(hari), rencana=VALUES(rencana), potongan=VALUES(potongan)";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
}

// ============================
// REKAP BULAN INI
// ============================
$total_ini = parseAngka($_POST['total_ini'] ?? 0);
$gagal_ini = parseAngka($_POST['gagalkan_ini'] ?? 0);
$jadi_ini = parseAngka($_POST['jadi_ini'] ?? 0);

$sql = "INSERT INTO rekap_bulan_ini (id_kelompok, total, gagalkan, rencana_jadi, id_cabang)
        VALUES ($id_kelompok, '$total_ini', '$gagal_ini', '$jadi_ini', '$id_cabang')
        ON DUPLICATE KEY UPDATE total=VALUES(total), gagalkan=VALUES(gagalkan), rencana_jadi=VALUES(rencana_jadi)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

// ============================
// REKAP BULAN DEPAN
// ============================
$total_depan = parseAngka($_POST['total_depan'] ?? 0);
$total_potongan = parseAngka($_POST['total_potongan'] ?? 0);
$gagal_rencana = parseAngka($_POST['gagalkan_rencana_depan'] ?? 0);
$gagal_potongan = parseAngka($_POST['gagalkan_potongan_depan'] ?? 0);
$jadi_rencana = parseAngka($_POST['rencana_jadi_depan'] ?? 0);
$jadi_potongan = parseAngka($_POST['potongan_jadi_depan'] ?? 0);

$sql = "INSERT INTO rekap_bulan_depan (
            id_kelompok, total_rencana, total_potongan,
            gagalkan_rencana, gagalkan_potongan,
            rencana_jadi, rencana_jadi_potongan, id_cabang
        ) VALUES (
            $id_kelompok, '$total_depan', '$total_potongan',
            '$gagal_rencana', '$gagal_potongan',
            '$jadi_rencana', '$jadi_potongan', '$id_cabang'
        ) ON DUPLICATE KEY UPDATE
            total_rencana=VALUES(total_rencana),
            total_potongan=VALUES(total_potongan),
            gagalkan_rencana=VALUES(gagalkan_rencana),
            gagalkan_potongan=VALUES(gagalkan_potongan),
            rencana_jadi=VALUES(rencana_jadi),
            rencana_jadi_potongan=VALUES(rencana_jadi_potongan)";
mysqli_query($conn, $sql) or die(mysqli_error($conn));

echo "Data berhasil disimpan.";
