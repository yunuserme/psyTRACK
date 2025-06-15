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

// Hastayı sil
$sql = "DELETE FROM patients WHERE id = ? AND psychologist_id = ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $param_id, $param_psychologist_id);
    
    $param_id = $id;
    $param_psychologist_id = $_SESSION["id"];
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Hasta başarıyla silindi.";
        $_SESSION['message_type'] = "success";
        header("location: ../index.php");
        exit();
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}

mysqli_close($conn);
?>