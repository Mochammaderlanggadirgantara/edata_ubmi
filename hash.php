<?php
include 'config/koneksi.php';

$result = $conn->query("SELECT id_user, password FROM TUser");

while ($row = $result->fetch_assoc()) {
    $hashed = password_hash($row['password'], PASSWORD_DEFAULT);
    $id = $row['id_user'];
    $conn->query("UPDATE TUser SET password='$hashed' WHERE id_user=$id");
}
echo "Semua password berhasil di-hash.";
?>