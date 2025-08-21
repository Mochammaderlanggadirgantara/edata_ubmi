<?php include '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">


<head>
    <title>Tambah Slip Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <h3 class="mb-4">Form Tambah Slip Gaji</h3>
        <form action="insert.php" method="POST">
            <div class="row mb-3">
                <div class="col">
                    <label>Bulan:</label>
                    <input type="text" name="bulan" class="form-control">
                </div>
                <div class="col">
                    <label>Tahun:</label>
                    <input type="text" name="tahun" class="form-control">
                </div>
            </div>

            <label>Nama, Jabatan, Tanggal Masuk:</label>
            <select name="id_user" class="form-control mb-3">
                <?php
                $users = $conn->query("SELECT id, nama, jabatan, tgl_masuk FROM tuser");
                while ($u = $users->fetch_assoc()) {
                    echo "<option value='{$u['id']}'>{$u['nama']} - {$u['jabatan']} - {$u['tgl_masuk']}</option>";
                }
                ?>
            </select>

            <?php
            $fields = [
                'gaji_pokok' => 'Gaji Pokok',
                'prestasi' => 'Prestasi',
                'bonus' => 'Bonus',
                'bon_prive' => 'Bon Prive',
                'seban' => 'Seban',
                'simp_wajib' => 'Simp. Wajib',
                'simp_sukarela' => 'Simp. Sukarela',
                'absensi' => 'Absensi',
                'lain_lain' => 'Lain-lain',
            ];
            foreach ($fields as $name => $label) {
                echo "<div class='mb-2'><label>$label:</label><input type='number' name='$name' class='form-control'></div>";
            }
            ?>

            <div class="mb-2"><label>Nama Daerah:</label><input type="text" name="nama_daerah" class="form-control"></div>
            <div class="mb-2"><label>Tanggal:</label><input type="date" name="tanggal" class="form-control"></div>
            <div class="mb-2"><label>Pimpinan:</label><input type="text" name="pimpinan" class="form-control"></div>
            <div class="mb-2"><label>Kasir:</label><input type="text" name="kasir" class="form-control"></div>
            <div class="mb-2"><label>Penerima:</label><input type="text" name="penerima" class="form-control"></div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</body>

</html>