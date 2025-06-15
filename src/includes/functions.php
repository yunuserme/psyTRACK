<?php
// Oturum kontrolü fonksiyonu
function checkLogin() {
    // Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: ../auth/login.php");
        exit;
    }
}

// XSS saldırılarına karşı koruma fonksiyonu
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Tarih formatını düzenleme
function formatDate($date) {
    return date('d.m.Y', strtotime($date));
}
?>