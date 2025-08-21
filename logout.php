<?php
session_start();
session_destroy();
unset($_SESSION['verifikasi_cabang']);
header("Location: index.php");
exit();
?>