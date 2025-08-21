<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelompok  = $_POST['kelompok'];
    $bulan     = $_POST['bulan']; // VARCHAR bulan (misalnya "Januari")
    $drop_baru = str_replace('.', '', $_POST['drop_baru']);
    $data      = [];
    $jumlah    = 0;

    // Loop data1..data12
    for ($i = 1; $i <= 12; $i++) {
        $data[$i] = str_replace('.', '', $_POST["data$i"]);
        $jumlah  += $data[$i];
    }

    $sisa_baru = $drop_baru - $jumlah;
    $catatan   = $_POST['catatan'];

    $sql = "INSERT INTO drop_baru (
                kelompok, bulan, drop_baru, 
                " . implode(',', array_map(fn($i) => "data$i", range(1, 12))) . ",
                jumlah, sisa_baru, catatan
            ) VALUES (
                '$kelompok', '$bulan', '$drop_baru', 
                '" . implode("','", $data) . "',
                '$jumlah', '$sisa_baru', '$catatan'
            )";

    mysqli_query($conn, $sql);
    header("Location: ../korwil_supervisor/drop_baru.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-4">
    <h3 class="mb-3">Tambah Data Kelompok</h3>
    <form method="post">
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" class="form-control" required>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Input Bulan -->
        <div class="mb-3">
            <label>Bulan</label>
            <select name="bulan" class="form-control" required>
                <option value="">-- Pilih Bulan --</option>
                <?php
                $daftarBulan = [
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ];
                foreach ($daftarBulan as $b) {
                    echo "<option value='$b'>$b</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Drop Baru</label>
            <input type="text" name="drop_baru" class="form-control" required>
        </div>

        <div class="row">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <div class="col-md-3 mb-3">
                    <label>Data <?= $i ?></label>
                    <input type="text" name="data<?= $i ?>" class="form-control" value="0">
                </div>
            <?php endfor; ?>
        </div>

        <div class="mb-3">
            <label>Catatan</label>
            <input type="text" name="catatan" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
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