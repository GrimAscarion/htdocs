<?php
// Mulai sesi
session_start();

// Hapus semua data sesi (membersihkan variabel)
session_unset();

// Hancurkan sesi sepenuhnya
session_destroy();

// Lakukan redirect kembali ke halaman login
header("Location: login.php");
exit;
?>