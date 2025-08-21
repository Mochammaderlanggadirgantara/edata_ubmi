<?php
session_start();
header('Content-Type: application/json');
include '../config/koneksi.php';
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

// Fungsi helper untuk angka
function toInt($val)
{
    return intval(str_replace('.', '', $val));
}
function toFloat($val)
{
    return floatval(str_replace(',', '.', str_replace('.', '', $val)));
}

// Ambil data POST
$pendapatan_bulan = $_POST['pendapatan_bulan'] ?? '';
$gaji_bulan       = $_POST['gaji_bulan'] ?? '';
$setoran          = toInt($_POST['setoran'] ?? 0);
$detailData       = isset($_POST['detailData']) ? json_decode($_POST['detailData'], true) : [];

if (!$pendapatan_bulan || !is_array($detailData)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit();
}

// Cek apakah sudah ada turpas_master untuk pendapatan_bulan ini
$stmt = $conn->prepare("SELECT id_master FROM turpas_master WHERE pendapatan_bulan = ?");
$stmt->bind_param("s", $pendapatan_bulan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_master = $row['id_master'];

    // Update juga gaji_bulan supaya konsisten
    $stmt_update = $conn->prepare("UPDATE turpas_master SET pendapatan_bulan = ?, gaji_bulan = ?, setoran = ? WHERE id_master = ?");
    $stmt_update->bind_param("ssii", $pendapatan_bulan, $gaji_bulan, $setoran, $id_master);
    $stmt_update->execute();
    $stmt_update->close();
} else {
    $stmt_insert = $conn->prepare("INSERT INTO turpas_master (pendapatan_bulan, gaji_bulan, setoran) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("ssi", $pendapatan_bulan, $gaji_bulan, $setoran);
    if (!$stmt_insert->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data master']);
        exit();
    }
    $id_master = $stmt_insert->insert_id;
    $stmt_insert->close();
}


// Simpan detail data
foreach ($detailData as $detail) {
    $id_user    = intval($detail['id_user']);
    $drop_val = toInt($detail['drop_val']);
    $storting = toInt($detail['storting']);
    $persen     = toFloat($detail['persen']);
    $gaji     = toInt($detail['gaji']);
    $cm       = toInt($detail['cm']);
    $mb       = toInt($detail['mb']);
    $ml       = toInt($detail['ml']);
    $goro     = toInt($detail['goro']);
    $um       = toInt($detail['um']);
    $bon_prive = toInt($detail['bon_prive']);
    $beban    = toInt($detail['beban']);
    $wajib    = toInt($detail['wajib']);
    $sukarela = toInt($detail['sukarela']);
    $absensi  = toInt($detail['absensi']);
    $lain_lain = toInt($detail['lain_lain']);

    $stmt_check = $conn->prepare("SELECT id_detail FROM turpas_detail WHERE id_master = ? AND id_user = ?");
    $stmt_check->bind_param("ii", $id_master, $id_user);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $row_check = $result_check->fetch_assoc();
        $id_detail = $row_check['id_detail'];

        $stmt_update = $conn->prepare("UPDATE turpas_detail 
            SET drop_val=?, storting=?, persen=?, gaji=?, cm=?, mb=?, ml=?, goro=?, um=?, bon_prive=?, beban=?, wajib=?, sukarela=?, absensi=?, lain_lain=? 
            WHERE id_detail=?");
        $stmt_update->bind_param(
            "iidiiiiiiiiiiiii",
            $drop_val,
            $storting,
            $persen,
            $gaji,
            $cm,
            $mb,
            $ml,
            $goro,
            $um,
            $bon_prive,
            $beban,
            $wajib,
            $sukarela,
            $absensi,
            $lain_lain,
            $id_detail
        );
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO turpas_detail 
            (id_master, id_user, drop_val, storting, persen, gaji, cm, mb, ml, goro, um, bon_prive, beban, wajib, sukarela, absensi, lain_lain) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param(
            "iiidiiiiiiiiiiiii",
            $id_master,
            $id_user,
            $drop_val,
            $storting,
            $persen,
            $gaji,
            $cm,
            $mb,
            $ml,
            $goro,
            $um,
            $bon_prive,
            $beban,
            $wajib,
            $sukarela,
            $absensi,
            $lain_lain
        );
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt_check->close();
}

echo json_encode(['status' => 'success']);
