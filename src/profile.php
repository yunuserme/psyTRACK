<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Kullanıcı giriş yapmış mı kontrol et
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth/login.php");
    exit;
}

$success_message = $error_message = "";

// Psikolog bilgilerini getir
$sql = "SELECT * FROM psychologists WHERE id = ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $_SESSION["id"];
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
        } else {
            // Psikolog bulunamadı
            header("location: index.php");
            exit();
        }
    } else {
        $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bilgi güncelleme mi şifre değiştirme mi?
    if(isset($_POST["update_info"])) {
        // Bilgileri güncelle
        $fullname = trim($_POST["fullname"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["phone"]);
        $specialty = trim($_POST["specialty"]);
        
        // Email kontrolü
        $email_err = "";
        if($email != $user["email"]) {
            $sql = "SELECT id FROM psychologists WHERE email = ? AND id != ?";
            
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_email, $param_id);
                $param_email = $email;
                $param_id = $_SESSION["id"];
                
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1) {
                        $email_err = "Bu e-posta adresi zaten kullanılıyor.";
                    }
                }
                
                mysqli_stmt_close($stmt);
            }
        }
        
        // Hata yoksa güncelle
        if(empty($email_err)) {
            $sql = "UPDATE psychologists SET fullname = ?, email = ?, phone = ?, specialty = ? WHERE id = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $param_fullname, $param_email, $param_phone, $param_specialty, $param_id);
                
                $param_fullname = $fullname;
                $param_email = $email;
                $param_phone = $phone;
                $param_specialty = $specialty;
                $param_id = $_SESSION["id"];
                
                if(mysqli_stmt_execute($stmt)) {
                    $success_message = "Bilgileriniz başarıyla güncellendi.";
                    
                    // Session bilgilerini güncelle
                    $_SESSION["fullname"] = $fullname;
                    
                    // Güncel bilgileri yeniden yükle
                    $user["fullname"] = $fullname;
                    $user["email"] = $email;
                    $user["phone"] = $phone;
                    $user["specialty"] = $specialty;
                } else {
                    $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                
                mysqli_stmt_close($stmt);
            }
        } else {
            $error_message = $email_err;
        }
    } elseif(isset($_POST["change_password"])) {
        // Şifre değiştir
        $current_password = trim($_POST["current_password"]);
        $new_password = trim($_POST["new_password"]);
        $confirm_password = trim($_POST["confirm_password"]);
        
        // Mevcut şifre kontrolü
        if(!password_verify($current_password, $user["password"])) {
            $error_message = "Mevcut şifreniz yanlış.";
        } elseif(strlen($new_password) < 6) {
            $error_message = "Yeni şifre en az 6 karakter olmalıdır.";
        } elseif($new_password != $confirm_password) {
            $error_message = "Şifreler eşleşmiyor.";
        } else {
            // Şifreyi güncelle
            $sql = "UPDATE psychologists SET password = ? WHERE id = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
                
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_id = $_SESSION["id"];
                
                if(mysqli_stmt_execute($stmt)) {
                    $success_message = "Şifreniz başarıyla değiştirildi.";
                } else {
                    $error_message = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                
                mysqli_stmt_close($stmt);
            }
        }
    }
}

include "includes/header.php";
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2>Profilim</h2>
        
        <?php 
        if(!empty($success_message)) {
            echo '<div class="alert alert-success">' . $success_message . '</div>';
        }
        
        if(!empty($error_message)) {
            echo '<div class="alert alert-danger">' . $error_message . '</div>';
        }
        ?>
        
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Kişisel Bilgiler</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Şifre Değiştir</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="profileTabsContent">
                    <!-- Kişisel Bilgiler Sekmesi -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Ad Soyad</label>
                                <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user["fullname"]); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user["phone"]); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="specialty" class="form-label">Uzmanlık Alanı</label>
                                <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($user["specialty"]); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user["username"]); ?>" disabled>
                                <div class="form-text">Kullanıcı adı değiştirilemez.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kayıt Tarihi</label>
                                <input type="text" class="form-control" value="<?php echo date('d.m.Y', strtotime($user["created_at"])); ?>" disabled>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_info" class="btn btn-primary">Bilgileri Güncelle</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Şifre Değiştir Sekmesi -->
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mevcut Şifre</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Yeni Şifre</label>
                                <input type="password" name="new_password" class="form-control" required>
                                <div class="form-text">Şifreniz en az 6 karakter olmalıdır.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Yeni Şifre (Tekrar)</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="change_password" class="btn btn-primary">Şifreyi Değiştir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include "includes/footer.php"; 
?>