<?php
header('Content-Type: application/json');
include '../config/koneksi.php';
session_start();

$id_cabang = $_SESSION['id_cabang'] ?? 0;

$target_murni  = $_POST['target_murni'] ?? 0;
$drop_baru     = $_POST['drop_baru'] ?? 0;
$hk            = $_POST['hk'] ?? 0;
$penagihan     = $_POST['penagihan'] ?? 0;
$pelunasan     = $_POST['pelunasan'] ?? 0;
$gaji          = $_POST['gaji'] ?? 0;
$bps           = $_POST['bps'] ?? 0;
$setor         = $_POST['setor'] ?? 0;
$pengembalian  = $_POST['pengembalian'] ?? 0;
$kasakhir      = $_POST['kasakhir'] ?? 0;
$katrolan      = $_POST['katrolan'] ?? 0;

// cek apakah data sudah ada
$cek = $conn->prepare("SELECT id_kalkulasi FROM kalkulasi_kas WHERE id_cabang=?");
$cek->bind_param("i", $id_cabang);
$cek->execute();
$cek->store_result();

if ($cek->num_rows > 0) {
    // UPDATE
    $stmt = $conn->prepare("UPDATE kalkulasi_kas 
        SET target_murni=?, drop_baru=?, hk=?, penagihan=?, pelunasan=?, gaji=?, bps=?, setor=?, pengembalian=?, kasakhir=?, katrolan=? 
        WHERE id_cabang=?");
    $stmt->bind_param(
        "iiiiiiiiiiii",
        $target_murni,
        $drop_baru,
        $hk,
        $penagihan,
        $pelunasan,
        $gaji,
        $bps,
        $setor,
        $pengembalian,
        $kasakhir,
        $katrolan,
        $id_cabang
    );
    $stmt->execute();
    $stmt->close();
} else {
    // INSERT
    $stmt = $conn->prepare("INSERT INTO kalkulasi_kas 
        (id_cabang, target_murni, drop_baru, hk, penagihan, pelunasan, gaji, bps, setor, pengembalian, kasakhir, katrolan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iiiiiiiiiiii",
        $id_cabang,
        $target_murni,
        $drop_baru,
        $hk,
        $penagihan,
        $pelunasan,
        $gaji,
        $bps,
        $setor,
        $pengembalian,
        $kasakhir,
        $katrolan
    );
    $stmt->execute();
    $stmt->close();
}

$cek->close();
$conn->close();

// 🔹 JSON response di akhir
echo json_encode([
    "status" => "success",
    "message" => "Data berhasil disimpan/diupdate!",
    "data" => [
        "target_murni"   => $target_murni,
        "drop_baru"      => $drop_baru,
        "hk"             => $hk,
        "penagihan"      => $penagihan,
        "pelunasan"      => $pelunasan,
        "gaji"           => $gaji,
        "bps"            => $bps,
        "setor"          => $setor,
        "pengembalian"   => $pengembalian,
        "kasakhir"       => $kasakhir,
        "katrolan"       => $katrolan,
    ]
]);
exit;
