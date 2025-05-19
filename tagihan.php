<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];

$query = "SELECT 
    r.id AS id,
    rm.nama_kamar,
    r.total_harga,
    r.check_in,
    r.created_at,
    p.foto_bukti,
    p.status AS status_pembayaran
FROM reservasi r
JOIN room rm ON r.room_id = rm.id_kamar
LEFT JOIN pembayaran p ON p.reservasi_id = r.id
WHERE r.user_id = $id AND r.status = 'pending';
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styleuser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        input[type="file"] {
            width: 100%;
            padding: 12px 16px;
            background-color: #2a2a2a;
            border: 1px solid #444;
            border-radius: 10px;
            color: #fff;
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
        <h2>Tagihan Pembayaran</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $batas = date('Y-m-d', strtotime($row['created_at'] . ' +3 days'));
            ?>
                <div class="card" style="margin-bottom: 20px;">
                    <p><strong>ID Reservasi:</strong> <?= $row['id'] ?></p>
                    <p><strong>Nama Kamar:</strong> <?= $row['nama_kamar'] ?></p>
                    <p><strong>Total Harga:</strong> Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></p>
                    <p><strong>Reservasi:</strong> <?= $row['created_at'] ?></p>
                    <p><strong>Check-in:</strong> <?= $row['check_in'] ?></p>
                    <p><strong>Bayar Sebelum:</strong> <?= $batas ?></p>
                    <p>Silakan lakukan pembayaran ke rekening <strong>BRI a.n. Dharma Hotel 1212121212122</strong><br>

                    <?php if ($row['status_pembayaran'] === 'menunggu_verifikasi' && !is_null($row['foto_bukti'])): ?>
                        <p><em>(Menunggu Verifikasi Admin)</em></p>
                    <?php else: ?>
                        Upload bukti pembayaran di bawah ini. Jika tidak dibayar sebelum tanggal tersebut, reservasi akan dibatalkan oleh admin.</p>
                        <form action="upload_bukti.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="reservasi_id" value="<?= $row['id'] ?>">
                            <div class="form-group">
                                <label for="foto_bayar">Unggah Bukti Pembayaran:</label>
                                <input type="file" name="foto_bayar" required>
                            </div>
                            <button type="submit" class="btn-submit">Kirim Bukti Pembayaran</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Tidak ada tagihan yang perlu dibayar.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
