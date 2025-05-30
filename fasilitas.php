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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fasilitas - Dharma Hotel</title>
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
    .facilities {
      padding: 80px 40px;
      background-color: #f9f9f9;
      text-align: center;
    }
    .facilities h2 {
      font-size: 2.2rem;
      margin-bottom: 35px;
      color: #222;
    }
    .facility-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }
    .facility {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .facility:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    .facility img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }
    .facility h3 {
      margin: 16px 0 8px;
      color: #1a1a1a;
      font-size: 1.25rem;
    }
    .facility p {
      margin: 6px 20px 16px;
      font-size: 0.95rem;
      line-height: 1.4;
      color: #444;
    }
    footer {
      background-color: #222;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }
    .facility-link {
  text-decoration: none;
  color: inherit;
  display: block;
}
.facility-link h3,
.facility-link p {
  text-decoration: none;
}
.facility p {
  height: 40px; /* sesuaikan tinggi */
  overflow: hidden;
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

<section class="facilities">
  <h2>Fasilitas Kami</h2>
  <div class="facility-grid">
    <?php
    $sql = "SELECT * FROM fasilitas";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $id = htmlspecialchars($row['id']);
        $nama = htmlspecialchars($row['nama']);
        $deskripsi = htmlspecialchars($row['deskripsi']);
        $gambar = htmlspecialchars($row['gambar']);
    ?>
        <a href="detailfasilitas.php?id=<?php echo $id; ?>" class="facility-link">
          <div class="facility">
            <img src="img/<?php echo $gambar; ?>" alt="<?php echo $nama; ?>">
            <h3><?php echo $nama; ?></h3>
            <p><?php echo $deskripsi; ?></p>
          </div>
        </a>
    <?php
      endwhile;
    else:
      echo "<p>Tidak ada fasilitas yang tersedia saat ini.</p>";
    endif;
    ?>
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

<?php
// Tutup koneksi database
$conn->close();
?>
