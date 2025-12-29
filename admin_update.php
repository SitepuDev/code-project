<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan ID ada
if (!isset($_GET['id'])) {
    header("Location: admin_list.php");
    exit;
}

$id = $_GET['id'];
$res = $conn->query("SELECT * FROM website WHERE id=$id");
$old = $res->fetch_assoc();

if (!$old) {
    header("Location: admin_list.php");
    exit;
}

if (isset($_POST['update'])) {
    $judul = $_POST['judul'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $folder = "storage/private/";

    // 1. Update data teks menggunakan Prepared Statement
    $stmt = $conn->prepare("UPDATE website SET judul=?, harga=?, kategori=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("sissi", $judul, $harga, $kategori, $deskripsi, $id);
    $stmt->execute();

    // 2. Logika Update Banyak Gambar (Jika ada file baru yang diunggah)
    if (!empty($_FILES['gambar']['name'][0])) {
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

        if (!empty($daftarNamaGambar)) {
            $stringGambar = implode(',', $daftarNamaGambar);
            // Hapus/Timpa kolom gambar_file dengan yang baru
            $conn->query("UPDATE website SET gambar_file='$stringGambar' WHERE id=$id");
        }
    }

    // 3. Logika Update File Demo (Opsional)
    if ($_FILES['demo']['name'] != "") {
        $extDemo = strtolower(pathinfo($_FILES['demo']['name'], PATHINFO_EXTENSION));
        $namaDemo = hash('sha256', uniqid()) . '.' . $extDemo;
        move_uploaded_file($_FILES['demo']['tmp_name'], $folder . $namaDemo);
        $conn->query("UPDATE website SET demo_file='$namaDemo' WHERE id=$id");
    }

    header("Location: admin_list.php?status=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Website - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-card { background: var(--bg-card); padding: 40px; border-radius: 20px; border: 1px solid var(--border); }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text); }
        .current-images { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }
        .img-preview { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid var(--border); }
    </style>
</head>
<body class="dashboard-page">
<main class="dashboard-content" style="max-width:900px; margin:auto; padding:50px 20px;">
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 28px;">Update: <?= htmlspecialchars($old['judul']) ?></h2>
        <p style="color: var(--text-soft);">Perbarui informasi website atau ganti koleksi gambar slider.</p>
    </div>

    <div class="admin-card">
        <form method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:20px;">
            
            <div class="form-group">
                <label>Judul Website</label>
                <input name="judul" value="<?= htmlspecialchars($old['judul']) ?>" class="form-control" required style="width:100%; padding:12px; background:var(--bg); color:var(--text); border:1px solid var(--border); border-radius:10px;">
            </div>
            
            <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" style="width:100%; padding:12px; background:var(--bg); color:var(--text); border:1px solid var(--border); border-radius:10px;">
                        <option value="Landing Page" <?= $old['kategori'] == 'Landing Page' ? 'selected' : '' ?>>Landing Page</option>
                        <option value="E-Commerce" <?= $old['kategori'] == 'E-Commerce' ? 'selected' : '' ?>>E-Commerce</option>
                        <option value="Portfolio" <?= $old['kategori'] == 'Portfolio' ? 'selected' : '' ?>>Portfolio</option>
                        <option value="Company Profile" <?= $old['kategori'] == 'Company Profile' ? 'selected' : '' ?>>Company Profile</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= $old['harga'] ?>" class="form-control" required style="width:100%; padding:12px; background:var(--bg); color:var(--text); border:1px solid var(--border); border-radius:10px;">
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi (Pisahkan fitur dengan koma untuk tampilan list nomor)</label>
                <textarea name="deskripsi" class="form-control" style="width:100%; padding:12px; background:var(--bg); color:var(--text); border:1px solid var(--border); border-radius:10px; min-height:120px;"><?= htmlspecialchars($old['deskripsi']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Ganti Gambar Slider (Multiple)</label>
                <input type="file" name="gambar[]" accept="image/*" multiple style="width:100%; padding:9px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
                <p style="font-size: 12px; color: var(--text-soft); margin-top: 5px;">*Kosongkan jika tidak ingin mengganti gambar. Jika upload baru, gambar lama akan dihapus dari tampilan.</p>
                
                <div class="current-images">
                    <?php 
                    $imgs = explode(',', $old['gambar_file']);
                    foreach($imgs as $img): if(!empty($img)):
                    ?>
                        <img src="image.php?file=<?= trim($old['akses_token']) ?>&img=<?= trim($img) ?>" class="img-preview" title="Gambar Saat Ini">
                    <?php endif; endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Ganti File Demo (ZIP)</label>
                <input type="file" name="demo" style="width:100%; padding:9px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text);">
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button name="update" class="btn-primary" style="flex: 2; padding:15px; border-radius:12px; background:var(--primary); color:white; border:none; font-weight:700; cursor:pointer; font-size: 16px;">💾 Simpan Perubahan</button>
                <a href="admin_list.php" style="flex: 1; text-align:center; padding: 15px; border-radius: 12px; border: 1px solid var(--border); color:var(--text-soft); text-decoration:none; font-weight: 600;">Batal</a>
            </div>
        </form>
    </div>
</main>
<script src="theme.js"></script>
</body>
</html>