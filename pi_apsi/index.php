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

// --- 1. FITUR FILTER TANGGAL ---
if (isset($_GET['filter_tanggal']) && !empty($_GET['filter_tanggal'])) {
    $tanggalTerbaru = $_GET['filter_tanggal'];
} else {
    // Default: Cari Tanggal Data Terbaru
    $sqlTanggal = "SELECT MAX(tanggal) as tanggal_terbaru FROM stok_harian";
    $resultTanggal = $conn->query($sqlTanggal);
    $rowTanggal = $resultTanggal->fetch_assoc();
    $tanggalTerbaru = $rowTanggal['tanggal_terbaru'] ? $rowTanggal['tanggal_terbaru'] : date('Y-m-d');
}

// --- 2. AMBIL DATA STOK LENGKAP ---
$sqlStok = "SELECT 
                m.nama_menu, 
                k.nama_kategori, 
                s.stok_awal,
                s.sisa_stok,
                m.harga
            FROM stok_harian s
            JOIN menu m ON s.id_menu = m.id_menu
            JOIN kategori k ON m.id_kategori = k.id_kategori
            WHERE s.tanggal = '$tanggalTerbaru'
            ORDER BY k.nama_kategori ASC, m.nama_menu ASC";
$resultStok = $conn->query($sqlStok);


// --- 3. PERHITUNGAN DATA ---
$stokPerKategori = [];
$dataRestock = [];
$totalAsetStok = 0; 
$totalOmset = 0; // Variabel baru untuk perhitungan omset
$batasRestock = 5; 

if ($resultStok->num_rows > 0) {
    while($row = $resultStok->fetch_assoc()) {
        $dataSemuaStok[] = $row;

        // Data Chart
        $kat = $row['nama_kategori'];
        if (!isset($stokPerKategori[$kat])) {
            $stokPerKategori[$kat] = 0;
        }
        $stokPerKategori[$kat] += $row['sisa_stok'];

        // Data Restock
        if ($row['sisa_stok'] <= $batasRestock) {
            $dataRestock[] = $row;
        }

        // Hitung Aset Sisa
        $totalAsetStok += ($row['sisa_stok'] * $row['harga']);

        // --- FITUR MENGHITUNG OMSET ---
        // (Stok Awal - Sisa Stok) * Harga
        $terjual = $row['stok_awal'] - $row['sisa_stok'];
        // Pastikan nilainya tidak minus jika salah input
        if ($terjual > 0) {
            $totalOmset += ($terjual * $row['harga']);
        }
    }
}

// Data Chart.js
$chartLabels = [];
$chartData = [];
foreach ($stokPerKategori as $kategori => $jumlah) {
    $chartLabels[] = $kategori;
    $chartData[] = $jumlah;
}

setlocale(LC_TIME, 'id_ID');
$tanggalFormatted = strftime('%A, %d %B %Y', strtotime($tanggalTerbaru));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Angkringan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --gradient-calm: linear-gradient(135deg, #a280c4 0%, #f0d97f 60%, #fcfcfc 100%);
            --bg-light: #f4f6f9;
            --text-dark: #333;
        }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--bg-light); color: var(--text-dark); display: flex; flex-direction: column; min-height: 100vh; }
        .header { background: var(--gradient-calm); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .nav-links { display: flex; gap: 10px; }
        .header .btn-nav { color: #5a3b75; text-decoration: none; font-weight: 600; background: rgba(255,255,255,0.4); padding: 8px 15px; border-radius: 20px; transition: 0.3s; }
        .header .btn-nav:hover { background: rgba(255,255,255,0.7); }
        .user-icon { font-size: 28px; color: #5a3b75; text-decoration: none; transition: transform 0.2s; cursor: pointer; }
        .user-icon:hover { transform: scale(1.1); }
        
        .main-content { flex: 1; padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        .top-section { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; gap: 30px; }
        .info-text { flex: 1; }
        .info-text h1 { font-size: 24px; font-weight: 600; margin: 0 0 10px 0; color: #4a4a4a; }
        
        .highlight-data { font-size: 16px; padding: 10px 15px; border-radius: 8px; display: inline-block; margin-top: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);}
        .aset-box { background: #eaddf3; color: #5a3b75; }
        .omset-box { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .filter-form { display: flex; gap: 10px; align-items: center; margin: 15px 0; background: #fff; padding: 10px 15px; border-radius: 10px; display: inline-flex; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-form input { padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        .filter-form button { padding: 8px 15px; background: #a280c4; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500; transition: 0.3s; }
        .filter-form button:hover { background: #8e6eb0; }

        .chart-container { flex: 0 0 250px; height: 250px; position: relative; }
        .bottom-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .search-bar { width: 100%; padding: 12px 20px; border-radius: 25px; border: 1px solid #ddd; background-color: #e8eaed; margin-bottom: 25px; font-family: inherit; box-sizing: border-box; }
        .stock-list { display: flex; flex-direction: column; gap: 15px; max-height: 500px; overflow-y: auto; padding-right: 5px; }
        .stock-item { background-color: #fff; padding: 15px 20px; border-radius: 15px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .stock-item:hover { transform: translateX(5px); }
        .stock-item .category-tag { font-size: 12px; color: #777; font-weight: 400; }
        .stock-count { background: #f4f6f9; padding: 5px 15px; border-radius: 15px; font-weight: 600; color: #5a3b75; border: 1px solid #eee;}
        
        .placeholder-box { background-color: #fff; border-radius: 25px; padding: 30px; display: flex; flex-direction: column; min-height: 300px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .placeholder-box h2 { margin-top: 0; color: #d9534f; font-size: 20px; display: flex; align-items: center; gap: 10px; }
        .restock-list { list-style-type: none; padding: 0; margin: 0; width: 100%; }
        .restock-list li { padding: 12px 0; border-bottom: 1px solid #f1f1f1; font-size: 16px; display: flex; justify-content: space-between; }
        .sisa-merah { color: #d9534f; font-weight: bold; background: #fdf2f2; padding: 2px 8px; border-radius: 5px; }
        .footer { background: var(--gradient-calm); padding: 20px; text-align: center; color: #5a3b75; font-weight: 500; }
        
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; right: 0; top: 40px; background-color: #fff; min-width: 150px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1); border-radius: 12px; overflow: hidden; z-index: 1; }
        .dropdown-content a { color: #333; padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; font-weight: 500; border-bottom: 1px solid #f1f1f1; }
        .dropdown-content a:hover { background-color: #f4f6f9; color: #a280c4; }
        .dropdown-content a.logout-btn { color: #d9534f; }
        .dropdown:hover .dropdown-content { display: block; }
        
        @media (max-width: 768px) { .top-section { flex-direction: column; align-items: center; } .chart-container { margin-top: 20px; } .bottom-section { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <header class="header">
        <div class="nav-links">
            <a href="input_stok.php" class="btn-nav">📝 Input Stok</a>
            <a href="manajemen_menu.php" class="btn-nav">⚙️ Kelola Menu</a>
        </div>
        
        <div class="dropdown">
            <div class="user-icon" title="Menu Profil">👤</div>
            <div class="dropdown-content">
                <a href="#">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</a>
                <a href="logout.php" class="logout-btn">🚪 Logout</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <section class="top-section">
            <div class="info-text">
                <h1>Laporan Penjualan & Stok</h1>
                
                <form method="GET" action="" class="filter-form">
                    <label for="filter_tanggal">Pilih Tanggal:</label>
                    <input type="date" id="filter_tanggal" name="filter_tanggal" value="<?php echo $tanggalTerbaru; ?>">
                    <button type="submit">Filter Data</button>
                </form>

                <p>Menampilkan data untuk tanggal: <strong><?php echo $tanggalFormatted; ?></strong></p>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div class="highlight-data aset-box">
                        Estimasi Aset Sisa Stok: <br><strong>Rp <?php echo number_format($totalAsetStok, 0, ',', '.'); ?></strong>
                    </div>
                    <div class="highlight-data omset-box">
                        Total Omset Penjualan: <br><strong>Rp <?php echo number_format($totalOmset, 0, ',', '.'); ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="stockPieChart"></canvas>
            </div>
        </section>

        <section class="bottom-section">
            <div class="left-column">
                <input type="text" class="search-bar" placeholder="Pencarian data stok/produk...">
                <div class="stock-list">
                    <?php if (isset($dataSemuaStok) && count($dataSemuaStok) > 0): ?>
                        <?php foreach ($dataSemuaStok as $item): ?>
                            <div class="stock-item">
                                <div>
                                    <?php echo htmlspecialchars($item['nama_menu']); ?><br>
                                    <span class="category-tag"><?php echo htmlspecialchars($item['nama_kategori']); ?> | Laku: <?php echo ($item['stok_awal'] - $item['sisa_stok']); ?> pcs</span>
                                </div>
                                <div class="stock-count">Sisa: <?php echo $item['sisa_stok']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center; color:#888; margin-top:20px;">Tidak ada data stok untuk tanggal ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="right-column">
                <div class="placeholder-box">
                    <h2>⚠️ Peringatan Restock (Sisa <= <?php echo $batasRestock; ?>)</h2>
                    <?php if (count($dataRestock) > 0): ?>
                        <ul class="restock-list">
                            <?php foreach ($dataRestock as $alertItem): ?>
                                <li>
                                    <?php echo htmlspecialchars($alertItem['nama_menu']); ?>
                                    <span class="sisa-merah">Sisa: <?php echo $alertItem['sisa_stok']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p style="text-align:center; margin-top: 50px;">Aman! Stok semua item masih mencukupi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        © <?php echo date('Y'); ?> Dashboard Angkringan - Manajemen Stok Digital
    </footer>

    <script>
        const chartLabels = <?php echo json_encode($chartLabels); ?>;
        const chartData = <?php echo json_encode($chartData); ?>;
        const ctx = document.getElementById('stockPieChart').getContext('2d');
        const calmColors = ['#a280c4', '#f0d97f', '#81c784', '#64b5f6', '#ff8a65', '#ba68c8'];
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartLabels,
                datasets: [{ data: chartData, backgroundColor: calmColors, borderColor: '#ffffff', borderWidth: 2 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15, font: { size: 11 } } },
                    title: { display: true, text: 'Komposisi Sisa Stok per Kategori', font: { size: 14, weight: 'normal' }, color: '#666', padding: { bottom: 20 } }
                }
            }
        });
    </script>
</body>
</html>