<?php
require_once "includes/config.php";
require_once "includes/functions.php";

// Kullanıcı giriş yapmış mı kontrol et
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth/login.php");
    exit;
}

// Hastaları getir
$sql = "SELECT * FROM patients WHERE psychologist_id = ? ORDER BY created_at DESC";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $_SESSION["id"];
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Hata: " . mysqli_error($conn);
    }
} else {
    echo "Hata: " . mysqli_error($conn);
}

// Header'ı dahil et
include "includes/header.php";
?>

<!-- Psikolog bilgileri kartı -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Psikolog Bilgileri</h5>
                <a href="profile.php" class="btn btn-light btn-sm">
                    <i class="fas fa-edit me-1"></i>Profili Düzenle
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fas fa-user me-2"></i>Ad Soyad:</strong> <?php echo htmlspecialchars($_SESSION["fullname"]); ?></p>
                        <?php
                        // Psikolog bilgilerini getir
                        $sql_user = "SELECT email, phone, specialty FROM psychologists WHERE id = ?";
                        if($stmt_user = mysqli_prepare($conn, $sql_user)) {
                            mysqli_stmt_bind_param($stmt_user, "i", $_SESSION["id"]);
                            if(mysqli_stmt_execute($stmt_user)) {
                                $result_user = mysqli_stmt_get_result($stmt_user);
                                if($user_data = mysqli_fetch_assoc($result_user)) {
                                    echo '<p><strong><i class="fas fa-envelope me-2"></i>E-posta:</strong> ' . htmlspecialchars($user_data["email"]) . '</p>';
                                    if(!empty($user_data["phone"])) {
                                        echo '<p><strong><i class="fas fa-phone me-2"></i>Telefon:</strong> ' . htmlspecialchars($user_data["phone"]) . '</p>';
                                    }
                                }
                            }
                            mysqli_stmt_close($stmt_user);
                        }
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?php
                        if(isset($user_data) && !empty($user_data["specialty"])) {
                            echo '<p><strong><i class="fas fa-graduation-cap me-2"></i>Uzmanlık Alanı:</strong> ' . htmlspecialchars($user_data["specialty"]) . '</p>';
                        }
                        
                        // Hasta sayısını getir
                        $sql_count = "SELECT COUNT(*) as total FROM patients WHERE psychologist_id = ?";
                        if($stmt_count = mysqli_prepare($conn, $sql_count)) {
                            mysqli_stmt_bind_param($stmt_count, "i", $_SESSION["id"]);
                            if(mysqli_stmt_execute($stmt_count)) {
                                $result_count = mysqli_stmt_get_result($stmt_count);
                                if($count_data = mysqli_fetch_assoc($result_count)) {
                                    echo '<p><strong><i class="fas fa-users me-2"></i>Toplam Hasta Sayısı:</strong> ' . $count_data["total"] . '</p>';
                                }
                            }
                            mysqli_stmt_close($stmt_count);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hasta Listesi başlığı ve hasta ekleme butonu -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list me-2"></i>Hasta Listesi</h2>
    <a href="patients/add.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Yeni Hasta Ekle
    </a>
</div>

<?php
// Mesaj kontrolü
if(isset($_SESSION['message'])) {
    echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
        ' . $_SESSION['message'] . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="row">
    <?php
    if(isset($result) && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="card patient-card mb-4 h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($row["fullname"]); ?></h5>
                <div>
                    <a href="patients/edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-warning btn-action" title="Düzenle"><i class="fas fa-edit"></i></a>
                    <button onclick="confirmDelete(<?php echo $row["id"]; ?>)" class="btn btn-sm btn-danger btn-action" title="Sil"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <p><strong><i class="fas fa-calendar-alt me-2"></i>Doğum Tarihi:</strong> <?php echo isset($row["birthdate"]) && $row["birthdate"] ? date('d.m.Y', strtotime($row["birthdate"])) : 'Belirtilmemiş'; ?></p>
                <p><strong><i class="fas fa-user-tag me-2"></i>Kişilik Tipi:</strong> <?php echo htmlspecialchars($row["personality_type"] ?? 'Belirtilmemiş'); ?></p>
                <p><strong><i class="fas fa-clock me-2"></i>Oluşturulma:</strong> <?php echo date('d.m.Y', strtotime($row["created_at"])); ?></p>
                <p><strong><i class="fas fa-edit me-2"></i>Son Güncelleme:</strong> <?php echo date('d.m.Y', strtotime($row["updated_at"])); ?></p>
                <div class="d-grid mt-3">
                    <a href="patients/edit.php?id=<?php echo $row["id"]; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Detayları Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
        }
    } else {
        echo '<div class="col-12"><div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Henüz hasta kaydı bulunmuyor. "Yeni Hasta Ekle" butonuna tıklayarak hasta ekleyebilirsiniz.
        </div></div>';
    }
    
    if(isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    ?>
</div>

<!-- JavaScript Fonksiyonları -->
<script>
    // Hasta silme onayı
    function confirmDelete(id) {
        if (confirm("Bu hastayı silmek istediğinizden emin misiniz?")) {
            window.location.href = "patients/delete.php?id=" + id;
        }
    }
</script>

<?php include "includes/footer.php"; ?>