<?php
session_start();

// Veritabanı bağlantı bilgileri
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // XAMPP için genelde 'root'
define('DB_PASSWORD', ''); // XAMPP için genelde boş
define('DB_NAME', 'psikoloji_sistem');

// Veritabanına bağlan
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Bağlantıyı kontrol et
if($conn === false) {
    die("HATA: Veritabanına bağlanılamadı. " . mysqli_connect_error());
}

// Türkçe karakter sorunu yaşamamak için
mysqli_set_charset($conn, "utf8");
?>