<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['customer_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add') {
    $sparepart_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($sparepart_id > 0) {
        // Cek apakah barang ini sudah ada di keranjang user ini
        $cek = $conn->query("SELECT id, qty FROM keranjang WHERE customer_id = '$user_id' AND sparepart_id = '$sparepart_id'");
        
        if ($cek->num_rows > 0) {
            // Jika sudah ada, tambahkan qty (jumlahnya) saja
            $data = $cek->fetch_assoc();
            $new_qty = $data['qty'] + 1;
            $cart_id = $data['id'];
            $conn->query("UPDATE keranjang SET qty = '$new_qty' WHERE id = '$cart_id'");
        } else {
            // Jika belum ada, masukkan sebagai baris baru
            $conn->query("INSERT INTO keranjang (customer_id, sparepart_id, qty) VALUES ('$user_id', '$sparepart_id', 1)");
        }
        
        // Cek darimana request ini berasal untuk direct
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'katalog.php';
        
        // Set pesan sukses menggunakan session
        $_SESSION['pesan_keranjang'] = "Produk berhasil ditambahkan ke keranjang!";
        header("Location: " . $redirect);
        exit;
    }
} 
elseif ($action == 'delete') {
    $cart_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    // Hapus dengan memastikan itu milik user yang sedang login (keamanan)
    $conn->query("DELETE FROM keranjang WHERE id = '$cart_id' AND customer_id = '$user_id'");
    
    $_SESSION['pesan_keranjang'] = "Produk dihapus dari keranjang.";
    // Kembalikan ke halaman profil tab keranjang
    header("Location: profil_akun.php?tab=keranjang");
    exit;
}
elseif ($action == 'update') {
    // Fitur untuk tombol + dan - di keranjang
    $cart_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $op = isset($_GET['op']) ? $_GET['op'] : '';
    
    $cek = $conn->query("SELECT qty FROM keranjang WHERE id = '$cart_id' AND customer_id = '$user_id'");
    if ($cek->num_rows > 0) {
        $data = $cek->fetch_assoc();
        $qty = $data['qty'];
        
        if ($op == 'plus') {
            $qty++;
        } elseif ($op == 'min' && $qty > 1) {
            $qty--;
        }
        
        $conn->query("UPDATE keranjang SET qty = '$qty' WHERE id = '$cart_id'");
    }
    
    header("Location: profil_akun.php?tab=keranjang");
    exit;
}

// Default fallback
header("Location: index.php");
exit;
?>