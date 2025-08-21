<?php
require '../vendor/autoload.php';
require '../config/koneksi.php'; // koneksi DB

use Mpdf\Mpdf;

// ambil semua data
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
";
$result = $conn->query($sql);

// fungsi format tanggal indo
function formatTanggalIndoLengkap($tanggal) {
    $bulanIndo = [
        '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
        '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
        '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
    ];
    if (!empty($tanggal)) {
        $hari  = date('d', strtotime($tanggal));
        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        return $hari . ' ' . $bulanIndo[$bulan] . ' ' . $tahun;
    }
    return "-";
}

$bulanIndo = [
    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April',
    '05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus',
    '09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember'
];

// mulai buffer html
ob_start();
?>
<style>
    @page { size: A4 landscape; margin: 20px; }
    body { font-family: Arial, sans-serif; font-size:12px; }
    .container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .page {
        width: 48%;
        border: 1px solid #000;
        padding: 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
        page-break-inside: avoid;
    }
</style>

<?php
// load css eksternal
$cssFiles = [
    '/edata_ubmi/assets/css/style.css',
    '/edata_ubmi/assets/css/sidebar-menu.css',
    '/edata_ubmi/assets/css/simplebar.css',
];
foreach ($cssFiles as $css) {
    $cssPath = $_SERVER['DOCUMENT_ROOT'].$css;
    if (file_exists($cssPath)) {
        echo '<style>'.file_get_contents($cssPath).'</style>';
    }
}

echo '<div class="container">';
while ($row = $result->fetch_assoc()) {
    // hitung pendapatan & potongan
    $gaji_pokok = $row['gaji'];
    $prestasi   = $row['cm'] + $row['mb'] + $row['ml'];
    $bonus      = $row['goro'] + $row['um'];
    $jumlah_pendapatan = $gaji_pokok + $prestasi + $bonus;
    $jumlah_potongan   = $row['bon_prive'] + $row['beban'] + $row['wajib'] +
                         $row['sukarela'] + $row['absensi'] + $row['lain_lain'];
    $pendapatan_bersih = $jumlah_pendapatan - $jumlah_potongan;

    echo '<div class="page">';
    include "slip_template.php";
    echo '</div>';
}
echo '</div>';

$html = ob_get_clean();

// Buat PDF dengan mPDF
$mpdf = new Mpdf(['format' => 'A4-L']); // A4 Landscape
$mpdf->WriteHTML($html);
$mpdf->Output("slip_gaji.pdf", "I"); // "I" = inline (lihat di browser), "D" = download
