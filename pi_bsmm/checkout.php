<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['customer_id'];

// Ambil data user untuk form alamat
$query_user = $conn->query("SELECT * FROM customers WHERE id='$user_id'");
$user = $query_user->fetch_assoc();

// Ambil isi keranjang
$query_keranjang = $conn->query("
    SELECT k.qty, s.id as prod_id, s.spek, s.merk_tipe_motor, s.harga_jual, s.image_url 
    FROM keranjang k 
    JOIN spareparts s ON k.sparepart_id = s.id 
    WHERE k.customer_id = '$user_id'
");

// Jika keranjang kosong, tendang balik ke profil
if($query_keranjang->num_rows == 0) {
    header("Location: profil_akun.php?tab=keranjang");
    exit;
}

$grand_total = 0;
$items = [];
while($row = $query_keranjang->fetch_assoc()){
    $items[] = $row;
    $grand_total += ($row['harga_jual'] * $row['qty']);
}

// ==========================================
// PROSES PEMBUATAN PESANAN KETIKA TOMBOL DIKLIK
// ==========================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buat_pesanan'])) {
    $alamat_pengiriman = $conn->real_escape_string(trim($_POST['alamat']));
    $metode_pembayaran = $conn->real_escape_string($_POST['metode_pembayaran']);
    
    // Generate Nomor Invoice Unik (INV-TahunBulanTanggal-Acak)
    $invoice = "INV-" . date("Ymd") . "-" . strtoupper(substr(uniqid(), -4));

    // 1. Simpan ke tabel transaksi
    $query_transaksi = "INSERT INTO transaksi (invoice, customer_id, total_tagihan, metode_pembayaran, alamat_pengiriman) 
                        VALUES ('$invoice', '$user_id', '$grand_total', '$metode_pembayaran', '$alamat_pengiriman')";
    
    if($conn->query($query_transaksi)) {
        $transaksi_id = $conn->insert_id; // Ambil ID transaksi yang baru saja dibuat

        // 2. Simpan ke tabel transaksi_detail (satu per satu produk)
        foreach($items as $item) {
            $prod_id = $item['prod_id'];
            $qty = $item['qty'];
            $harga = $item['harga_jual'];
            $conn->query("INSERT INTO transaksi_detail (transaksi_id, sparepart_id, qty, harga_satuan) VALUES ('$transaksi_id', '$prod_id', '$qty', '$harga')");
        }

        // 3. Hapus keranjang setelah dicheckout
        $conn->query("DELETE FROM keranjang WHERE customer_id='$user_id'");

        // 4. Redirect ke halaman sukses
        header("Location: pesanan_sukses.php?invoice=$invoice");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pembayaran - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="profil_akun.php?tab=keranjang" class="flex items-center gap-3 text-gray-500 font-semibold hover:text-purple-700 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i> Kembali ke Keranjang
            </a>
            <h1 class="text-xl font-bold text-gray-800">Checkout <span class="text-purple-700">Pesanan</span></h1>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8 max-w-5xl">
        <form action="" method="POST" class="flex flex-col lg:flex-row gap-8">
            
            <!-- BAGIAN KIRI: Form Pengiriman & Pembayaran -->
            <div class="lg:w-2/3 space-y-6">
                
                <!-- Alamat Pengiriman -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="map-pin" class="text-purple-600"></i> Alamat Pengiriman
                    </h3>
                    <div class="bg-purple-50 p-4 rounded-xl border border-purple-100 mb-4">
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($user['nama_lengkap']) ?> <span class="font-normal text-gray-500">(<?= htmlspecialchars($user['no_telp']) ?>)</span></p>
                    </div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Alamat</label>
                    <textarea name="alamat" rows="3" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 p-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"><?= htmlspecialchars($user['alamat_pengiriman'] ?? $user['alamat_lengkap']) ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">*Pastikan alamat sudah lengkap (RT/RW, Desa, Kecamatan, Kota/Kabupaten) untuk mempermudah kurir.</p>
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="credit-card" class="text-purple-600"></i> Pilih Metode Pembayaran
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="Transfer BCA" required class="peer hidden">
                            <div class="border border-gray-200 rounded-xl p-4 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                <div class="font-bold text-gray-800">Transfer Bank BCA</div>
                                <div class="text-sm text-gray-500 mt-1">Dicek manual via WhatsApp</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="Transfer BRI" class="peer hidden">
                            <div class="border border-gray-200 rounded-xl p-4 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                <div class="font-bold text-gray-800">Transfer Bank BRI</div>
                                <div class="text-sm text-gray-500 mt-1">Dicek manual via WhatsApp</div>
                            </div>
                        </label>
                        <label class="cursor-pointer md:col-span-2">
                            <input type="radio" name="metode_pembayaran" value="COD / Bayar di Bengkel" class="peer hidden">
                            <div class="border border-gray-200 rounded-xl p-4 peer-checked:border-purple-600 peer-checked:bg-purple-50 transition">
                                <div class="font-bold text-gray-800">Bayar di Bengkel (COD)</div>
                                <div class="text-sm text-gray-500 mt-1">Ambil dan bayar langsung di Siska Maju Motor</div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- BAGIAN KANAN: Ringkasan Belanja -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="text-purple-600"></i> Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        <?php foreach($items as $item): 
                             $img = !empty($item['image_url']) ? $item['image_url'] : 'https://via.placeholder.com/50?text=Oli';
                        ?>
                        <div class="flex gap-3 items-center border-b border-gray-50 pb-3">
                            <img src="<?= $img ?>" class="w-12 h-12 object-cover rounded bg-gray-100 border border-gray-200">
                            <div class="flex-grow">
                                <h4 class="text-sm font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($item['spek']) ?></h4>
                                <p class="text-xs text-gray-500"><?= $item['qty'] ?> x Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2 mb-6">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal Produk</span>
                            <span>Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Ongkos Kirim</span>
                            <span class="text-green-600 font-medium">Hitung via WhatsApp</span>
                        </div>
                    </div>

                    <div class="flex justify-between text-lg font-black text-gray-800 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <span>Total Tagihan</span>
                        <span class="text-purple-700">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                    </div>

                    <button type="submit" name="buat_pesanan" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
                        Buat Pesanan <i data-lucide="check-circle"></i>
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3"></i> Transaksi Aman
                    </p>
                </div>
            </div>

        </form>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>