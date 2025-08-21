<?php
include 'db.php';
$id = $_GET['id'];

// Mengambil data lama
$sql_select = "SELECT * FROM index_program WHERE id=$id";
$result = mysqli_query($conn, $sql_select);
$row = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $klp = $_POST['klp'];
    $drop_rekap = (float)$_POST['drop_rekap'];
    $storting_rekap = (float)$_POST['storting_rekap'];
    $indeks = (float)$_POST['indeks'];
    $program = (float)$_POST['program'];

    // ===== LOGIKA PERHITUNGAN =====
    $storting_jadi = $program * ($indeks / 100);
    $min_drop = $drop_rekap - $program;
    $min_storting = $storting_rekap - $storting_jadi;
    // ==============================

    $sql_update = "UPDATE index_program SET
                    tanggal='$tanggal', klp='$klp', drop_rekap='$drop_rekap', storting_rekap='$storting_rekap',
                    indeks='$indeks', program='$program', storting_jadi='$storting_jadi', min_drop='$min_drop',
                    min_storting='$min_storting'
                   WHERE id=$id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: index_program.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Program</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Edit Data</h2>

        <form action="edit.php?id=<?php echo $id; ?>" method="post">
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="klp" class="form-label">KLP</label>
                <input type="number" class="form-control" id="klp" name="klp" value="<?php echo $row['klp']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="drop_rekap" class="form-label">DROP REKAP</label>
                <input type="number" class="form-control" id="drop_rekap" name="drop_rekap" value="<?php echo $row['drop_rekap']; ?>">
            </div>
            <div class="mb-3">
                <label for="storting_rekap" class="form-label">STORTING REKAP</label>
                <input type="number" class="form-control" id="storting_rekap" name="storting_rekap" value="<?php echo $row['storting_rekap']; ?>">
            </div>
            <div class="mb-3">
                <label for="indeks" class="form-label">INDEX</label>
                <input type="text" class="form-control" id="indeks" name="indeks" value="<?php echo $row['indeks']; ?>">
            </div>
            <div class="mb-3">
                <label for="program" class="form-label">PROGRAM</label>
                <input type="number" class="form-control" id="program" name="program" value="<?php echo $row['program']; ?>">
            </div>
            <div class="mb-3">
                <label for="storting_jadi" class="form-label">STORTING JADI</label>
                <input type="number" class="form-control" id="storting_jadi" name="storting_jadi" value="<?php echo $row['storting_jadi']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="min_drop" class="form-label">MIN DROP</label>
                <input type="number" class="form-control" id="min_drop" name="min_drop" value="<?php echo $row['min_drop']; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="min_storting" class="form-label">MIN STORTING</label>
                <input type="number" class="form-control" id="min_storting" name="min_storting" value="<?php echo $row['min_storting']; ?>" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index_program.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script>
        function hitungLogika() {
            let program = parseFloat(document.getElementById('program').value) || 0;
            let indeks = parseFloat(document.getElementById('indeks').value) || 0;
            let drop_rekap = parseFloat(document.getElementById('drop_rekap').value) || 0;
            let storting_rekap = parseFloat(document.getElementById('storting_rekap').value) || 0;

            let storting_jadi = program * (indeks / 100);
            let min_drop = drop_rekap - program;
            let min_storting = storting_rekap - storting_jadi;

            document.getElementById('storting_jadi').value = Math.round(storting_jadi);
            document.getElementById('min_drop').value = Math.round(min_drop);
            document.getElementById('min_storting').value = Math.round(min_storting);
        }

        ['program', 'indeks', 'drop_rekap', 'storting_rekap'].forEach(id => {
            document.getElementById(id).addEventListener('input', hitungLogika);
        });
    </script>
</body>

</html>