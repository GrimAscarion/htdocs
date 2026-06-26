<?php
// --- MULAI SESSION & CEK LOGIN ---
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// --- KONEKSI DATABASE ---
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_angkringan';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pesan_sukses = "";

// --- PROSES SIMPAN DATA (JIKA FORM DISUBMIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_stok'])) {
    $tanggal = $_POST['tanggal'];
    
    // Looping semua data yang diinput
    foreach ($_POST['stok_awal'] as $id_menu => $stok_awal) {
        $sisa_stok = $_POST['sisa_stok'][$id_menu];
        
        // Hanya simpan jika stok_awal diisi (lebih dari 0)
        if ($stok_awal > 0 || $sisa_stok > 0) {
            
            // Cek apakah data untuk tanggal & menu ini sudah ada
            $cek_sql = "SELECT id_stok FROM stok_harian WHERE tanggal = '$tanggal' AND id_menu = '$id_menu'";
            $cek_result = $conn->query($cek_sql);
            
            if ($cek_result->num_rows > 0) {
                // Jika sudah ada, UPDATE data
                $update_sql = "UPDATE stok_harian SET stok_awal = '$stok_awal', sisa_stok = '$sisa_stok' 
                               WHERE tanggal = '$tanggal' AND id_menu = '$id_menu'";
                $conn->query($update_sql);
            } else {
                // Jika belum ada, INSERT data baru
                $insert_sql = "INSERT INTO stok_harian (tanggal, id_menu, stok_awal, sisa_stok) 
                               VALUES ('$tanggal', '$id_menu', '$stok_awal', '$sisa_stok')";
                $conn->query($insert_sql);
            }
        }
    }
    $pesan_sukses = "Data stok untuk tanggal " . date('d-m-Y', strtotime($tanggal)) . " berhasil disimpan!";
}

// --- AMBIL DATA MENU UNTUK DITAMPILKAN DI FORM ---
$sqlMenu = "SELECT m.id_menu, m.nama_menu, k.nama_kategori 
            FROM menu m 
            JOIN kategori k ON m.id_kategori = k.id_kategori 
            WHERE m.ketersediaan = 'Tersedia'
            ORDER BY k.id_kategori ASC, m.nama_menu ASC";
$resultMenu = $conn->query($sqlMenu);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Stok Harian - Angkringan</title>
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
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .header {
            background: var(--gradient-calm);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: #5a3b75;
            text-decoration: none;
            font-weight: 600;
            background: rgba(255,255,255,0.4);
            padding: 8px 15px;
            border-radius: 20px;
            transition: 0.3s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.7);
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        h2 { color: #5a3b75; margin-top: 0; }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="date"] {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th { background-color: #f8f9fa; color: #555; }

        .kategori-row td {
            background-color: #eaddf3;
            font-weight: 600;
            color: #5a3b75;
            text-align: center;
        }

        input[type="number"] {
            width: 80px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
        }

        .btn-submit {
            background: #a280c4;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 30px;
            width: 100%;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #8e6eb0;
            box-shadow: 0 4px 10px rgba(162, 128, 196, 0.4);
        }
    </style>
</head>
<body>

    <header class="header">
        <a href="index.php">⬅ Kembali ke Dashboard</a>
        <div style="font-size: 24px;">📝</div>
    </header>

    <div class="container">
        <h2>Input Stok Harian</h2>
        <p style="color: #666; margin-bottom: 30px;">Masukkan jumlah barang yang dibawa (Stok Awal) dan sisanya saat warung tutup (Sisa Stok).</p>

        <?php if($pesan_sukses != ""): ?>
            <div class="alert-success">
                ✅ <?php echo $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="tanggal">Tanggal Jualan:</label>
                <!-- Default ke hari ini -->
                <input type="date" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th style="text-align: center;">Stok Awal (Bawa)</th>
                        <th style="text-align: center;">Sisa Stok (Sisa)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $current_kategori = "";
                    if ($resultMenu->num_rows > 0) {
                        while($row = $resultMenu->fetch_assoc()) {
                            // Membuat pemisah (header) antar kategori agar rapi
                            if ($current_kategori != $row['nama_kategori']) {
                                $current_kategori = $row['nama_kategori'];
                                echo "<tr class='kategori-row'><td colspan='3'>Kategori: $current_kategori</td></tr>";
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                                <td style="text-align: center;">
                                    <input type="number" name="stok_awal[<?php echo $row['id_menu']; ?>]" min="0" placeholder="0">
                                </td>
                                <td style="text-align: center;">
                                    <input type="number" name="sisa_stok[<?php echo $row['id_menu']; ?>]" min="0" placeholder="0">
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='3'>Tidak ada menu tersedia.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <button type="submit" name="simpan_stok" class="btn-submit">💾 Simpan Data Stok</button>
        </form>
    </div>

</body>
</html>