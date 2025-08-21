<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

include '../config/koneksi.php';
$_SESSION['id_cabang'] = $row['id_cabang']; // dari tabel users
$id_cabang = $_SESSION['id_cabang'];
$pendapatan_bulan = isset($_GET['pendapatan_bulan']) ? $_GET['pendapatan_bulan'] : date("Y-m");
$pendapatan_tanggal = $pendapatan_bulan . "-01";

// 🔹 Ambil user sesuai aturan
$query = "
    SELECT id_user, nama_user, jabatan, tgl_masuk, status, tgl_nonaktif
    FROM tuser
    WHERE id_cabang = ?
      AND (
            status = 'aktif'
            OR (
                status != 'aktif'
                AND tgl_nonaktif IS NOT NULL
                AND DATE_FORMAT(tgl_nonaktif, '%Y-%m') >= ?
            )
          )
    ORDER BY jabatan, nama_user
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $id_cabang, $pendapatan_bulan);
$stmt->execute();
$result = $stmt->get_result();

$non_mantri = [];
$mantri = [];

while ($row = $result->fetch_assoc()) {
    if (strtolower($row['jabatan']) === 'mantri') {
        $mantri[] = $row;
    } else {
        $non_mantri[] = $row;
    }
}

echo json_encode([
    'status' => 'success',
    'non_mantri' => $non_mantri,
    'mantri' => $mantri
]);
