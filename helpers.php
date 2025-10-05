<?php
// helpers.php

// CSRF helpers
function csrf_ensure_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    $t = csrf_ensure_token();
    echo '<input type="hidden" name="csrf_token" value="'.h($t).'">';
}
function csrf_check() {
    $ok = isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    if (!$ok) {
        http_response_code(400);
        die('Bad Request: invalid CSRF token.');
    }
}
?>
