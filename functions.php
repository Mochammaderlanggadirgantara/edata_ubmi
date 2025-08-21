<?php
// CSRF token sederhana (bisa dikembangkan lebih aman dengan session)
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

// Pilihan bulan
function opsi_bulan($selected = null) {
    $bulan = [
        1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
        5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
        9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
    ];
    $html = '';
    foreach ($bulan as $num => $nama) {
        $sel = ($num == $selected) ? 'selected' : '';
        $html .= "<option value='$num' $sel>$nama</option>";
    }
    return $html;
}

// Pilihan kelompok (1 - 10)
function opsi_klp($selected = null) {
    $html = '';
    for ($i = 1; $i <= 10; $i++) {
        $sel = ($i == $selected) ? 'selected' : '';
        $html .= "<option value='$i' $sel>Kelompok $i</option>";
    }
    return $html;
}

// Format angka jadi ribuan (contoh: 10000 -> 10.000)
function fmt_int($value) {
    return number_format((int)$value, 0, ',', '.');
}
