<?php
include 'koneksi.php';
$error = "";
$sukses = "";

if (isset($_POST['daftar'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // cek username sudah ada
  $cek = $conn->query("SELECT * FROM admin WHERE username='$username'");
  if ($cek->num_rows > 0) {
    $error = "Username sudah digunakan";
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $conn->query("INSERT INTO admin (username,password) VALUES ('$username','$hash')");
    $sukses = "Pendaftaran berhasil. Silakan login.";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">
  <h2>Daftar Admin</h2>

  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="daftar">Daftar</button>
  </form>

  <p class="error"><?= $error ?></p>
  <p style="color:#22c55e"><?= $sukses ?></p>

  <div class="auth-extra">
    <a href="login.php">← Kembali ke Login</a>
  </div>
</div>

</body>
</html>
