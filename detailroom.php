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

if (isset($_GET['id_kamar'])) {
    $id_kamar = $_GET['id_kamar'];
    $sql = "SELECT * FROM room WHERE id_kamar = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_kamar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        die("Room not found.");
    }
} else {
    die("Invalid Room ID.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($room['nama_kamar']); ?> - Dharma Hotel</title>
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
    .room-detail {
      padding: 80px 40px;
      background-color: #f9f9f9;
      text-align: center;
    }

    .room-detail img {
      width: 100%;
      height: 1000px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .room-detail h2 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .room-detail p {
      font-size: 1.1rem;
      line-height: 1.6;
      color: #444;
      margin-bottom: 15px;
    }

    .room-detail .price {
      font-size: 1.5rem;
      font-weight: bold;
      color: #d4af37;
    }

    footer {
    background-color: #222;
    color: #fff;
    padding: 40px 20px;
    text-align: center;
  }
    .booking-button {
  display: inline-block;
  margin-top: 20px;
  padding: 12px 24px;
  background-color: #d4af37;
  color: white;
  font-weight: bold;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.booking-button:hover {
  background-color: #b7950b;
  color: white;
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

  <section class="room-detail">
    <img src="img/<?php echo htmlspecialchars($room['gambar']); ?>" alt="<?php echo htmlspecialchars($room['nama_kamar']); ?>">
    <h2><?php echo htmlspecialchars($room['nama_kamar']); ?></h2>
    <p><strong>Deskripsi:</strong> <?php echo nl2br(htmlspecialchars($room['deskripsi'])); ?></p>
    <p><strong>Tersedia:</strong> <?php echo htmlspecialchars($room['ketersediaan']); ?> kamar</p>
    <p class="price">Rp<?php echo number_format((int)$room['harga'], 0, ',', '.'); ?> / malam</p>
    <a href="login.php" class="booking-button">Reservasi Sekarang</a>
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
