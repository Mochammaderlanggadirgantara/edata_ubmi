
<?php
include '../config/koneksi.php';

$stmt = $conn->prepare("
    INSERT INTO database_rencana_kalkulasi
    (id_kelompok, total, gagalkan, rencana_jadi, target_program, program_murni)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiiiii",
    $_POST['id_kelompok'],
    $_POST['total'],
    $_POST['gagalkan'],
    $_POST['rencana_jadi'],
    $_POST['target_program'],
    $_POST['program_murni']
);

if ($stmt->execute()) {
    echo "✅ Data rencana berhasil disimpan";
} else {
    echo "❌ Gagal menyimpan data rencana: " . $conn->error;
}
