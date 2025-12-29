<?php
include 'koneksi.php';
header("Content-Type: application/json");

$data = [];

/**
 * Update Query:
 * 1. Menggunakan LEFT JOIN ke tabel 'transaksi'
 * 2. Menggunakan SUM(jumlah) untuk menghitung total unit terjual per produk
 * 3. Menggunakan GROUP BY agar data tidak duplikat
 */
$q = $conn->query("
  SELECT 
    w.id,
    w.judul,
    w.deskripsi,
    w.harga,
    w.akses_token,
    w.kategori,
    w.gambar_file,
    IFNULL(SUM(t.jumlah), 0) AS total_terjual
  FROM website w
  LEFT JOIN transaksi t ON w.id = t.website_id
  GROUP BY w.id
");

if (!$q) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

while ($row = $q->fetch_assoc()) {
    // Memastikan tipe data angka tetap konsisten
    $row['total_terjual'] = (int)$row['total_terjual'];
    $data[] = $row;
}

echo json_encode($data);
?>