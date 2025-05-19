<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id']; // Ambil user id yang login

// Ambil data reservasi yang sudah selesai dan belum diberi ulasan
$query = "SELECT r.id, r.check_in, r.check_out, r.status, rm.nama_kamar, r.total_harga
FROM reservasi r
JOIN room rm ON r.room_id = rm.id_kamar
WHERE r.user_id = $id 
  AND r.status = 'disetujui' 
  AND NOT EXISTS (
    SELECT 1 
    FROM ulasan u 
    WHERE u.reservasi_id = r.id
  )
ORDER BY r.created_at DESC";

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
    <title>Ulasan</title>
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
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
            color: #ffffff;
        }

        .card p {
            margin: 10px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #ccc;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 12px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            border-radius: 10px;
            color: #fff;
        }

        .rating {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
        }

        .rating input {
            display: none;
        }

        .rating label {
            font-size: 20px;
            cursor: pointer;
            color: #FFD700;
        }

        .rating input:checked ~ label {
            color: #FFD700;
        }

        .btn-submit {
            background: linear-gradient(135deg, #00bfff, #007acc);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #009acc, #005f99);
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
        <h2>Ulasan Reservasi</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <form action="proses_ulasan.php" method="POST">
                <div class="card-grid">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="card">
                            <p><strong>Nama Kamar:</strong> <?= $row['nama_kamar'] ?></p>
                            <p><strong>Total Harga:</strong> <?= number_format($row['total_harga'], 0, ',', '.') ?></p>
                            <p><strong>Check-in:</strong> <?= $row['check_in'] ?></p>
                            <p><strong>Check-out:</strong> <?= $row['check_out'] ?></p>

                            <div class="form-group">
                                <label for="isi_ulasan_<?= $row['id'] ?>">Isi Ulasan:</label>
                                <textarea name="isi_ulasan_<?= $row['id'] ?>" id="isi_ulasan_<?= $row['id'] ?>" required></textarea>
                            </div>

<div class="form-group">
    <label for="rating<?= $row['id'] ?>">Rating:</label>
    <input type="number" id="rating<?= $row['id'] ?>" name="rating_<?= $row['id'] ?>" value="" min="1" max="5" step="1" required />
</div>


                            <!-- Menambahkan hidden field untuk reservasi_id -->
                            <input type="hidden" name="reservasi_id" value="<?= $row['id'] ?>" />
                            <input type="submit" class="btn-submit" value="Kirim Ulasan" />
                        </div>
                    <?php endwhile; ?>
                </div>
            </form>
        <?php else: ?>
            <p>Tidak ada reservasi yang dapat diulas.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
