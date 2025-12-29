<?php
session_start();

// Menghapus semua data session
session_unset();
session_destroy();

// Arahkan ke tampilan umum (halaman utama)
header("Location: index.php"); 
exit;
?>