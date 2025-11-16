function generatePassword(length = 12) {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let password = '';
    for (let i = 0; i < length; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

function attachPasswordGenerator(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);
    
    if (button && input) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const password = generatePassword(12);
            input.value = password;
            input.type = 'text';
        });
    }
}
