<?php
function recalculateSaldo($conn)
{
    $saldo = 0;

    // Ambil semua data urut berdasarkan ID (atau tanggal, sesuai kebutuhan)
    $result = $conn->query("SELECT * FROM tabungan ORDER BY id ASC");

    while ($row = $result->fetch_assoc()) {
        $debit = $row['debit'];
        $kredit = $row['kredit'];
        $saldo += $debit - $kredit;

        // Update saldo di baris ini
        $update = $conn->prepare("UPDATE tabungan SET saldo = ? WHERE id = ?");
        $update->bind_param("di", $saldo, $row['id']);
        $update->execute();
        $update->close();
    }
}
