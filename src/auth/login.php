<?php
$no_login_required = true;
require_once "../includes/config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Kullanıcı adı kontrolü
    if(empty(trim($_POST["username"]))) {
        $username_err = "Lütfen kullanıcı adınızı girin.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Şifre kontrolü
    if(empty(trim($_POST["password"]))) {
        $password_err = "Lütfen şifrenizi girin.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Doğrulama
    if(empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, fullname, username, password FROM psychologists WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                // Kullanıcı var mı kontrol et
                if(mysqli_stmt_num_rows($stmt) == 1) {                    
                    mysqli_stmt_bind_result($stmt, $id, $fullname, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($password, $hashed_password)) {
                            // Şifre doğru, oturum başlat
                            session_start();
                            
                            // Oturum değişkenlerini ayarla
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["fullname"] = $fullname;
                            
                            // Ana sayfaya yönlendir
                            header("location: ../index.php");
                        } else {
                            // Şifre yanlış
                            $login_err = "Geçersiz kullanıcı adı veya şifre.";
                        }
                    }
                } else {
                    // Kullanıcı bulunamadı
                    $login_err = "Geçersiz kullanıcı adı veya şifre.";
                }
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<?php include_once "../includes/header.php"; ?>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Giriş Yap</h4>
            </div>
            <div class="card-body">
                <?php 
                if(!empty($login_err)) {
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }        
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Giriş Yap</button>
                        <a href="register.php" class="btn btn-secondary">Hesap Oluştur</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>