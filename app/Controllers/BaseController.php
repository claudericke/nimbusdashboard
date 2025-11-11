<?php

class BaseController {
    protected function view($viewPath, $data = []) {
        extract($data);
        $viewFile = __DIR__ . "/../../views/{$viewPath}.php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("View not found: {$viewPath}");
        }
    }

    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth() {
        if (!Session::isLoggedIn()) {
            $this->redirect('/');
        }
    }

    protected function requireSuperuser() {
        $this->requireAuth();
        if (!Session::isSuperuser()) {
            $this->redirect('/dashboard');
        }
    }
}
