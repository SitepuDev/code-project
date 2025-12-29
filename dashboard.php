<?php
include 'auth.php';
include 'koneksi.php';

/* =====================
   STATISTIK
===================== */
$totalProduk = $conn->query("SELECT COUNT(*) total FROM website")->fetch_assoc()['total'];

$totalTerjual = $conn->query("SELECT IFNULL(SUM(jumlah),0) total FROM transaksi")->fetch_assoc()['total'];

$totalPendapatan = $conn->query("SELECT IFNULL(SUM(total),0) total FROM transaksi")->fetch_assoc()['total'];

/* =====================
   DATA GRAFIK
===================== */
$grafik = [];
$q = $conn->query("
  SELECT DATE_FORMAT(created_at,'%b') bulan, SUM(total) total
  FROM transaksi
  GROUP BY MONTH(created_at)
  ORDER BY MONTH(created_at) ASC
");

while ($row = $q->fetch_assoc()) {
  $grafik[] = $row;
}

/* Jika data kosong, buat dummy agar grafik tidak kosong melompong */
if(empty($grafik)) {
    $grafik = [['bulan' => 'Jan', 'total' => 0], ['bulan' => 'Feb', 'total' => 0]];
}

/* =====================
   DATA RIWAYAT PENJUALAN
===================== */
$riwayat = $conn->query("SELECT * FROM transaksi ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - WEBPRO</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Styling Konsisten */
    .dashboard-header { padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-card); border-bottom: 1px solid var(--border); }
    .dashboard-header .logo { font-weight: 800; color: var(--primary); letter-spacing: 1px; font-size: 20px; }
    
    .dashboard-nav { display: flex; gap: 30px; padding: 0 40px; background: var(--bg-card); border-bottom: 1px solid var(--border); }
    .dashboard-nav a { padding: 15px 0; text-decoration: none; color: var(--text-soft); font-weight: 600; font-size: 14px; position: relative; }
    .dashboard-nav a.active { color: var(--primary); }
    .dashboard-nav a.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--primary); }
    
    .btn-logout { color: #ef4444; border: 1px solid #ef4444; padding: 5px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }

    .dashboard-content { max-width: 1100px; margin: auto; padding: 40px 20px; }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
    .stat-card { background: var(--bg-card); border-radius: 18px; padding: 30px; border: 1px solid var(--border); }
    .stat-card h3 { font-size: 32px; margin: 0; color: var(--primary); }
    
    .chart-box, .history-box { background: var(--bg-card); border-radius: 20px; padding: 30px; border: 1px solid var(--border); margin-bottom: 30px; }
    
    .table-res { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .table-res th, .table-res td { text-align: left; padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; color: var(--text); }
    .badge-sold { background: rgba(99, 102, 241, 0.15); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; }
  </style>
</head>
<body class="dashboard-page">

<header class="dashboard-header">
  <div class="logo">ADMIN PANEL</div>
  <div style="display:flex; align-items:center; gap:20px;">
    <span>Admin: <b><?= $_SESSION['admin'] ?></b></span>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>
</header>

<nav class="dashboard-nav">
  <a href="dashboard.php" class="active">🏠 Dashboard</a>
  <a href="admin.php">➕ Input Website</a>
  <a href="input_penjualan.php">💰 Catat Penjualan</a>
  <a href="admin_list.php">📂 Data Admin</a>
  <a href="index.php" target="_blank">🌐 Lihat Web</a>
</nav>

<main class="dashboard-content">
  <section class="stats">
    <div class="stat-card">
      <h3><?= $totalProduk ?></h3>
      <p>Total Produk</p>
    </div>
    <div class="stat-card">
      <h3><?= $totalTerjual ?></h3>
      <p>Total Terjual</p>
    </div>
    <div class="stat-card">
      <h3 style="color:var(--success)">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h3>
      <p>Total Pendapatan</p>
    </div>
  </section>

  <section class="chart-box">
    <h3 style="margin-bottom: 25px; font-size: 18px;">📈 Grafik Pendapatan Bulanan</h3>
    <canvas id="chartPenjualan" height="100"></canvas>
  </section>

  <section class="history-box">
    <h3 style="margin:0; font-size: 18px;">Riwayat Penjualan Terakhir</h3>
    <div style="overflow-x: auto;">
        <table class="table-res">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Nama Project</th>
              <th>Klien</th>
              <th>Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $riwayat->fetch_assoc()): ?>
                <tr>
                  <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                  <td><b><?= $row['nama_website'] ?></b></td>
                  <td><?= $row['nama_klien'] ?></td>
                  <td><b>Rp <?= number_format($row['total'], 0, ',', '.') ?></b></td>
                </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
    </div>
  </section>
</main>

<script>
const ctx = document.getElementById('chartPenjualan').getContext('2d');

/* Gradien warna untuk efek bergelombang yang mewah */
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)');
gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($grafik,'bulan')) ?>,
    datasets: [{
      label: 'Pendapatan',
      data: <?= json_encode(array_column($grafik,'total')) ?>,
      borderColor: '#6366f1',
      backgroundColor: gradient,
      borderWidth: 4,
      fill: true,
      /* KUNCI AGAR BERGELOMBANG: tension */
      tension: 0.5, 
      pointRadius: 5,
      pointBackgroundColor: '#6366f1',
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    scales: { 
      y: { 
        beginAtZero: true,
        grid: { color: 'rgba(255,255,255,0.05)' }
      },
      x: { grid: { display: false } }
    },
    plugins: { legend: { display: false } }
  }
});
</script>
</body>
</html>