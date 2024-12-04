document.addEventListener('DOMContentLoaded', function() {
    // Using event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('#togglePassword')) {
            const passwordField = document.querySelector('#password');
            const icon = e.target.closest('#togglePassword').querySelector('i');
            
            // Toggle password visibility
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle icon
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    });
});