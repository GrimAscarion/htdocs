<?php
session_start();
require_once 'koneksi.php';

// Pastikan ada parameter invoice di URL
if(!isset($_GET['invoice'])) {
    header("Location: index.php");
    exit;
}

$invoice = $conn->real_escape_string($_GET['invoice']);
$user_id = $_SESSION['customer_id'];

// Ambil data transaksi
$query = $conn->query("SELECT * FROM transaksi WHERE invoice='$invoice' AND customer_id='$user_id'");

if($query->num_rows == 0) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

$transaksi = $query->fetch_assoc();

// Pesan otomatis untuk WhatsApp Siska
$pesan_wa = "Halo Admin Siska, saya ingin konfirmasi pesanan dari website.%0A%0A";
$pesan_wa .= "*No. Invoice:* " . $transaksi['invoice'] . "%0A";
$pesan_wa .= "*Total Tagihan:* Rp " . number_format($transaksi['total_tagihan'], 0, ',', '.') . "%0A";
$pesan_wa .= "*Metode Pembayaran:* " . $transaksi['metode_pembayaran'] . "%0A%0A";
$pesan_wa .= "Tolong diinfokan total ongkos kirim dan nomor rekeningnya ya. Terima kasih!";

// Nomor WA Admin Siska (Ubah dengan nomor Siska yang asli, format 628...)
$nomor_wa_admin = "6281234567890"; 
$link_wa = "https://wa.me/{$nomor_wa_admin}?text={$pesan_wa}";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white max-w-lg w-full rounded-3xl shadow-xl overflow-hidden border border-gray-100 text-center relative p-8 md:p-12">
        
        <!-- Icon Success Animation -->
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i data-lucide="check" class="w-12 h-12 text-green-600"></i>
        </div>

        <h1 class="text-3xl font-black text-gray-800 mb-2">Hore! Pesanan Dibuat</h1>
        <p class="text-gray-500 mb-6">Terima kasih telah berbelanja di Siska Maju Motor.</p>

        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8 text-left">
            <div class="flex justify-between mb-3 border-b border-gray-200 pb-3">
                <span class="text-gray-500 text-sm font-semibold">No. Invoice</span>
                <span class="text-gray-900 font-bold"><?= $transaksi['invoice'] ?></span>
            </div>
            <div class="flex justify-between mb-3 border-b border-gray-200 pb-3">
                <span class="text-gray-500 text-sm font-semibold">Metode Bayar</span>
                <span class="text-gray-900 font-bold"><?= $transaksi['metode_pembayaran'] ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 text-sm font-semibold">Total Tagihan</span>
                <span class="text-purple-700 font-black text-lg">Rp <?= number_format($transaksi['total_tagihan'], 0, ',', '.') ?></span>
            </div>
        </div>

        <h4 class="font-bold text-gray-800 mb-3">Langkah Selanjutnya:</h4>
        <p class="text-sm text-gray-600 mb-6">
            Segera hubungi Admin Siska melalui WhatsApp untuk konfirmasi biaya ongkos kirim dan instruksi transfer pembayaran.
        </p>

        <!-- Tombol WhatsApp Konfirmasi (Warna Hijau Khas WA) -->
        <a href="<?= $link_wa ?>" target="_blank" class="w-full bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-4 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-lg mb-4">
            <i data-lucide="message-circle" class="w-6 h-6"></i> Konfirmasi ke WhatsApp
        </a>
        
        <a href="index.php" class="text-gray-500 font-semibold text-sm hover:text-purple-700 transition">
            Kembali ke Beranda
        </a>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>