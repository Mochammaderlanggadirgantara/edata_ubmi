<?php
include '../config/koneksi.php';

$id = $_GET['id'] ?? 0;
$data = mysqli_query($conn, "SELECT * FROM drop_baru WHERE id=$id");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    die("Data tidak ditemukan!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelompok   = $_POST['kelompok'];
    $drop_baru  = str_replace('.', '', $_POST['drop_baru']);
    $bulan      = $_POST['bulan']; // simpan nama bulan
    $data_arr   = [];
    $jumlah     = 0;

    // ambil data1 - data12
    for ($i = 1; $i <= 12; $i++) {
        $data_arr[$i] = str_replace('.', '', $_POST["data$i"]);
        $jumlah += $data_arr[$i];
    }

    $sisa_baru = $drop_baru - $jumlah;
    $catatan   = $_POST['catatan'];

    // build query update
    $sql = "UPDATE drop_baru SET 
                kelompok='$kelompok',
                drop_baru='$drop_baru',
                bulan='$bulan',";
    for ($i = 1; $i <= 12; $i++) {
        $sql .= "data$i='" . $data_arr[$i] . "',";
    }
    $sql .= " jumlah='$jumlah', 
              sisa_baru='$sisa_baru',
              catatan='$catatan'
            WHERE id='$id'";

    mysqli_query($conn, $sql);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
    <h3 class="mb-3">Edit Data Kelompok</h3>
    <form method="post">
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $row['kelompok'] ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Bulan</label>
            <select name="bulan" class="form-control" required>
                <?php
                $bulanList = [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember"
                ];
                foreach ($bulanList as $bln): ?>
                    <option value="<?= $bln ?>" <?= $bln == $row['bulan'] ? 'selected' : '' ?>><?= $bln ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Drop Baru</label>
            <input type="text" name="drop_baru" class="form-control" value="<?= $row['drop_baru'] ?>" required>
        </div>

        <div class="row">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <div class="col-md-3 mb-3">
                    <label>Data <?= $i ?></label>
                    <input type="text" name="data<?= $i ?>" class="form-control" value="<?= $row["data$i"] ?>">
                </div>
            <?php endfor; ?>
        </div>

        <div class="mb-3">
            <label>Catatan</label>
            <input type="text" name="catatan" class="form-control" value="<?= $row['catatan'] ?>">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="../korwil_supervisor/drop_baru.php" class="btn btn-secondary">Kembali</a>
    </form>

    <script>
        // Fungsi format angka ribuan
        function formatRibuan(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Pasang listener ke semua input angka
        document.querySelectorAll('input[type="text"]').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/\./g, ""); // hapus titik lama
                if (!isNaN(value) && value !== "") {
                    this.value = formatRibuan(value);
                } else {
                    this.value = "";
                }
            });
        });
    </script>

</body>

</html>