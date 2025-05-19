<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];

$user_q = mysqli_query($conn, "SELECT full_name, foto, role FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($user_q);

$jumlah_reservasi_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reservasi WHERE id = $id");
$jumlah_reservasi = mysqli_fetch_assoc($jumlah_reservasi_q)['total'];

$status_reservasi_q = mysqli_query($conn, "SELECT status FROM reservasi WHERE id = $id ORDER BY created_at DESC LIMIT 1");
$status_reservasi = mysqli_fetch_assoc($status_reservasi_q)['status'] ?? 'Belum ada reservasi';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <h2>Selamat datang, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>

        <div class="card-grid">
            <div class="card">
                <h3>Data Pribadi</h3>
                <img class="profile-img" src="img/<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto Profil">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            </div>
            <div class="card">
                <h3>Jumlah Pemesanan</h3>
                <p style="font-size: 2em;"><?php echo $jumlah_reservasi; ?> kali</p>
            </div>
            <div class="card">
                <h3>Status Pemesanan Terakhir</h3>
                <p style="font-size: 1.5em;"> <?php echo htmlspecialchars($status_reservasi); ?></p>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>
