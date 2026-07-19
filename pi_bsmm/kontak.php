<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="bg-gradient-to-r from-purple-700 via-yellow-400 to-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="bg-white px-3 py-1.5 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-1 inline-flex items-center cursor-pointer">
                <img src="logo_web.png" alt="Logo Siska Maju Motor" class="h-10 md:h-12 w-auto object-contain">
            </a>
            
            <nav class="hidden md:flex gap-6 font-semibold text-gray-800">
                <a href="index.php" class="hover:text-purple-800 transition">Beranda</a>
                <a href="katalog.php" class="hover:text-purple-800 transition">Katalog</a>
                <a href="promo.php" class="hover:text-purple-800 transition">Promo</a>
                <a href="kontak.php" class="text-purple-900 border-b-2 border-purple-900 transition">Kontak Kami</a>
            </nav>

            <div class="md:hidden text-gray-800">
                <i data-lucide="menu"></i>
            </div>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-12 md:py-20 flex items-center justify-center">
        
        <div class="w-full max-w-5xl">
            <!-- Judul Halaman -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Hubungi Tim Kami</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Pilih divisi yang ingin Anda hubungi. Kami siap membantu segala kebutuhan Anda mulai dari urusan teknis website hingga informasi stok oli.
                </p>
            </div>

            <!-- GRID CARD KONTAK (Sesuai Wireframe 2 Kotak Besar) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                
                <!-- KARTU 1: WEB DEVELOPER (Kamu/Agpivt) -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 flex flex-col items-center text-center hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <!-- Foto Profil Placeholder (Bisa diganti image asli nanti) -->
                    <div class="w-32 h-32 rounded-full overflow-hidden mb-6 border-4 border-purple-100 group-hover:border-purple-300 transition duration-300">
                        <img src="agp.jpeg" alt="Profil Developer" class="w-full h-full object-cover">
                    </div>
                    
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">Angga</h3>
                    <span class="text-sm font-bold text-purple-700 bg-purple-50 border border-purple-200 px-4 py-1.5 rounded-full mb-6 shadow-sm">
                        Lead Web Developer & IT Support
                    </span>
                    
                    <p class="text-gray-600 mb-8 flex-grow leading-relaxed">
                        Menemukan error pada website? Ingin memberikan saran fitur baru? Atau ada penawaran kerja sama di bidang IT? Silakan hubungi saya untuk urusan teknis web Siska Maju Motor.
                    </p>
                    
                    <!-- Tombol Instagram (Sesuai link yang diberikan) -->
                    <a href="https://www.instagram.com/agpivt?igsh=MW50NXJleHp6cjc1ZA==" target="_blank" rel="noopener noreferrer" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold py-4 rounded-2xl transition flex items-center justify-center gap-3 shadow-lg hover:shadow-xl text-lg">
                        <i data-lucide="instagram" class="w-6 h-6"></i> DM Instagram
                    </a>
                </div>

                <!-- KARTU 2: ADMIN (Siska) -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 flex flex-col items-center text-center hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 group">
                    <!-- Foto Profil Placeholder (Bisa diganti image asli nanti) -->
                    <div class="w-32 h-32 rounded-full overflow-hidden mb-6 border-4 border-yellow-100 group-hover:border-yellow-400 transition duration-300">
                        <img src="siska.jpeg" alt="Profil Admin" class="w-full h-full object-cover">
                    </div>
                    
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">Siska</h3>
                    <span class="text-sm font-bold text-yellow-700 bg-yellow-50 border border-yellow-200 px-4 py-1.5 rounded-full mb-6 shadow-sm">
                        Head Admin & Customer Service
                    </span>
                    
                    <p class="text-gray-600 mb-8 flex-grow leading-relaxed">
                        Punya pertanyaan seputar rekomendasi oli yang cocok untuk motormu? Ingin cek ketersediaan stok atau konfirmasi pengiriman barang? Jangan sungkan hubungi admin Siska!
                    </p>
                    
                    <!-- Tombol Instagram (Sesuai link yang diberikan) -->
                    <a href="https://www.instagram.com/siska.njln_?igsh=MTBqcHJzamFycnlqeA==" target="_blank" rel="noopener noreferrer" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-4 rounded-2xl transition flex items-center justify-center gap-3 shadow-lg hover:shadow-xl text-lg">
                        <i data-lucide="instagram" class="w-6 h-6"></i> DM Instagram
                    </a>
                </div>

            </div>
        </div>

    </main>

    <!-- FOOTER (Konsisten dengan index.php) -->
    <footer class="bg-gradient-to-r from-purple-800 via-yellow-500 to-white mt-auto">
        <div class="container mx-auto px-4 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-8">
                
                <!-- Info Bengkel -->
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

                <!-- Social Media Logos -->
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
                &copy; <?= date("Y"); ?> Siska Maju Motor. Semua Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <!-- Inisialisasi Icon -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>