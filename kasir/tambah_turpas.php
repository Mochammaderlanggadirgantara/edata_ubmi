<?php
session_start();
header('Content-Type: application/json');
require '../config/koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

$id_cabang = $_SESSION['id_cabang']; // 🔹 patokan id_cabang

// Helper konversi angka format ribuan ke int
function toInt($val)
{
    return intval(str_replace('.', '', $val));
}

function toFloat($val)
{
    if ($val === null || $val === '') return 0.0;

    $val = trim($val);

    if (strpos($val, ',') !== false) {
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
    } else {
        if (substr_count($val, '.') > 1) {
            $parts = explode('.', $val);
            $last = array_pop($parts);
            $val = implode('', $parts) . '.' . $last;
        }
    }

    return floatval($val);
}

$pendapatan_bulan = $_POST['pendapatan_bulan'] ?? '';
$gaji_bulan       = $_POST['gaji_bulan'] ?? null;
$setoran          = toInt($_POST['setoran'] ?? 0);
$detailData       = isset($_POST['detailData']) ? json_decode($_POST['detailData'], true) : [];

// Validasi format tanggal
if (!$pendapatan_bulan || !is_array($detailData)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit();
}
if (preg_match('/^\d{4}-\d{2}$/', $pendapatan_bulan)) {
    $pendapatan_bulan .= '-01';
}
if ($gaji_bulan && preg_match('/^\d{4}-\d{2}$/', $gaji_bulan)) {
    $gaji_bulan .= '-01';
}

$conn->begin_transaction();

try {
    // 🔹 Cek apakah master sudah ada untuk cabang ini
    $stmtCheck = $conn->prepare("SELECT id_master 
                                 FROM turpas_master 
                                 WHERE pendapatan_bulan = ? AND id_cabang = '$id_cabang'");
    $stmtCheck->bind_param("si", $pendapatan_bulan, $id_cabang);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck->num_rows > 0) {
        $rowMaster = $resCheck->fetch_assoc();
        $id_master = $rowMaster['id_master'];

        // Update master
        $stmtUpdateMaster = $conn->prepare("UPDATE turpas_master 
                                            SET gaji_bulan = ?, setoran = ? 
                                            WHERE id_master = ? AND id_cabang = ?");
        $stmtUpdateMaster->bind_param("siii", $gaji_bulan, $setoran, $id_master, $id_cabang);
        if (!$stmtUpdateMaster->execute()) {
            throw new Exception("Gagal update master: " . $stmtUpdateMaster->error);
        }
    } else {
        // Insert master baru
        $stmtInsertMaster = $conn->prepare("INSERT INTO turpas_master 
                                            (pendapatan_bulan, gaji_bulan, setoran, id_cabang) 
                                            VALUES (?, ?, ?, ?)");
        $stmtInsertMaster->bind_param("ssii", $pendapatan_bulan, $gaji_bulan, $setoran, $id_cabang);
        if (!$stmtInsertMaster->execute()) {
            throw new Exception("Gagal insert master: " . $stmtInsertMaster->error);
        }
        $id_master = $stmtInsertMaster->insert_id;
    }

    $debug = [];

    // Loop simpan detail
    foreach ($detailData as $i => $detail) {
        $id_detail  = $detail['id_detail'] ?? null;
        $id_user    = intval($detail['id_user'] ?? 0);

        if ($id_user <= 0) {
            throw new Exception("Baris ke-" . ($i + 1) . ": ID user kosong/tidak valid");
        }

        $debug[] = [
            'baris' => $i + 1,
            'detail_mentah' => $detail,
            'persen_setelah_toFloat' => toFloat($detail['persen'] ?? 0)
        ];

        if (!$id_detail) {
            $stmtExist = $conn->prepare("SELECT id_detail 
                                         FROM turpas_detail 
                                         WHERE id_master=? AND id_user=? AND id_cabang=?");
            $stmtExist->bind_param("iii", $id_master, $id_user, $id_cabang);
            $stmtExist->execute();
            $resExist = $stmtExist->get_result();
            if ($resExist->num_rows > 0) {
                $id_detail = $resExist->fetch_assoc()['id_detail'];
            }
        }

        $drop_val   = toInt($detail['drop_val'] ?? 0);
        $storting   = toInt($detail['storting'] ?? 0);
        $persen     = toFloat($detail['persen'] ?? 0);
        $gaji       = toInt($detail['gaji'] ?? 0);
        $cm         = toInt($detail['cm'] ?? 0);
        $mb         = toInt($detail['mb'] ?? 0);
        $ml         = toInt($detail['ml'] ?? 0);
        $goro       = toInt($detail['goro'] ?? 0);
        $um         = toInt($detail['um'] ?? 0);
        $bon_prive  = toInt($detail['bon_prive'] ?? 0);
        $beban      = toInt($detail['beban'] ?? 0);
        $wajib      = toInt($detail['wajib'] ?? 0);
        $sukarela   = toInt($detail['sukarela'] ?? 0);
        $absensi    = toInt($detail['absensi'] ?? 0);
        $lain_lain  = toInt($detail['lain_lain'] ?? 0);

        if ($id_detail) {
            // Update detail
            $stmtDetail = $conn->prepare("
                UPDATE turpas_detail 
                SET drop_val=?, storting=?, persen=?, gaji=?, cm=?, mb=?, ml=?, goro=?, um=?, bon_prive=?, beban=?, wajib=?, sukarela=?, absensi=?, lain_lain=? 
                WHERE id_detail=? AND id_cabang=?");
            $stmtDetail->bind_param(
                "iidiiiiiiiiiiiiii",
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
                $id_detail,
                $id_cabang
            );
            if (!$stmtDetail->execute()) {
                throw new Exception("Gagal update detail baris " . ($i + 1) . ": " . $stmtDetail->error);
            }
        } else {
            // Insert detail
            $stmtDetail = $conn->prepare("
                INSERT INTO turpas_detail 
                (id_master, id_user, drop_val, storting, persen, gaji, cm, mb, ml, goro, um, bon_prive, beban, wajib, sukarela, absensi, lain_lain, id_cabang) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtDetail->bind_param(
                "iiiidiiiiiiiiiiiii",
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
                $lain_lain,
                $id_cabang
            );
            if (!$stmtDetail->execute()) {
                throw new Exception("Gagal insert detail baris " . ($i + 1) . ": " . $stmtDetail->error);
            }
        }
    }

    $conn->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil disimpan',
        'debug' => $debug
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
