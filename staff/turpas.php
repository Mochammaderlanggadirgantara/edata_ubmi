<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['jabatan'] != 'kasir') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    exit();
}
include '../config/koneksi.php';
// dari tabel users
// Ambil semua user kecuali yang jabatan-nya mantri
$query = "SELECT id_user, nama_user, jabatan, tgl_masuk FROM tuser WHERE jabatan != 'mantri'";
$result = mysqli_query($conn, $query);

// Ambil semua user hanya yang jabatan-nya mantri
$queryMantri = "SELECT id_user, nama_user, jabatan, tgl_masuk FROM tuser WHERE jabatan = 'mantri'";
$resultMantri = mysqli_query($conn, $queryMantri);

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .table th,
    .table td {
        vertical-align: middle;
    }

    .sub-header {
        background-color: #d3d3d3;
    }

    .table-striped tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    th,
    td {
        border: 1px solid #dee2e6;
    }

    td.border-split,
    th.border-split {
        border-left: 3px solid #6c757d;
    }

    th.kolom,
    td.kolom {
        min-width: 160px;
        white-space: nowrap;
    }

    .table input.form-control {
        width: auto;
        min-width: 50px;
        max-width: 100%;
        padding: 2px 4px;
        font-size: 0.875rem;
        text-align: center;
        display: inline-block;
        white-space: nowrap;
    }

    #baris-total td,
    #baris-total-mantri td {
        background-color: rgb(142, 170, 173) !important;
        /* kuning */
        font-weight: bold;
        color: #000 !important;
    }

    #baris-total td {
        border-top: 3px solid #6c757d !important;
    }

    #baris-total-mantri td {
        border-top: 3px solid #495057 !important;
    }
</style>

<div class="container py-4">
    <h4 class="mb-4 text-center fw-bold">Turpas Gaji</h4>

    <form id="formTurpas">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="setoran" class="form-label fw-semibold">Setoran</label>
                <input type="text" id="setoran" name="setoran" class="form-control text-end">
            </div>
            <div class="col-md-4">
                <label for="pendapatan_bulan" class="form-label fw-semibold">Pendapatan Bulan</label>
                <input type="month" id="pendapatan_bulan" name="pendapatan_bulan" class="form-control text-end">
            </div>
            <div class="col-md-4">
                <label for="gaji_bulan" class="form-label fw-semibold">Gaji Bulan</label>
                <input type="date" id="gaji_bulan" name="gaji_bulan" class="form-control text-end" readonly>
            </div>
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-striped text-center align-middle">
                <thead>
                    <!-- Baris Header 1 -->
                    <tr class="sub-header">
                        <th colspan="17">GAJI</th>
                        <th colspan="8" class="border-split">POTONGAN</th>
                    </tr>

                    <!-- Baris Header 2 -->
                    <tr class="sub-header">
                        <th rowspan="2">NO</th>
                        <th rowspan="2" class="kolom">NAMA</th>
                        <th rowspan="2" style="width:200px; white-space:nowrap;">&nbsp;&nbsp;&nbsp;&nbsp;JABATAN&nbsp;&nbsp;&nbsp;&nbsp;</th>

                        <th rowspan="2" style="width:160px; white-space:nowrap;">TANGGAL MASUK</th>
                        <th rowspan="2">DROP</th>
                        <th rowspan="2">STORTING</th>
                        <th rowspan="2">%</th>
                        <th rowspan="2">INDEX</th>
                        <th rowspan="2">GAJI</th>
                        <th rowspan="2">RUMUS</th>
                        <th colspan="3">PRESTASI</th>
                        <th rowspan="2">DO</th>
                        <th rowspan="2">GORO</th>
                        <th rowspan="2">UM</th>
                        <th rowspan="2">JUMLAH PENDAPATAN</th>
                        <th rowspan="2" class="border-split">BON PRIVE</th>
                        <th rowspan="2">BEBAN</th>
                        <th colspan="2">SIMPANAN</th>
                        <th rowspan="2">ABSENSI</th>
                        <th rowspan="2">LAIN-LAIN</th>
                        <th rowspan="2">JUMLAH POTONGAN</th>
                        <th rowspan="2" class="border-split">PENDAPATAN BERSIH</th>
                    </tr>

                    <!-- Baris Header 3 -->
                    <tr class="sub-header">
                        <th>CM</th>
                        <th>MB</th>
                        <th>ML</th>
                        <th>WAJIB</th>
                        <th>SUKARELA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="baris">
                            <td><?= $no++ ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama_user']) ?>
                                <input type="hidden" name="id_user[]" value="<?= $row['id_user'] ?>">
                            </td>
                            <td><?= htmlspecialchars($row['jabatan']) ?></td>
                            <td><?= htmlspecialchars($row['tgl_masuk']) ?></td>
                            <td><input type="text" class="form-control drop" name="drop[]" readonly></td>
                            <td><input type="text" class="form-control storting" name="storting[]" readonly></td>
                            <td><input type="text" class="form-control persen" name="persen[]"></td>
                            <td><input type="text" class="form-control index" name="index[]" readonly></td>
                            <td><input type="text" class="form-control gaji" name="gaji[]"></td>
                            <td><input type="text" class="form-control rumus" name="rumus[]" readonly></td>
                            <td><input type="text" class="form-control cm" name="cm[]"></td>
                            <td><input type="text" class="form-control mb" name="mb[]"></td>
                            <td><input type="text" class="form-control ml" name="ml[]"></td>
                            <td><input type="text" class="form-control do" name="do[]" readonly></td>
                            <td><input type="text" class="form-control goro" name="goro[]"></td>
                            <td><input type="text" class="form-control um" name="um[]"></td>
                            <td><input type="text" class="form-control jumlah_pendapatan" name="jumlah_pendapatan[]" readonly></td>
                            <td class="border-split"><input type="text" class="form-control bon_prive" name="bon_prive[]"></td>
                            <td><input type="text" class="form-control beban" name="beban[]"></td>
                            <td><input type="text" class="form-control wajib" name="wajib[]"></td>
                            <td><input type="text" class="form-control sukarela" name="sukarela[]"></td>
                            <td><input type="text" class="form-control absensi" name="absensi[]"></td>
                            <td><input type="text" class="form-control lain_lain" name="lain_lain[]"></td>
                            <td><input type="text" class="form-control jumlah_potongan" name="jumlah_potongan[]" readonly></td>
                            <td class="border-split"><input type="text" class="form-control pendapatan_bersih" name="pendapatan_bersih[]" readonly></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="fw-bold" id="baris-total">
                        <td colspan="4" class="text-start">TOTAL</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td><span class="total-gaji"></span></td>
                        <td><span class="total-rumus"></span></td>
                        <td><span class="total-cm"></span></td>
                        <td><span class="total-mb"></span></td>
                        <td><span class="total-ml"></span></td>
                        <td><span class="total-do"></span></td>
                        <td><span class="total-goro"></span></td>
                        <td><span class="total-um"></span></td>
                        <td><span class="total-jumlah_pendapatan"></span></td>
                        <td class="border-split"><span class="total-bon_prive"></span></td>
                        <td><span class="total-beban"></span></td>
                        <td><span class="total-wajib"></span></td>
                        <td><span class="total-sukarela"></span></td>
                        <td><span class="total-absensi"></span></td>
                        <td><span class="total-lain_lain"></span></td>
                        <td><span class="total-jumlah_potongan"></span></td>
                        <td class="border-split"><span class="total-pendapatan_bersih"></span></td>
                    </tr>

                    <!-- mantri -->
                    <?php
                    while ($rowMantri = mysqli_fetch_assoc($resultMantri)) : ?>
                        <tr class="barisMantri">
                            <td><?= $no++ ?></td>
                            <td>
                                <?= htmlspecialchars($rowMantri['nama_user']) ?>
                                <input type="hidden" name="id_user[]" value="<?= $rowMantri['id_user'] ?>">
                            </td>
                            <td><?= htmlspecialchars($rowMantri['jabatan']) ?></td>
                            <td><?= htmlspecialchars($rowMantri['tgl_masuk']) ?></td>
                            <td><input type="text" class="form-control drop" name="drop[]"></td>
                            <td><input type="text" class="form-control storting" name="storting[]"></td>
                            <td><input type="text" class="form-control persen" name="persen[]"></td>
                            <td><input type="text" class="form-control index" name="index[]" readonly></td>
                            <td><input type="text" class="form-control gaji" name="gaji[]"></td>
                            <td><input type="text" class="form-control rumus" name="rumus[]" readonly></td>
                            <td><input type="text" class="form-control cm" name="cm[]"></td>
                            <td><input type="text" class="form-control mb" name="mb[]"></td>
                            <td><input type="text" class="form-control ml" name="ml[]"></td>
                            <td><input type="text" class="form-control do" name="do[]" readonly></td>
                            <td><input type="text" class="form-control goro" name="goro[]"></td>
                            <td><input type="text" class="form-control um" name="um[]"></td>
                            <td><input type="text" class="form-control jumlah_pendapatan" name="jumlah_pendapatan[]" readonly></td>
                            <td class="border-split"><input type="text" class="form-control bon_prive" name="bon_prive[]"></td>
                            <td><input type="text" class="form-control beban" name="beban[]"></td>
                            <td><input type="text" class="form-control wajib" name="wajib[]"></td>
                            <td><input type="text" class="form-control sukarela" name="sukarela[]"></td>
                            <td><input type="text" class="form-control absensi" name="absensi[]"></td>
                            <td><input type="text" class="form-control lain_lain" name="lain_lain[]"></td>
                            <td><input type="text" class="form-control jumlah_potongan" name="jumlah_potongan[]" readonly></td>
                            <td class="border-split"><input type="text" class="form-control pendapatan_bersih" name="pendapatan_bersih[]" readonly></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="fw-bold" id="baris-total-mantri">
                        <td colspan="4" class="text-start">TOTAL</td>
                        <td><span class="total-mantri-drop"></span></td>
                        <td><span class="total-mantri-storting"></span></td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            <span class="total-mantri-gaji"></span>
                            <br>
                            <small class="text gabungan-gaji"></small>
                        </td>
                        <td>
                            <span class="total-mantri-rumus"></span>
                            <br>
                            <small class="text gabungan-rumus"></small>
                        </td>
                        <td>
                            <span class="total-mantri-cm"></span>
                            <br>
                            <small class="text gabungan-cm"></small>
                        </td>
                        <td>
                            <span class="total-mantri-mb"></span>
                            <br>
                            <small class="text gabungan-mb"></small>
                        </td>
                        <td>
                            <span class="total-mantri-ml"></span>
                            <br>
                            <small class="text gabungan-ml"></small>
                        </td>
                        <td>
                            <span class="total-mantri-do"></span>
                            <br>
                            <small class="text gabungan-do"></small>
                        </td>
                        <td>
                            <span class="total-mantri-goro"></span>
                            <br>
                            <small class="text gabungan-goro"></small>
                        </td>
                        <td>
                            <span class="total-mantri-um"></span>
                            <br>
                            <small class="text gabungan-um"></small>
                        </td>
                        <td>
                            <span class="total-mantri-jumlah_pendapatan"></span>
                            <br>
                            <small class="text gabungan-jumlah_pendapatan"></small>
                        </td>
                        <td class="border-split">
                            <span class="total-mantri-bon_prive"></span>
                            <br>
                            <small class="text gabungan-bon_prive"></small>
                        </td>
                        <td>
                            <span class="total-mantri-beban"></span>
                            <br>
                            <small class="text gabungan-beban"></small>
                        </td>
                        <td>
                            <span class="total-mantri-wajib"></span>
                            <br>
                            <small class="text gabungan-wajib"></small>
                        </td>
                        <td>
                            <span class="total-mantri-sukarela"></span>
                            <br>
                            <small class="text gabungan-sukarela"></small>
                        </td>
                        <td>
                            <span class="total-mantri-absensi"></span>
                            <br>
                            <small class="text gabungan-absensi"></small>
                        </td>
                        <td>
                            <span class="total-mantri-lain_lain"></span>
                            <br>
                            <small class="text gabungan-lain_lain"></small>
                        </td>
                        <td>
                            <span class="total-mantri-jumlah_potongan"></span>
                            <br>
                            <small class="text gabungan-jumlah_potongan"></small>
                        </td>
                        <td class="border-split">
                            <span class="total-mantri-pendapatan_bersih"></span>
                            <br>
                            <small class="text gabungan-pendapatan_bersih"></small>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
        <button type="submit" id="btnSimpan" class="btn btn-primary">Simpan</button>
    </form>
</div>
<script>
    // Fungsi untuk format angka
    function formatNumber(value) {
        return isNaN(value) ? '' : new Intl.NumberFormat('id-ID').format(value);
    }

    function getVal(input) {
        return parseFloat(input?.value?.replace(/\./g, '')) || 0;
    }

    function formatNumberInput(input) {
        if (input.classList.contains('persen')) {
            let val = input.value.replace(',', '.');
            val = val.replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) {
                val = parts[0] + '.' + parts[1];
            }
            input.value = val;
            return;
        }
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Fungsi hitung pendapatan, potongan, bersih
    function updatePendapatanDanPotongan(row) {
        const gaji = row.querySelector('.gaji');
        const cm = row.querySelector('.cm');
        const mb = row.querySelector('.mb');
        const ml = row.querySelector('.ml');
        const doInput = row.querySelector('.do');
        const goro = row.querySelector('.goro');
        const um = row.querySelector('.um');
        const jumlahPendapatan = row.querySelector('.jumlah_pendapatan');

        const bonPrive = row.querySelector('.bon_prive');
        const beban = row.querySelector('.beban');
        const wajib = row.querySelector('.wajib');
        const sukarela = row.querySelector('.sukarela');
        const absensi = row.querySelector('.absensi');
        const lainLain = row.querySelector('.lain_lain');
        const jumlahPotongan = row.querySelector('.jumlah_potongan');
        const pendapatanBersih = row.querySelector('.pendapatan_bersih');

        const totalPendapatan = getVal(gaji) + getVal(cm) + getVal(mb) + getVal(ml) + getVal(doInput) + getVal(goro) + getVal(um);
        jumlahPendapatan.value = formatNumber(totalPendapatan);

        const totalPotongan = getVal(bonPrive) + getVal(beban) + getVal(wajib) + getVal(sukarela) + getVal(absensi) + getVal(lainLain);
        jumlahPotongan.value = formatNumber(totalPotongan);

        pendapatanBersih.value = formatNumber(totalPendapatan - totalPotongan);
    }

    // Fungsi utama untuk setup event listener
    function setupBaris(selector) {
        document.querySelectorAll(selector).forEach(row => {
            const inputs = row.querySelectorAll('input.form-control');
            let gajiManual = false;

            const gaji = row.querySelector('.gaji');
            gaji.addEventListener('input', () => {
                gajiManual = true; // User ubah gaji → jangan auto update lagi
                formatNumberInput(gaji);
                updatePendapatanDanPotongan(row);
                updateTotalPerKolom();
                updateTotalPerKolomMantri();
            });

            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    formatNumberInput(input);

                    // Reset gajiManual kalau persen atau storting diubah
                    if (input.classList.contains('persen') || input.classList.contains('storting')) {
                        gajiManual = false;
                        updatePendapatanDanPotongan(row);
                        updateTotalPerKolom();
                        updateTotalPerKolomMantri();
                    }

                    const drop = row.querySelector('.drop');
                    const storting = row.querySelector('.storting');
                    const persen = row.querySelector('.persen');
                    const index = row.querySelector('.index');
                    const rumus = row.querySelector('.rumus');
                    const doInput = row.querySelector('.do');

                    const dropVal = getVal(drop);
                    const stortingVal = getVal(storting);
                    const persenVal = parseFloat(persen.value.replace(',', '.')) || 0;
                    const setoranVal = getVal(document.getElementById('setoran'));

                    // INDEX
                    index.value = dropVal > 0 ? Math.round((stortingVal / dropVal) * 100) + '%' : '';

                    // RUMUS
                    const rumusVal = Math.ceil(stortingVal * (persenVal / 100));
                    rumus.value = stortingVal > 0 && persenVal > 0 ? formatNumber(rumusVal) : '';

                    // GAJI hanya diupdate kalau belum diubah manual
                    if (!gajiManual) {
                        const gajiVal = Math.ceil(rumusVal / 1000) * 1000;
                        gaji.value = (stortingVal > 0 && persenVal > 0) ? formatNumber(gajiVal) : '';
                    }

                    // DO
                    let jabatan = row.querySelector('td:nth-child(3)')?.textContent.trim().toLowerCase();
                    let persenDO = 0.025;

                    if (jabatan === 'kasir') {
                        persenDO = 0.007;
                    } else if (jabatan === 'staff') {
                        persenDO = 0.004;
                    } else if (jabatan === 'mantri') {
                        persenDO = 0;
                    } else if (jabatan === 'pimpinan') {
                        persenDO = 0.06;
                    }

                    doInput.value = setoranVal > 0 ? formatNumber(Math.ceil(setoranVal * persenDO)) : '';



                    // Hitung pendapatan & potongan
                    updatePendapatanDanPotongan(row);

                    updateTotalPerKolom();
                    updateTotalPerKolomMantri();
                });
            });
        });
    }

    // Terapkan ke baris biasa & mantri
    setupBaris('tr.baris');
    setupBaris('tr.barisMantri');


    // Format saat halaman dimuat
    document.querySelectorAll('input.form-control').forEach(input => {
        if (input.type === 'text') {
            input.addEventListener('input', function() {
                formatNumberInput(this);
            });
        }
    });

    document.getElementById('pendapatan_bulan').addEventListener('change', function() {
        const value = this.value;
        if (!value) return;
        const [year, month] = value.split('-').map(Number);
        const nextMonth = new Date(year, month, 1);
        const yyyy = nextMonth.getFullYear();
        const mm = String(nextMonth.getMonth() + 1).padStart(2, '0');
        document.getElementById('gaji_bulan').value = `${yyyy}-${mm}-01`;
    });

    //total
    function updateTotalPerKolom() {
        const fields = [
            'gaji', 'rumus', 'cm', 'mb', 'ml',
            'do', 'goro', 'um', 'jumlah_pendapatan', 'bon_prive', 'beban',
            'wajib', 'sukarela', 'absensi', 'lain_lain', 'jumlah_potongan', 'pendapatan_bersih'
        ];

        fields.forEach(field => {
            let total = 0;

            document.querySelectorAll(`tr.baris .${field}`).forEach(input => {
                const val = parseFloat(input.value.replace(/\./g, '').replace(',', '.')) || 0;
                total += val;
            });

            const totalInput = document.querySelector(`.total-${field}`);
            if (totalInput) {
                totalInput.textContent = formatNumber(total);
            }

        });
    }


    function updateTotalPerKolomMantri() {
        const fields1 = [
            'drop', 'storting', 'gaji', 'rumus', 'cm', 'mb', 'ml',
            'do', 'goro', 'um', 'jumlah_pendapatan', 'bon_prive', 'beban',
            'wajib', 'sukarela', 'absensi', 'lain_lain', 'jumlah_potongan', 'pendapatan_bersih'
        ];

        // --- Variabel total ---
        let totalGajiMantri = 0,
            totalGajiNonMantri = 0;
        let totalRumusMantri = 0,
            totalRumusNonMantri = 0;
        let totalCmMantri = 0,
            totalCmNonMantri = 0;
        let totalMbMantri = 0,
            totalMbNonMantri = 0;
        let totalMlMantri = 0,
            totalMlNonMantri = 0;
        let totalDoMantri = 0,
            totalDoNonMantri = 0;
        let totalGoroMantri = 0,
            totalGoroNonMantri = 0;
        let totalUmMantri = 0,
            totalUmNonMantri = 0;
        let totalJumlahPendapatanMantri = 0,
            totalJumlahPendapatanNonMantri = 0;

        let totalBonPriveMantri = 0,
            totalBonPriveNonMantri = 0;
        let totalBebanMantri = 0,
            totalBebanNonMantri = 0;
        let totalSimpananWajibMantri = 0,
            totalSimpananWajibNonMantri = 0;
        let totalSimpananSukarelaMantri = 0,
            totalSimpananSukarelaNonMantri = 0;
        let totalAbsensiMantri = 0,
            totalAbsensiNonMantri = 0;
        let totalLainLainMantri = 0,
            totalLainLainNonMantri = 0;
        let totalJumlahPotonganMantri = 0,
            totalJumlahPotonganNonMantri = 0;

        let totalPendapatanBersihMantri = 0,
            totalPendapatanBersihNonMantri = 0;


        // --- Hitung Non Mantri ---
        const parseVal = (el) => parseFloat(el.value.replace(/\./g, '').replace(',', '.')) || 0;

        document.querySelectorAll(`tr.baris .gaji`).forEach(el => totalGajiNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .rumus`).forEach(el => totalRumusNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .cm`).forEach(el => totalCmNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .mb`).forEach(el => totalMbNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .ml`).forEach(el => totalMlNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .do`).forEach(el => totalDoNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .goro`).forEach(el => totalGoroNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .um`).forEach(el => totalUmNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .jumlah_pendapatan`).forEach(el => totalJumlahPendapatanNonMantri += parseVal(el));


        document.querySelectorAll(`tr.baris .bon_prive`).forEach(el => totalBonPriveNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .beban`).forEach(el => totalBebanNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .wajib`).forEach(el => totalSimpananWajibNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .sukarela`).forEach(el => totalSimpananSukarelaNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .absensi`).forEach(el => totalAbsensiNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .lain_lain`).forEach(el => totalLainLainNonMantri += parseVal(el));
        document.querySelectorAll(`tr.baris .jumlah_potongan`).forEach(el => totalJumlahPotonganNonMantri += parseVal(el));

        document.querySelectorAll(`tr.baris .pendapatan_bersih`).forEach(el => totalPendapatanBersihNonMantri += parseVal(el));


        // --- Hitung Mantri ---
        fields1.forEach(field1 => {
            let totalMantri = 0;

            document.querySelectorAll(`tr.barisMantri .${field1}`).forEach(el => {
                const val1 = parseVal(el);
                totalMantri += val1;

                if (field1 === 'gaji') totalGajiMantri += val1;
                if (field1 === 'rumus') totalRumusMantri += val1;
                if (field1 === 'cm') totalCmMantri += val1;
                if (field1 === 'mb') totalMbMantri += val1;
                if (field1 === 'ml') totalMlMantri += val1;
                if (field1 === 'do') totalDoMantri += val1;
                if (field1 === 'goro') totalGoroMantri += val1;
                if (field1 === 'um') totalUmMantri += val1;
                if (field1 === 'jumlah_pendapatan') totalJumlahPendapatanMantri += val1;

                if (field1 === 'bon_prive') totalBonPriveMantri += val1;
                if (field1 === 'beban') totalBebanMantri += val1;
                if (field1 === 'wajib') totalSimpananWajibMantri += val1;
                if (field1 === 'sukarela') totalSimpananSukarelaMantri += val1;
                if (field1 === 'absensi') totalAbsensiMantri += val1;
                if (field1 === 'lain_lain') totalLainLainMantri += val1;
                if (field1 === 'jumlah_potongan') totalJumlahPotonganMantri += val1;

                if (field1 === 'pendapatan_bersih') totalPendapatanBersihMantri += val1;
            });

            const totalInputMantri = document.querySelector(`.total-mantri-${field1}`);
            if (totalInputMantri) {
                totalInputMantri.textContent = formatNumber(totalMantri);
            }
        });

        // --- Gabungan ---
        const setGabungan = (cls, val) => {
            const el = document.querySelector(cls);
            if (el) el.textContent = `Total: ${formatNumber(val)}`;
        };

        setGabungan('.gabungan-gaji', totalGajiNonMantri + totalGajiMantri);
        setGabungan('.gabungan-rumus', totalRumusNonMantri + totalRumusMantri);
        setGabungan('.gabungan-cm', totalCmNonMantri + totalCmMantri);
        setGabungan('.gabungan-mb', totalMbNonMantri + totalMbMantri);
        setGabungan('.gabungan-ml', totalMlNonMantri + totalMlMantri);
        setGabungan('.gabungan-do', totalDoNonMantri + totalDoMantri);
        setGabungan('.gabungan-goro', totalGoroNonMantri + totalGoroMantri);
        setGabungan('.gabungan-um', totalUmNonMantri + totalUmMantri);
        setGabungan('.gabungan-jumlah_pendapatan', totalJumlahPendapatanNonMantri + totalJumlahPendapatanMantri);

        setGabungan('.gabungan-bon_prive', totalBonPriveNonMantri + totalBonPriveMantri);
        setGabungan('.gabungan-beban', totalBebanNonMantri + totalBebanMantri);
        setGabungan('.gabungan-wajib', totalSimpananWajibNonMantri + totalSimpananWajibMantri);
        setGabungan('.gabungan-sukarela', totalSimpananSukarelaNonMantri + totalSimpananSukarelaMantri);
        setGabungan('.gabungan-absensi', totalAbsensiNonMantri + totalAbsensiMantri);
        setGabungan('.gabungan-lain_lain', totalLainLainNonMantri + totalLainLainMantri);
        setGabungan('.gabungan-jumlah_potongan', totalJumlahPotonganNonMantri + totalJumlahPotonganMantri);

        setGabungan('.gabungan-pendapatan_bersih', totalPendapatanBersihNonMantri + totalPendapatanBersihMantri);
    }



    // input setoran
    document.getElementById('setoran').addEventListener('input', function() {
        formatNumberInput(this); // biar setoran rapi
        const setoranVal = getVal(this);

        // Loop semua baris (non-mantri & mantri)
        document.querySelectorAll('tr.baris, tr.barisMantri').forEach(row => {
            const doInput = row.querySelector('.do');
            let jabatan = row.querySelector('td:nth-child(3)')?.textContent.trim().toLowerCase();
            let persenDO = 0.025;

            if (jabatan === 'kasir') {
                persenDO = 0.007;
            } else if (jabatan === 'staff') {
                persenDO = 0.004;
            } else if (jabatan === 'mantri') {
                persenDO = 0;
            } else if (jabatan === 'pimpinan') {
                persenDO = 0.06;
            }

            doInput.value = setoranVal > 0 ? formatNumber(Math.ceil(setoranVal * persenDO)) : '';

            // Update jumlah pendapatan di baris ini
            const gajiVal = getVal(row.querySelector('.gaji'));
            const cm = getVal(row.querySelector('.cm'));
            const mb = getVal(row.querySelector('.mb'));
            const ml = getVal(row.querySelector('.ml'));
            const goro = getVal(row.querySelector('.goro'));
            const um = getVal(row.querySelector('.um'));
            const jumlahPendapatan = row.querySelector('.jumlah_pendapatan');

            jumlahPendapatan.value = formatNumber(gajiVal + cm + mb + ml + getVal(doInput) + goro + um);

            // Hitung ulang potongan & pendapatan bersih
            const bonPrive = getVal(row.querySelector('.bon_prive'));
            const beban = getVal(row.querySelector('.beban'));
            const wajib = getVal(row.querySelector('.wajib'));
            const sukarela = getVal(row.querySelector('.sukarela'));
            const absensi = getVal(row.querySelector('.absensi'));
            const lainLain = getVal(row.querySelector('.lain_lain'));
            const jumlahPotongan = row.querySelector('.jumlah_potongan');
            jumlahPotongan.value = formatNumber(bonPrive + beban + wajib + sukarela + absensi + lainLain);

            const pendapatanBersih = row.querySelector('.pendapatan_bersih');
            pendapatanBersih.value = formatNumber(getVal(jumlahPendapatan) - getVal(jumlahPotongan));
        });

        // Update total semua kolom
        updateTotalPerKolom();
        updateTotalPerKolomMantri();
    });

    function updateDropStortingNonMantri() {
        // Ambil total drop & storting dari semua baris mantri
        let totalDropMantri = 0;
        let totalStortingMantri = 0;

        document.querySelectorAll('tr.barisMantri').forEach(row => {
            totalDropMantri += parseFloat(row.querySelector('.drop').value.replace(/\./g, '').replace(',', '.')) || 0;
            totalStortingMantri += parseFloat(row.querySelector('.storting').value.replace(/\./g, '').replace(',', '.')) || 0;
        });

        // Format angka ke ribuan
        const formatRibuan = num => num.toLocaleString('id-ID');

        // Isi otomatis ke semua non-mantri tanpa trigger input
        document.querySelectorAll('tr.baris:not(.barisMantri)').forEach(row => {
            const dropInput = row.querySelector('.drop');
            const stortingInput = row.querySelector('.storting');

            dropInput.value = formatRibuan(totalDropMantri);
            stortingInput.value = formatRibuan(totalStortingMantri);

            // Paksa jalankan logika setupBaris
            dropInput.dispatchEvent(new Event('input', {
                bubbles: true
            }));
            stortingInput.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });

        // Panggil hitung total tabel sekali saja
        updateTotalPerKolom();
    }

    // Jalankan saat halaman load
    document.addEventListener('DOMContentLoaded', updateDropStortingNonMantri);

    // Jalankan juga setiap ada perubahan input di baris mantri
    document.querySelectorAll('tr.barisMantri .drop, tr.barisMantri .storting').forEach(input => {
        input.addEventListener('input', updateDropStortingNonMantri);
    });
</script>
<script>
    function cleanNumber(val) {
        if (!val) return 0;
        return parseInt(val.replace(/\./g, ''), 10) || 0;
    }

    function cleanFloat(val) {
        if (!val) return 0;
        // Hilangkan titik ribuan, ganti koma dengan titik desimal
        return parseFloat(val.replace(/\./g, '').replace(',', '.')) || 0;
    }

    $('#formTurpas').on('submit', function(e) {
        e.preventDefault();

        const pendapatan_bulan_raw = $('#pendapatan_bulan').val(); // hasil: YYYY-MM
        const pendapatan_bulan = pendapatan_bulan_raw ? pendapatan_bulan_raw + '-01' : ''; // ubah jadi YYYY-MM-01

        const gaji_bulan = $('#gaji_bulan').val(); // sudah YYYY-MM-DD
        const setoran = cleanNumber($('#setoran').val());


        let detailData = [];
        $('tr.baris, tr.barisMantri').each(function() {
            const id_user = $(this).find('input[name="id_user[]"]').val();
            detailData.push({
                id_user: id_user,
                drop_val: cleanNumber($(this).find('input.drop').val()),
                storting: cleanNumber($(this).find('input.storting').val()),
                persen: cleanFloat($(this).find('input.persen').val()),
                gaji: cleanNumber($(this).find('input.gaji').val()),
                cm: cleanNumber($(this).find('input.cm').val()),
                mb: cleanNumber($(this).find('input.mb').val()),
                ml: cleanNumber($(this).find('input.ml').val()),
                goro: cleanNumber($(this).find('input.goro').val()),
                um: cleanNumber($(this).find('input.um').val()),
                bon_prive: cleanNumber($(this).find('input.bon_prive').val()),
                beban: cleanNumber($(this).find('input.beban').val()),
                wajib: cleanNumber($(this).find('input.wajib').val()),
                sukarela: cleanNumber($(this).find('input.sukarela').val()),
                absensi: cleanNumber($(this).find('input.absensi').val()),
                lain_lain: cleanNumber($(this).find('input.lain_lain').val())
            });
        });

        $.ajax({
            url: 'kasir/tambah_turpas.php',
            type: 'POST',
            dataType: 'json',
            data: {
                pendapatan_bulan,
                gaji_bulan,
                setoran,
                detailData: JSON.stringify(detailData)
            },
            success: function(response) {
                if (response.status === 'success') {
                    alert('Data berhasil disimpan');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('AJAX error: ' + error);
            }
        });
    });
</script>