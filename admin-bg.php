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

// Ambil data background dari tabel bg (asumsi hanya ada satu baris dengan id = 1)
$query_bg = mysqli_query($conn, "SELECT * FROM bg WHERE id = 1");
$bg_data = mysqli_fetch_assoc($query_bg);
$current_bg = $bg_data['gambar'] ?? 'default.jpg'; // Set default jika tidak ada

$upload_error = '';
$upload_success = '';

if (isset($_POST['ganti_bg'])) {
    if (isset($_FILES['bg_baru']) && $_FILES['bg_baru']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['bg_baru']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp = $_FILES['bg_baru']['tmp_name'];
        $file_size = $_FILES['bg_baru']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_ext, $allowed_types)) {
            $upload_error = 'Format file tidak diizinkan. Hanya JPG, JPEG, dan PNG yang diperbolehkan.';
        } elseif ($file_size > $max_size) {
            $upload_error = 'Ukuran file terlalu besar. Maksimal 2MB.';
        } else {
            $new_file_name = uniqid('bg_') . '.' . $file_ext;
            $upload_path = 'img/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus file lama jika bukan default
                if ($current_bg !== 'default.jpg' && file_exists('img/' . $current_bg)) {
                    unlink('img/' . $current_bg);
                }

                // Update database
                $update_query = mysqli_query($conn, "UPDATE bg SET gambar = '$new_file_name' WHERE id = 1");
                if ($update_query) {
                    $upload_success = 'Gambar latar belakang berhasil diganti!';
                    $current_bg = $new_file_name; // Update tampilan segera
                } else {
                    $upload_error = 'Terjadi kesalahan saat memperbarui database.';
                    // Hapus file yang baru diupload jika gagal update database
                    if (file_exists($upload_path)) {
                        unlink($upload_path);
                    }
                }
            } else {
                $upload_error = 'Terjadi kesalahan saat mengupload file.';
            }
        }
    } else {
        $upload_error = 'Silakan pilih file gambar.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Latar Belakang</title>
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .bg-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .bg-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .current-bg {
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }
        .current-bg img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .form-ganti-bg {
            margin-top: 20px;
        }
        .form-ganti-bg label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-ganti-bg input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-ganti {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-ganti:hover {
            background-color: #1d4ed8;
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
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
<div class="content">
    <div class="bg-container">
        <h2>Kelola Latar Belakang</h2>

        <?php if ($upload_error): ?>
            <div class="alert alert-danger"><?= $upload_error ?></div>
        <?php endif; ?>

        <?php if ($upload_success): ?>
            <div class="alert alert-success"><?= $upload_success ?></div>
        <?php endif; ?>

        <div class="current-bg">
            <h3>Latar Belakang Saat Ini</h3>
            <img src="img/<?= htmlspecialchars($current_bg) ?>" alt="Latar Belakang Saat Ini">
        </div>

        <form method="post" enctype="multipart/form-data" class="form-ganti-bg">
            <label for="bg_baru">Ganti Latar Belakang:</label>
            <input type="file" name="bg_baru" id="bg_baru" accept=".jpg,.jpeg,.png" required>
            <button type="submit" class="btn-ganti" name="ganti_bg">Ganti</button>
        </form>
    </div>
</div>
</body>
</html>