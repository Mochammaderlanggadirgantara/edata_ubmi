<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form Input Kelompok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            max-width: 800px;
            margin: 40px auto;
        }
    </style>
</head>

<body>

    <div class="container form-section">
        <h3 class="mb-4">Form Input Data Kelompok</h3>
        <div class="mb-4">
            <h5>Import Data dari Excel</h5>
            <form action="../korwil_supervisor/import_excel.php" method="post" enctype="multipart/form-data" class="d-flex gap-2">
                <input type="file" name="file_excel" accept=".xlsx" class="form-control" required>
                <button type="submit" class="btn btn-success">Import</button>
            </form>
            <small class="text-muted">Format Excel harus sesuai: Bulan, Minggu, Hari, Kelompok, Target, CM, MB, Drop Baru, T. Keluar</small>
        </div>

        <form action="insert_target.php" method="post" id="kelompokForm">
            <div class="row mb-3">
                <div class="mb-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select" required>
                        <option value="">Pilih Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                        </sele
                            <!-- Tahun -->
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="number" class="form-control" name="tahun" id="tahun" value="<?= date('Y') ?>" required>
                        </div>

                </div>


                <div class="row mb-3">
                    <!-- Kelompok -->
                    <div class="mb-3">
                        <label for="kelompok" class="form-label">Kelompok</label>
                        <select name="kelompok" id="kelompok" class="form-select" required>
                            <option value="">Pilih Kelompok</option>
                            <option value="Kelompok 1">Kelompok 1</option>
                            <option value="Kelompok 2">Kelompok 2</option>
                            <option value="Kelompok 3">Kelompok 3</option>
                            <option value="Kelompok 4">Kelompok 4</option>
                            <option value="Kelompok 5">Kelompok 5</option>
                            <option value="Kelompok 6">Kelompok 6</option>
                            <option value="Kelompok 7">Kelompok 7</option>
                            <option value="Kelompok 8">Kelompok 8</option>
                            <option value="Kelompok 9">Kelompok 9</option>
                            <option value="Kelompok 10">Kelompok 10</option>
                        </select>
                    </div>

                    <!-- Minggu -->
                    <div class="mb-3">
                        <label for="minggu" class="form-label">Minggu</label>
                        <select name="minggu" id="minggu" class="form-select" required>
                            <option value="">Pilih Minggu</option>
                            <option value="Minggu 1">Minggu 1</option>
                            <option value="Minggu 2">Minggu 2</option>
                            <option value="Minggu 3">Minggu 3</option>
                            <option value="Minggu 4">Minggu 4</option>
                            <option value="Minggu 5">Minggu 5</option>
                        </select>
                    </div>
                    <!-- Hari -->
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari</label>
                        <select name="hari" id="hari" class="form-select" required>
                            <option value="">Pilih Hari</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="target" class="form-label">Target</label>
                        <input type="number" class="form-control" name="target" id="target" required>
                    </div>
                    <div class="col">
                        <label for="cm" class="form-label">CM</label>
                        <input type="number" class="form-control" name="cm" id="cm" required>
                    </div>
                    <div class="col">
                        <label for="mb" class="form-label">MB</label>
                        <input type="number" class="form-control" name="mb" id="mb" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="drop_baru" class="form-label">Drop Baru</label>
                        <input type="number" class="form-control" name="drop_baru" id="drop_baru" required>
                    </div>
                    <div class="col">
                        <label for="t_masuk" class="form-label">T. Masuk (13%)</label>
                        <input type="number" class="form-control" name="t_masuk" id="t_masuk" readonly>
                    </div>
                    <div class="col">
                        <label for="t_keluar" class="form-label">T. Keluar</label>
                        <input type="number" class="form-control" name="t_keluar" id="t_keluar">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col">
                        <label for="t_jadi" class="form-label">T. Jadi</label>
                        <input type="number" class="form-control" name="t_jadi" id="t_jadi" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Data</button>
        </form>
    </div>

    <script>
        const targetInput = document.getElementById('target');
        const dropBaruInput = document.getElementById('drop_baru');
        const tMasukInput = document.getElementById('t_masuk');
        const tKeluarInput = document.getElementById('t_keluar');
        const tJadiInput = document.getElementById('t_jadi');

        function calculate() {
            const dropBaru = parseFloat(dropBaruInput.value) || 0;
            const tMasuk = dropBaru * 0.13;
            tMasukInput.value = tMasuk.toFixed(2);

            const target = parseFloat(targetInput.value) || 0;
            const tKeluar = parseFloat(tKeluarInput.value) || 0;
            const tJadi = target + tMasuk - tKeluar;
            tJadiInput.value = tJadi.toFixed(2);
        }

        dropBaruInput.addEventListener('input', calculate);
        targetInput.addEventListener('input', calculate);
        tKeluarInput.addEventListener('input', calculate);
    </script>

</body>

</html>