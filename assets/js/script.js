
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.toggle-password');
    passwordToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const passwordField = document.querySelector(toggle.getAttribute('data-target'));
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        });
    });
});
