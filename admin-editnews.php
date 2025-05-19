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

// Ambil ID news dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID news tidak valid!'); window.location='admin-news.php';</script>";
    exit;
}
$id_news = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data news berdasarkan ID
$query_get_news = mysqli_query($conn, "SELECT * FROM news WHERE id = $id_news");
$data_news = mysqli_fetch_assoc($query_get_news);

// Jika data news tidak ditemukan
if (!$data_news) {
    echo "<script>alert('Data news tidak ditemukan!'); window.location='admin-news.php';</script>";
    exit;
}

$upload_error = '';

if (isset($_POST['edit_news'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $published_at = mysqli_real_escape_string($conn, $_POST['published_at']);
    $nama_gambar_baru = $data_news['gambar']; // Default ke gambar lama

    if (isset($_FILES['gambar_baru']) && $_FILES['gambar_baru']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['gambar_baru']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp = $_FILES['gambar_baru']['tmp_name'];
        $file_size = $_FILES['gambar_baru']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_ext, $allowed_types)) {
            $upload_error = 'Format file tidak diizinkan. Hanya JPG, JPEG, dan PNG yang diperbolehkan.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'Ukuran file terlalu besar. Maksimal 2MB.';
        } else {
            $new_file_name = uniqid('news_') . '.' . $file_ext;
            $upload_path = 'img/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus file lama jika bukan default dan ada
                if (!empty($data_news['gambar']) && file_exists('img/' . $data_news['gambar'])) {
                    unlink('img/' . $data_news['gambar']);
                }
                $nama_gambar_baru = $new_file_name;
            } else {
                $upload_error = 'Terjadi kesalahan saat mengupload file.';
            }
        }
    }

    if (empty($upload_error)) {
        $query_update = mysqli_query($conn, "UPDATE news SET title = '$title', slug = '$slug', gambar = '$nama_gambar_baru', author = '$author', content = '$content', published_at = '$published_at', updated_at = NOW() WHERE id = $id_news");
        if ($query_update) {
            echo "<script>alert('news berhasil diperbarui!'); window.location='admin-news.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memperbarui news!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit news</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
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
        .current-image {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .current-image img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
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
            <h2>Edit news</h2>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger"><?= $upload_error ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Judul news:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($data_news['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="slug">Slug (URL):</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($data_news['slug']) ?>" required>
                    <small class="form-text text-muted">Gunakan huruf kecil, angka, dan tanda hubung (-).</small>
                </div>
                <div class="form-group">
                    <label>Gambar Saat Ini:</label>
                    <div class="current-image">
                        <?php if ($data_news['gambar']): ?>
                            <img src="img/<?= htmlspecialchars($data_news['gambar']) ?>" alt="<?= htmlspecialchars($data_news['title']) ?>">
                        <?php else: ?>
                            Tidak ada gambar saat ini.
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gambar_baru">Ganti Gambar news (opsional):</label>
                    <input type="file" id="gambar_baru" name="gambar_baru" accept=".jpg,.jpeg,.png">
                    <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG. Ukuran maksimal: 2MB. Jika tidak ingin mengganti gambar, biarkan kosong.</small>
                </div>
                <div class="form-group">
                    <label for="author">Penulis:</label>
                    <input type="text" id="author" name="author" value="<?= htmlspecialchars($data_news['author']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="published_at">Tanggal Publikasi:</label>
                    <input type="datetime-local" id="published_at" name="published_at" value="<?= date('Y-m-d\TH:i', strtotime($data_news['published_at'])) ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Isi news:</label>
                    <textarea id="content" name="content" rows="8" required><?= htmlspecialchars($data_news['content']) ?></textarea>
                </div>
                <button type="submit" class="btn-simpan" name="edit_news">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>