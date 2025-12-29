<?php
include 'auth.php';
include 'koneksi.php';

// 1. LOGIKA TAMBAH ADMIN BARU
if (isset($_POST['tambah'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $pass);
    
    if($stmt->execute()){
        header("Location: admin_list.php?status=admin_ditambah");
        exit;
    }
}

// 2. LOGIKA HAPUS WEBSITE (KATALOG)
if (isset($_GET['hapus_web'])) {
    $id = (int)$_GET['hapus_web'];
    $res = $conn->query("SELECT gambar_file, demo_file FROM website WHERE id = $id");
    $data = $res->fetch_assoc();
    
    if ($data) {
        if (file_exists("storage/private/" . $data['gambar_file'])) unlink("storage/private/" . $data['gambar_file']);
        if (file_exists("storage/private/" . $data['demo_file'])) unlink("storage/private/" . $data['demo_file']);
        
        $conn->query("DELETE FROM website WHERE id = $id");
        header("Location: admin_list.php?status=web_terhapus");
        exit;
    }
}

$resultWeb = $conn->query("SELECT * FROM website ORDER BY id DESC");
$resultAdmin = $conn->query("SELECT id, username FROM admin");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin & Katalog - WEBPRO</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-header { padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); border-bottom: 1px solid var(--border); }
        .dashboard-nav { display: flex; gap: 30px; padding: 0 40px; background: var(--bg-card); border-bottom: 1px solid var(--border); }
        .dashboard-nav a { padding: 15px 0; text-decoration: none; color: var(--text-soft); font-weight: 600; font-size: 14px; position: relative; display: flex; align-items: center; gap: 8px; }
        .dashboard-nav a.active { color: var(--primary); }
        .dashboard-nav a.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--primary); }
        .btn-logout { color: #ef4444; border: 1px solid #ef4444; padding: 5px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .manage-section { margin-bottom: 50px; }
        .table-container { background: var(--bg-card); border-radius: 15px; border: 1px solid var(--border); overflow-x: auto; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; color: var(--text); }
        th, td { padding: 15px 20px; border-bottom: 1px solid var(--border); text-align: left; }
        th { background: rgba(255,255,255,0.03); font-size: 12px; text-transform: uppercase; color: var(--text-soft); }
        .btn-edit { color: var(--success); text-decoration: none; font-weight: 700; margin-right: 15px; }
        .btn-delete { color: #ef4444; text-decoration: none; font-weight: 700; }
        .badge-cat { font-size: 10px; background: var(--primary); color: white; padding: 2px 8px; border-radius: 4px; }
    </style>
</head>
<body class="dashboard-page">

<header class="dashboard-header">
    <div class="logo" style="font-weight: 800; color: var(--primary); letter-spacing: 1px;">ADMIN PANEL</div>
    <div style="display:flex; align-items:center; gap:20px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <button id="themeToggle" class="theme-toggle" style="background:none; border:none; cursor:pointer; font-size: 18px;">🌙</button>
            <span style="font-size:14px;">Admin: <b><?= $_SESSION['admin']; ?></b></span>
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</header>

<nav class="dashboard-nav">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="admin.php">➕ Input Website</a>
    <a href="input_penjualan.php">💰 Catat Penjualan</a>
    <a href="admin_list.php" class="active">📂 Data Admin</a>
    <a href="index.php" target="_blank">🌐 Lihat Web</a>
</nav>

<main class="dashboard-content" style="max-width:1100px; margin:auto; padding:40px 20px;">
    <section class="manage-section">
        <div class="dashboard-title">
            <h2 style="font-size:24px;">Daftar Katalog Website</h2>
            <p style="color:var(--text-soft);">Kelola data website yang muncul di halaman katalog publik.</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Judul & Kategori</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($web = $resultWeb->fetch_assoc()): ?>
                    <tr>
                        <td><img src="image.php?file=<?= $web['akses_token'] ?>" style="width: 70px; border-radius: 6px;"></td>
                        <td>
                            <div style="font-weight:700; margin-bottom:4px;"><?= $web['judul'] ?></div>
                            <span class="badge-cat"><?= $web['kategori'] ?></span>
                        </td>
                        <td><b>Rp <?= number_format($web['harga'], 0, ',', '.') ?></b></td>
                        <td>
                            <a href="admin_update.php?id=<?= $web['id'] ?>" class="btn-edit">EDIT</a>
                            <a href="admin_list.php?hapus_web=<?= $web['id'] ?>" onclick="return confirm('Yakin hapus website ini?')" class="btn-delete">HAPUS</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="manage-section">
        <div class="dashboard-title">
            <h2 style="font-size:24px;">Akun Administrator</h2>
            <p style="color:var(--text-soft);">Tambah akun admin baru untuk mengelola panel.</p>
        </div>

        <form method="POST" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap: wrap; background: var(--bg-card); padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
            <input name="username" placeholder="Username Baru" required style="padding:12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); flex:1; min-width:200px;">
            <input name="password" type="password" placeholder="Password" required style="padding:12px; border-radius:8px; border:1px solid var(--border); background:var(--bg); color:var(--text); flex:1; min-width:200px;">
            <button name="tambah" class="btn-primary" style="padding:12px 25px; border-radius:8px; cursor:pointer; background: var(--primary); color: white; border: none; font-weight: 700;">+ Tambah Admin</button>
        </form>

        <div class="table-container" style="max-width: 600px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($adm = $resultAdmin->fetch_assoc()): ?>
                    <tr>
                        <td><?= $adm['id'] ?></td>
                        <td><b><?= $adm['username'] ?></b></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
    const btn = document.getElementById('themeToggle');
    btn.onclick = () => {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', newTheme);
        btn.textContent = newTheme === 'light' ? '🌙' : '☀️';
    };
</script>
</body>
</html>