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

$sql = "SELECT * FROM room";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dharma Hotel - Room</title>
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

    footer {
    background-color: #222;
    color: #fff;
    padding: 40px 20px;
    text-align: center;
  }
    .rooms {
      padding: 80px 40px 60px;
      text-align: center;
      background-color: #f9f9f9;
    }

    .rooms h2 {
      font-size: 2.2rem;
      margin-bottom: 35px;
      color: #222;
    }

    .room-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .room-link {
      text-decoration: none;
      color: inherit;
    }

    .room {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .room:hover,
    .room-link:hover .room {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .room img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .room h3 {
      margin: 16px 0 8px;
      color: #1a1a1a;
      font-size: 1.25rem;
    }

    .room p {
      margin: 6px 20px;
      font-size: 0.95rem;
      line-height: 1.4;
      color: #444;
    }

    .room strong {
      color: #d4af37;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 2.2rem;
      }
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
    <h1>Dharma Hotel</h1>
  </header>

  <section class="rooms">
    <h2>Our Room Collection</h2>
    <div class="room-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <a href="<?php echo 'detailroom.php?id_kamar=' . urlencode($row['id_kamar']); ?>" class="room-link">
            <div class="room">
              <img src="img/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_kamar']); ?>">
              <h3><?php echo htmlspecialchars($row['nama_kamar']); ?></h3>
              <p>
                <?php echo strlen($row['deskripsi']) > 50
                  ? htmlspecialchars(substr($row['deskripsi'], 0, 50)) . '...'
                  : htmlspecialchars($row['deskripsi']); ?>
              </p>
              <p><strong>Rp<?php echo number_format((int)$row['harga'], 0, ',', '.'); ?></strong> / malam</p>
              <p>Tersedia: <?php echo htmlspecialchars($row['ketersediaan']); ?> kamar</p>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="grid-column: 1 / -1; color: #777;">Tidak ada kamar yang tersedia saat ini.</p>
      <?php endif; ?>
    </div>
  </section>

<footer style="text-align: center; padding: 40px 20px; background-color: #111; color: #fff;">
  <h3 style="margin-bottom: 10px;">About Dharma Hotel</h3>
  <p style="max-width: 600px; margin: 0 auto 20px;">
    <?php echo $footer['tentangkami_beranda']; ?>
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
