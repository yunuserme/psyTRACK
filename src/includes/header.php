<?php
// Eğer oturum kontrolü gerekiyorsa
if(!isset($no_login_required)) {
    require_once __DIR__ . "/functions.php";
    checkLogin();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Psikoloji Hasta Takip Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/psikoloji_sistem/assets/css/style.css">
    
    <!-- Navbar stilleri için CSS -->
    <style>
        /* Ana navbar stili */
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        /* Tüm navbar linkleri için temel stil */
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 10px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin: 0 3px;
        }
        
        /* Hover efekti - menü öğesinin üzerine gelindiğinde */
        .navbar-dark .navbar-nav .nav-link:hover:not(.user-greeting) {
            background-color: rgba(255, 255, 255, 0.2);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Çıkış butonuna özel hover efekti */
        .navbar-dark .navbar-nav .logout-link:hover {
            background-color: #dc3545 !important; /* Bootstrap danger rengi */
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
        }
        
        /* Aktif sayfa vurgusu */
        .navbar-dark .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white !important;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* İkonlar için stil */
        .navbar-dark .navbar-nav .nav-link i {
            margin-right: 5px;
        }
        
        /* Kullanıcı adı gösterilen span için stil - buton görünümü olmayacak */
        .navbar-dark .navbar-nav .user-greeting {
            color: white !important;
            cursor: default;
            padding: 10px 15px;
        }
        
        /* Kullanıcı adı hover etkisi olmasın */
        .navbar-dark .navbar-nav .user-greeting:hover {
            background-color: transparent;
            transform: none;
            box-shadow: none;
        }
        
        /* Navbar container özel stilleri */
        .navbar > .container {
            position: relative;
            max-width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        /* Sol menü grubu */
        .left-menu-group {
            padding-left: 130px; /* Önceki değeri koruyoruz */
        }
        
        /* Sağ menü grubu - extreme right positioning */
        .right-menu-group {
            position: absolute;
            right: 15px; /* Container padding'i dikkate alarak */
            top: 0;
        }
        
        /* Mobil menü için özel stil */
        @media (max-width: 992px) {
            .navbar-dark .navbar-nav .nav-link {
                margin: 5px 0;
            }
            
            .navbar-dark .navbar-nav .nav-link.active {
                background-color: rgba(255, 255, 255, 0.2);
            }
            
            .left-menu-group {
                padding-left: 0;
            }
            
            .right-menu-group {
                position: static;
                right: auto;
                top: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/psikoloji_sistem/">Psikoloji Hasta Takip</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <ul class="navbar-nav left-menu-group">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/psikoloji_sistem/">
                                <i class="fas fa-home me-1"></i> Ana Sayfa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/patients/add.php') !== false) ? 'active' : ''; ?>" href="/psikoloji_sistem/patients/add.php">
                                <i class="fas fa-user-plus me-1"></i> Hasta Ekle
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>" href="/psikoloji_sistem/profile.php">
                                <i class="fas fa-user-cog me-1"></i> Profilim
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav right-menu-group">
                        <li class="nav-item">
                            <span class="nav-link user-greeting">
                                <i class="fas fa-user me-1"></i> Hoş geldiniz, <?php echo htmlspecialchars($_SESSION["fullname"] ?? "Kullanıcı"); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link logout-link" href="/psikoloji_sistem/auth/logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Çıkış Yap
                            </a>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" href="/psikoloji_sistem/auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Giriş Yap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>" href="/psikoloji_sistem/auth/register.php">
                                <i class="fas fa-user-plus me-1"></i> Kayıt Ol
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">