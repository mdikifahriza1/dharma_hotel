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

$upload_error = '';

if (isset($_POST['tambah_news'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $nama_gambar = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_size = $_FILES['gambar']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_ext, $allowed_types)) {
            $upload_error = 'Format file tidak diizinkan. Hanya JPG, JPEG, dan PNG yang diperbolehkan.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'Ukuran file terlalu besar. Maksimal 2MB.';
        } else {
            $new_file_name = uniqid('news_') . '.' . $file_ext;
            $upload_path = 'img/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $nama_gambar = $new_file_name;
            } else {
                $upload_error = 'Terjadi kesalahan saat mengupload file.';
            }
        }
    }

    if (empty($upload_error)) {
        $query_tambah = mysqli_query($conn, "INSERT INTO news (title, slug, gambar, author, content, published_at, updated_at) VALUES ('$title', '$slug', '$nama_gambar', '$author', '$content', NOW(), NOW())");
        if ($query_tambah) {
            echo "<script>alert('news berhasil ditambahkan!'); window.location='admin-news.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menambahkan news!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah news</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 800px;
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
        input[type="text"],
        input[type="datetime-local"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
        }
        .form-check {
            margin-top: 10px;
            margin-bottom: 15px;
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
            <h2>Tambah news Baru</h2>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger"><?= $upload_error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Judul news:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="slug">Slug (URL):</label>
                    <input type="text" id="slug" name="slug" required>
                    <small class="form-text text-muted">Gunakan huruf kecil, angka, dan tanda hubung (-).</small>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar news:</label>
                    <input type="file" id="gambar" name="gambar" accept=".jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB.</small>
                </div>
                <div class="form-group">
                    <label for="author">Penulis:</label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($admin['full_name']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="published_at">Tanggal Publikasi:</label>
                    <input type="datetime-local" id="published_at" name="published_at" value="<?= date('Y-m-d\TH:i') ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Isi news:</label>
                    <textarea id="content" name="content" rows="8" required></textarea>
                </div>
                <button type="submit" class="btn-simpan" name="tambah_news">Simpan news</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>