<?php
include 'auth.php';
include 'koneksi.php';

$error = ""; $sukses = "";

if (isset($_POST['simpan_transaksi'])) {
    $id_web = $_POST['website_id'];
    $pembeli = $_POST['nama_klien'];
    $harga_final = $_POST['total_bayar'];

    if(!empty($id_web) && !empty($pembeli)) {
        // Ambil judul website otomatis
        $res = $conn->query("SELECT judul FROM website WHERE id = '$id_web'");
        $dataWeb = $res->fetch_assoc();
        $nama_web_terpilih = $dataWeb['judul'];

        $stmt_t = $conn->prepare("INSERT INTO transaksi (website_id, nama_klien, nama_website, jumlah, total) VALUES (?, ?, ?, 1, ?)");
        $stmt_t->bind_param("issi", $id_web, $pembeli, $nama_web_terpilih, $harga_final);
        
        if($stmt_t->execute()) {
            $sukses = "Penjualan berhasil dicatat! Cek di Dashboard.";
        } else {
            $error = "Gagal: " . $conn->error;
        }
    }
}

$listWebsite = $conn->query("SELECT id, judul, harga FROM website ORDER BY judul ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catat Penjualan - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">

<header class="dashboard-header" style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); border-bottom: 1px solid var(--border);">
    <div class="logo" style="font-weight: 800; color: var(--primary);">ADMIN PANEL</div>
    <div class="admin-info">
        <span>Admin: <b><?= $_SESSION['admin']; ?></b></span>
    </div>
</header>

<nav class="dashboard-nav" style="display: flex; gap: 30px; padding: 0 40px; background: var(--bg-card); border-bottom: 1px solid var(--border);">
    <a href="dashboard.php" style="padding: 15px 0; text-decoration: none; color: var(--text-soft);">🏠 Dashboard</a>
    <a href="admin.php" style="padding: 15px 0; text-decoration: none; color: var(--text-soft);">➕ Input Website</a>
    <a href="input_penjualan.php" class="active" style="padding: 15px 0; text-decoration: none; color: var(--primary); border-bottom: 3px solid var(--primary);">💰 Catat Penjualan</a>
    <a href="admin_list.php" style="padding: 15px 0; text-decoration: none; color: var(--text-soft);">📂 Data Admin</a>
    <a href="index.php" target="_blank" style="padding: 15px 0; text-decoration: none; color: var(--text-soft);">🌐 Lihat Web</a>
</nav>

<main style="max-width:700px; margin:auto; padding:50px 20px;">
    <div class="admin-card" style="background: var(--bg-card); padding: 30px; border-radius: 20px; border: 1px solid var(--border);">
        <h2 style="margin-bottom: 20px;">💰 Catat Penjualan Baru</h2>
        
        <?php if($sukses) echo "<p style='color:var(--success);'>$sukses</p>"; ?>
        <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <label style="display:block; margin-bottom:10px;">Pilih Website</label>
            <select name="website_id" required style="width:100%; padding:12px; margin-bottom:20px; border-radius:10px; background:var(--bg); color:var(--text); border:1px solid var(--border);">
                <?php while($web = $listWebsite->fetch_assoc()): ?>
                    <option value="<?= $web['id'] ?>"><?= $web['judul'] ?></option>
                <?php endwhile; ?>
            </select>

            <label style="display:block; margin-bottom:10px;">Nama Klien</label>
            <input name="nama_klien" required style="width:100%; padding:12px; margin-bottom:20px; border-radius:10px; background:var(--bg); color:var(--text); border:1px solid var(--border);">

            <label style="display:block; margin-bottom:10px;">Harga Deal (Rp)</label>
            <input type="number" name="total_bayar" required style="width:100%; padding:12px; margin-bottom:20px; border-radius:10px; background:var(--bg); color:var(--text); border:1px solid var(--border);">

            <button name="simpan_transaksi" style="width:100%; padding:15px; background:var(--primary); color:white; border:none; border-radius:10px; font-weight:700; cursor:pointer;">Simpan Penjualan</button>
        </form>
    </div>
</main>

</body>
</html>