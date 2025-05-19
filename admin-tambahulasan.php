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

// Ambil reservasi_id dan user_id dari URL
if (isset($_GET['reservasi_id']) && is_numeric($_GET['reservasi_id']) && isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $reservasi_id = mysqli_real_escape_string($conn, $_GET['reservasi_id']);
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

    // Pastikan reservasi ada dan statusnya disetujui (opsional)
    $query_reservasi = mysqli_query($conn, "SELECT id, status FROM reservasi WHERE id = $reservasi_id AND status = 'disetujui'");
    if (mysqli_num_rows($query_reservasi) == 0) {
        echo "<script>alert('Reservasi tidak valid atau belum disetujui!'); window.location='admin-ulasan.php';</script>";
        exit;
    }

} else {
    echo "<script>alert('ID Reservasi dan User ID tidak valid!'); window.location='admin-ulasan.php';</script>";
    exit;
}

$upload_error = '';

if (isset($_POST['tambah_ulasan'])) {
    $isi_ulasan = mysqli_real_escape_string($conn, $_POST['isi_ulasan']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $tanggal_ulasan = date('Y-m-d H:i:s'); // Tanggal saat ini

    $query_insert_ulasan = mysqli_query($conn, "
        INSERT INTO ulasan (user_id, reservasi_id, isi_ulasan, rating, tanggal)
        VALUES ('$user_id', '$reservasi_id', '$isi_ulasan', '$rating', '$tanggal_ulasan')
    ");

    if ($query_insert_ulasan) {
        echo "<script>alert('Ulasan berhasil ditambahkan!'); window.location='admin-ulasan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menambahkan ulasan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Ulasan</title>
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
        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
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
            <h2>Berikan Ulasan</h2>
            <?php if ($upload_error): ?>
                <div class="alert alert-danger"><?= $upload_error ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="reservasi_id" value="<?= $reservasi_id ?>">
                <div class="form-group">
                    <label for="isi_ulasan">Isi Ulasan:</label>
                    <textarea id="isi_ulasan" name="isi_ulasan" required></textarea>
                </div>
                <div class="form-group">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required>
                </div>
                <button type="submit" class="btn-simpan" name="tambah_ulasan">Kirim Ulasan</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>