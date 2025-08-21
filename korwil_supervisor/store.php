<?php
$conn = new mysqli("localhost", "root", "", "evaluasi_mantri");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$klp       = $_POST['klp'];
$kekuatan  = intval($_POST['kekuatan']);
$program   = intval($_POST['program']);
$index_val = floatval($_POST['index_val']);
$drop_val  = intval($_POST['drop_val']);
$storting  = intval($_POST['storting']);
$rencana   = intval($_POST['rencana']);
$baru      = intval($_POST['baru']);
$gagalkan  = intval($_POST['gagalkan']);

// Total per kolom
$total_kekuatan = $kekuatan;
$total_program  = $program;
$total_drop     = $drop_val;
$total_storting = $storting;
$total_rencana  = $rencana;
$total_baru     = $baru;
$total_gagalkan = $gagalkan;

// Hitung index %
$total_index = ($storting != 0 && $program != 0) ? ($storting / $program) * 100 : 0;

$stmt = $conn->prepare("INSERT INTO evaluasi_program_mantri 
    (klp, kekuatan, program, `index`, drop_val, storting, rencana, baru, gagalkan, 
     total_kekuatan, total_program, total_index, total_drop, total_storting, total_rencana, total_baru, total_gagalkan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("siiidiiiiiiiiiiii",
    $klp, $kekuatan, $program, $index_val, $drop_val, $storting, $rencana, $baru, $gagalkan,
    $total_kekuatan, $total_program, $total_index, $total_drop, $total_storting, $total_rencana, $total_baru, $total_gagalkan);

if($stmt->execute()){
    echo "Data berhasil disimpan.";
}else{
    echo "Error: ".$stmt->error;
}

$stmt->close();
$conn->close();
?>
