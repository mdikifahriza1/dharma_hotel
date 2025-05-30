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

$sql = "SELECT id, posisi, departemen, deskripsi, kualifikasi, gambar, jumlah_kebutuhan, tanggal_buka, tanggal_tutup FROM rekrutmen WHERE status = 'dibuka' ORDER BY tanggal_buka DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rekrutmen - Dharma Hotel</title>
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
      text-shadow: 2px 2px 5px #000;
    }
    header h1 {
      font-size: 3rem;
      margin-top: 60px;
    }
    .news-section {
      padding: 80px 40px;
      background-color: #f9f9f9;
      text-align: center;
    }
    .news-section h2 {
      font-size: 2.2rem;
      margin-bottom: 35px;
      color: #222;
    }
.news-grid {
  display: flex;
  flex-direction: column;
  gap: 30px;
  align-items: center;
}
.news-item {
  max-width: 800px;
  width: 100%;
}
.news-item img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 15px;
}

    .news-item h3 {
      margin-top: 0;
      color: #1a1a1a;
      font-size: 1.25rem;
    }
    .news-item p {
      font-size: 0.95rem;
      line-height: 1.5;
      margin: 10px 0;
    }
    .news-item .btn-lamar {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #0066cc;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s ease;
    }
    .news-item .btn-lamar:hover {
      background-color: #004c99;
    }
    footer {
      background-color: #222;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }

    .toggle-text {
  color: #0066cc;
  cursor: pointer;
  font-weight: 600;
  margin-left: 5px;
  text-decoration: none;
}
.toggle-text:hover {
  text-decoration: underline;
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
  <h1>Rekrutmen Dharma Hotel</h1>
</header>

<section class="news-section">
  <h2>Lowongan Terbaru</h2>
  <div class="news-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
<div class="news-item">
  <?php if (!empty($row['gambar'])): ?>
    <img src="img/rekrutmen/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['posisi']); ?>">
  <?php endif; ?>
  <h3><?php echo htmlspecialchars($row['posisi']); ?> - <?php echo htmlspecialchars($row['departemen']); ?></h3>
  
  <!-- Deskripsi -->
  <p><strong>Deskripsi:</strong> 
    <span class="short-text"><?php echo htmlspecialchars(substr(strip_tags($row['deskripsi']), 0, 150)); ?></span>
    <?php if (strlen(strip_tags($row['deskripsi'])) > 150): ?>
      <span class="more-text" style="display:none;"><?php echo htmlspecialchars(substr(strip_tags($row['deskripsi']), 150)); ?></span>
      <a href="#" class="toggle-text">Selengkapnya</a>
    <?php endif; ?>
  </p>

  <!-- Kualifikasi -->
  <p><strong>Kualifikasi:</strong> 
    <span class="short-text"><?php echo htmlspecialchars(substr(strip_tags($row['kualifikasi']), 0, 150)); ?></span>
    <?php if (strlen(strip_tags($row['kualifikasi'])) > 150): ?>
      <span class="more-text" style="display:none;"><?php echo htmlspecialchars(substr(strip_tags($row['kualifikasi']), 150)); ?></span>
      <a href="#" class="toggle-text">Selengkapnya</a>
    <?php endif; ?>
  </p>
  
  <p><strong>Jumlah:</strong> <?php echo $row['jumlah_kebutuhan']; ?> orang</p>
  <p><strong>Periode:</strong> <?php echo date("d M Y", strtotime($row['tanggal_buka'])); ?> - <?php echo date("d M Y", strtotime($row['tanggal_tutup'])); ?></p>
  <a class="btn-lamar" href="lamar.php?id=<?php echo $row['id']; ?>">Lamar Sekarang</a>
</div>
<?php endwhile; ?>
  </div>
</section>

<footer>
  <h3>About Dharma Hotel</h3>
  <p style="max-width: 600px; margin: 0 auto 20px;"><?php echo $footer['deskripsi']; ?></p>
  <p><strong>Alamat:</strong> <?php echo $footer['alamat']; ?></p>
  <div style="margin-top: 20px;">
    <a href="https://instagram.com/<?php echo $footer['instagram']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
    <a href="https://facebook.com/<?php echo $footer['facebook']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;"><i class="fab fa-facebook-f"></i></a>
    <a href="https://youtube.com/<?php echo $footer['youtube']; ?>" target="_blank" style="color:#ffd700; margin:0 10px; font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
  </div>
  <p style="margin-top: 30px; font-size: 0.9rem; color: #aaa;">&copy; <?php echo date("Y"); ?> Dharma Hotel. All rights reserved.</p>
<script>
  document.querySelectorAll('.toggle-text').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const moreText = this.previousElementSibling;
      const shortText = moreText.previousElementSibling;
      if (moreText.style.display === 'none') {
        moreText.style.display = 'inline';
        this.textContent = 'Sembunyikan';
      } else {
        moreText.style.display = 'none';
        this.textContent = 'Selengkapnya';
      }
    });
  });
</script>

</footer>

</body>
</html>
<?php $conn->close(); ?>
