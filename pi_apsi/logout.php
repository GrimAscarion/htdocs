<?php
// Memulai sesi untuk mengenali sesi siapa yang sedang aktif
session_start();

// Menghapus semua variabel sesi
session_unset();

// Menghancurkan sesi sepenuhnya
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.php");
exit();
?>