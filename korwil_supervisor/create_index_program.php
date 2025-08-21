<?php
include '../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $klp = $_POST['klp'];
    $drop_rekap = (float)$_POST['drop_rekap'];
    $storting_rekap = (float)$_POST['storting_rekap'];
    $indeks = (float)$_POST['indeks'];
    $program = (float)$_POST['program'];

    // ===== LOGIKA PERHITUNGAN =====
    // Storting jadi = program * index%
    $storting_jadi = $program * ($indeks / 100);

    // Min drop = drop rekap - program
    $min_drop = $drop_rekap - $program;

    // Min storting = storting rekap - storting jadi
    $min_storting = $storting_rekap - $storting_jadi;
    // ==============================

    $sql = "INSERT INTO index_program 
            (tanggal, klp, drop_rekap, storting_rekap, indeks, program, storting_jadi, min_drop, min_storting)
            VALUES 
            ('$tanggal', '$klp', '$drop_rekap', '$storting_rekap', '$indeks', '$program', '$storting_jadi', '$min_drop', '$min_storting')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index_program.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
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
    <form action="save_index_program.php" method="post">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="klp" class="form-label">KLP</label>
            <select class="form-select" id="klp" name="klp" required>
                <option value="">-- Pilih Kelompok --</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>">Kelompok <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="drop_rekap" class="form-label">DROP REKAP</label>
            <input type="number" class="form-control" id="drop_rekap" name="drop_rekap" value="0">
        </div>
        <div class="mb-3">
            <label for="storting_rekap" class="form-label">STORTING REKAP</label>
            <input type="number" class="form-control" id="storting_rekap" name="storting_rekap" value="0">
        </div>
        <div class="mb-3">
            <label for="indeks" class="form-label">INDEX</label>
            <input type="text" class="form-control" id="indeks" name="indeks" value="0.00">
        </div>
        <div class="mb-3">
            <label for="program" class="form-label">PROGRAM</label>
            <input type="number" class="form-control" id="program" name="program" value="0">
        </div>
        <div class="mb-3">
            <label for="storting_jadi" class="form-label">STORTING JADI</label>
            <input type="number" class="form-control" id="storting_jadi" name="storting_jadi" value="0">
        </div>
        <div class="mb-3">
            <label for="min_drop" class="form-label">MIN DROP</label>
            <input type="number" class="form-control" id="min_drop" name="min_drop" value="0">
        </div>
        <div class="mb-3">
            <label for="min_storting" class="form-label">MIN STORTING</label>
            <input type="number" class="form-control" id="min_storting" name="min_storting" value="0">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index_program.php" class="btn btn-secondary">Batal</a>
    </form>
    <script>
        // Fungsi format ribuan
        function formatRibuan(angka) {
            if (angka === "" || isNaN(angka)) return "";
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Fungsi menghitung logika
        function hitungLogika() {
            let program = parseFloat(document.getElementById('program').value) || 0;
            let indeks = parseFloat(document.getElementById('indeks').value) || 0;
            let drop_rekap = parseFloat(document.getElementById('drop_rekap').value) || 0;
            let storting_rekap = parseFloat(document.getElementById('storting_rekap').value) || 0;

            // Storting jadi = program * index%
            let storting_jadi = program * (indeks / 100);

            // Min drop = drop rekap - program
            let min_drop = drop_rekap - program;

            // Min storting = storting rekap - storting jadi
            let min_storting = storting_rekap - storting_jadi;

            // Update nilai di input (tanpa titik desimal ribuan untuk DB)
            document.getElementById('storting_jadi').value = Math.round(storting_jadi);
            document.getElementById('min_drop').value = Math.round(min_drop);
            document.getElementById('min_storting').value = Math.round(min_storting);
        }

        // Pasang listener ke input yang mempengaruhi perhitungan
        ['program', 'indeks', 'drop_rekap', 'storting_rekap'].forEach(id => {
            document.getElementById(id).addEventListener('input', hitungLogika);
        });
    </script>


</body>

</html>