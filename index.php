<?php
session_start();
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEBPRO | Jasa Website Profesional</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- ANIMASI TRANSISI MASUK --- */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Sembunyikan konten sebelum animasi jalan */
        .reveal {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Delay agar elemen muncul bergantian (Stagger effect) */
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }

        /* --- STYLING HERO --- */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5%;
            text-align: center;
            background: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        }

        .hero-content {
            max-width: 900px;
            width: 100%;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            line-height: 1.1;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .hero-content p {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--text-soft);
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }

        .btn-katalog {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 18px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            border: 2px solid var(--primary);
        }

        .btn-katalog:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.6);
            background: transparent;
            color: var(--primary);
        }

        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(var(--bg-rgb), 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid var(--border);
            /* Navbar juga ikut reveal */
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">WEBPRO</div>
    <div class="nav-links" style="display: flex; align-items: center; gap: 15px;">
        <button id="themeToggle" class="theme-toggle">🌙</button>
        <?php if (isset($_SESSION['admin'])): ?>
            <a href="dashboard.php" class="btn-login" style="padding: 8px 20px;">Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="btn-login" style="padding: 8px 20px;">Login</a>
        <?php endif; ?>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1 class="reveal delay-1">
            Website Profesional <br>
            <span style="background: linear-gradient(to right, var(--primary), #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                untuk Bisnis Anda
            </span>
        </h1>
        
        <p class="reveal delay-2">
            Tingkatkan kepercayaan pelanggan dengan desain website modern, responsif, dan performa tinggi yang siap pakai.
        </p>
        
        <div class="reveal delay-3" style="margin-top: 10px;">
            <a href="katalog.php" class="btn-katalog">Eksplorasi Katalog</a>
        </div>
    </div>
</section>

<script src="theme.js"></script>
</body>
</html>