<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ubmi_base_new";

$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil dan validasi data dari form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bulan      = $_POST['bulan'] ?? '';
    $tahun      = (int) ($_POST['tahun'] ?? 0);
    $kelompok   = $_POST['kelompok'] ?? '';
    $minggu     = $_POST['minggu'] ?? '';
    $hari       = $_POST['hari'] ?? '';
    $target     = (int) ($_POST['target'] ?? 0);
    $cm         = (int) ($_POST['cm'] ?? 0);
    $mb         = (int) ($_POST['mb'] ?? 0);
    $drop_baru  = (int) ($_POST['drop_baru'] ?? 0);
    $t_masuk    = (float) ($_POST['t_masuk'] ?? 0);
    $t_keluar   = (int) ($_POST['t_keluar'] ?? 0);
    $t_jadi     = (float) ($_POST['t_jadi'] ?? 0);

    // Validasi minimal
    if (empty($bulan) || empty($tahun)) {
        die("Bulan dan Tahun tidak boleh kosong!");
    }

    // Query insert
    $sql = "INSERT INTO target_ubmi (
        bulan, tahun, kelompok, minggu, hari, target, cm, mb, drop_baru, t_masuk, t_keluar, t_jadi
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare statement gagal: " . $conn->error);
    }

    // String tipe harus sama dengan jumlah kolom
    // s = string, i = integer, d = double/float
    $stmt->bind_param(
        "sssssiiiidid",
        $bulan,     // s
        $tahun,     // s (atau i kalau kolomnya integer)
        $kelompok,  // s
        $minggu,    // s
        $hari,      // s
        $target,    // i
        $cm,        // i
        $mb,        // i
        $drop_baru, // i
        $t_masuk,   // d
        $t_keluar,  // i
        $t_jadi     // d
    );


    if ($stmt->execute()) {
        echo "<script>
            alert('Data berhasil disimpan!');
            window.location.href = 'data_target_ubmi.php';
        </script>";
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
