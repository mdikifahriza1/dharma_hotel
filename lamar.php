<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";
$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$bg_query = mysqli_query($conn, "SELECT gambar FROM bg LIMIT 1");
$bg_row = mysqli_fetch_assoc($bg_query);
$bg_img = $bg_row ? $bg_row['gambar'] : 'img1.jpg';


$footer_query = mysqli_query($conn, "SELECT * FROM about LIMIT 1");
$footer = mysqli_fetch_assoc($footer_query);

$popup_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rekrutmen_id = $_POST['rekrutmen_id']; // Assuming you'll pass this from a recruitment page
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'];
    $pengalaman_kerja = $_POST['pengalaman_kerja'];
    $status_lamaran = 'baru'; // Default status for new applications

    // Handle CV file upload
    $cv_file_name = null;
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "cv/";
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $cv_file_name = uniqid() . '_' . basename($_FILES["cv_file"]["name"]);
        $target_file = $target_dir . $cv_file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a PDF
        if ($imageFileType != "pdf") {
            echo "<script>alert('Sorry, only PDF files are allowed for CVs.');</script>";
            $cv_file_name = null;
        } else {
            if (!move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_file)) {
                echo "<script>alert('Sorry, there was an error uploading your CV file.');</script>";
                $cv_file_name = null;
            }
        }
    }

    if ($cv_file_name) {
        // Generate unique registration number
        $nomor_pendaftaran = 'DH' . date('Ymd') . strtoupper(substr(uniqid(), -4));

        // Calculate test date (2 days after registration)
        $test_date = date('Y-m-d', strtotime('+2 days'));

        $stmt = $conn->prepare("INSERT INTO pelamar (rekrutmen_id, nama_lengkap, email, no_hp, alamat, pendidikan_terakhir, pengalaman_kerja, cv_file, status_lamaran, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("issssssss", $rekrutmen_id, $nama_lengkap, $email, $no_hp, $alamat, $pendidikan_terakhir, $pengalaman_kerja, $cv_file_name, $status_lamaran);

        if ($stmt->execute()) {
            $popup_message = "Nomor Pendaftaran anda " . $nomor_pendaftaran . " Silahkan datang ke Dharma Hotel pada tanggal " . date('d F Y', strtotime($test_date)) . " untuk mengikuti Tes. Terima Kasih.";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lamar Pekerjaan - Dharma Hotel</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      color: #333;
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
      color: white;
      height: 50vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      text-shadow: 2px 2px 5px #000;
      margin-bottom: 20px;
    }
    header h1 {
      font-size: 3rem;
      margin-top: 60px;
    }
    .form-container {
      padding: 40px;
      max-width: 700px;
      margin: 20px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group textarea,
    .form-group select,
    .form-group input[type="file"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-sizing: border-box;
      font-size: 1rem;
    }
    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }
    .form-group button {
      display: block;
      width: 100%;
      padding: 15px;
      background-color: #d4af37;
      color: white;
      font-weight: bold;
      font-size: 1.1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .form-group button:hover {
      background-color: #b7950b;
    }
    footer {
      background-color: #222;
      color: #fff;
      padding: 40px 20px;
      text-align: center;
    }
    footer a {
        color: #ffd700;
        text-decoration: none;
        margin: 0 10px;
        font-size: 1.5rem;
    }
    footer a:hover {
        color: #fff;
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
    <a href="rekrutmen.php">Rekrutmen</a>
    <a href="ulasan1.php">Ulasan</a> 
    <a href="login.php">Login</a> 
  </nav>

<header>
    <h1>Lamar Pekerjaan</h1>
</header>

<section class="form-container">
    <h2>Form Lamaran Pekerjaan</h2>
    <form action="lamar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="rekrutmen_id" value="1"> <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="no_hp">Nomor HP:</label>
            <input type="tel" id="no_hp" name="no_hp" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" required></textarea>
        </div>
        <div class="form-group">
            <label for="pendidikan_terakhir">Pendidikan Terakhir:</label>
            <input type="text" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
        </div>
        <div class="form-group">
            <label for="pengalaman_kerja">Pengalaman Kerja:</label>
            <textarea id="pengalaman_kerja" name="pengalaman_kerja" required></textarea>
        </div>
        <div class="form-group">
            <label for="cv_file">Upload CV (PDF only):</label>
            <input type="file" id="cv_file" name="cv_file" accept=".pdf" required>
        </div>
        <div class="form-group">
            <button type="submit">Lamar Sekarang</button>
        </div>
    </form>
</section>

<footer style="text-align: center; padding: 40px 20px; background-color: #111; color: #fff;">
  <h3 style="margin-bottom: 10px;">About Dharma Hotel</h3>
  <p style="max-width: 600px; margin: 0 auto 20px;">
    <?php echo $footer['deskripsi']; ?>
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
    © <?php echo date("Y"); ?> Dharma Hotel. All rights reserved.
  </p>
</footer>

<script>
    <?php if (!empty($popup_message)): ?>
        alert("<?php echo $popup_message; ?>");
        // Optionally, you can redirect the user after the popup
        // window.location.href = 'index.php';
    <?php endif; ?>
</script>

</body>
</html>

<?php $conn->close(); ?>