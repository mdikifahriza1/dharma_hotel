<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];

// Ambil data user saat ini
$user_q = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($user_q);

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // tidak di-escape karena dicek dulu

    // Jika ada foto baru
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = basename($_FILES['foto']['name']);
        $foto_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($foto_tmp, "img/$foto_name");
    } else {
        $foto_name = $user['foto']; // Gunakan foto lama
    }

    // Siapkan query update
    if (!empty($password)) {
        // Note: Belum menggunakan password_hash, pertimbangkan untuk keamanan
        $update_q = mysqli_query($conn, "UPDATE users SET
            full_name = '$full_name',
            email = '$email',
            username = '$username',
            password = '$password',
            nik = '$nik',
            tanggal_lahir = '$tanggal_lahir',
            jenis_kelamin = '$jenis_kelamin',
            alamat = '$alamat',
            telepon = '$telepon',
            foto = '$foto_name',
            updated_at = NOW()
            WHERE id = $id");
    } else {
        $update_q = mysqli_query($conn, "UPDATE users SET
            full_name = '$full_name',
            email = '$email',
            username = '$username',
            nik = '$nik',
            tanggal_lahir = '$tanggal_lahir',
            jenis_kelamin = '$jenis_kelamin',
            alamat = '$alamat',
            telepon = '$telepon',
            foto = '$foto_name',
            updated_at = NOW()
            WHERE id = $id");
    }

    if ($update_q) {
        header("Location: profiluser.php");
        exit;
    } else {
        $error = "Gagal mengupdate profil.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profil</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="styleuser.css" />
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
      <h2>Edit Profil</h2>
      <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="full_name">Nama Lengkap</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />
        </div>
        <div class="form-group">
          <label for="nik">NIK</label>
          <input type="text" name="nik" value="<?= htmlspecialchars($user['nik']) ?>" required />
        </div>
        <div class="form-group">
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" required />
        </div>
        <div class="form-group">
          <label for="jenis_kelamin">Jenis Kelamin</label>
          <select name="jenis_kelamin" required>
            <option value="L" <?= $user['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $user['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>
        <div class="form-group">
          <label for="alamat">Alamat</label>
          <textarea name="alamat" rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="telepon">Telepon</label>
          <input type="text" name="telepon" value="<?= htmlspecialchars($user['telepon']) ?>" />
        </div>
        <div class="form-group">
          <label for="foto">Foto (biarkan kosong jika tidak ingin mengubah)</label>
          <input type="file" name="foto" />
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required />
        </div>
        <div class="form-group">
          <label for="password">Password (biarkan kosong jika tidak ingin mengubah)</label>
          <input type="password" name="password" placeholder="********" />
        </div>
        <button class="btn-submit" type="submit">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</body>
</html>
