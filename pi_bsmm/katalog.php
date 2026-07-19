<?php
session_start();
require_once 'koneksi.php';

// Ambil SEMUA data produk dari tabel spareparts
$query_products = "SELECT * FROM spareparts ORDER BY id DESC";
$result_products = $conn->query($query_products);
$total_produk = $result_products->num_rows;

// Ambil data kategori 
$query_categories = "SELECT * FROM categories";
$result_categories = $conn->query($query_categories);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="bg-gradient-to-r from-purple-700 via-yellow-400 to-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="bg-white px-3 py-1.5 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-1 inline-flex items-center cursor-pointer">
                <img src="logo_web.png" alt="Logo Siska Maju Motor" class="h-10 md:h-12 w-auto object-contain">
            </a>
            
            <nav class="hidden md:flex gap-6 font-semibold text-gray-800 items-center">
                <a href="index.php" class="hover:text-purple-800 transition">Beranda</a>
                <a href="katalog.php" class="text-purple-900 border-b-2 border-purple-900 transition">Katalog</a>
                <a href="promo.php" class="hover:text-purple-800 transition">Promo</a>
                <a href="kontak.php" class="hover:text-purple-800 transition">Kontak Kami</a>
                
                <!-- Navigasi Profil di Header -->
                <?php if(isset($_SESSION['customer_id'])): ?>
                <div class="ml-4 flex items-center gap-3 border-l-2 border-gray-300 pl-6">
                    <a href="profil_akun.php" class="flex items-center gap-2 bg-purple-100 text-purple-800 px-4 py-2 rounded-full hover:bg-purple-200 hover:shadow-md transition font-bold text-sm">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil
                    </a>
                </div>
                <?php else: ?>
                <div class="ml-4 flex items-center gap-3 border-l-2 border-gray-300 pl-6">
                    <a href="login.php" class="bg-purple-700 text-white px-4 py-2 rounded-full hover:bg-purple-800 transition font-bold text-sm">Login</a>
                </div>
                <?php endif; ?>
            </nav>

            <div class="md:hidden flex items-center gap-4 text-gray-800">
                <a href="profil_akun.php?tab=keranjang" class="hover:text-purple-700"><i data-lucide="shopping-cart"></i></a>
                <i data-lucide="menu"></i>
            </div>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <?php if(isset($_SESSION['pesan_keranjang'])): ?>
            <div id="alert-keranjang" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $_SESSION['pesan_keranjang'] ?></div>
                <button onclick="document.getElementById('alert-keranjang').style.display='none'"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        <?php unset($_SESSION['pesan_keranjang']); endif; ?>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Katalog Produk</h1>
            <p class="text-gray-500 text-sm flex items-center gap-2">
                <a href="index.php" class="hover:text-purple-600 transition">Beranda</a> 
                <i data-lucide="chevron-right" class="w-4 h-4"></i> 
                <span class="text-purple-700 font-medium">Katalog Semua Produk</span>
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- SIDEBAR FILTER (Tetap Sama) -->
            <aside class="lg:w-1/4 flex-shrink-0">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 sticky top-28 custom-scrollbar max-h-[calc(100vh-120px)] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2"><i data-lucide="filter" class="w-5 h-5 text-purple-600"></i> Filter</h3>
                        <button class="text-sm text-red-500 hover:text-red-700 font-medium">Reset</button>
                    </div>
                    <!-- (Filter content dihilangkan untuk menghemat baris, sesuai dengan aslinya) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Nama/Merk</label>
                        <div class="relative">
                            <input type="text" placeholder="Cth: Motul, Busi..." class="w-full bg-gray-50 border border-gray-200 px-4 py-2.5 rounded-lg text-sm outline-none">
                            <i data-lucide="search" class="absolute right-3 top-2.5 w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <div class="lg:w-3/4">
                <div class="flex flex-col sm:flex-row items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 gap-4">
                    <p class="text-gray-600 text-sm font-medium">Menampilkan <span class="text-gray-900 font-bold"><?= $total_produk ?></span> produk</p>
                </div>

                <!-- Grid Produk -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php while ($product = $result_products->fetch_assoc()): 
                        $img = !empty($product['image_url']) ? $product['image_url'] : 'https://via.placeholder.com/400?text=No+Image';
                        $harga_format = number_format($product['harga_jual'], 0, ',', '.');
                        $nama_produk = $product['spek'] . ' (' . $product['merk_tipe_motor'] . ')';
                    ?>
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group flex flex-col h-full relative">
                        <div class="absolute top-2 left-2 z-10">
                            <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm border border-gray-100">
                                <?= $product['merk_tipe_motor'] ?>
                            </span>
                        </div>

                        <div class="bg-gray-100 rounded-xl aspect-square mb-4 overflow-hidden relative">
                            <img src="<?= $img ?>" alt="<?= $nama_produk ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        
                        <div class="flex-grow flex flex-col">
                            <h4 class="font-semibold text-gray-800 text-sm line-clamp-2 mb-1 hover:text-purple-700 cursor-pointer transition"><?= $nama_produk ?></h4>
                            <p class="text-purple-700 font-bold text-lg mt-auto">Rp <?= $harga_format ?></p>
                        </div>
                        
                        <!-- TOMBOL AKSI BARU -->
                        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2">
                            <!-- Tombol Masuk Keranjang -->
                            <a href="keranjang_aksi.php?action=add&id=<?= $product['id'] ?>&redirect=katalog.php" class="w-full border border-purple-200 text-purple-700 hover:bg-purple-50 text-xs font-bold py-2.5 rounded-lg transition flex items-center justify-center gap-1 text-center">
                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> Keranjang
                            </a>
                            <!-- Tombol Beli Cepat (Langsung lempar ke tab keranjang) -->
                            <a href="keranjang_aksi.php?action=add&id=<?= $product['id'] ?>&redirect=profil_akun.php?tab=keranjang" class="w-full bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold py-2.5 rounded-lg transition flex items-center justify-center gap-1 shadow-md text-center">
                                Beli Cepat
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER (Tetap sama) -->
    <footer class="bg-gradient-to-r from-purple-800 via-yellow-500 to-white mt-auto py-6 text-center text-sm font-medium">
        &copy; <?= date("Y"); ?> Siska Maju Motor. Semua Hak Cipta Dilindungi.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>