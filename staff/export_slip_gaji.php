<?php
require '../vendor/autoload.php';
require '../config/koneksi.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);


// Ambil id_detail dari URL
$id_detail = isset($_GET['id_detail']) ? intval($_GET['id_detail']) : 0;

$sql = "
    SELECT d.*, 
           m.gaji_bulan,
           m.pendapatan_bulan,
           u.nama_user,
           u.jabatan,
           u.tgl_masuk,
           c.nama_cabang
    FROM turpas_detail d
    JOIN turpas_master m ON d.id_master = m.id_master
    JOIN tuser u ON d.id_user = u.id_user
    JOIN cabang c ON d.id_cabang = c.id_cabang
    WHERE d.id_detail = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_detail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<div class='alert alert-danger'>❌ Data slip gaji tidak ditemukan.</div>";
    exit;
}

// ✅ Hitung setelah $row ada
$gaji_pokok = $row['gaji'];
$prestasi   = $row['cm'] + $row['mb'] + $row['ml'];
$bonus      = $row['goro'] + $row['um'];
$jumlah_pendapatan = $gaji_pokok + $prestasi + $bonus;

// Hitung potongan
$jumlah_potongan = $row['bon_prive'] + $row['beban'] + $row['wajib'] +
                   $row['sukarela'] + $row['absensi'] + $row['lain_lain'];


                 
function formatTanggalIndoLengkap($tanggal) {
    $bulanIndo = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    if (!empty($tanggal)) {
        $hari  = date('d', strtotime($tanggal)); // ambil tanggal (dd)
        $bulan = date('m', strtotime($tanggal)); // ambil bulan (mm)
        $tahun = date('Y', strtotime($tanggal)); // ambil tahun (yyyy)
        return $hari . ' ' . $bulanIndo[$bulan] . ' ' . $tahun;
    }
    return "-";
}
// Pendapatan bersih
$pendapatan_bersih = $jumlah_pendapatan - $jumlah_potongan;
$bulanIndo = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

$tanggal = $row['pendapatan_bulan']; // contoh: 2025-08-01

if (!empty($tanggal)) {
    $bulan = date('m', strtotime($tanggal)); // 08
    $tahun = date('Y', strtotime($tanggal)); // 2025
    echo $bulanIndo[$bulan] . ' ' . $tahun; // Agustus 2025
} else {
    echo "-";
}

// style + layout
$html = '<style>
     /* Animasi ringan untuk modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: scale(0.9);
        }

        .modal.fade.show .modal-dialog {
            transform: scale(1);
            .slip-container {
                        max-width: 600px;
                        margin: 30px auto;
                        border: 1px dashed #000;
                        padding: 20px;
                        background-color: #fff;
                    }

                    .kop img {
                        width: 60px;
                        height: 60px;
                    }

                    .kop-title {
                        font-weight: bold;
                        text-transform: uppercase;
                        font-size: 14px;
                    }

                    .kop-sub {
                        font-size: 12px;
                        margin-top: -5px;
                    }

                    table tr td {
                        font-size: 14px;
                    }

                    .section-title {
                        font-weight: bold;
                        text-decoration: underline;
                        margin-top: 10px;
                    }

                    .ttd td {
                        height: 80px;
                        vertical-align: bottom;
                    }
        }
</style>';
$counter = 0;
$html = '<div class="page">';

while($row = $result->fetch_assoc()){
    $counter++;

    $bulanTampil = $bulanIndo[date('m', strtotime($row['pendapatan_bulan']))] . ' ' . date('Y', strtotime($row['pendapatan_bulan']));
    $nama        = htmlspecialchars($row['nama_user']);
    $jabatan     = htmlspecialchars($row['jabatan']);
    $tgl_masuk   = htmlspecialchars($row['tgl_masuk']);
    $bon_prive   = number_format($row['bon_prive'],0,',','.');
    $beban       = number_format($row['beban'],0,',','.');
    $wajib       = number_format($row['wajib'],0,',','.');
    $sukarela    = number_format($row['sukarela'],0,',','.');
    $absensi     = number_format($row['absensi'],0,',','.');
    $lain_lain   = number_format($row['lain_lain'],0,',','.');
    $tanggal     = formatTanggalIndoLengkap($row['gaji_bulan']);

    $gajiPokok   = number_format($gaji_pokok,0,',','.');
    $prestasiF   = number_format($prestasi,0,',','.');
    $bonusF      = number_format($bonus,0,',','.');
    $jumlahPend  = number_format($jumlah_pendapatan,0,',','.');
    $jumlahPot   = number_format($jumlah_potongan,0,',','.');
    $bersih      = number_format($pendapatan_bersih,0,',','.');

    $html .= <<<HTML
    <div class="slip-container border rounded shadow-sm p-4" style="max-width:700px; margin:auto; border:1px solid #000;">
        <!-- Kop -->
        <div class="text-center">
            <div class="d-flex justify-content-start align-items-center mb-2">
                <img src="../assets/images/logo ubmi.png" alt="Logo" style="height:85px; margin-right:10px;">
                <div style="flex:1; text-align:center;">
                    <div style="font-weight:bold; font-size:16px;">KOPERASI SIMPAN PINJAM (KSP)</div>
                    <div style="font-weight:bold; font-size:17px;">"KSP USAHA BERSAMA MANDIRI INDONESIA"</div>
                    <div style="font-size:13px;">BADAN HUKUM : No. AHU-0005202.AH.01.26.TAHUN 2020</div>
                </div>
            </div>
            <div style="font-weight:bold; margin-top:5px; font-size:15px;">
                TANDA TERIMA PENDAPATAN BULAN : <span style="text-transform:capitalize;">$bulanTampil</span>
            </div>
        </div>

        <!-- Informasi Karyawan -->
        <table class="table table-borderless mt-3 mb-2" style="font-size:14px;">
            <tr><td style="width:130px;">Nama</td><td>: $nama</td></tr>
            <tr><td>Jabatan</td><td>: $jabatan</td></tr>
            <tr><td>Tgl. Masuk</td><td>: $tgl_masuk</td></tr>
        </table>

        <!-- Pendapatan -->
        <div style="font-weight:bold; font-size:14px; margin-top:10px;">PENDAPATAN</div>
        <table class="table table-borderless table-sm" style="font-size:14px;">
            <tr><td style="width:130px;">Gaji Pokok</td><td>: Rp. $gajiPokok</td></tr>
            <tr><td>Prestasi</td><td>: Rp. $prestasiF</td></tr>
            <tr><td>Bonus</td><td>: Rp. $bonusF</td></tr>
            <tr style="font-weight:bold; white-space:nowrap;">
                <td colspan="2" style="padding-left:150px; background: linear-gradient(to right, transparent 150px, #000 150px); background-size: 100% 2px; background-repeat: no-repeat; background-position: 0 0;">
                    Jumlah Pendapatan : Rp. $jumlahPend
                </td>
            </tr>
        </table>

        <!-- Potongan -->
        <div style="font-weight:bold; font-size:14px; margin-top:5px;">POTONGAN</div>
        <table class="table table-borderless table-sm" style="font-size:14px;">
            <tr><td style="width:130px;">Bon Prive</td><td>: Rp. $bon_prive</td></tr>
            <tr><td>Beban</td><td>: Rp. $beban</td></tr>
            <tr><td>Simp. Wajib</td><td>: Rp. $wajib</td></tr>
            <tr><td>Simp. Sukarela</td><td>: Rp. $sukarela</td></tr>
            <tr><td>Absensi</td><td>: Rp. $absensi</td></tr>
            <tr><td>Lain-lain</td><td>: Rp. $lain_lain</td></tr>
            <tr style="font-weight:bold; white-space:nowrap;">
                <td colspan="2" style="padding-left:150px; background: linear-gradient(to right, transparent 150px, #000 150px); background-size: 100% 2px; background-repeat: no-repeat; background-position: 0 0;">
                    Jumlah Potongan : Rp. $jumlahPot
                </td>
            </tr>
            <tr style="font-weight:bold; white-space:nowrap;">
                <td colspan="2" style="padding-left:150px;">
                    Pendapatan Bersih : Rp. $bersih
                </td>
            </tr>
        </table>

        <!-- Tanggal -->
        <div class=" text-end mt-4 mb-2" style="font-size:14px;">
            Gresik, $tanggal
        </div>

        <!-- Mengetahui -->
        <div class="text-center" style="font-size:14px;">Mengetahui</div>
        <table class="table table-borderless text-center mt-2" style="font-size:14px; margin-bottom:40px;">
            <tr>
                <td>Pimpinan</td>
                <td>Kasir</td>
                <td>Penerima</td>
            </tr>
            <tr style="height:60px;"></tr>
            <tr style="font-weight:bold;">
                <td>Rizaldi Aditya H</td>
                <td>Amelia Rahmadhani</td>
                <td>RIZALDI ADITYA H</td>
            </tr>
        </table>
    </div>
HTML;



    if($counter % 2 == 0){
        $html .= '</div><div class="page">';
    }
}
$html .= '</div>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("slip_gaji.pdf", ["Attachment" => true]);
