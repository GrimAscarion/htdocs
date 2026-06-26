<?php
// login.php - Halaman Login Angkringan
session_start();

// JIKA SUDAH LOGIN, LANGSUNG TENDANG KE DASHBOARD
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_angkringan';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error_pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error_pesan = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Angkringan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gradient-calm: linear-gradient(135deg, #a280c4 0%, #f0d97f 60%, #fcfcfc 100%);
            --bg-light: #f4f6f9;
            --text-dark: #333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: var(--gradient-calm);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--text-dark);
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center;
        }

        h2 {
            color: #5a3b75;
            margin-bottom: 10px;
            font-weight: 600;
        }

        p {
            color: #777;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-family: inherit;
            box-sizing: border-box;
            outline: none;
        }
        
        .form-group input:focus {
            border-color: #a280c4;
        }

        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #ebccd1;
        }

        .btn-login {
            background: #a280c4;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #8e6eb0;
            box-shadow: 0 4px 10px rgba(162, 128, 196, 0.4);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Sistem Admin</h2>
        <p>Silakan login untuk kelola stok angkringan</p>

        <?php if($error_pesan != ""): ?>
            <div class="alert-danger">
                ❌ <?php echo $error_pesan; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" class="btn-login">🔒 Masuk</button>
        </form>
    </div>

</body>
</html>