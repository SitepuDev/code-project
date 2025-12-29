<?php
session_start();
include 'koneksi.php';

$error = "";

if (isset($_POST['login'])) {

  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if ($username === "" || $password === "") {
    $error = "Username dan password wajib diisi";
  } else {

    // 🔐 AMAN (prepared statement)
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = $admin['username'];

      // ✅ PASTI KE DASHBOARD
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Username atau password salah";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="auth-card">

  <div class="auth-header">
    <span>Admin Login</span>
    <button id="themeToggle" class="theme-toggle">🌙</button>
  </div>

  <h2>Login Admin</h2>

  <form method="POST" class="auth-form" autocomplete="off">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login" class="btn-primary">Login</button>

    <?php if ($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
  </form>

  <div class="auth-extra">
    <a href="register.php">Daftar sebagai Admin</a>
  </div>

</div>

<script src="theme.js"></script>
</body>
</html>
