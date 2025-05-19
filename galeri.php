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

// Ambil kategori dari tabel galeri
$kategori_query = mysqli_query($conn, "SELECT DISTINCT kategori FROM galeri");
$kategori = mysqli_fetch_all($kategori_query, MYSQLI_ASSOC);

// Ambil data gambar berdasarkan kategori
$kategori_selected = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$sql = "SELECT * FROM galeri";
if ($kategori_selected) {
    $sql .= " WHERE kategori = '" . mysqli_real_escape_string($conn, $kategori_selected) . "'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dharma Hotel - Galeri</title>
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
    .gallery {
      padding: 80px 40px 60px;
      text-align: center;
      background-color: #f9f9f9;
    }

    .gallery h2 {
      font-size: 2.2rem;
      margin-bottom: 35px;
      color: #222;
    }

    .category-buttons {
      margin-bottom: 30px;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .category-buttons a {
      text-decoration: none;
      padding: 10px 20px;
      background-color: #ffd700;
      color: #222;
      border-radius: 5px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .category-buttons a:hover {
      background-color: #ffb300;
    }

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .gallery-item {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .gallery-item img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .gallery-item h3 {
      margin: 16px 0 8px;
      color: #1a1a1a;
      font-size: 1.25rem;
    }

    .gallery-item p {
      margin: 6px 20px;
      font-size: 0.95rem;
      line-height: 1.4;
      color: #444;
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 2.2rem;
      }
    }
    
    .modal {
  display: none;
  position: fixed;
  z-index: 2000;
  padding-top: 60px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.9);
}

.modal-content {
  margin: auto;
  display: block;
  max-width: 90%;
  max-height: 80%;
  animation: zoom 0.3s ease;
  border-radius: 10px;
}

@keyframes zoom {
  from {transform: scale(0.7);}
  to {transform: scale(1);}
}

.close {
  position: absolute;
  top: 25px;
  right: 35px;
  color: #fff;
  font-size: 40px;
  font-weight: bold;
  cursor: pointer;
  transition: color 0.3s;
}

.close:hover {
  color: #ffd700;
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

<section class="gallery">
  <h2>Gallery</h2>

  <!-- Kategori Tombol -->
  <div class="category-buttons">
    <a href="galeri.php">All</a>
    <?php foreach ($kategori as $cat): ?>
      <a href="galeri.php?kategori=<?php echo urlencode($cat['kategori']); ?>"><?php echo htmlspecialchars($cat['kategori']); ?></a>
    <?php endforeach; ?>
  </div>

  <!-- Galeri Grid -->
  <div class="gallery-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="gallery-item">
          <img src="img/galeri/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>">
          <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
          <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="grid-column: 1 / -1; color: #777;">Tidak ada gambar yang tersedia.</p>
    <?php endif; ?>
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

<div id="imageModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <img class="modal-content" id="modalImage">
</div>

<script>
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");

  // Pasang event listener ke semua gambar
  document.querySelectorAll(".gallery-item img").forEach(img => {
    img.addEventListener("click", function () {
      modal.style.display = "block";
      modalImg.src = this.src;
    });
  });

  function closeModal() {
    modal.style.display = "none";
  }

  // Tutup modal jika klik di luar gambar
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>

</body>
</html>

<?php $conn->close(); ?>
