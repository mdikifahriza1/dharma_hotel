<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id']; // Ambil user id yang login

// Ambil riwayat pemesanan berdasarkan user_id
$query = "SELECT r.id, r.check_in, r.check_out, r.jumlah_tamu, r.room_id, r.status, r.total_harga, r.created_at, r.updated_at, 
                 u.full_name AS user_name, 
                 rm.nama_kamar
          FROM reservasi r
          JOIN users u ON r.user_id = u.id
          JOIN room rm ON r.room_id = rm.id_kamar
          WHERE r.user_id = $id ORDER BY r.created_at DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="styleuser.css">
    <style>
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background-color: #1e1e1e;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #333;
        }

        td {
            background-color: #2a2a2a;
        }

        .btn-edit {
            display: inline-block;
            background-color: #00bfff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .btn-edit:hover {
            background-color: #009acc;
        }
    </style>
</head>
<body>
    <div class="sidebar">
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
            <h2>Riwayat Pemesanan</h2>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Pemesanan</th>
                            <th>Nama Pengguna</th>
                            <th>Nama Kamar</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Jumlah Tamu</th>
                            <th>Status</th>
                            <th>Total Harga</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['user_name'] ?></td>
                                <td><?= $row['nama_kamar'] ?></td>
                                <td><?= $row['check_in'] ?></td>
                                <td><?= $row['check_out'] ?></td>
                                <td><?= $row['jumlah_tamu'] ?></td>
                                <td><?= ucfirst($row['status']) ?></td>
                                <td><?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td><?= $row['updated_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Belum ada riwayat pemesanan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
