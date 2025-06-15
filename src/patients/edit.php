<?php
require_once "../includes/config.php";
require_once "../includes/functions.php";

// Kullanıcı giriş yapmış mı kontrol et
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// ID kontrolü
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    header("location: ../index.php");
    exit();
}

$id = trim($_GET["id"]);
$fullname = $birthdate = $personality_type = $core_beliefs = $cognitive_distortions = $general_issues = $notes = "";
$fullname_err = "";

// Hasta verilerini getir
$sql = "SELECT * FROM patients WHERE id = ? AND psychologist_id = ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $param_id, $param_psychologist_id);
    $param_id = $id;
    $param_psychologist_id = $_SESSION["id"];
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            $fullname = $row["fullname"];
            $birthdate = $row["birthdate"];
            $personality_type = $row["personality_type"];
            $core_beliefs = $row["core_beliefs"];
            $cognitive_distortions = $row["cognitive_distortions"];
            $general_issues = $row["general_issues"];
            $notes = $row["notes"];
        } else {
            // Hasta bulunamadı
            header("location: ../index.php");
            exit();
        }
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ad-Soyad kontrolü
    if(empty(trim($_POST["fullname"]))) {
        $fullname_err = "Lütfen hastanın adını ve soyadını girin.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }
    
    // Diğer alanlar
    $birthdate = !empty($_POST["birthdate"]) ? $_POST["birthdate"] : NULL;
    $personality_type = trim($_POST["personality_type"]);
    $core_beliefs = trim($_POST["core_beliefs"]);
    $cognitive_distortions = trim($_POST["cognitive_distortions"]);
    $general_issues = trim($_POST["general_issues"]);
    $notes = trim($_POST["notes"]);
    
    // Hata kontrolü
    if(empty($fullname_err)) {
        $sql = "UPDATE patients SET fullname = ?, birthdate = ?, personality_type = ?, core_beliefs = ?, cognitive_distortions = ?, general_issues = ?, notes = ? WHERE id = ? AND psychologist_id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssii", $param_fullname, $param_birthdate, $param_personality_type, $param_core_beliefs, $param_cognitive_distortions, $param_general_issues, $param_notes, $param_id, $param_psychologist_id);
            
            $param_fullname = $fullname;
            $param_birthdate = $birthdate;
            $param_personality_type = $personality_type;
            $param_core_beliefs = $core_beliefs;
            $param_cognitive_distortions = $cognitive_distortions;
            $param_general_issues = $general_issues;
            $param_notes = $notes;
            $param_id = $id;
            $param_psychologist_id = $_SESSION["id"];
            
            if(mysqli_stmt_execute($stmt)) {
                // Başarılı, ana sayfaya yönlendir
                $_SESSION['message'] = "Hasta bilgileri başarıyla güncellendi.";
                $_SESSION['message_type'] = "success";
                header("location: ../index.php");
                exit();
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

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Hasta Bilgilerini Düzenle</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="post" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullname" class="form-label">Ad Soyad *</label>
                            <input type="text" name="fullname" class="form-control <?php echo (!empty($fullname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fullname; ?>" required>
                            <div class="invalid-feedback">
                                <?php echo $fullname_err; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="birthdate" class="form-label">Doğum Tarihi</label>
                            <input type="date" name="birthdate" class="form-control" value="<?php echo $birthdate; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="personality_type" class="form-label">Kişilik Tipi</label>
                        <input type="text" name="personality_type" class="form-control" value="<?php echo $personality_type; ?>" placeholder="Örn: INTJ, ENFP">
                    </div>
                    
                    <div class="mb-3">
                        <label for="core_beliefs" class="form-label">Ara İnançlar</label>
                        <textarea name="core_beliefs" class="form-control" rows="3"><?php echo $core_beliefs; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cognitive_distortions" class="form-label">Bilişsel Çarpıtmalar</label>
                        <textarea name="cognitive_distortions" class="form-control" rows="3"><?php echo $cognitive_distortions; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="general_issues" class="form-label">Genel Sıkıntılar</label>
                        <textarea name="general_issues" class="form-control" rows="3"><?php echo $general_issues; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notlar</label>
                        <textarea name="notes" class="form-control" rows="5"><?php echo $notes; ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="../index.php" class="btn btn-secondary">İptal</a>
                        <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>