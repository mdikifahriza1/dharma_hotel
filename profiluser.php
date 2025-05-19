<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$user_q = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($user_q);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil Pengguna</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link rel="stylesheet" href="styleuser.css" />
</head>
<body>
  <div class="menu-toggle" onclick="toggleMenu()">
    <i class="fas fa-bars"></i>
  </div>
  <div class="sidebar" id="sidebar">
    <a href="dashboarduser.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="profiluser.php"><i class="fas fa-user"></i> Profil Saya</a>
    <a href="riwayatpemesanan.php"><i class="fas fa-clock"></i> Riwayat</a>
    <a href="tagihan.php"><i class="fas fa-money-bill"></i> Tagihan</a>
    <a href="reservasi.php"><i class="fas fa-calendar-check"></i> Reservasi</a>
    <a href="ulasan.php"><i class="fas fa-star"></i> Ulasan</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="content">
    <div class="card">
      <img src="img/<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto Profil" class="profile-img">
      <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>NIK:</strong> <?php echo htmlspecialchars($user['nik']); ?></p>
      <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($user['tanggal_lahir']); ?></p>
      <p><strong>Jenis Kelamin:</strong> <?php echo $user['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
      <p><strong>Alamat:</strong> <?php echo htmlspecialchars($user['alamat']); ?></p>
      <p><strong>Telepon:</strong> <?php echo htmlspecialchars($user['telepon']); ?></p>
      <a class="btn-edit" href="editprofil.php">Edit Profil</a>
    </div>
  </div>

  <script>
    function toggleMenu() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>
</body>
</html>
