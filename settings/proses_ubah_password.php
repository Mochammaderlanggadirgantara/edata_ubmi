<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST['id_user'];
    $username = $conn->real_escape_string($_POST['username']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $ulangi_password = $_POST['ulangi_password_baru'];

    $sql = "SELECT * FROM TUser WHERE id_user = $id_user";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Verifikasi password lama
    if (!password_verify($password_lama, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Password lama salah.']);
        exit;
    }

    // Validasi konfirmasi password baru
    if ($password_baru !== $ulangi_password) {
        echo json_encode(['status' => 'error', 'message' => 'Password baru dan konfirmasi tidak cocok.']);
        exit;
    }

    // Hash password baru dan update
    $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE TUser SET username=?, password=? WHERE id_user=?");
    $update->bind_param("ssi", $username, $hashed, $id_user);

    if ($update->execute()) {
        // Redirect ke halaman beranda (ubah sesuai kebutuhan)
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil diubah.', 'redirect' => 'beranda.php']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah data.']);
    }

    $update->close();
}
?>