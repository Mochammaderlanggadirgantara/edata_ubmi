 <link rel="stylesheet" href="/edata_ubmi/assets/css/sidebar-menu.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/simplebar.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/apexcharts.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/prism.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/rangeslider.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/quill.snow.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/google-icon.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/remixicon.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/fullcalendar.main.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/jsvectormap.min.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/lightpick.css">
    <link rel="stylesheet" href="/edata_ubmi/assets/css/style.css">
    
<div class="page" style="border:1px solid #000; padding:12px; margin:4px; font-size:12px;">
    <!-- Kop -->
    <div style="text-align:center;">
        <div style="display:flex; align-items:center; justify-content:center; margin-bottom:6px;">
            <img src="http://localhost/edata_ubmi/assets/images/logo ubmi.png" style="height:60px; margin-right:10px;">
            <div style="flex:1; text-align:center;">
                <div style="font-weight:bold; font-size:13px;">KOPERASI SIMPAN PINJAM (KSP)</div>
                <div style="font-weight:bold; font-size:13px;">"KSP USAHA BERSAMA MANDIRI INDONESIA"</div>
                <div style="font-size:10px;">BADAN HUKUM : No. AHU-0005202.AH.01.26.TAHUN 2020</div>
            </div>
        </div>
        <div style="font-weight:bold; font-size:12px; margin:5px 0;">
            TANDA TERIMA PENDAPATAN BULAN :
            <?= $bulanIndo[date('m', strtotime($row['pendapatan_bulan']))] . ' ' . date('Y', strtotime($row['pendapatan_bulan'])) ?>
        </div>
    </div>

    <!-- Data Karyawan -->
    <table style="width:100%; font-size:11px; margin-top:6px;">
        <tr><td>Nama</td><td>: <?= htmlspecialchars($row['nama_user']); ?></td></tr>
        <tr><td>Jabatan</td><td>: <?= htmlspecialchars($row['jabatan']); ?></td></tr>
        <tr><td>Tgl. Masuk</td><td>: <?= htmlspecialchars($row['tgl_masuk']); ?></td></tr>
    </table>

    <!-- Pendapatan -->
    <div style="font-weight:bold; font-size:11px; margin-top:8px;">PENDAPATAN</div>
    <table style="width:100%; font-size:11px;">
        <tr><td>Gaji Pokok</td><td>: Rp <?= number_format($gaji_pokok,0,',','.') ?></td></tr>
        <tr><td>Prestasi</td><td>: Rp <?= number_format($prestasi,0,',','.') ?></td></tr>
        <tr><td>Bonus</td><td>: Rp <?= number_format($bonus,0,',','.') ?></td></tr>
        <tr style="font-weight:bold;"><td colspan="2">Jumlah Pendapatan : Rp <?= number_format($jumlah_pendapatan,0,',','.') ?></td></tr>
    </table>

    <!-- Potongan -->
    <div style="font-weight:bold; font-size:11px; margin-top:6px;">POTONGAN</div>
    <table style="width:100%; font-size:11px;">
        <tr><td>Bon Prive</td><td>: Rp <?= number_format($row['bon_prive'],0,',','.') ?></td></tr>
        <tr><td>Beban</td><td>: Rp <?= number_format($row['beban'],0,',','.') ?></td></tr>
        <tr><td>Simp. Wajib</td><td>: Rp <?= number_format($row['wajib'],0,',','.') ?></td></tr>
        <tr><td>Simp. Sukarela</td><td>: Rp <?= number_format($row['sukarela'],0,',','.') ?></td></tr>
        <tr><td>Absensi</td><td>: Rp <?= number_format($row['absensi'],0,',','.') ?></td></tr>
        <tr><td>Lain-lain</td><td>: Rp <?= number_format($row['lain_lain'],0,',','.') ?></td></tr>
        <tr style="font-weight:bold;"><td colspan="2">Jumlah Potongan : Rp <?= number_format($jumlah_potongan,0,',','.') ?></td></tr>
        <tr style="font-weight:bold;"><td colspan="2">Pendapatan Bersih : Rp <?= number_format($pendapatan_bersih,0,',','.') ?></td></tr>
    </table>

    <!-- Tanggal -->
    <div style="text-align:right; font-size:11px; margin-top:8px;">
        Gresik, <?= formatTanggalIndoLengkap($row['gaji_bulan']) ?>
    </div>

    <!-- Tanda Tangan -->
    <table style="width:100%; text-align:center; font-size:11px; margin-top:10px;">
        <tr><td>Pimpinan</td><td>Kasir</td><td>Penerima</td></tr>
        <tr style="height:40px;"></tr>
        <tr style="font-weight:bold;"><td>Rizaldi Aditya H</td><td>Amelia Rahmadhani</td><td><?= strtoupper($row['nama_user']); ?></td></tr>
    </table>
</div>
