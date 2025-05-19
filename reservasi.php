<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id']; // Ambil user id yang login

// Ambil data kamar yang tersedia untuk reservasi
$query = "SELECT * FROM room WHERE ketersediaan > 0";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Menangani pemesanan yang diajukan oleh pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $jumlah_tamu = $_POST['jumlah_tamu'];

    // Menghitung total harga berdasarkan harga kamar, jumlah hari, dan jumlah tamu
    $room_query = "SELECT harga FROM room WHERE id_kamar = $room_id";
    $room_result = mysqli_query($conn, $room_query);
    $room_data = mysqli_fetch_assoc($room_result);
    $harga = $room_data['harga'];

    $jumlah_hari = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    if ($jumlah_hari < 1) $jumlah_hari = 1; // minimal 1 hari

    $total_harga = $harga * $jumlah_hari * $jumlah_tamu;

    // Simpan pemesanan ke database
    $insert_query = "INSERT INTO reservasi (user_id, room_id, check_in, check_out, jumlah_tamu, total_harga, status) 
                     VALUES ('$id', '$room_id', '$check_in', '$check_out', '$jumlah_tamu', '$total_harga', 'pending')";
    if (mysqli_query($conn, $insert_query)) {
        $reservasi_id = mysqli_insert_id($conn);

        // Masukkan data ke tabel pembayaran
        $insert_pembayaran = "INSERT INTO pembayaran (reservasi_id, status) VALUES ('$reservasi_id', 'menunggu_verifikasi')";
        if (mysqli_query($conn, $insert_pembayaran)) {
            echo "<script>alert('Reservasi dan data pembayaran berhasil disimpan!'); window.location='riwayatpemesanan.php';</script>";
        } else {
            echo "<script>alert('Reservasi berhasil, tetapi gagal menyimpan pembayaran.');</script>";
        }
    } else {
        echo "<script>alert('Reservasi gagal!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Kamar</title>
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

        input, select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #333;
            background-color: #2a2a2a;
            color: #fff;
        }

        label {
            font-weight: bold;
            color: #fff;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 20px;
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
            <h2>Reservasi Kamar</h2>
            <form method="POST" class="form-container">
                <label for="room_id">Pilih Kamar:</label>
                <select name="room_id" id="room_id" required>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?= $row['id_kamar'] ?>"><?= $row['nama_kamar'] ?> - Rp <?= number_format($row['harga'], 0, ',', '.') ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="check_in">Tanggal Check-in:</label>
                <input type="date" name="check_in" id="check_in" required>

                <label for="check_out">Tanggal Check-out:</label>
                <input type="date" name="check_out" id="check_out" required>

                <label for="jumlah_tamu">Jumlah Tamu:</label>
                <input type="number" name="jumlah_tamu" id="jumlah_tamu" min="1" required>

                <button type="submit" class="btn-edit">Buat Reservasi</button>
            </form>
        </div>
    </div>
</body>
</html>
