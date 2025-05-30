<?php
include 'koneksi.php';

$bg_query = mysqli_query($conn, "SELECT gambar FROM bg LIMIT 1");
$bg_row = mysqli_fetch_assoc($bg_query);
$bg_img = $bg_row ? $bg_row['gambar'] : 'img1.jpg';

$about_query = mysqli_query($conn, "SELECT tentangkami_beranda FROM about LIMIT 1");
$about_row = mysqli_fetch_assoc($about_query);
$tentang_kami = $about_row ? $about_row['tentangkami_beranda'] : '';

$footer_query = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$footer = mysqli_fetch_assoc($footer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dharma Hotel - Selamat Datang</title>
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

  .hero {
    background: url('img/<?php echo $bg_img; ?>') no-repeat center center/cover;
    height: 90vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    text-shadow: 2px 2px 6px #000;
    position: relative;
  }

  .hero-content {
    max-width: 700px;
  }

  .hero h1 {
    font-size: 3.5rem;
    margin-bottom: 20px;
  }

  .hero p {
    font-size: 1.3rem;
    margin-bottom: 30px;
  }

  .hero a {
    background-color: #ffd700;
    color: #000;
    padding: 12px 30px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    transition: background 0.3s ease;
  }

  .hero a:hover {
    background-color: #e6c200;
  }

  section {
    padding: 80px 40px;
    background-color: #fff;
  }

  section h2 {
    text-align: center;
    font-size: 2.2rem;
    margin-bottom: 40px;
    color: #222;
  }

  .intro,
  .features,
  .rooms-preview {
    max-width: 1100px;
    margin: 0 auto;
    text-align: center;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 30px;
  }

  .feature {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }

  .feature img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .feature h4 {
    margin-bottom: 10px;
    font-size: 1.1rem;
    color: #333;
  }

  .rooms-preview a {
    display: inline-block;
    margin-top: 30px;
    padding: 10px 25px;
    background-color: #d4af37;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s ease;
  }

  .rooms-preview a:hover {
    background-color: #c29e2e;
  }

  .room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }

  .room-link {
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: block;
  }

  .room-link:hover .room {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  .room {
    background: #f9f9f9;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    padding: 15px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: left;
  }

  .room img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
  }

  .room h3 {
    margin: 15px 0 10px;
    font-size: 1.2rem;
    color: #222;
  }

  .room p {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.4;
  }

  footer {
    background-color: #222;
    color: #fff;
    padding: 40px 20px;
    text-align: center;
  }

  @media (max-width: 768px) {
    .hero h1 {
      font-size: 2.2rem;
    }

    .hero p {
      font-size: 1rem;
    }
  }

  .selengkapnya-btn {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 25px;
  background-color: #ffd700;
  color: #000;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease;
}

.selengkapnya-btn:hover {
  background-color: #e6c200;
}

.rooms, .features {
  text-align: center;
}

  /* Optional spacing between sections like rooms and facilities */
  section + section {
    margin-top: -40px;
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

  <section class="hero">
    <div class="hero-content">
      <h1>Selamat Datang di Dharma Hotel</h1>
      <p>Rasakan kenyamanan dan kemewahan di jantung Kabupaten Blitar</p>
      <a href="login.php">Reservasi Sekarang</a>
    </div>
  </section>

  <section class="rooms">
    <h2>Tentang Kami</h2>
    <p><?php echo $tentang_kami; ?></p>
  </section>

  <section class="rooms">
  <h2>Fasilitas</h2>
  <div class="room-grid">
    <?php
    $fasilitas = $conn->query("SELECT * FROM fasilitas LIMIT 5");
    while ($f = $fasilitas->fetch_assoc()):
    ?>
      <a href="detailfasilitas.php?id=<?= $f['id'] ?>" class="room-link">
        <div class="room">
          <img src="img/<?= htmlspecialchars($f['gambar']) ?>" alt="<?= htmlspecialchars($f['nama']) ?>">
          <h3><?= htmlspecialchars($f['nama']) ?></h3>
          <p><?= strlen($f['deskripsi']) > 50 ? htmlspecialchars(substr($f['deskripsi'], 0, 50)) . '...' : htmlspecialchars($f['deskripsi']) ?></p>
        </div>
      </a>
    <?php endwhile; ?>
  </div>
  <div style="margin-top: 30px;">
  <a href="fasilitas.php" class="selengkapnya-btn">Lihat Semua Fasilitas</a>
</div>
</section>

  <section class="rooms">
  <h2>Rooms</h2>
  <div class="room-grid">
    <?php
    $room = $conn->query("SELECT * FROM room LIMIT 5");
    while ($r = $room->fetch_assoc()):
    ?>
      <a href="detailroom.php?id_kamar=<?= $r['id_kamar'] ?>" class="room-link">
        <div class="room">
          <img src="img/<?= htmlspecialchars($r['gambar']) ?>" alt="<?= htmlspecialchars($r['nama_kamar']) ?>">
          <h3><?= htmlspecialchars($r['nama_kamar']) ?></h3>
          <p><?= strlen($r['deskripsi']) > 50 ? htmlspecialchars(substr($r['deskripsi'], 0, 50)) . '...' : htmlspecialchars($r['deskripsi']) ?></p>
          <p><strong>Rp<?= number_format($r['harga'], 0, ',', '.') ?></strong> / malam</p>
        </div>
      </a>
    <?php endwhile; ?>
  </div>
  <div style="margin-top: 30px;">
  <a href="room.php" class="selengkapnya-btn">Lihat Semua Room</a>
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
