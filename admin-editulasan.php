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

// Ambil ID ulasan dari parameter GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID Ulasan tidak valid!'); window.location='admin-ulasan.php';</script>";
    exit;
}

$id_ulasan = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data ulasan berdasarkan ID
$query_get_ulasan = mysqli_query($conn, "SELECT u.*, us.full_name, r.id AS id_reservasi 
                                        FROM ulasan u
                                        JOIN users us ON u.user_id = us.id
                                        JOIN reservasi r ON u.reservasi_id = r.id
                                        WHERE u.id = $id_ulasan");
$data_ulasan = mysqli_fetch_assoc($query_get_ulasan);

if (!$data_ulasan) {
    echo "<script>alert('Data ulasan tidak ditemukan!'); window.location='admin-ulasan.php';</script>";
    exit;
}

if (isset($_POST['edit_ulasan'])) {
    $isi_ulasan = mysqli_real_escape_string($conn, $_POST['isi_ulasan']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);

    $query_update_ulasan = mysqli_query($conn, "UPDATE ulasan SET isi_ulasan = '$isi_ulasan', rating = '$rating' WHERE id = $id_ulasan");

    if ($query_update_ulasan) {
        echo "<script>alert('Ulasan berhasil diperbarui!'); window.location='admin-ulasan.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui ulasan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ulasan</title>
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
            min-height: 120px;
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
            <h2>Edit Ulasan</h2>
            <form method="post">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Pengulas:</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($data_ulasan['full_name']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="id_reservasi">ID Reservasi:</label>
                    <input type="text" id="id_reservasi" name="id_reservasi" value="<?= htmlspecialchars($data_ulasan['id_reservasi']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="isi_ulasan">Isi Ulasan:</label>
                    <textarea id="isi_ulasan" name="isi_ulasan" required><?= htmlspecialchars($data_ulasan['isi_ulasan']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" value="<?= htmlspecialchars($data_ulasan['rating']) ?>" required>
                </div>
                <button type="submit" class="btn-simpan" name="edit_ulasan">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
