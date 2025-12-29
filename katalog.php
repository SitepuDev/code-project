<?php
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Website Premium | WEBPRO</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- UPGRADED UI/UX STYLES (Tetap Sesuai Permintaan) --- */
        
        .announcement-bar {
            background: linear-gradient(90deg, var(--primary), #a855f7);
            color: white;
            padding: 10px 0;
            position: fixed;
            top: 70px; 
            width: 100%;
            z-index: 99;
            overflow: hidden;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .marquee-content {
            display: flex;
            white-space: nowrap;
            animation: marquee 20s linear infinite;
        }

        .marquee-content span {
            padding-right: 50px;
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

     .full-katalog {
    padding-top: 140px; /* navbar + announcement */
    padding-left: 5%;
    padding-right: 5%;
    padding-bottom: 80px;
    background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05), transparent);
    min-height: 100vh;
}


        .header-katalog {
            max-width: 700px;
            margin: 0 auto 50px;
            text-align: center;
        }

        .header-katalog h1 {
            font-size: clamp(2.5rem, 6vw, 3.5rem);
            background: linear-gradient(to right, var(--primary), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .filter-tags {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 10px 24px;
            border-radius: 50px;
            border: 2px solid var(--border);
            font-size: 14px;
            font-weight: 600;
            color: var(--text-soft);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: var(--bg-card);
        }

        .tag.active, .tag:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
        }

        .catalog {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 35px;
        }
        /* --- SHOPEE STYLE UPGRADE --- */
.card-website {
    background: var(--bg-card);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    position: relative;
}

.card-website:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    border-color: var(--primary);
}

.price-tag {
    color: #ee4d2d; /* Warna Oranye Shopee */
    font-size: 1.4rem;
    font-weight: 800;
    margin: 10px 0;
}

.sold-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-soft);
    margin-bottom: 15px;
}

.star-rating {
    color: #ffce3d;
    font-weight: bold;
}

.badge-diskon {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ff4757;
    color: white;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    z-index: 2;
}
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo"><a href="index.php" style="text-decoration:none;">WEBPRO</a></div>
    <div class="nav-links" style="display:flex; align-items:center; gap:20px;">
        <a href="index.php" style="font-weight:600; color:var(--text-soft); text-decoration:none;">Beranda</a>
        <button id="themeToggle" class="theme-toggle">🌙</button>
        <?php if(isset($_SESSION['admin'])): ?>
            <a href="dashboard.php" class="btn-login" style="padding: 5px 15px; font-size:13px;">Admin</a>
        <?php endif; ?>
    </div>
</nav>

<div class="announcement-bar">
    <div class="marquee-content">
        <span>🚀 <b>Menerima Jasa Pembuatan Aplikasi</b> (Android, iOS, & Web Custom) — Hubungi Kami untuk Konsultasi Gratis!</span>
        <span>✨ Promo Spesial Akhir Tahun: Diskon 20% untuk Pembuatan Website E-Commerce!</span>
        <span>🔥 <b>Menerima Jasa Pembuatan Aplikasi</b> — Solusi Digital Terbaik untuk Bisnis Anda.</span>
    </div>
</div>

<main class="full-katalog">
    <div class="header-katalog">
        <h1>Katalog Website</h1>
        <p>Eksplorasi koleksi desain premium kami. Setiap template dirancang untuk performa maksimal dan pengalaman pengguna yang luar biasa.</p>
    </div>

    <div class="filter-tags">
        <div class="tag active" onclick="filterKatalog('Semua', this)">Semua</div>
        <div class="tag" onclick="filterKatalog('Landing Page', this)">Landing Page</div>
        <div class="tag" onclick="filterKatalog('E-Commerce', this)">E-Commerce</div>
        <div class="tag" onclick="filterKatalog('Portfolio', this)">Portfolio</div>
        <div class="tag" onclick="filterKatalog('Company Profile', this)">Company Profile</div>
    </div>

    <div class="catalog">
        <p style="text-align:center; grid-column: 1/-1; color: var(--text-soft); font-style: italic;">
            Sedang memuat karya terbaik untuk Anda...
        </p>
    </div>
</main>

<footer style="padding: 60px 20px; text-align: center; border-top: 1px solid var(--border); background: var(--bg-card); margin-top: 50px;">
    <p style="color: var(--text-soft); font-size: 14px; letter-spacing: 0.5px;">
        &copy; 2024 <b>WEBPRO Digital Agency</b>. Developed by ELROY ABRAM ANUGRAHTA SITEPU.
    </p>
</footer>

<script src="script.js"></script>
<script src="theme.js"></script>

<script>
    // PERBAIKAN LOGIKA FILTER:
    // Fungsi ini sekarang akan mencari data di dalam variabel 'allData' yang ada di script.js
    function filterKatalog(kategori, el) {
        // 1. Ubah tampilan tombol aktif
        document.querySelectorAll('.tag').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        
        // 2. Cek apakah variabel allData (dari fetch script.js) sudah tersedia
        if (typeof allData !== 'undefined' && allData.length > 0) {
            if (kategori === "Semua") {
                renderKatalog(allData);
            } else {
                // Filter data berdasarkan kategori (Case Insensitive & Trim Spasi)
                const filtered = allData.filter(item => 
                    (item.kategori || "").toLowerCase().trim() === kategori.toLowerCase().trim()
                );
                renderKatalog(filtered);
            }
        } else {
            console.error("Data belum dimuat dari server.");
        }
    }
</script>

</body>
</html>