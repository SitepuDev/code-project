<?php
include 'koneksi.php';

if (!isset($_GET['file'])) {
  die("Akses ditolak");
}

$token = $_GET['file'];

$q = $conn->query("SELECT * FROM website WHERE akses_token='$token'");
$data = $q->fetch_assoc();

if (!$data) {
  die("Token tidak valid");
}

$filePath = "storage/private/" . $data['demo_file'];

if (!file_exists($filePath)) {
  die("File tidak ditemukan");
}

header("Content-Type: text/html");
readfile($filePath);
