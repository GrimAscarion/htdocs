<?php
session_start();

// Jika sudah login, cegah masuk ke halaman login lagi
if(isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'koneksi.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $result = $conn->query("SELECT * FROM customers WHERE email='$email'");

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password (mencocokkan teks asli dengan hash bcrypt di DB)
        if(password_verify($password, $user['password'])) {
            // Jika berhasil, simpan data ke dalam sesi browser (SESSION)
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['nama_lengkap'];
            
            // Arahkan kembali ke halaman depan
            header("Location: index.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Alamat email tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - Siska Maju Motor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- Dekorasi Latar Belakang -->
    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden relative z-10 m-4 p-8 sm:p-10">
        
        <div class="text-center mb-8">
            <a href="index.php" class="inline-block mb-4 hover:scale-105 transition transform">
                <img src="logo_web.png" alt="Siska Maju Motor" class="h-12 w-auto mx-auto" onerror="this.src='https://via.placeholder.com/150x40?text=Siska+Maju'">
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali</h2>
            <p class="text-gray-500 text-sm mt-2">Silakan masuk ke akun Anda untuk melanjutkan belanja.</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-200 flex gap-3 items-start">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                    <input type="email" name="email" required placeholder="email@contoh.com" class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-sm font-semibold text-gray-700">Password</label>
                    <a href="#" class="text-xs text-purple-600 hover:text-purple-800 font-medium">Lupa Password?</a>
                </div>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 text-gray-800 pl-10 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white font-bold py-3.5 rounded-xl transition shadow-lg mt-4 flex items-center justify-center gap-2">
                Masuk <i data-lucide="log-in" class="w-5 h-5"></i>
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="register.php" class="font-bold text-purple-700 hover:text-purple-900 transition underline">Daftar sekarang</a>
            </p>
            <div class="mt-6">
                <a href="index.php" class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1 transition">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i> Kembali ke Beranda
                </a>
            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>