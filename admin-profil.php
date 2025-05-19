<?php
include 'koneksi.php';
include 'sidebar.php';

session_start();
$admin_id = $_SESSION['id'] ?? null;

if (!$admin_id) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id AND role = 'admin'");
$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    echo "<script>alert('Data admin tidak ditemukan!'); window.location='logout.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-detail {
            margin-bottom: 10px;
        }
        .profile-detail strong {
            display: inline-block;
            width: 150px;
        }
        .profile-photo {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-photo img {
            max-width: 120px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
        .btn-edit {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-edit:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="profile-container">
        <h2>Profil Saya</h2>
        <div class="profile-photo">
            <img src="img/<?= $admin['foto'] ?>" alt="Foto Profil">
        </div>
        <div class="profile-detail"><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></div>
        <div class="profile-detail"><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></div>
        <div class="profile-detail"><strong>Nama Lengkap:</strong> <?= htmlspecialchars($admin['full_name']) ?></div>
        <div class="profile-detail"><strong>NIK:</strong> <?= htmlspecialchars($admin['nik']) ?></div>
        <div class="profile-detail"><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($admin['tanggal_lahir']) ?></div>
        <div class="profile-detail"><strong>Jenis Kelamin:</strong> <?= $admin['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
        <div class="profile-detail"><strong>Alamat:</strong> <?= htmlspecialchars($admin['alamat']) ?></div>
        <div class="profile-detail"><strong>Telepon:</strong> <?= htmlspecialchars($admin['telepon']) ?></div>
        <div class="profile-detail"><strong>Role:</strong> <?= htmlspecialchars($admin['role']) ?></div>

</div>
</body>
</html>