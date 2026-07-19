<?php
// Konfigurasi database
$host = "localhost"; // Nama host server database (biasanya localhost jika pakai XAMPP)
$user = "root";      // Username default phpMyAdmin
$pass = "";          // Password default biasanya kosong. Jika kamu pakai password, isi di sini
$db   = "inventorisparepart"; // Nama database yang disepakati dengan partner (Admin)

// Membuat instance koneksi baru menggunakan MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Mengecek apakah koneksi berhasil atau error
if ($conn->connect_error) {
    // Jika gagal, hentikan eksekusi script dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Catatan: 
// Kita tidak perlu menulis "echo 'Koneksi Berhasil';" di sini.
// Karena jika file ini di-include ke halaman web, tulisan tersebut akan merusak desain (muncul di atas header).
// Jika tidak ada error yang muncul, berarti koneksi sudah sukses berjalan di background!
?>