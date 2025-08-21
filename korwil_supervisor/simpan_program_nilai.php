<?php
include '../config/koneksi.php';
// cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $program = $_POST['program'] ?? null;
    $nilai   = $_POST['nilai'] ?? null;

    // validasi sederhana
    if ($program !== null && $nilai !== null) {
        // gunakan prepared statement
        $stmt = $conn->prepare("INSERT INTO program_nilai (program, nilai) VALUES (?, ?)");
        $stmt->bind_param("ii", $program, $nilai); // i = integer

        if ($stmt->execute()) {
            echo "Data berhasil disimpan!";
        } else {
            echo "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Program dan Nilai harus diisi!";
    }
}

$conn->close();
?>
