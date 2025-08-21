<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/helpers.php';

if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(403); exit('CSRF invalid'); }

$bulan = (int)($_POST['bulan'] ?? 0);
$tahun = (int)($_POST['tahun'] ?? 0);
$klp   = (int)($_POST['klp'] ?? 0);
$program = to_int($_POST['program'] ?? '0');
$sv      = to_int($_POST['storting_valid'] ?? '0');

if ($bulan<1 || $bulan>12 || $tahun<2000 || $tahun>2100 || $klp<1 || $klp>10) {
  exit('Input tidak valid.');
}

$idx = hitung_index($program, $sv);

$stmt = $mysqli->prepare("INSERT INTO index_bulanan (bulan,tahun,klp,program,storting_valid,idx_akhir) VALUES (?,?,?,?,?,?) 
                          ON DUPLICATE KEY UPDATE program=VALUES(program), storting_valid=VALUES(storting_valid), idx_akhir=VALUES(idx_akhir)");
$stmt->bind_param('iiiidd', $bulan,$tahun,$klp,$program,$sv,$idx);
$stmt->execute();

header("Location: index.php?bulan=$bulan&tahun=$tahun&klp=$klp");
