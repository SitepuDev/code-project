<?php
$conn = new mysqli("localhost", "root", "", "katalog_website");

if ($conn->connect_error) {
  die("Koneksi gagal");
}
?>
