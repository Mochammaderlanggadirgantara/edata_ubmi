<?php
include '../config/koneksi.php';
session_start();
$id_kelompok = $_SESSION['id_kelompok'] ?? 0;
$id_cabang = $_SESSION['id_cabang'];
// Ambil rencana_jadi dari tabel rekap_bulan_ini
$queryRencanaJadi = "SELECT rencana_jadi FROM rekap_bulan_ini WHERE id_kelompok = $id_kelompok AND id_cabang = $id_cabang";
$resultRencanaJadi = mysqli_query($conn, $queryRencanaJadi);
$totalRencanaJadi = 0;
if ($rowRencanaJadi = mysqli_fetch_assoc($resultRencanaJadi)) {
    $totalRencanaJadi = $rowRencanaJadi['rencana_jadi'];
}

$query = "
SELECT 
    p.*, 
    a.persen1, a.persen2, a.persen3, a.target_program
FROM 
    program_mantri p
INNER JOIN 
    analisa_storting a 
ON 
    p.id = a.program_id
WHERE 
    p.id_kelompok = $id_kelompok
ORDER BY 
    p.id DESC
LIMIT 1 
";


$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$dataProgram = [];
$dataAnalisa = [];

if ($data) {
    $dataProgram = [
        'baru' => $data['baru'],
        'storting_jl' => $data['storting_jl'],
        'storting_jd' => $data['storting_jd'],
        'hari_kerja' => $data['hari_kerja'],
        'penagihan' => $data['penagihan'],
        'minggu' => $data['minggu']
    ];

    $dataAnalisa = [
        'persen1' => $data['persen1'],
        'persen2' => $data['persen2'],
        'persen3' => $data['persen3'],
        'target_program' => $data['target_program'],
    ];
}
?>
<div class="card shadow mb-4">
    <div class="card-header bg-secondary text-white text-center">
        <h5 class="mb-0">LIST VIEW DATA MANTRI</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 text-center align-middle">
                <thead class="text-white" style="background-color: black;">
                    <tr>
                        <th>HARI</th>
                        <th>TARGET</th>
                        <th>CM</th>
                        <th>MB</th>
                    </tr>
                </thead>
                <tbody style="background-color: orange; font-weight: bold;">
                    <?php
                    // Query gabungan: ambil dari targetmantri_babat1 dan ambil t_jadi minggu ke-4 dari target_berjalan
                    $query = "SELECT a.hari, a.cm, a.mb, IFNULL(b.t_jadi, 0) AS target FROM targetmantri_babat1 a LEFT JOIN target_berjalan b 
                                ON a.hari = b.hari AND b.minggu = 4 WHERE a.id_kelompok = $id_kelompok AND b.id_kelompok = $id_kelompok";
                    $result = mysqli_query($conn, $query);

                    $total_target = 0;
                    $total_cm = 0;
                    $total_mb = 0;

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
                        echo "<td>" . number_format($row['target'], 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['cm'], 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['mb'], 0, ',', '.') . "</td>";
                        echo "</tr>";

                        $total_target += $row['target'];
                        $total_cm += $row['cm'];
                        $total_mb += $row['mb'];
                    }
                    ?>

                </tbody>
                <tfoot>
                    <tr style="background-color: black; color: white;">
                        <td>JUMLAH</td>
                        <td style="background-color: orange;"><?php echo number_format($total_target, 0, ',', '.'); ?>
                        </td>
                        <td style="background-color: orange;"><?php echo number_format($total_cm, 0, ',', '.'); ?></td>
                        <td style="background-color: orange;"><?php echo number_format($total_mb, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="row mt-4">
    <!-- Kolom Kalkulasi Program -->
    <div class="col-md-8 mb-3">
        <div class="card shadow h-100">
            <div class="card-header bg-secondary text-white text-center">
                <h5 class="mb-0">KALKULASI PROGRAM MANTRI</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0" style="font-weight: bold;">
                        <tbody>
                            <tr>
                                <td style="background-color: black; color: white;">TARGET</td>
                                <td id="target-program-mantri" class="text-center">
                                    <?php echo number_format($total_target, 0, ',', '.'); ?>
                                </td>
                                <td id="target-program-index" class="text-center">0</td>
                            </tr>
                            <tr>
                                <td style="background-color: black; color: white;">PELUNASAN</td>
                                <td id="pelunasan-program-mantri" class="text-center">
                                    <?php echo number_format($totalRencanaJadi, 0, ',', '.'); ?>
                                </td>
                                <td id="pelunasan-program-index" class="text-center">
                                    <?php echo number_format(round($totalRencanaJadi * 0.13 * 2), 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: black; color: white;">BARU</td>
                                <td><input type="text" class="form-control text-center number-input"
                                        id="baru-program-mantri" value=""></td>
                                <td id="baru-program-index" class="text-center">0</td>
                            </tr>
                            <tr>
                                <td style="background-color: black; color: white;">STORTING JL</td>
                                <td colspan="2">
                                    <input type="text" class="form-control text-center number-input"
                                        id="storting-jl-mantri" value="">
                                </td>
                            </tr>

                            <tr>
                                <td style="background-color: black; color: white;">STORTING JD</td>
                                <td>
                                    <input type="text" class="form-control text-center number-input"
                                        id="storting-jd-mantri" value="" name="storting_jd_mantri">
                                </td>
                                <td id="storting-jd-index" class="text-center">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Keterangan Tambahan -->
    <div class="col-md-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-header bg-secondary text-white text-center">
                <h5 class="mb-0">KETERANGAN TAMBAHAN</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <form id="form-keterangan">
                        <table class="table table-bordered text-center align-middle mb-0" style="font-weight: bold;">
                            <tbody>
                                <tr style="background-color: black; color: white;">
                                    <td>KURANG HARI KERJA</td>
                                </tr>
                                <tr style="background-color: yellow;">
                                    <td> <input type="text" class="form-control text-center number-input"
                                            id="input-hari-kerja" value="">
                                    </td>
                                </tr>
                                <tr style="background-color: black; color: white;">
                                    <td>PENAGIHAN</td>
                                </tr>
                                <tr style="background-color: yellow;">
                                    <td><input type="text" class="form-control text-center number-input"
                                            name="penagihan" id="input-penagihan" value="">
                                    </td>
                                </tr>
                                <tr style="background-color: black; color: white;">
                                    <td>1 MINGGU</td>
                                </tr>
                                <tr style="background-color: yellow;">
                                    <td> <input type="text" class="form-control text-center number-input"
                                            id="input-minggu" value="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header bg-secondary text-white text-center">
        <h5 class="mb-0">KEKUATAN PROGRAM</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle mb-0" style="font-weight: bold;">
                <thead>
                    <tr>
                        <th>INDEX</th>
                        <th>PROGRAM DROP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" id="persen-1" class="form-control text-center persen-input" value="115">
                        </td>
                        <td id="storting-1"></td>
                    </tr>
                    <tr>
                        <td><input type="text" id="persen-2" class="form-control text-center persen-input" value="120">
                        </td>
                        <td id="storting-2"></td>
                    </tr>
                    <tr>
                        <td><input type="text" id="persen-3" class="form-control text-center persen-input" value="123">
                        </td>
                        <td id="storting-3"></td>
                    </tr>
                    <tr>
                        <td><strong>TARGET PROGRAM</strong></td>
                        <td>
                            <input type="text" id="storting-target" class="form-control text-center number-input"
                                value="">
                        </td>

                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>

<div class="card shadow mt-3 mb-5">
    <div class="card-header bg-secondary text-white text-center">
        <h5 class="mb-0">ANALISA STORTING</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle mb-0" style="font-weight: bold;">
                <thead>
                    <tr>
                        <th>INDEX</th>
                        <th>T. STORTING</th>
                        <th>PLUS / MINUS</th>
                    </tr>
                </thead>
                <tbody id="analisa-table-body">
                    <tr>
                        <td class="persen-value">115%</td>
                        <td class="t-storting">0</td>
                        <td class="plus-minus">0</td>
                    </tr>
                    <tr>
                        <td class="persen-value">120%</td>
                        <td class="t-storting">0</td>
                        <td class="plus-minus">0</td>
                    </tr>
                    <tr>
                        <td class="persen-value">123%</td>
                        <td class="t-storting">0</td>
                        <td class="plus-minus">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="text-center my-4">
    <button class="btn btn-success btn-lg" onclick="simpanData()">Simpan Data</button>
</div>

<script>
    function formatNumberWithDot(value) {
        return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.querySelectorAll('.number-input').forEach(input => {
        input.value = formatNumberWithDot(input.value);

        input.addEventListener('input', () => {
            const pos = input.selectionStart;
            const raw = input.value.replace(/\D/g, '');
            input.value = formatNumberWithDot(raw);
            const newPos = pos + (input.value.length - raw.length);
            input.setSelectionRange(newPos, newPos);
        });

        input.addEventListener('keypress', e => {
            if (!/\d/.test(e.key)) e.preventDefault();
        });
        input.addEventListener('paste', e => {
            if (/\D/.test((e.clipboardData || window.clipboardData).getData('text'))) e.preventDefault();
        });
    });

    document.querySelectorAll('.persen-input').forEach(input => {
        const format = () => {
            const angka = input.value.replace(/\D/g, '');
            input.value = angka ? angka + '%' : '';
        };

        format();
        input.addEventListener('input', format);
        input.addEventListener('keypress', e => {
            if (!/\d/.test(e.key)) e.preventDefault();
        });
        input.addEventListener('paste', e => {
            if (/\D/.test((e.clipboardData || window.clipboardData).getData('text'))) e.preventDefault();
        });
    });

    function hitungIndexProgram() {
        let t = parseInt(document.getElementById("target-program-mantri")?.innerText.replace(/\./g, '') || '0');
        let m = parseInt(document.getElementById("input-minggu")?.value.replace(/\./g, '') || '1');
        let h = parseInt(document.getElementById("input-hari-kerja")?.value.replace(/\./g, '') || '0');

        let hasil = m > 0 ? Math.round((t / m) * h) : 0;
        document.getElementById("target-program-index").innerText = formatNumberWithDot(hasil.toString());

        hitungStortingJDIndex();
    }

    ["input-minggu", "input-hari-kerja"].forEach(id => {
        document.getElementById(id)?.addEventListener('input', hitungIndexProgram);
    });

    window.addEventListener("DOMContentLoaded", hitungIndexProgram);

    function hitungBaruIndex() {
        let baru = parseInt(document.getElementById("baru-program-mantri")?.value.replace(/\./g, '') || '0');
        let penagihan = parseInt(document.querySelector('input[name="penagihan"]')?.value.replace(/\./g, '') || '0');
        let hasil = Math.round(baru * 0.13 * penagihan);
        document.getElementById("baru-program-index").innerText = formatNumberWithDot(hasil.toString());

        hitungStortingJDIndex();
    }

    document.getElementById("baru-program-mantri")?.addEventListener("input", hitungBaruIndex);
    document.querySelector('input[name="penagihan"]')?.addEventListener("input", hitungBaruIndex);

    window.addEventListener("DOMContentLoaded", () => {
        hitungPelunasanIndex();
        hitungBaruIndex();
    });

    function hitungStortingJDIndex() {
        let ids = [
            "target-program-index",
            "pelunasan-program-index",
            "baru-program-index",
            "storting-jl-mantri"
        ];

        let total = ids.reduce((sum, id) => {
            let el = document.getElementById(id);
            if (!el) return sum;

            // Gunakan .innerText jika <td>, .value jika <input>
            let val = el.tagName === "TD" ?
                parseInt(el.innerText.replace(/\./g, '') || '0') :
                parseInt(el.value.replace(/\./g, '') || '0');
            return sum + val;
        }, 0);

        let stortingJD = parseInt(document.querySelector('input[name="storting_jd_mantri"]')?.value.replace(/\./g, '') || '0');
        total += stortingJD;

        let el = document.getElementById("storting-jd-index");
        if (el) el.innerText = formatNumberWithDot(total.toString());
    }


    // Semua elemen pemicu
    [
        "target-program-index",
        "pelunasan-program-index",
        "baru-program-index",
        "storting-jl-mantri"
    ].forEach(id => {
        document.getElementById(id)?.addEventListener('input', hitungStortingJDIndex);
    });

    document.querySelector('input[name="storting_jd_mantri"]')?.addEventListener("input", hitungStortingJDIndex);
    window.addEventListener("DOMContentLoaded", hitungStortingJDIndex);

    function updateStortingByIndex() {
        const base = parseInt(document.getElementById('storting-jd-index')?.innerText.replace(/\./g, '') || '0');
        for (let i = 1; i <= 3; i++) {
            let persen = parseFloat((document.getElementById('persen-' + i)?.value || '0').replace('%', '')) || 0;
            let hasil = persen ? base / (persen / 100) : 0;
            document.getElementById('storting-' + i).innerText = Math.round(hasil).toLocaleString('id-ID');
        }
    }

    function updatePersenValue() {
        document.querySelectorAll('#analisa-table-body tr').forEach((row, i) => {
            const val = document.getElementById('persen-' + (i + 1))?.value || '';
            const cell = row.querySelector('.persen-value');
            if (cell) cell.innerText = val;
        });
    }

    function updateTStorting() {
        const base = parseInt(document.getElementById('storting-target')?.value.replace(/\./g, '') || '0');
        document.querySelectorAll('#analisa-table-body tr').forEach(row => {
            let persen = parseFloat(row.querySelector('.persen-value')?.innerText.replace('%', '') || '0') / 100;
            row.querySelector('.t-storting').innerText = Math.round(base * persen).toLocaleString('id-ID');
        });
    }

    function updatePlusMinus() {
        const base = parseInt(document.getElementById('storting-jd-index')?.innerText.replace(/\./g, '') || '0');
        document.querySelectorAll('#analisa-table-body tr').forEach(row => {
            const target = parseInt(row.querySelector('.t-storting')?.innerText.replace(/\./g, '') || '0');
            const selisih = base - target;
            const cell = row.querySelector('.plus-minus');
            if (cell) {
                cell.innerText = selisih.toLocaleString('id-ID');
                cell.style.color = selisih < 0 ? '#721c24' : '#155724';
            }
        });
    }

    for (let i = 1; i <= 3; i++) {
        document.getElementById('persen-' + i)?.addEventListener('input', () => {
            updatePersenValue();
            updateStortingByIndex();
            updateTStorting();
            updatePlusMinus();
        });
    }

    document.getElementById('storting-target')?.addEventListener('input', () => {
        updateTStorting();
        updatePlusMinus();
    });

    document.getElementById('storting-jd-index')?.addEventListener('input', () => {
        updateStortingByIndex();
        updatePlusMinus();
    });

    ["baru-program-mantri", "storting-jl-mantri", "storting-jd-mantri", "input-hari-kerja", "input-minggu", "input-penagihan"].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("input", () => {
                updateStortingByIndex();
                updatePlusMinus(); // agar selisih ikut terupdate
            });
        }
    });

    [1, 2, 3].forEach(i => {
        document.getElementById(`persen-${i}`)?.addEventListener("input", () => {
            updatePersenValue();
            updateTStorting();
            updatePlusMinus();
        });
    });

    //simpan
    function simpanData() {
        const data = {
            // Grup 1: Program Mantri + Keterangan Tambahan
            baru: document.getElementById('baru-program-mantri').value.replace(/\./g, ''),
            storting_jl: document.getElementById('storting-jl-mantri').value.replace(/\./g, ''),
            storting_jd: document.getElementById('storting-jd-mantri').value.replace(/\./g, ''),
            hari_kerja: document.getElementById('input-hari-kerja').value.replace(/\./g, ''),
            penagihan: document.getElementById('input-penagihan').value.replace(/\./g, ''),
            minggu: document.getElementById('input-minggu').value.replace(/\./g, ''),

            // Grup 2: Analisa Storting
            persen1: document.getElementById('persen-1').value.replace('%', ''),
            persen2: document.getElementById('persen-2').value.replace('%', ''),
            persen3: document.getElementById('persen-3').value.replace('%', ''),
            storting_target: document.getElementById('storting-target').value.replace(/\./g, ''),
        };

        fetch('mantri/simpan_kalkulasi_mantri.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.text())
            .then(response => {
                alert("Data berhasil disimpan!\n\n" + response);
            })
            .catch(error => {
                alert("Gagal menyimpan data!");
                console.error(error);
            });
    }
</script>
<script>
    fetch('mantri/get_kalkulasi.php')
        .then(res => res.json())
        .then(response => {
            console.log("DATA FETCHED:", response);
            if (response.success) {
                const dataProgram = response.dataProgram;
                const dataAnalisa = response.dataAnalisa;

                // Isi input dari program_mantri
                document.getElementById('baru-program-mantri').value = formatNumberWithDot(dataProgram.baru || '');
                document.getElementById('storting-jl-mantri').value = formatNumberWithDot(dataProgram.storting_jl || '');
                document.getElementById('storting-jd-mantri').value = formatNumberWithDot(dataProgram.storting_jd || '');
                document.getElementById('input-hari-kerja').value = formatNumberWithDot(dataProgram.hari_kerja || '');
                document.getElementById('input-penagihan').value = formatNumberWithDot(dataProgram.penagihan || '');
                document.getElementById('input-minggu').value = formatNumberWithDot(dataProgram.minggu || '');

                // Isi input dari analisa_storting
                document.getElementById('persen-1').value = (dataAnalisa.persen1 || '') + '%';
                document.getElementById('persen-2').value = (dataAnalisa.persen2 || '') + '%';
                document.getElementById('persen-3').value = (dataAnalisa.persen3 || '') + '%';
                document.getElementById('storting-target').value = formatNumberWithDot(dataAnalisa.target_program || '');

                // Jalankan ulang kalkulasi jika perlu
                //5 dibawah penting banget
                hitungIndexProgram();
                hitungBaruIndex();
                hitungStortingJDIndex();
                updateStortingByIndex();
                updatePersenValue();

                updateTStorting();
                updatePlusMinus();
            } else {
                //alert("Data kalkulasi belum tersedia.");
            }
        });
</script>