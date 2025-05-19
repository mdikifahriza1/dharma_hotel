<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";
$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$bg_query = mysqli_query($conn, "SELECT gambar FROM bg LIMIT 1");
$bg_row = mysqli_fetch_assoc($bg_query);
$bg_img = $bg_row ? $bg_row['gambar'] : 'img1.jpg';

$footer_query = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$footer = mysqli_fetch_assoc($footer_query);

// Ambil berita berdasarkan slug
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';
$news_query = $conn->query("SELECT * FROM news WHERE slug = '$slug' AND is_published = 1");

if ($news_query->num_rows === 0) {
  die("Berita tidak ditemukan.");
}

$news = $news_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($news['title']); ?> - Dharma Hotel</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      color: #333;
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
      color: white;
      height: 40vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-shadow: 2px 2px 5px #000;
      margin-bottom: 20px;
    }
    header h1 {
      font-size: 2.5rem;
      margin-top: 60px;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    .container img {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .container h2 {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #222;
    }
    .container .date {
      font-size: 0.9rem;
      color: #777;
      margin-bottom: 20px;
    }
    .container p {
      line-height: 1.6;
      font-size: 1rem;
      color: #333;
    }
    footer {
      background-color: #222;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
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
  <a href="ulasan1.php">Ulasan</a> 
  <a href="login.php">Login</a> 
</nav>

<header>
  <h1>Dharma Hotel</h1>
</header>

<div class="container">
  <?php if ($news['gambar']): ?>
    <img src="img/<?php echo htmlspecialchars($news['gambar']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
  <?php endif; ?>
  <h2><?php echo htmlspecialchars($news['title']); ?></h2>
  <div class="date">Dipublikasikan pada: <?php echo date('d M Y', strtotime($news['published_at'])); ?></div>
  <p><?php echo nl2br($news['content']); ?></p>
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
<?php $conn->close(); ?>
