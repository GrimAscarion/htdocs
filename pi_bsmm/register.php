<?php
session_start();

// Jika user sudah login, langsung lempar ke index
if(isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'koneksi.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $telp = $conn->real_escape_string(trim($_POST['telp']));
    $alamat = $conn->real_escape_string(trim($_POST['alamat']));
    
    // Enkripsi password menggunakan bcrypt untuk keamanan
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah pernah didaftarkan
    $cek_email = $conn->query("SELECT id FROM customers WHERE email='$email'");
    
    if($cek_email->num_rows > 0) {
        $error = "Email tersebut sudah terdaftar! Silakan gunakan email lain atau Login.";
    } else {
        // Masukkan data pelanggan baru ke database
        $query = "INSERT INTO customers (nama_lengkap, email, password, no_telp, alamat_lengkap) 
                  VALUES ('$nama', '$email', '$password', '$telp', '$alamat')";
                  
        if($conn->query($query)) {
            $success = "Pendaftaran berhasil! Silakan login untuk mulai berbelanja.";
        } else {
            $error = "Terjadi kesalahan sistem: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- Dekorasi Latar Belakang -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl flex flex-col md:flex-row overflow-hidden relative z-10 m-4">
        
        <!-- Bagian Kiri (Informasi/Banner) -->
        <div class="md:w-5/12 bg-gradient-to-br from-purple-700 to-purple-900 text-white p-10 flex flex-col justify-center relative overflow-hidden hidden md:flex">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-bl-full"></div>
            <div class="relative z-10">
                <a href="index.php" class="inline-block mb-8 bg-white/20 p-2 rounded-xl backdrop-blur-sm hover:bg-white/30 transition">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-white"></i>
                </a>
                <h2 class="text-3xl font-bold mb-4">Bergabunglah<br>Bersama Kami!</h2>
                <p class="text-purple-100 leading-relaxed text-sm">
                    Buat akun sekarang untuk menikmati kemudahan belanja oli mesin secara online. Simpan keranjang belanjaanmu dan dapatkan info diskon terbaru dari Siska Maju Motor.
                </p>
            </div>
            
            <div class="mt-auto relative z-10 pt-10">
                <p class="text-sm text-purple-200">Sudah punya akun?</p>
                <a href="login.php" class="inline-block mt-2 font-bold text-yellow-400 hover:text-yellow-300 transition flex items-center gap-2">
                    Masuk ke Akun <i data-lucide="arrow-right-circle" class="w-5 h-5"></i>
                </a>
            </div>
        </div>

        <div class="md:w-7/12 p-8 md:p-12">
            <div class="md:hidden flex justify-between items-center mb-6">
                <a href="index.php" class="text-gray-400 hover:text-purple-600"><i data-lucide="arrow-left"></i></a>
                <a href="login.php" class="text-sm font-bold text-purple-600">Masuk Akun</a>
            </div>

            <div class="mb-8 text-center md:text-left">
                <h3 class="text-2xl font-bold text-gray-800">Daftar Akun Baru</h3>
                <p class="text-gray-500 text-sm mt-1">Lengkapi data diri Anda di bawah ini.</p>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-200 flex gap-3 items-start">
                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p><?= $error ?></p>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm mb-6 border border-green-200 flex gap-3 items-start">
                    <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p><?= $success ?></p>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input type="text" name="nama" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">No. WhatsApp/Telp</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                            <input type="text" name="telp" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                        <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                        <input type="password" name="password" required minlength="6" class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">*Minimal 6 karakter</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap Pengiriman</label>
                    <textarea name="alamat" rows="3" required class="w-full bg-gray-50 border border-gray-200 text-gray-800 p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm"></textarea>
                </div>

                <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-3 rounded-xl transition shadow-lg mt-6">
                    Buat Akun Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>