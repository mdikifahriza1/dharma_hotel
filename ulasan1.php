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

// Query untuk mengambil data ulasan
$sql = "SELECT u.full_name, u.foto, ul.isi_ulasan, ul.rating, ul.tanggal 
        FROM ulasan ul 
        JOIN users u ON ul.user_id = u.id 
        ORDER BY ul.tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ulasan - Dharma Hotel</title>
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
      height: 50vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      text-shadow: 2px 2px 5px #000;
    }
    header h1 {
      font-size: 3rem;
      margin-top: 60px;
    }
    .review-section {
      padding: 80px 40px;
      background-color: #f9f9f9;
      text-align: center;
    }
    .review-section h2 {
      font-size: 2.2rem;
      margin-bottom: 35px;
      color: #222;
    }
    .review-chat {
      display: flex;
      flex-direction: column;
      gap: 20px;
      max-width: 800px;
      margin: 0 auto;
      text-align: left;
    }
    .review-chat .message {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 10px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .review-chat .message .avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      overflow: hidden;
    }
    .review-chat .message .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .review-chat .message .content {
      flex: 1;
      padding: 0 10px;
    }
    .review-chat .message .content .name {
      font-weight: 600;
      color: #333;
    }
    .review-chat .message .content .text {
      margin-top: 8px;
      color: #555;
      font-size: 1rem;
    }
    .review-chat .message .content .rating {
      margin-top: 8px;
      color: #FFD700;
      font-size: 1.2rem;
    }
    .star-rating {
      color: #FFD700;
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

  <section class="review-section">
    <h2>Ulasan Pengunjung</h2>
    <div class="review-chat">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="message">
          <div class="avatar">
            <img src="img/<?php echo $row['foto']; ?>" alt="Foto <?php echo htmlspecialchars($row['full_name']); ?>">
          </div>
          <div class="content">
            <div class="name"><?php echo htmlspecialchars($row['full_name']); ?></div>
            <div class="text"><?php echo htmlspecialchars($row['isi_ulasan']); ?></div>
            <div class="rating">
              <?php
                $rating = (int)$row['rating'];
                for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $rating) {
                    echo '<i class="fa fa-star star-rating"></i>';
                  } else {
                    echo '<i class="fa fa-star-o star-rating"></i>';
                  }
                }
              ?>
            </div>
            <div class="date"><?php echo date("d M Y", strtotime($row['tanggal'])); ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </section>

  <footer style="text-align: center; padding: 40px 20px; background-color: #111; color: #fff;">
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
