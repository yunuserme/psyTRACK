// Silme işlemi için onay
function confirmDelete(id) {
    if (confirm("Bu hastayı silmek istediğinizden emin misiniz?")) {
        window.location.href = "/psikoloji-sistem/patients/delete.php?id=" + id;
    }
}

// Form doğrulama
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
});