<?php
include '../config/koneksi.php';

// Ambil data dari form
$id         = $_POST['id_user'] ?? null;
$nama       = $_POST['nama_user'] ?? '';
$jabatan    = $_POST['jabatan'] ?? '';
$tgl        = $_POST['tgl_masuk'] ?? '';
$username   = $_POST['username'] ?? '';
$password   = $_POST['password'] ?? '';
$id_cabang  = $_POST['id_cabang'] ?? null;
$id_kelompok = $_POST['id_kelompok'] ?? null;

// Validasi form
$errors = [];

// Validasi nama
if (empty($nama)) {
    $errors[] = "Nama tidak boleh kosong.";
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $nama)) {
    $errors[] = "Nama hanya boleh berisi huruf dan spasi.";
}

// Validasi username
if (empty($username)) {
    $errors[] = "Username tidak boleh kosong.";
} elseif (strlen($username) < 5) {
    $errors[] = "Username harus terdiri dari minimal 5 karakter.";
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM TUser WHERE username = ? AND id_user != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0) {
        $errors[] = "Username '$username' sudah tersedia. Silakan pilih username lain.";
    }
}

// Validasi password
if (!empty($password) && strlen($password) < 6) {
    $errors[] = "Password harus terdiri dari minimal 6 karakter.";
}

// Validasi tanggal masuk
if (empty($tgl)) {
    $errors[] = "Tanggal masuk tidak boleh kosong.";
} elseif (!strtotime($tgl)) {
    $errors[] = "Tanggal masuk tidak valid.";
}

// Validasi jabatan
$valid_jabatan = ['Pengawas', 'Pimpinan', 'Kepala_Mantri', 'kasir', 'staff', 'mantri'];
if (!in_array($jabatan, $valid_jabatan)) {
    $errors[] = "Jabatan tidak valid.";
}

// Validasi cabang
if (empty($id_cabang) || !is_numeric($id_cabang)) {
    $errors[] = "Cabang harus dipilih.";
}

// Validasi kelompok khusus mantri
if ($jabatan === 'mantri') {
    if (empty($id_kelompok) || !is_numeric($id_kelompok)) {
        $errors[] = "Kelompok harus dipilih untuk jabatan Mantri.";
    }
} else {
    $id_kelompok = null; // otomatis null kalau bukan mantri
}

// Jika ada error, tampilkan
if (count($errors) > 0) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
    echo "<a href='javascript:history.back()'>Kembali ke form</a>";
    exit;
}

// Simpan ke database
if ($id) {
    // UPDATE
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("
            UPDATE TUser 
            SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, password=?, id_cabang=?, id_kelompok=? 
            WHERE id_user=?");
        $stmt->bind_param("ssssssii", $nama, $jabatan, $tgl, $username, $hashed, $id_cabang, $id_kelompok, $id);
    } else {
        $stmt = $conn->prepare("
            UPDATE TUser 
            SET nama_user=?, jabatan=?, tgl_masuk=?, username=?, id_cabang=?, id_kelompok=? 
            WHERE id_user=?");
        $stmt->bind_param("ssssiii", $nama, $jabatan, $tgl, $username, $id_cabang, $id_kelompok, $id);
    }
} else {
    // INSERT
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("
        INSERT INTO TUser (nama_user, jabatan, tgl_masuk, username, password, id_cabang, id_kelompok) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $nama, $jabatan, $tgl, $username, $hashed, $id_cabang, $id_kelompok);
}

// Eksekusi
if ($stmt->execute()) {
    header("Location: ../korwil_supervisor/data_karyawan.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
