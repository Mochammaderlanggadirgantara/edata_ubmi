<?php
include '../config/koneksi.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM kalkulasi_km WHERE id=$id");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelompok = $_POST['kelompok'];
    $target_program = (int)preg_replace('/\D/', '', $_POST['target_program']);
    $program_murni = (int)preg_replace('/\D/', '', $_POST['program_murni']);
    $global_jumlah_storting = (int)preg_replace('/\D/', '', $_POST['global_jumlah_storting']);

    $jumlah_pelunasan = floor($target_program * 0.13);
    $jumlah_other_global = $global_jumlah_storting;

    $stmt = $conn->prepare("UPDATE kalkulasi_km SET kelompok=?, target_program=?, program_murni=?, jumlah_pelunasan=?, global_jumlah_storting=?, jumlah_other_global=? WHERE id=?");
    $stmt->bind_param("siiiiii", $kelompok, $target_program, $program_murni, $jumlah_pelunasan, $global_jumlah_storting, $jumlah_other_global, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: read_kalkulasi.php");
    exit();
}
?>

<form method="POST">
    Kelompok:
    <select name="kelompok">
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="Kelompok <?= $i ?>" <?= $data['kelompok'] == "Kelompok $i" ? 'selected' : '' ?>>Kelompok <?= $i ?></option>
        <?php endfor; ?>
    </select><br>
    Target Program: <input type="number" name="target_program" value="<?= intval($data['target_program']) ?>"><br>
    Program Murni: <input type="number" name="program_murni" value="<?= intval($data['program_murni']) ?>"><br>
    Global Jumlah Storting: <input type="number" name="global_jumlah_storting" value="<?= intval($data['global_jumlah_storting']) ?>"><br>
    <button type="submit">Update</button>
</form>