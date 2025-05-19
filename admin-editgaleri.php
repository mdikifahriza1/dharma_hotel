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

// Pastikan ID galeri ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_galeri = mysqli_real_escape_string($conn, $_GET['id']);

    // Ambil data galeri dari database
    $query_galeri = mysqli_query($conn, "SELECT * FROM galeri WHERE id = $id_galeri");
    $data_galeri = mysqli_fetch_assoc($query_galeri);

    if (!$data_galeri) {
        echo "<script>alert('Data galeri tidak ditemukan!'); window.location='admin-galeri.php';</script>";
        exit;
    }

    $kategori = $data_galeri['kategori'];
    $judul = $data_galeri['judul'];
    $deskripsi = $data_galeri['deskripsi'];
    $gambar = $data_galeri['gambar']; // Nama file gambar
    $tanggal = $data_galeri['tanggal'];
    
} else {
    echo "<script>alert('ID galeri tidak valid!'); window.location='admin-galeri.php';</script>";
    exit;
}

$error = '';

if (isset($_POST['edit_galeri'])) {
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

    // Proses upload gambar baru jika ada
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
                // Hapus gambar lama jika ada
                if (!empty($gambar) && file_exists('img/' . $gambar)) {
                    unlink('img/' . $gambar);
                }
                $gambar = $new_file_name; // Simpan nama file baru
            }
        }
    }

    if (empty($error)) {
        // Update data galeri ke database
        $query_update_galeri = mysqli_query($conn, "UPDATE galeri SET 
                                                kategori = '$kategori',
                                                judul = '$judul',
                                                deskripsi = '$deskripsi',
                                                gambar = '$gambar'
                                                WHERE id = $id_galeri");

        if ($query_update_galeri) {
            echo "<script>alert('Data galeri berhasil diubah!'); window.location='admin-galeri.php';</script>";
            exit;
        } else {
            $error = "Terjadi kesalahan saat mengubah data galeri.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Foto Galeri</title>
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
        .img-preview {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="form-container">
            <h2>Edit Foto Galeri</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kategori">Kategori:</label>
                    <select id="kategori" name="kategori" required>
                        <option value="" disabled>Pilih Kategori</option>
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
                    <input type="file" id="gambar" name="gambar">
                    <?php if ($gambar): ?>
                        <img src="img/<?= htmlspecialchars($gambar) ?>" alt="Preview Gambar" class="img-preview">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-simpan" name="edit_galeri">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk menampilkan preview gambar yang diupload
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.onload = function() {
                const img = document.createElement('img');
                img.src = reader.result;
                img.classList.add('img-preview');
                const previewContainer = document.querySelector('.form-group'); // Ambil container yang sesuai
                const existingPreview = document.querySelector('.img-preview');
                if (existingPreview) {
                    previewContainer.removeChild(existingPreview); // Hapus preview sebelumnya
                }
                previewContainer.appendChild(img); // Tambahkan preview baru
            }
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
