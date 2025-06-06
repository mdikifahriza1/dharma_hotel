<?php
include 'koneksi.php';
include 'sidebar.php';

// Cek jika admin login
session_start();
$admin_id = $_SESSION['admin_id'] ?? null;

// Cek jika ada ID pengguna yang ingin diedit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Ambil data pengguna dari database
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($result);

    // Cek jika data pengguna ditemukan
    if (!$user) {
        echo "<script>alert('Pengguna tidak ditemukan!'); window.location='admin-profil.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID pengguna tidak ditemukan!'); window.location='admin-profil.php';</script>";
    exit();
}

// Proses update data pengguna
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek jika file gambar ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_size = $_FILES['foto']['size'];
        $foto_error = $_FILES['foto']['error'];

        // Validasi foto
        if ($foto_error === 0) {
            $foto_ext = pathinfo($foto_name, PATHINFO_EXTENSION);
            $foto_ext = strtolower($foto_ext);
            $allowed_ext = ['jpg', 'jpeg', 'png'];

            if (in_array($foto_ext, $allowed_ext)) {
                if ($foto_size < 5000000) { // Maksimal 5MB
                    $foto_new_name = uniqid('', true) . '.' . $foto_ext;
                    $foto_dest = 'img/' . $foto_new_name;
                    move_uploaded_file($foto_tmp, $foto_dest);
                } else {
                    $error_msg = 'Ukuran file foto terlalu besar!';
                }
            } else {
                $error_msg = 'Hanya file JPG, JPEG, PNG yang diperbolehkan!';
            }
        } else {
            $error_msg = 'Terjadi kesalahan saat mengupload foto!';
        }
    } else {
        // Jika foto tidak diupload, gunakan foto lama
        $foto_new_name = $user['foto'];
    }

    // Enkripsi password jika diubah
    if (!empty($password)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $password_hashed = $user['password'];
    }

    // Jika tidak ada error, update data pengguna
    if (empty($error_msg)) {
        $query = "UPDATE users SET
                  username = '$username',
                  email = '$email',
                  password = '$password_hashed',
                  full_name = '$full_name',
                  nik = '$nik',
                  tanggal_lahir = '$tanggal_lahir',
                  jenis_kelamin = '$jenis_kelamin',
                  alamat = '$alamat',
                  telepon = '$telepon',
                  role = '$role',
                  foto = '$foto_new_name'
                  WHERE id = $user_id";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Data pengguna berhasil diubah!'); window.location='admin-profil.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah data pengguna. Silakan coba lagi!'); window.location='admin-editusers.php?id=$user_id';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }

        .btn {
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
<div class="content">
    <h1>Edit Pengguna</h1>
    <form method="POST" enctype="multipart/form-data">
        <?php if (isset($error_msg)): ?>
            <p style="color: red;"><?= $error_msg ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?= $user['username'] ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= $user['email'] ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <label for="full_name">Nama Lengkap</label>
            <input type="text" name="full_name" id="full_name" value="<?= $user['full_name'] ?>" required>
        </div>

        <div class="form-group">
            <label for="nik">NIK</label>
            <input type="text" name="nik" id="nik" value="<?= $user['nik'] ?>" required>
        </div>

        <div class="form-group">
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="<?= $user['tanggal_lahir'] ?>" required>
        </div>

        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="L" <?= $user['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $user['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" name="alamat" id="alamat" value="<?= $user['alamat'] ?>" required>
        </div>

        <div class="form-group">
            <label for="telepon">Telepon</label>
            <input type="text" name="telepon" id="telepon" value="<?= $user['telepon'] ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label for="foto">Foto Profil (Kosongkan jika tidak ingin mengubah)</label>
            <input type="file" name="foto" id="foto" accept="image/*">
        </div>

        <div class="form-group">
            <button type="submit" name="submit" class="btn">Simpan Perubahan</button>
        </div>
    </form>
</div>
</body>
</html>
