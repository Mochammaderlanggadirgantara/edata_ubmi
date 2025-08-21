<?php
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['id_kelompok'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Kelompok tidak dikenali']);
    exit;
}
$id_cabang = $_SESSION['id_cabang'];
$id_kelompok = intval($_SESSION['id_kelompok']);
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

// Cek apakah data sudah ada untuk minggu dan kelompok ini
$minggu = $data['minggu'];
$cekQuery = "SELECT id FROM program_mantri WHERE minggu = ? AND id_kelompok = ? and id_cabang = ?";
$stmt = $conn->prepare($cekQuery);
$stmt->bind_param("ii", $minggu, $id_kelompok);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Data sudah ada, lakukan update
    $row = $result->fetch_assoc();
    $program_id = $row['id'];

    $query1 = "UPDATE program_mantri SET 
        baru = ?, 
        storting_jl = ?, 
        storting_jd = ?, 
        hari_kerja = ?, 
        penagihan = ?
        WHERE id = ? AND id_kelompok = ? and id_cabang = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param(
        "iiiiiiii",
        $data['baru'],
        $data['storting_jl'],
        $data['storting_jd'],
        $data['hari_kerja'],
        $data['penagihan'],
        $program_id,
        $id_kelompok,
        $id_cabang
    );
    $stmt1->execute();

    $query2 = "UPDATE analisa_storting SET 
        persen1 = ?, 
        persen2 = ?, 
        persen3 = ?, 
        target_program = ?
        WHERE program_id = ? AND id_kelompok = ? and id_cabang = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param(
        "iiiiiii",
        $data['persen1'],
        $data['persen2'],
        $data['persen3'],
        $data['storting_target'],
        $program_id,
        $id_kelompok,
        $id_cabang
    );
    $stmt2->execute();
} else {
    // Data belum ada, lakukan insert
    $query1 = "INSERT INTO program_mantri 
        (baru, storting_jl, storting_jd, hari_kerja, penagihan, minggu, id_kelompok, id_cabang)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param(
        "iiiiiiii",
        $data['baru'],
        $data['storting_jl'],
        $data['storting_jd'],
        $data['hari_kerja'],
        $data['penagihan'],
        $data['minggu'],
        $id_kelompok,
        $id_cabang
    );
    $stmt1->execute();

    // Ambil ID yang baru saja dimasukkan
    $queryCek = "SELECT id FROM program_mantri WHERE id_kelompok = ? ORDER BY id DESC LIMIT 1 and id_cabang = ?";
    $stmtCek = $conn->prepare($queryCek);
    $stmtCek->bind_param("i", $id_kelompok);
    $stmtCek->execute();
    $resultCek = $stmtCek->get_result();
    $program_id = $resultCek->fetch_assoc()['id'];

    $query2 = "INSERT INTO analisa_storting 
        (program_id, id_kelompok, persen1, persen2, persen3, target_program, id_cabang)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param(
        "iiiiiii",
        $program_id,
        $id_kelompok,
        $data['persen1'],
        $data['persen2'],
        $data['persen3'],
        $data['storting_target'],
        $id_cabang
    );
    $stmt2->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
