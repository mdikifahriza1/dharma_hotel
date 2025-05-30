# dharma_hotel
Tugas untuk Mata Kuliah Desain Web. Bisa digunakan bebas oleh siapapun.

Ikuti langkah langkah berikut untuk menginstal dan menyiapkan project hingga bisa diakses melalui browser.


Langkah 1: Instalasi Laragon

1. Unduh Laragon dari situs resmi: [https://laragon.org](https://laragon.org)
2. Instal Laragon seperti biasa.
3. Setelah instalasi selesai, buka aplikasi Laragon.


Langkah 2: Ekstrak dan Pindahkan File Project

1. Ekstrak file ZIP project ini.
2. Salin seluruh isi folder hasil ekstrak ke direktori:

   C:\laragon\www\dharma_hotel

   > Pastikan struktur folder sudah benar dan semua file ada di dalam folder dharma_hotel.


Langkah 3: Membuat Database

1. Buka Laragon dan klik Menu > Database > phpMyAdmin atau buka http://localhost/phpmyadmin di browser.
2. Login (default username: root, tanpa password).
1. Di dalam phpMyAdmin, pastikan tidak ada database dharma_hotel, untuk mencegah konflik.
2. Klik tab Import.
3. Pilih file SQL dari folder db (db/dharma_hotel.sql).
4. Klik Go untuk mulai proses import.



 ⚙️ Langkah 5: Konfigurasi Koneksi Database

1. Buka file:

   
   C:\laragon\www\dharma_hotel\koneksi.php
   

2. Pastikan konfigurasi seperti berikut:

   php
   <?php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "dharma_hotel";

   $koneksi = mysqli_connect($host, $user, $pass, $db);

   if (!$koneksi) {
       die("Koneksi gagal: " . mysqli_connect_error());
   }
   ?>
   



 🌐 Langkah 6: Jalankan di Browser

1. Pastikan Laragon sedang berjalan.

2. Buka browser dan akses:

   
   http://localhost/dharma_hotel
   

3. Jika semua langkah dilakukan dengan benar, project akan tampil di browser.



✅ Selesai!

