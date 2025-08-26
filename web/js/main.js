function showAlert(type, message) {
    Swal.fire({
        icon: type,          // 'success' | 'error' | 'warning' | 'info' | 'question'
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
    });
}
