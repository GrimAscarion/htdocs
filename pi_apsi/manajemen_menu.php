<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = new mysqli('localhost', 'root', '', 'db_angkringan');
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// 1. PROSES HAPUS
if (isset($_GET['hapus_kategori'])) {
    $id = $_GET['hapus_kategori'];
    // Hati-hati: Karena di DB ada ON DELETE CASCADE, menghapus kategori akan menghapus menu didalamnya.
    $conn->query("DELETE FROM kategori WHERE id_kategori='$id'");
    header("Location: manajemen_menu.php"); exit();
}

if (isset($_GET['hapus_menu'])) {
    $id = $_GET['hapus_menu'];
    $conn->query("DELETE FROM menu WHERE id_menu='$id'");
    header("Location: manajemen_menu.php"); exit();
}

// 2. PROSES TAMBAH KATEGORI
if (isset($_POST['simpan_kategori'])) {
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $conn->query("INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    header("Location: manajemen_menu.php"); exit();
}

// 3. PROSES SIMPAN / EDIT MENU
if (isset($_POST['simpan_menu'])) {
    $id_menu      = $_POST['id_menu'];
    $id_kategori  = $_POST['id_kategori'];
    $nama_menu    = $conn->real_escape_string($_POST['nama_menu']);
    $harga        = $_POST['harga'];
    $ketersediaan = $_POST['ketersediaan'];

    if ($id_menu == "") {
        // Mode Tambah Baru
        $conn->query("INSERT INTO menu (id_kategori, nama_menu, harga, ketersediaan) 
                      VALUES ('$id_kategori', '$nama_menu', '$harga', '$ketersediaan')");
    } else {
        // Mode Edit
        $conn->query("UPDATE menu SET id_kategori='$id_kategori', nama_menu='$nama_menu', harga='$harga', ketersediaan='$ketersediaan' 
                      WHERE id_menu='$id_menu'");
    }
    header("Location: manajemen_menu.php"); exit();
}

// Tarik Data Untuk Tabel
$kategori_result = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$menu_result = $conn->query("SELECT m.*, k.nama_kategori FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori ORDER BY k.nama_kategori ASC, m.nama_menu ASC");

// Mode Edit Menu
$editMenu = null;
if (isset($_GET['edit_menu'])) {
    $id_edit = $_GET['edit_menu'];
    $editMenu = $conn->query("SELECT * FROM menu WHERE id_menu='$id_edit'")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu - Angkringan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --gradient-calm: linear-gradient(135deg, #a280c4 0%, #f0d97f 60%, #fcfcfc 100%); --bg-light: #f4f6f9; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--bg-light); color: #333; }
        .header { background: var(--gradient-calm); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: #5a3b75; text-decoration: none; font-weight: 600; background: rgba(255,255,255,0.4); padding: 8px 15px; border-radius: 20px; transition: 0.3s; }
        .header a:hover { background: rgba(255,255,255,0.7); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: flex; gap: 30px; flex-wrap: wrap; align-items: flex-start; }
        
        .card { background: #fff; padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box; }
        .col-kategori { flex: 1; min-width: 300px; }
        .col-menu { flex: 2; min-width: 500px; }
        
        h3 { color: #5a3b75; margin-top: 0; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 5px; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; box-sizing: border-box; }
        
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-family: inherit; }
        .btn-primary { background: #a280c4; color: white; }
        .btn-primary:hover { background: #8e6eb0; }
        .btn-danger { background: #d9534f; color: white; padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 5px; }
        .btn-warning { background: #f0ad4e; color: white; padding: 5px 10px; font-size: 12px; text-decoration: none; border-radius: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php">⬅ Kembali ke Dashboard</a>
        <div style="font-size: 24px;">⚙️</div>
    </header>

    <div class="container">
        
        <div class="card col-kategori">
            <h3>Manajemen Kategori</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Kategori Baru</label>
                    <input type="text" name="nama_kategori" required placeholder="Contoh: Gorengan">
                </div>
                <button type="submit" name="simpan_kategori" class="btn btn-primary">Tambah Kategori</button>
            </form>

            <table>
                <tr><th>Nama Kategori</th><th>Aksi</th></tr>
                <?php while($row = $kategori_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                    <td>
                        <a href="?hapus_kategori=<?php echo $row['id_kategori']; ?>" class="btn-danger" onclick="return confirm('Yakin hapus? Menu di dalamnya juga akan terhapus!');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="card col-menu">
            <h3><?php echo $editMenu ? 'Edit Menu' : 'Tambah Menu Baru'; ?></h3>
            <form method="POST">
                <input type="hidden" name="id_menu" value="<?php echo $editMenu ? $editMenu['id_menu'] : ''; ?>">
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Pilih Kategori</label>
                        <select name="id_kategori" required>
                            <option value="">-- Pilih --</option>
                            <?php 
                            $kategori_result->data_seek(0); // Reset pointer
                            while($k = $kategori_result->fetch_assoc()): 
                                $selected = ($editMenu && $editMenu['id_kategori'] == $k['id_kategori']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $k['id_kategori']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu" required value="<?php echo $editMenu ? htmlspecialchars($editMenu['nama_menu']) : ''; ?>">
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" required value="<?php echo $editMenu ? $editMenu['harga'] : ''; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Ketersediaan</label>
                        <select name="ketersediaan">
                            <option value="Tersedia" <?php echo ($editMenu && $editMenu['ketersediaan'] == 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="Habis" <?php echo ($editMenu && $editMenu['ketersediaan'] == 'Habis') ? 'selected' : ''; ?>>Habis</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="simpan_menu" class="btn btn-primary">
                    <?php echo $editMenu ? 'Simpan Perubahan' : 'Tambahkan Menu'; ?>
                </button>
                <?php if($editMenu): ?>
                    <a href="manajemen_menu.php" class="btn" style="background:#eee; text-decoration:none; color:#333; margin-left:10px;">Batal Edit</a>
                <?php endif; ?>
            </form>

            <table style="margin-top:30px;">
                <tr>
                    <th>Kategori</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while($row = $menu_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <span style="color: <?php echo $row['ketersediaan'] == 'Tersedia' ? 'green' : 'red'; ?>;">
                            <?php echo $row['ketersediaan']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="?edit_menu=<?php echo $row['id_menu']; ?>" class="btn-warning">Edit</a>
                        <a href="?hapus_menu=<?php echo $row['id_menu']; ?>" class="btn-danger" onclick="return confirm('Hapus menu ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>