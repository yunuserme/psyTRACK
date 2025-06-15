<?php
$no_login_required = true;
require_once "../includes/config.php";

$fullname = $username = $email = $phone = $specialty = $password = $confirm_password = "";
$fullname_err = $username_err = $email_err = $phone_err = $specialty_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ad-Soyad doğrulama
    if(empty(trim($_POST["fullname"]))) {
        $fullname_err = "Lütfen adınızı ve soyadınızı girin.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }
    
    // Kullanıcı adı doğrulama
    if(empty(trim($_POST["username"]))) {
        $username_err = "Lütfen bir kullanıcı adı girin.";
    } else {
        $sql = "SELECT id FROM psychologists WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Bu kullanıcı adı zaten alınmış.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // E-posta doğrulama
    if(empty(trim($_POST["email"]))) {
        $email_err = "Lütfen bir e-posta adresi girin.";
    } else {
        $sql = "SELECT id FROM psychologists WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "Bu e-posta adresi zaten kullanılıyor.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Telefon ve uzmanlık alanı
    $phone = trim($_POST["phone"]);
    $specialty = trim($_POST["specialty"]);
    
    // Şifre doğrulama
    if(empty(trim($_POST["password"]))) {
        $password_err = "Lütfen bir şifre girin.";     
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $password_err = "Şifre en az 6 karakter olmalıdır.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Şifre onayı doğrulama
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Lütfen şifrenizi onaylayın.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Şifreler eşleşmiyor.";
        }
    }
    
    // Hata kontrolü
    if(empty($fullname_err) && empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        $sql = "INSERT INTO psychologists (fullname, username, email, phone, specialty, password) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $param_fullname, $param_username, $param_email, $param_phone, $param_specialty, $param_password);
            
            $param_fullname = $fullname;
            $param_username = $username;
            $param_email = $email;
            $param_phone = $phone;
            $param_specialty = $specialty;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Şifreyi hash'le
            
            if(mysqli_stmt_execute($stmt)) {
                // Kayıt başarılı, login sayfasına yönlendir
                header("location: login.php");
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
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Psikolog Kaydı</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Ad Soyad</label>
                        <input type="text" name="fullname" class="form-control <?php echo (!empty($fullname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fullname; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo $fullname_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo $username_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo $email_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo $phone; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="specialty" class="form-label">Uzmanlık Alanı</label>
                        <input type="text" name="specialty" class="form-control" value="<?php echo $specialty; ?>" placeholder="Örn: Bilişsel Davranışçı Terapi, Şema Terapi">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Şifre Tekrar</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" required>
                        <div class="invalid-feedback">
                            <?php echo $confirm_password_err; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                        <a href="login.php" class="btn btn-secondary">Zaten hesabım var</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>