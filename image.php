<?php
include 'koneksi.php';

if (!isset($_GET['file'])) {
    http_response_code(403);
    exit;
}

$token = $_GET['file'];
$requested_img = isset($_GET['img']) ? $_GET['img'] : null;

// Gunakan prepared statement agar aman
$q = $conn->prepare("SELECT gambar_file FROM website WHERE akses_token=?");
$q->bind_param("s", $token);
$q->execute();
$result = $q->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    http_response_code(404);
    exit;
}

// Tambahkan array_map('trim', ...) untuk membersihkan spasi di sekitar nama file
$images = array_map('trim', explode(',', $data['gambar_file']));
$target_file = "";

if ($requested_img) {
    // Bersihkan juga input dari URL sebelum dicek
    $requested_img = trim($requested_img);
    if (in_array($requested_img, $images)) {
        $target_file = $requested_img;
    } else {
        http_response_code(404);
        exit;
    }
} else {
    $target_file = !empty($images[0]) ? $images[0] : "";
}

$path = "storage/private/" . $target_file;

if (empty($target_file) || !file_exists($path)) {
    http_response_code(404);
    exit;
}

$mime = mime_content_type($path);
header("Content-Type: $mime");
header("Content-Length: " . filesize($path));
readfile($path);