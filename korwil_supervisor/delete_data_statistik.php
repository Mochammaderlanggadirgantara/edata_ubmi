<?php
include "../config/koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cari minggu supaya redirect tetap ke filter yang benar
    $sql = "SELECT minggu FROM data_statistik WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $minggu = $row ? $row['minggu'] : 1;

    $sql = "DELETE FROM data_statistik_leader WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: read_data_statistik.php?minggu=$minggu");
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header("Location: read_data_statistik.php");
}
