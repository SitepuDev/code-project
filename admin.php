<?php
include 'auth.php'; // Memastikan admin sudah login
include 'koneksi.php'; // Menghubungkan ke database

$error = ""; $sukses = "";

// ==========================================
// 1. LOGIKA SIMPAN WEBSITE BARU (KATALOG)
// ==========================================
if (isset($_POST['simpan'])) {
    if (empty($_POST['judul']) || empty($_POST['harga'])) {
        $error = "Judul dan harga wajib diisi";
    } else {
        $token = bin2hex(random_bytes(16)); 
        $folder = "storage/private/";
        if (!is_dir($folder)) { mkdir($folder, 0755, true); }

        $daftarNamaGambar = [];
        $jumlahGambar = count($_FILES['gambar']['name']);

        for ($i = 0; $i < $jumlahGambar; $i++) {
            $tmpName = $_FILES['gambar']['tmp_name'][$i];
            $fileName = $_FILES['gambar']['name'][$i];
            if ($tmpName != "") {
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $namaBaru = hash('sha256', uniqid() . $i) . '.' . $ext;
                if (move_uploaded_file($tmpName, $folder . $namaBaru)) {
                    $daftarNamaGambar[] = $namaBaru;
                }
            }
        }
        $stringGambar = implode(',', $daftarNamaGambar);

        $extDemo = strtolower(pathinfo($_FILES['demo']['name'], PATHINFO_EXTENSION));
        $namaDemo = hash('sha256', uniqid()) . '.' . $extDemo;
        move_uploaded_file($_FILES['demo']['tmp_name'], $folder . $namaDemo);

        $stmt = $conn->prepare("INSERT INTO website (judul, deskripsi, harga, gambar_file, demo_file, akses_token, kategori) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssissss", $_POST['judul'], $_POST['deskripsi'], $_POST['harga'], $stringGambar, $namaDemo, $token, $_POST['kategori']);
        
        if ($stmt->execute()) { 
            $sukses = "Website berhasil ditambahkan ke katalog!"; 
        } else { 
            $error = "Gagal simpan website: " . $conn->error; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Input Katalog | WEBPRO</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-card { background: var(--bg-card); padding: 30px; border-radius: 20px; border: 1px solid var(--border); margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .dashboard-nav { display: flex; gap: 30px; padding: 0 40px; background: var(--bg-card); border-bottom: 1px solid var(--border); }
        .dashboard-nav a { padding: 15px 0; text-decoration: none; color: var(--text-soft); font-weight: 600; font-size: 14px; position: relative; }
        .dashboard-nav a.active { color: var(--primary); }
        .dashboard-nav a.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--primary); }
        label { display:block; margin-bottom:8px; font-weight:600; font-size: 14px; color: var(--text); }
        input, select, textarea { width:100%; padding:12px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text); box-sizing: border-box; }
        .btn-submit { padding:15px; border-radius:12px; background:var(--primary); color:white; border:none; font-weight:700; cursor:pointer; width: 100%; margin-top: 10px; transition: 0.3s; }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>

<header class="dashboard-header" style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); border-bottom: 1px solid var(--border);">
  <div class="logo" style="font-weight: 800; color: var(--primary); font-size: 20px;">ADMIN PANEL</div>
  <div style="display:flex;align-items:center;gap:20px;">
    <span>Admin: <b><?= $_SESSION['admin']; ?></b></span>
    <a href="logout.php" style="color: #ef4444; text-decoration:none; font-weight:600; border: 1px solid #ef4444; padding: 5px 15px; border-radius: 8px;">Logout</a>
  </div>
</header>

<nav class="dashboard-nav">
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="admin.php" class="active">➕ Input Website</a>
  <a href="input_penjualan.php">💰 Catat Penjualan</a>
  <a href="admin_list.php">📂 Data Admin</a>
  <a href="index.php" target="_blank">🌐 Lihat Web</a>
</nav>

<main style="max-width:900px; margin:auto; padding:40px 20px;">

    <?php if($error): ?>
        <p style="color:#ef4444; background:rgba(239,68,68,0.1); padding:15px; border-radius:10px; text-align:center; margin-bottom: 20px;"><?= $error; ?></p>
    <?php endif; ?>
    <?php if($sukses): ?>
        <p style="color:var(--success); background:rgba(16,185,129,0.1); padding:15px; border-radius:10px; text-align:center; margin-bottom: 20px;"><?= $sukses; ?></p>
    <?php endif; ?>

    <div class="admin-card">
        <h2 style="margin-bottom: 20px; font-size: 22px;">➕ Tambah Katalog Website</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group" style="margin-bottom:15px;">
                <label>Judul Website</label>
                <input name="judul" placeholder="Contoh: Website Toko Baju Modern" required>
            </div>
            <div class="form-grid" style="margin-bottom:15px;">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori">
                        <option>Landing Page</option>
                        <option>E-Commerce</option>
                        <option>Portfolio</option>
                        <option>Company Profile</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" placeholder="150000" required>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label>Deskripsi (Poin-poin dipisah koma)</label>
                <textarea name="deskripsi" rows="3" placeholder="SEO Friendly, Responsive, Fast Loading"></textarea>
            </div>
            <div class="form-grid" style="margin-bottom:15px;">
                <div class="form-group">
                    <label>Gambar Preview (Bisa pilih banyak)</label>
                    <input type="file" name="gambar[]" multiple required>
                </div>
                <div class="form-group">
                    <label>File Demo (ZIP)</label>
                    <input type="file" name="demo" required>
                </div>
            </div>
            <button name="simpan" class="btn-submit">🚀 Simpan Ke Katalog</button>
        </form>
    </div>

</main>

<script src="theme.js"></script>
</body>
</html>