<?php 
include "koneksi.php";
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password']; // tanpa hash
    $full_name = $_POST['full_name'];
    $nik      = $_POST['nik'];
    $tgl_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat   = $_POST['alamat'];
    $telepon  = $_POST['telepon'];

    $foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'user_' . uniqid() . '.' . strtolower($ext);
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/" . $foto);
    }

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username atau Email sudah digunakan.";
    } else {
        $sql = "INSERT INTO users 
            (username, email, password, full_name, foto, nik, tanggal_lahir, jenis_kelamin, alamat, telepon, role)
            VALUES 
            ('$username', '$email', '$password', '$full_name', '$foto', '$nik', '$tgl_lahir', '$jenis_kelamin', '$alamat', '$telepon', 'user')";

        if (mysqli_query($conn, $sql)) {
            $success = "Pendaftaran berhasil. Silakan login.";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}

// Ambil background dan footer
$bg_query = mysqli_query($conn, "SELECT gambar FROM bg LIMIT 1");
$bg_row = mysqli_fetch_assoc($bg_query);
$bg_img = $bg_row ? $bg_row['gambar'] : 'img1.jpg';

$footer_query = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$footer = mysqli_fetch_assoc($footer_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Dharma Hotel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
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
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-shadow: 2px 2px 5px #000;
            color: white;
        }
        header h1 {
            font-size: 3rem;
            margin-top: 60px;
        }
        .form-box {
            background-color: rgba(255,255,255,0.98);
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: -80px auto 50px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .form-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-box input,
        .form-box select,
        .form-box textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        .form-box button {
            width: 100%;
            padding: 12px;
            background-color: #0066cc;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-box button:hover {
            background-color: #004999;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .message.success { color: green; }
        .message.error { color: red; }
        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .btn-container a {
            padding: 10px 25px;
            border-radius: 25px;
            border: 2px solid #0066cc;
            background-color: #f9f9f9;
            color: #0066cc;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }
        .btn-container a:hover {
            background-color: #0066cc;
            color: white;
        }
        footer {
            background-color: #111;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
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
    <h1>Form Pendaftaran</h1>
</header>

<div class="form-box">
    <h2>Buat Akun Baru</h2>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="text" name="full_name" placeholder="Nama Lengkap" required />
        <input type="file" name="foto" accept="image/*" required />
        <input type="text" name="nik" placeholder="NIK" required />
        <input type="date" name="tanggal_lahir" required />
        <select name="jenis_kelamin" required>
            <option value="">-- Jenis Kelamin --</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>
        <textarea name="alamat" placeholder="Alamat Lengkap" required></textarea>
        <input type="text" name="telepon" placeholder="No. Telepon" required />
        <button type="submit">Daftar</button>
    </form>

    <div class="btn-container">
        <a href="login.php">Sudah punya akun? Login</a>
    </div>
</div>

<footer>
    <h3 style="margin-bottom: 10px;">About Dharma Hotel</h3>
    <p style="max-width: 600px; margin: 0 auto 20px;">
        <?php echo $footer['tentangkami_beranda']; ?>
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
