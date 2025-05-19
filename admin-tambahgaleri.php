<?php
include 'koneksi.php';
include 'sidebar.php';

session_start();
$admin_id = $_SESSION['id'] ?? null;

if (!$admin_id) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id AND role = 'admin'");
$admin = mysqli_fetch_assoc($query_admin);

if (!$admin) {
    echo "<script>alert('Data admin tidak ditemukan!'); window.location='logout.php';</script>";
    exit;
}

$error = '';
$kategori = '';
$judul = '';
$deskripsi = '';
$gambar = '';

if (isset($_POST['tambah_galeri'])) {
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Validasi input
    if (empty($kategori)) {
        $error = "Kategori harus diisi.";
    } elseif (empty($judul)) {
        $error = "Judul harus diisi.";
    } elseif (empty($deskripsi)) {
        $error = "Deskripsi harus diisi.";
    }

    // Proses upload gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_name = $_FILES['gambar']['name'];
        $file_size = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_type = $_FILES['gambar']['type'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_type, $allowed_types)) {
            $error = "Jenis file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.";
        } elseif ($file_size > $max_size) {
            $error = "Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'galeri_' . time() . '.' . $file_ext; // Nama unik
            $upload_path = 'img/' . $new_file_name;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $error = "Gagal mengupload gambar.";
            } else {
                $gambar = $new_file_name; // Simpan nama file untuk disimpan di database
            }
        }
    } else {
        $error = "Gambar harus diupload."; // Set error jika tidak ada gambar yang diupload
    }

    if (empty($error)) {
        // Simpan data galeri ke database
        $query_insert_galeri = mysqli_query($conn, "INSERT INTO galeri (kategori, judul, deskripsi, gambar, tanggal) 
                                                VALUES ('$kategori', '$judul', '$deskripsi', '$gambar', NOW())");

        if ($query_insert_galeri) {
            echo "<script>alert('Data galeri berhasil ditambahkan!'); window.location='admin-galeri.php';</script>";
            exit;
        } else {
            $error = "Terjadi kesalahan saat menambahkan data galeri.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Foto Galeri</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }
        textarea,
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        input[type="file"] {
            padding: 0;
        }
        .btn-simpan {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-simpan:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h2>Tambah Foto Galeri</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kategori">Kategori:</label>
                    <select id="kategori" name="kategori" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="fasilitas" <?= $kategori == 'fasilitas' ? 'selected' : '' ?>>Fasilitas</option>
                        <option value="event" <?= $kategori == 'event' ? 'selected' : '' ?>>Event</option>
                        <option value="kamar" <?= $kategori == 'kamar' ? 'selected' : '' ?>>Kamar</option>
                        <option value="sekitar" <?= $kategori == 'sekitar' ? 'selected' : '' ?>>Sekitar</option>
                        <option value="lainnya" <?= $kategori == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="judul">Judul:</label>
                    <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($deskripsi) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar (Max 2MB, JPG, PNG, GIF):</label>
                    <input type="file" id="gambar" name="gambar" required>
                </div>
                <button type="submit" class="btn-simpan" name="tambah_galeri">Tambah Foto</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
