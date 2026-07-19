<?php
session_start();

// Logika Keamanan Wajib Login
// Jika sistem mendeteksi tidak ada sesi customer_id, maka lempar kembali ke halaman login
if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// Mengambil 4 data produk terbaru dari tabel spareparts (milik Admin)
$query_products = "SELECT * FROM spareparts ORDER BY id DESC LIMIT 4";
$result_products = $conn->query($query_products);

// Mengambil data kategori dari tabel categories (milik Admin)
$query_categories = "SELECT * FROM categories LIMIT 5";
$result_categories = $conn->query($query_categories);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Oli Mesin - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <div id="intro-overlay" class="fixed inset-0 z-[100] bg-black flex items-center justify-center transition-opacity duration-1000">
        <video id="intro-video" class="w-full h-full object-cover" autoplay muted playsinline>
            <source src="intro_index.mp4" type="video/mp4">
            Maaf, browser Anda tidak mendukung pemutaran video.
        </video>
    </div>

    <header class="bg-gradient-to-r from-purple-700 via-yellow-400 to-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">

            <a href="index.php" class="bg-white px-3 py-1.5 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-1 inline-flex items-center cursor-pointer">
                <img src="logo_web.png" alt="Logo Siska Maju Motor" class="h-10 md:h-12 w-auto object-contain">
            </a>
            
            <nav class="hidden md:flex gap-6 font-semibold text-gray-800 items-center">
                <a href="index.php" class="text-purple-900 border-b-2 border-purple-900 transition">Beranda</a>
                <a href="katalog.php" class="hover:text-purple-800 transition">Katalog</a>
                <a href="promo.php" class="hover:text-purple-800 transition">Promo</a>
                <a href="kontak.php" class="hover:text-purple-800 transition">Kontak Kami</a>
                
                <!-- BAGIAN BARU: Ikon Profil & Logout untuk Desktop -->
                <div class="ml-4 flex items-center gap-3 border-l-2 border-gray-300 pl-6">
                    <a href="profil_akun.php" class="flex items-center gap-2 bg-purple-100 text-purple-800 px-4 py-2 rounded-full hover:bg-purple-200 hover:shadow-md transition font-bold text-sm">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Akun
                    </a>
                    <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')" class="flex items-center gap-2 bg-red-100 text-red-600 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition font-bold text-sm shadow-sm">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </nav>

            <!-- BAGIAN BARU: Ikon Profil & Logout untuk Mobile -->
            <div class="md:hidden flex items-center gap-4 text-gray-800">
                <a href="profil_akun.php" class="text-purple-700 bg-purple-100 p-2 rounded-full"><i data-lucide="user" class="w-5 h-5"></i></a>
                <a href="logout.php" onclick="return confirm('Keluar dari akun?')" class="text-red-500 bg-red-50 p-2 rounded-full"><i data-lucide="log-out" class="w-5 h-5"></i></a>
                <button><i data-lucide="menu" class="w-6 h-6"></i></button>
            </div>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-8 md:p-12 flex flex-col justify-center">
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-bold px-3 py-1 rounded-full w-max mb-4">Promo Spesial Bulan Ini!</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 leading-tight">
                        Sambut Flash Sale <br><span class="text-purple-700">Pusat Oli Siska Maju Motor</span>
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg">
                        Temukan penawaran terbaik untuk berbagai merk oli mesin berkualitas. Apapun jenis motor Anda—Matic, Manual, atau Sport—kami siapkan pelumas maksimal untuk menjaga performa mesinnya. Diskon berlaku hingga waktu yang ditentukan admin!
                    </p>
                    <div class="flex gap-4">
                        <a href="promo.php" class="bg-purple-700 hover:bg-purple-800 text-white px-6 py-3 rounded-lg font-semibold transition shadow-lg flex items-center gap-2">
                            Lihat Promo Oli <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
                <div class="relative h-64 md:h-auto">
                    <img src="https://images.unsplash.com/photo-1620050865842-83b3848b3bf2?auto=format&fit=crop&w=800&q=80" alt="Banner Oli Mesin" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-white via-transparent to-transparent"></div>
                </div>
            </div>
        </section>

        <div class="flex flex-col lg:flex-row gap-8">

            <div class="lg:w-3/4">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="text-purple-600"></i> Etalase Produk
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php while ($product = $result_products->fetch_assoc()): 
                        $img = !empty($product['image_url']) ? $product['image_url'] : 'https://via.placeholder.com/400?text=No+Image';
                        $harga = 'Rp ' . number_format($product['harga_jual'], 0, ',', '.');
                        $nama_produk = $product['spek'] . ' (' . $product['merk_tipe_motor'] . ')';
                    ?>
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group flex flex-col h-full relative">
                        
                        <?php if(!empty($product['discount_label'])): ?>
                            <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-10">
                                <?= $product['discount_label'] ?>
                            </div>
                        <?php endif; ?>

                        <div class="bg-gray-100 rounded-xl aspect-square mb-4 overflow-hidden relative">
                            <img src="<?= $img ?>" alt="<?= $nama_produk ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        
                        <div class="flex-grow flex flex-col">
                            <h4 class="font-semibold text-gray-800 text-sm line-clamp-2 mb-1"><?= $nama_produk ?></h4>
                            <p class="text-purple-700 font-bold text-lg mt-auto"><?= $harga ?></p>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <button class="w-full border-2 border-purple-600 text-purple-600 hover:bg-purple-50 font-semibold py-2 rounded-lg transition flex items-center justify-center gap-2">
                                <i data-lucide="shopping-cart" class="w-4 h-4"></i> Masuk Keranjang
                            </button>
                            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 rounded-lg transition flex items-center justify-center gap-2 shadow-md">
                                <i data-lucide="credit-card" class="w-4 h-4"></i> Beli Produk
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="lg:w-1/4 space-y-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="list" class="w-5 h-5 text-gray-500"></i> Kategori Populer
                    </h4>
                    <div class="space-y-3">
                        <?php while ($cat = $result_categories->fetch_assoc()): ?>
                            <a href="katalog.php?kategori=<?= $cat['id'] ?>" class="block w-full bg-gray-100 hover:bg-yellow-100 hover:text-yellow-800 text-gray-600 text-center py-3 rounded-xl font-medium transition duration-300">
                                <?= $cat['name'] ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-gradient-to-r from-purple-800 via-yellow-500 to-white mt-auto">
        <div class="container mx-auto px-4 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-8">
                
                <div class="md:w-1/2 text-white">
                    <h3 class="text-2xl font-bold mb-3 drop-shadow-md">Siska Maju Motor - Pusat Oli</h3>
                    <p class="text-white/90 leading-relaxed max-w-md drop-shadow-sm font-medium">
                        Spesialis penyedia oli mesin original dan terpercaya. Kami menghadirkan berbagai pilihan pelumas terbaik untuk memaksimalkan performa serta keawetan mesin motor Matic, Manual, hingga Sport Anda.
                    </p>
                    <p class="mt-4 text-sm text-purple-100">
                        📍 Jl. Raya Pelumas No. 123, Jakarta <br>
                        📞 Buka Setiap Hari: 08.00 - 17.00 WIB
                    </p>
                </div>

                <div class="text-center md:text-right">
                    <h4 class="text-gray-800 font-bold mb-4 bg-white/70 inline-block px-4 py-1 rounded-full backdrop-blur-sm shadow-sm">Ikuti Kami</h4>
                    <div class="flex justify-center md:justify-end gap-4">
                        <a href="#" class="bg-white text-purple-700 p-3 rounded-full hover:bg-gray-100 hover:scale-110 transition shadow-lg">
                            <i data-lucide="instagram"></i>
                        </a>
                        <a href="#" class="bg-white text-purple-700 p-3 rounded-full hover:bg-gray-100 hover:scale-110 transition shadow-lg">
                            <i data-lucide="facebook"></i>
                        </a>
                        <a href="#" class="bg-white text-purple-700 p-3 rounded-full hover:bg-gray-100 hover:scale-110 transition shadow-lg">
                            <i data-lucide="twitter"></i>
                        </a>
                        <a href="#" class="bg-white text-purple-700 p-3 rounded-full hover:bg-gray-100 hover:scale-110 transition shadow-lg">
                            <i data-lucide="youtube"></i>
                        </a>
                    </div>
                </div>

            </div>
            
            <div class="border-t border-white/30 mt-8 pt-6 text-center text-purple-900 font-medium text-sm">
                &copy; <?= date("Y"); ?> Bengkel Siska Maju Motor. Semua Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const introOverlay = document.getElementById('intro-overlay');
            const introVideo = document.getElementById('intro-video');

            if (sessionStorage.getItem('introSudahDiputar')) {
                introOverlay.style.display = 'none';
            } else {
                document.body.style.overflow = 'hidden';

                const tutupIntro = () => {
                    introOverlay.classList.add('opacity-0');
                    
                    setTimeout(() => {
                        introOverlay.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }, 1000); 

                    sessionStorage.setItem('introSudahDiputar', 'true');
                };

                if (introVideo) {
                    introVideo.addEventListener('ended', tutupIntro);
                    
                    introVideo.addEventListener('error', () => {
                        setTimeout(tutupIntro, 4000);
                    });
                } else {
                    setTimeout(tutupIntro, 4000);
                }
            }
        });
    </script>
</body>
</html>