<?php
session_start();
include '../config/koneksi.php';

$id_kelompok = intval($_SESSION['id_kelompok'] ?? 0);
$id_cabang   = intval($_SESSION['id_cabang'] ?? 0);

// Ambil data terakhir dari program_mantri yang ada relasinya ke analisa_storting
$query = "
SELECT 
    p.*, 
    a.persen1, a.persen2, a.persen3, a.target_program
FROM 
    program_mantri p
INNER JOIN 
    analisa_storting a 
ON 
    p.id = a.program_id
WHERE 
    p.id_kelompok = $id_kelompok
AND p.id_cabang = $id_cabang
ORDER BY 
    p.id DESC
LIMIT 1
";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

if ($data = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'success' => true,
        'dataProgram' => [
            'baru' => $data['baru'],
            'storting_jl' => $data['storting_jl'],
            'storting_jd' => $data['storting_jd'],
            'hari_kerja' => $data['hari_kerja'],
            'penagihan' => $data['penagihan'],
            'minggu' => $data['minggu']
        ],
        'dataAnalisa' => [
            'persen1' => $data['persen1'],
            'persen2' => $data['persen2'],
            'persen3' => $data['persen3'],
            'target_program' => $data['target_program']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
}
