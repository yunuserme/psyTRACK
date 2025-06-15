<?php
// Oturumu başlat
session_start();
 
// Tüm oturum değişkenlerini temizle
$_SESSION = array();
 
// Oturum çerezini yok et
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
 
// Oturumu sonlandır
session_destroy();
 
// Giriş sayfasına yönlendir
header("location: /psikoloji_sistem/auth/login.php");
exit;
?>