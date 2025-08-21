<?php
include '../config/koneksi.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelompok = $_POST['kelompok'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $minggu = $_POST['minggu'];
    $hari = $_POST['hari'];
    $drop_baru = $_POST['drop_baru'];
    $t_keluar = $_POST['t_keluar'];

    // Hitung t_masuk otomatis
    $t_masuk = $drop_baru * 0.14;

    // Cari minggu sebelumnya
    $prev_minggu = "Minggu " . ((int) filter_var($minggu, FILTER_SANITIZE_NUMBER_INT) - 1);

    // Ambil t_jadi minggu sebelumnya
    $prev_t_jadi = 0;
    $cek = mysqli_query($conn, "SELECT t_jadi FROM target_ubmi 
                                WHERE kelompok='$kelompok' 
                                AND bulan='$bulan' 
                                AND tahun='$tahun' 
                                AND minggu='$prev_minggu'
                                ORDER BY id DESC LIMIT 1");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $prev_t_jadi = $row['t_jadi'];
    }

    // Hitung t_jadi sekarang
    $t_jadi = $prev_t_jadi + $t_masuk - $t_keluar;

    // Simpan ke DB
    $sql = "INSERT INTO target_ubmi (kelompok, bulan, tahun, minggu, hari, drop_baru, t_masuk, t_keluar, t_jadi) 
            VALUES ('$kelompok','$bulan','$tahun','$minggu','$hari','$drop_baru','$t_masuk','$t_keluar','$t_jadi')";

    if (mysqli_query($conn, $sql)) {
        $message = "<div class='alert alert-success'>Data berhasil disimpan!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Gagal simpan: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Target</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="container py-4">
    <h3 class="mb-3">Tambah Data Target</h3>

    <form method="POST" action="simpan_target.php">
        <div class="mb-3">
            <label>Kelompok</label>
            <select name="kelompok" id="kelompok" class="form-control" required>
                <option value="">-- Pilih Kelompok --</option>
                <?php while ($row = mysqli_fetch_assoc($kelompokList)): ?>
                    <option value="<?= $row['kelompok'] ?>"><?= $row['kelompok'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Bulan</label>
            <select name="bulan" id="bulan" class="form-control" required>
                <option value="">-- Pilih Bulan --</option>
                <?php while ($row = mysqli_fetch_assoc($bulanList)): ?>
                    <option value="<?= $row['bulan'] ?>"><?= $row['bulan'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tahun</label>
            <select name="tahun" id="tahun" class="form-control" required>
                <option value="">-- Pilih Tahun --</option>
                <?php while ($row = mysqli_fetch_assoc($tahunList)): ?>
                    <option value="<?= $row['tahun'] ?>"><?= $row['tahun'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Hari</label>
            <select name="hari" id="hari" class="form-control" required>
                <option value="">-- Pilih Hari --</option>
                <?php while ($row = mysqli_fetch_assoc($hariList)): ?>
                    <option value="<?= $row['hari'] ?>"><?= $row['hari'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Minggu</label>
            <select name="minggu" class="form-control" required>
                <option value="">-- Pilih Minggu --</option>
                <option value="Minggu 1">Minggu 1</option>
                <option value="Minggu 2">Minggu 2</option>
                <option value="Minggu 3">Minggu 3</option>
                <option value="Minggu 4">Minggu 4</option>
            </select>
        </div>

        <!-- Auto terisi dari database -->
        <div class="mb-3">
            <label>Target</label>
            <input type="number" name="target" id="target" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>CM</label>
            <input type="number" name="cm" id="cm" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>MB</label>
            <input type="number" name="mb" id="mb" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>Drop Baru</label>
            <input type="number" name="drop_baru" id="drop_baru" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>T. Masuk (14%)</label>
            <input type="number" name="t_masuk" id="t_masuk" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>T. Keluar</label>
            <input type="number" name="t_keluar" id="t_keluar" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>T. Jadi</label>
            <input type="number" name="t_jadi" id="t_jadi" class="form-control" readonly>
        </div>


        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
    <script>
        document.getElementById('drop_baru').addEventListener('input', function() {
            let drop = parseFloat(this.value) || 0;
            let tMasuk = drop * 0.14;
            document.getElementById('t_masuk').value = tMasuk.toFixed(2);
        });
    </script>

    <script>
        $(document).ready(function() {
            function loadTarget() {
                var kelompok = $("#kelompok").val();
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                var hari = $("#hari").val();

                if (kelompok && bulan && tahun && hari) {
                    $.ajax({
                        url: "get_target.php",
                        type: "POST",
                        data: {
                            kelompok: kelompok,
                            bulan: bulan,
                            tahun: tahun,
                            hari: hari
                        },
                        success: function(data) {
                            var obj = JSON.parse(data);
                            if (obj) {
                                $("#target").val(obj.target);
                                $("#cm").val(obj.cm);
                                $("#mb").val(obj.mb);
                            } else {
                                $("#target").val('');
                                $("#cm").val('');
                                $("#mb").val('');
                            }
                        }
                    });
                }
            }

            $("#kelompok, #bulan, #tahun, #hari").change(loadTarget);
        });
    </script>
</body>

</html>