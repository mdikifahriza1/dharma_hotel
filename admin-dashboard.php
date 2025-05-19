<?php
include 'koneksi.php';
include 'sidebar.php';

// Statistik pengguna berdasarkan role
$total_admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'admin'"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"))['total'];

// Total kamar berdasarkan penjumlahan ketersediaan
$total_kamar_tersedia = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(ketersediaan) AS total FROM room"))['total'];

// Statistik lainnya
$total_reservasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reservasi"))['total'];
$menunggu_verifikasi = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) AS total FROM pembayaran WHERE status = 'menunggu_verifikasi' AND foto_bukti IS NOT NULL"))['total'];
$total_ulasan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ulasan"))['total'];
$total_news = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM news"))['total'];
$total_fasilitas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM fasilitas"))['total'];
$total_galeri = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM galeri"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="content">
<button class="sidebar-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>    
<h1>Dashboard Admin</h1>
    <div class="card">
        <h2>Statistik Umum</h2>
        <ul style="list-style: none; padding-left: 0;">
            <li><strong><i class="fas fa-user-shield"></i> Admin:</strong> <?= $total_admins ?></li>
            <li><strong><i class="fas fa-users"></i> Pengguna Biasa:</strong> <?= $total_users ?></li>
            <li><strong><i class="fas fa-bed"></i> Kamar Tersedia:</strong> <?= $total_kamar_tersedia ?></li>
            <li><strong><i class="fas fa-calendar-alt"></i> Total Reservasi:</strong> <?= $total_reservasi ?></li>
            <li><strong><i class="fas fa-clock"></i> Menunggu Verifikasi Pembayaran:</strong> <?= $menunggu_verifikasi ?></li>
            <li><strong><i class="fas fa-star"></i> Ulasan:</strong> <?= $total_ulasan ?></li>
            <li><strong><i class="fas fa-newspaper"></i> Berita:</strong> <?= $total_news ?></li>
            <li><strong><i class="fas fa-swimming-pool"></i> Fasilitas:</strong> <?= $total_fasilitas ?></li>
            <li><strong><i class="fas fa-images"></i> Galeri:</strong> <?= $total_galeri ?></li>
        </ul>
    </div>

    <div class="card">
        <h2>Selamat Datang!</h2>
        <p>Anda sedang berada di dashboard admin <strong>Dharma Hotel</strong>. Gunakan sidebar untuk mengelola konten dan data sistem.</p>
    </div>
</div>
<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
}
</script>
</body>
</html>
