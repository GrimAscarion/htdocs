<?php
session_start();

if(!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$user_id = $_SESSION['customer_id'];
$pesan_sukses = '';
$pesan_error = '';

// Notifikasi dari aksi keranjang
if(isset($_SESSION['pesan_keranjang'])){
    $pesan_sukses = $_SESSION['pesan_keranjang'];
    unset($_SESSION['pesan_keranjang']);
}

// Proses Simpan Update Biodata
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_simpan_biodata'])) {
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $telp = $conn->real_escape_string(trim($_POST['telp']));
    $alamat = $conn->real_escape_string(trim($_POST['alamat']));

    $query_update = "UPDATE customers SET nama_lengkap='$nama', no_telp='$telp', alamat_lengkap='$alamat' WHERE id='$user_id'";
    if($conn->query($query_update)) {
        $pesan_sukses = "Biodata berhasil diperbarui!";
        $_SESSION['customer_name'] = $nama; 
    } else {
        $pesan_error = "Gagal memperbarui biodata. Silakan coba lagi.";
    }
}

// Proses Upload Foto Profil
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_foto'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_extension = pathinfo($_FILES["file_foto"]["name"], PATHINFO_EXTENSION);
    $new_filename = "profil_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $uploadOk = 1;
    $imageFileType = strtolower($file_extension);

    $check = getimagesize($_FILES["file_foto"]["tmp_name"]);
    if($check !== false) { $uploadOk = 1; } else { $pesan_error = "File bukan gambar."; $uploadOk = 0; }
    if ($_FILES["file_foto"]["size"] > 2000000) { $pesan_error = "Ukuran maksimal 2 MB."; $uploadOk = 0; }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $pesan_error = "Hanya format JPG, JPEG, & PNG."; $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["file_foto"]["tmp_name"], $target_file)) {
            $conn->query("UPDATE customers SET foto_profil='$new_filename' WHERE id='$user_id'");
            $pesan_sukses = "Foto profil berhasil diunggah!";
        } else {
            $pesan_error = "Terjadi kesalahan saat memindahkan file.";
        }
    }
}

// Ambil data user
$query_user = $conn->query("SELECT * FROM customers WHERE id='$user_id'");
$user = $query_user->fetch_assoc();
$foto_profil_url = !empty($user['foto_profil']) ? 'uploads/' . $user['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap']) . '&background=random&color=fff&size=150';

// Ambil data Keranjang
$query_keranjang = $conn->query("
    SELECT k.id as cart_id, k.qty, s.id as prod_id, s.spek, s.merk_tipe_motor, s.harga_jual, s.image_url 
    FROM keranjang k 
    JOIN spareparts s ON k.sparepart_id = s.id 
    WHERE k.customer_id = '$user_id'
    ORDER BY k.id DESC
");
$total_item_keranjang = $query_keranjang->num_rows;
$grand_total = 0;

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'biodata';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Akun - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .tab-content { display: none; animation: fadeIn 0.3s ease-in-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3 text-purple-700 font-bold text-lg hover:text-purple-900 transition">
                <i data-lucide="arrow-left" class="w-6 h-6"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <img src="logo_web.png" alt="Logo" class="h-8 object-contain" onerror="this.src='https://via.placeholder.com/150x40?text=Siska+Maju'">
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        
        <?php if($pesan_sukses): ?>
            <div id="alert-box" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $pesan_sukses ?></div>
                <button onclick="document.getElementById('alert-box').style.display='none'"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        <?php endif; ?>

        <?php if($pesan_error): ?>
            <div id="alert-box-err" class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3"><i data-lucide="alert-circle" class="w-5 h-5"></i> <?= $pesan_error ?></div>
                <button onclick="document.getElementById('alert-box-err').style.display='none'"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        <?php endif; ?>

        <div class="flex flex-col md:flex-row gap-6">
            
            <aside class="md:w-1/4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    <div class="p-6 bg-gradient-to-br from-purple-700 to-purple-900 text-white text-center">
                        <div class="relative w-24 h-24 mx-auto mb-4">
                            <img src="<?= $foto_profil_url ?>" alt="Foto Profil" class="w-full h-full rounded-full object-cover border-4 border-white/30 shadow-lg">
                        </div>
                        <h3 class="font-bold text-lg truncate"><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
                        <p class="text-purple-200 text-xs mt-1">Pelanggan Siska Motor</p>
                    </div>

                    <nav class="flex flex-col py-2">
                        <button onclick="switchTab('biodata')" id="menu-biodata" class="sidebar-menu w-full flex items-center justify-between px-6 py-4 text-left font-semibold transition <?= $active_tab == 'biodata' ? 'border-l-4 border-purple-700 bg-purple-50 text-purple-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50' ?>">
                            <div class="flex items-center gap-3"><i data-lucide="user" class="w-5 h-5"></i> Biodata User</div>
                        </button>
                        <button onclick="switchTab('keranjang')" id="menu-keranjang" class="sidebar-menu w-full flex items-center justify-between px-6 py-4 text-left font-semibold transition <?= $active_tab == 'keranjang' ? 'border-l-4 border-purple-700 bg-purple-50 text-purple-700' : 'border-l-4 border-transparent text-gray-600 hover:bg-gray-50' ?>">
                            <div class="flex items-center gap-3"><i data-lucide="shopping-cart" class="w-5 h-5"></i> Cek Keranjang</div>
                            <?php if($total_item_keranjang > 0): ?>
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $total_item_keranjang ?></span>
                            <?php endif; ?>
                        </button>
                        <a href="katalog.php" class="w-full flex items-center gap-3 px-6 py-4 text-left text-gray-600 hover:bg-gray-50 hover:text-purple-600 font-semibold border-l-4 border-transparent transition">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i> Beli Produk Baru
                        </a>
                        <hr class="my-2 border-gray-100">
                        <a href="logout.php" onclick="return confirm('Yakin ingin keluar dari sesi ini?')" class="w-full flex items-center gap-3 px-6 py-4 text-left text-red-500 hover:bg-red-50 font-semibold border-l-4 border-transparent transition">
                            <i data-lucide="log-out" class="w-5 h-5"></i> Logout
                        </a>
                    </nav>
                </div>
            </aside>

            <div class="md:w-3/4">
                
                <!-- TAB BIODATA -->
                <div id="tab-biodata" class="tab-content <?= $active_tab == 'biodata' ? 'active' : '' ?> bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Pengaturan Biodata</h2>

                    <div class="mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200 flex flex-col md:flex-row items-center gap-6">
                        <img src="<?= $foto_profil_url ?>" alt="Preview" class="w-24 h-24 rounded-full object-cover shadow-sm border-2 border-white">
                        <div class="flex-grow text-center md:text-left">
                            <h4 class="font-bold text-gray-800 mb-1">Ganti Foto Profil</h4>
                            <p class="text-xs text-gray-500 mb-4">Format: JPG/PNG, Maksimal: 2MB</p>
                            
                            <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 items-center">
                                <label class="cursor-pointer bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                                    <i data-lucide="upload-cloud" class="w-4 h-4"></i> Pilih File
                                    <input type="file" name="file_foto" class="hidden" id="input_foto" required onchange="document.getElementById('nama_file').innerText = this.files[0].name">
                                </label>
                                <span id="nama_file" class="text-xs text-gray-500 mx-2 truncate max-w-[150px]">Belum ada file dipilih</span>
                                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow">Upload</button>
                            </form>
                        </div>
                    </div>

                    <form action="" method="POST" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email <span class="text-xs text-gray-400 font-normal">(Tidak bisa diubah)</span></label>
                                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly class="w-full bg-gray-200 border border-gray-300 text-gray-500 cursor-not-allowed px-4 py-3 rounded-xl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telp" value="<?= htmlspecialchars($user['no_telp']) ?>" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Pengiriman Lengkap</label>
                            <textarea name="alamat" rows="4" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500"><?= htmlspecialchars($user['alamat_lengkap']) ?></textarea>
                        </div>
                        <div class="pt-4 flex justify-end">
                            <button type="submit" name="btn_simpan_biodata" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold px-8 py-3 rounded-xl shadow-md transition flex items-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB KERANJANG -->
                <div id="tab-keranjang" class="tab-content <?= $active_tab == 'keranjang' ? 'active' : '' ?> bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
                    <div class="flex items-center justify-between mb-6 border-b pb-4">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                            Keranjang Belanja
                        </h2>
                        <span class="bg-purple-100 text-purple-700 text-sm font-bold px-3 py-1 rounded-full"><?= $total_item_keranjang ?> Item</span>
                    </div>

                    <?php if($total_item_keranjang > 0): ?>
                        <div class="flex flex-col lg:flex-row gap-8">
                            <div class="lg:w-2/3 space-y-4">
                                <?php 
                                while($item = $query_keranjang->fetch_assoc()): 
                                    $subtotal_item = $item['harga_jual'] * $item['qty'];
                                    $grand_total += $subtotal_item;
                                    $img = !empty($item['image_url']) ? $item['image_url'] : 'https://via.placeholder.com/100?text=No+Image';
                                ?>
                                <div class="flex flex-col sm:flex-row items-center gap-4 p-4 border border-gray-100 rounded-xl bg-gray-50 hover:bg-white hover:shadow-md transition">
                                    <img src="<?= $img ?>" class="w-24 h-24 object-cover rounded-lg bg-white border border-gray-200">
                                    
                                    <div class="flex-grow text-center sm:text-left">
                                        <h4 class="font-bold text-gray-800 leading-tight"><?= htmlspecialchars($item['spek']) ?></h4>
                                        <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($item['merk_tipe_motor']) ?></p>
                                        <p class="text-purple-700 font-bold">Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></p>
                                    </div>
                                    
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex items-center bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                            <a href="keranjang_aksi.php?action=update&op=min&id=<?= $item['cart_id'] ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition"><i data-lucide="minus" class="w-4 h-4"></i></a>
                                            <span class="px-3 py-1 text-sm font-bold border-l border-r border-gray-200"><?= $item['qty'] ?></span>
                                            <a href="keranjang_aksi.php?action=update&op=plus&id=<?= $item['cart_id'] ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition"><i data-lucide="plus" class="w-4 h-4"></i></a>
                                        </div>
                                        <a href="keranjang_aksi.php?action=delete&id=<?= $item['cart_id'] ?>" onclick="return confirm('Hapus produk ini dari keranjang?')" class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1 font-semibold">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="lg:w-1/3">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 sticky top-24">
                                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Belanja</h3>
                                    
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span>Total Harga (<?= $total_item_keranjang ?>)</span>
                                        <span>Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600 mb-4 pb-4 border-b border-gray-300">
                                        <span>Biaya Admin</span>
                                        <span>Gratis</span>
                                    </div>
                                    
                                    <div class="flex justify-between text-lg font-black text-gray-800 mb-6">
                                        <span>Total Tagihan</span>
                                        <span class="text-purple-700">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                                    </div>

                                    <!-- PERUBAHAN DISINI: Tombol kini mengarahkan ke halaman checkout.php -->
                                    <a href="checkout.php" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-3.5 rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-center">
                                        Lanjut Pembayaran <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="min-h-[400px] flex flex-col items-center justify-center text-center">
                            <div class="bg-gray-50 w-32 h-32 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-inner">
                                <i data-lucide="shopping-cart" class="w-16 h-16 text-gray-300"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Keranjang Belanjaan Kosong</h2>
                            <p class="text-gray-500 mb-8 max-w-sm">Anda belum memasukkan produk apapun ke dalam keranjang. Yuk, mulai cari oli terbaik untuk motormu!</p>
                            <a href="katalog.php" class="bg-purple-700 hover:bg-purple-800 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center gap-2 transform hover:-translate-y-1">
                                <i data-lucide="search" class="w-5 h-5"></i> Cari Produk Sekarang
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');
            document.querySelectorAll('.sidebar-menu').forEach(el => {
                el.classList.remove('border-purple-700', 'bg-purple-50', 'text-purple-700');
                el.classList.add('border-transparent', 'text-gray-600');
            });
            const activeMenu = document.getElementById('menu-' + tabId);
            activeMenu.classList.remove('border-transparent', 'text-gray-600');
            activeMenu.classList.add('border-purple-700', 'bg-purple-50', 'text-purple-700');
            window.history.pushState(null, '', '?tab=' + tabId);
        }
    </script>
</body>
</html>