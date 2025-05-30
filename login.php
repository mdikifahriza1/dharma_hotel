<?php
session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$user' AND password='$pass' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'user') {
            header("Location: dashboarduser.php");
            exit;
        } else {
            header("Location: admin-dashboard.php");
            exit;
        }
    } else {
        $error = "Username atau password salah.";
    }
}

$footer_query = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$footer = mysqli_fetch_assoc($footer_query);
$bg_query = mysqli_query($conn, "SELECT gambar FROM bg LIMIT 1");
$bg_row = mysqli_fetch_assoc($bg_query);
$bg_img = $bg_row ? $bg_row['gambar'] : 'img1.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login - Dharma Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }
        nav {
            background: rgba(0, 0, 0, 0.7);
            padding: 15px 0;
            display: flex;
            justify-content: center;
            gap: 40px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #ffd700;
        }
        header {
            background: url('img/<?php echo $bg_img; ?>') no-repeat center center/cover;
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-shadow: 2px 2px 5px #000;
            color: white;
        }
        header h1 {
            font-size: 3rem;
            margin-top: 60px;
        }
        .login-box {
            background-color: white;
            max-width: 400px;
            margin: -100px auto 60px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 25px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
        }
        .btn-container button,
        .btn-container a {
            padding: 10px 20px;
            border-radius: 25px;
            border: 2px solid #0066cc;
            background-color: #f9f9f9;
            color: #0066cc;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }
        .btn-container button:hover,
        .btn-container a:hover {
            background-color: #0066cc;
            color: white;
        }
        .error-msg {
            color: red;
            margin-bottom: 15px;
        }
        footer {
            background-color: #111;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="room.php">Room</a>
    <a href="berita.php">Berita</a>
    <a href="galeri.php">Galeri</a>
    <a href="fasilitas.php">Fasilitas</a>
    <a href="rekrutmen.php">Rekrutmen</a>
    <a href="ulasan1.php">Ulasan</a> 
    <a href="login.php">Login</a> 
  </nav>

<header>
    <h1>Login ke Dharma Hotel</h1>
</header>

<div class="login-box">
    <h2>Masuk ke Akun Anda</h2>
    <?php if (!empty($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <div class="btn-container">
            <button type="submit">Sign in</button>
            <a href="signup.php">Create Account</a>
        </div>
    </form>
</div>

<footer>
  <h3 style="margin-bottom: 10px;">About Dharma Hotel</h3>
  <p style="max-width: 600px; margin: 0 auto 20px;">
    <?php echo $footer['deskripsi']; ?>
  </p>
  <p><strong>Alamat:</strong> <?php echo $footer['alamat']; ?></p>

  <div style="margin-top: 20px;">
    <a href="https://instagram.com/<?php echo $footer['instagram']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;">
      <i class="fab fa-instagram"></i>
    </a>
    <a href="https://facebook.com/<?php echo $footer['facebook']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://youtube.com/<?php echo $footer['youtube']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;">
      <i class="fab fa-youtube"></i>
    </a>
  </div>

  <p style="margin-top: 30px; font-size: 0.9rem; color: #aaa;">
    &copy; <?php echo date("Y"); ?> Dharma Hotel. All rights reserved.
  </p>
</footer>

</body>
</html>
